<?php

namespace App\Http\Controllers;

use App\CSVImporter;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Products\ChangeLog;
use App\Models\Products\ChangeLogDataTypes\CreatedDataType;
use App\Models\Products\ChangeLogDataTypes\UpdatedDataType;
use App\Models\Products\Product;
use App\Models\Products\ProductDescription;
use Illuminate\Http\Request;
use DB, URL, Redirect;

class InventoryController extends Controller
{
    /**
     * InventoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show inventory index page or send
     * queried data back.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {

        $query = Product::with('images')->where('parent_id', NULL);

        if (!auth()->user()->is_admin) {
            $query->whereMerchantId(auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            });
        }
        
        if (($status = $request->get('status')) != '') {
            if($status == Product::STATUS_PENDING) {
                $query->leftJoin(DB::raw('(SELECT product_id, count(*) cat_num FROM categories_products GROUP BY product_id) tmp'), 'tmp.product_id', '=', 'products.id')
                    ->where('status', '!=', Product::STATUS_PROCESSING)
                    ->whereRaw('tmp.cat_num IS NULL');
            } else {
                $query->where('status', $status);
            }
        }

        if((($status = $request->get('status')) == '') && (!$request->get('search'))){
            $query->where('status', '!=', '5');
        }

        $guarded = (new Product())->getGuarded();
        $sort = $request->get('sort');

        if ($sort && !in_array($sort, $guarded)) {
            $query->orderBy($sort, $request->get('sort_direction'));
        }
                   
        $totalCount = $query->count();
        $products = $query->paginate()->appends($request->except('page'));          

        return view('pages.inventory.index', compact('products', 'totalCount'));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('pages.inventory.create');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, Product::getValidationRules(auth()->id()));

        $product = Product::createWithChildren($request);

        if (!$product) {
            return redirect()->back()->withInput($request->all());
        }

        $data = new CreatedDataType(CHANNEL_MERCHANT_PORTAL, $product->price, $product->quantity ?: 0);
        ChangeLog::log($product->id, CHANNEL_MERCHANT_PORTAL, ChangeLog::ACTION_CREATED, $data);

        session()->flash('success', 'Product successfully created.');

        return redirect("/inventory/$product->id/edit");
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $query = Product::with(['children', 'images', 'categories'])
            ->with(['changeLogs' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }]);

        if (auth()->user()->is_admin) {
            $product = $query->findOrFail($id);
        }
        else {
            $product = $query->whereMerchantId(auth()->id())->findOrFail($id);
        }

        $categories = Category::getAllNested();

        return view('pages.inventory.edit', compact('product', 'categories', 'history'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->is_admin) {
            $product = Product::findOrFail($id);
        }
        else {
            $product = Product::whereMerchantId(auth()->id())->findOrFail($id);
        }

        switch ($request->get('_type')) {
            case 'categories':
                $this->detachProductCategories($product);
            
                if(count($request->get('categoryIds')) > 0) {
                    $this->attachProductCategories($request->get('categoryIds'), $product);
                    /*if($product->weight > 0) {
                        $product->status = Product::STATUS_ACTIVE;
                        //$product->save();
                    }*/
                    
                    // set publish price
                    //$product->publish_price = $product->price + $product->categoryFee;
                    //$product->save();
                } else {
                    /*$product->status = Product::STATUS_PENDING_NO_CATEGORY;
                    $product->save();*/
                }

                return api('Good');
            case 'specs':
                // Remove empty fields first.
                $specs = $request->get('specs') ?: [];

                $formatted = [];

                foreach ($specs as $spec) {
                    if ($spec['name']) {
                        $formatted[$spec['name']] = $spec['values'] ?? [];
                    }
                }

                $product->specs = $formatted;
                $product->save();

                return redirect("/inventory/$product->id/edit");
        }

        $this->validate($request, [
            'name'                    => 'required|string|max:255',
            'condition'               => 'required|in:new,used,reconditioned',
            'manufacturer'            => 'string',
            'model_number'            => 'max:255',
            'product_identifier'      => 'max:255',
            'product_identifier_type' => 'required_with:product_identifier',
            'msrp'                    => '',
            'price'                   => 'required|numeric',
            'quantity'                => 'required|integer',
        ]);

        $attributes = $request->only(
            //'name', 'description', 'condition', 'manufacturer', 'model_number', 'quantity',
            'name', 'condition', 'manufacturer', 'model_number', 'quantity',
            'product_identifier', 'product_identifier_type', 'msrp', 'price',
            'weight', 'weight_unit', 'height', 'width', 'length', 'dimensions_unit'
        );
        
        // update product status
        if($request->get('weight') == 0){
            if(($product->status == 1) || ($product->status == 2)){
                $product->status = Product::STATUS_PENDING_NO_WEIGHT;
            }
        }else{
            if($product->status != 2){
                $product->status = Product::STATUS_ACTIVE;
            }
        }

        $product->save();
        
        // save description to products_description table
        $descriptionRow = ProductDescription::firstOrCreate(['product_id' => $id, 'title' => $product->name]);
        $descriptionRow->description = $request->get('description');
        $descriptionRow->save();
        
        // Log if price changed.
        if ($product->price != $attributes['price']) {
            $data = new UpdatedDataType(
                UpdatedDataType::ENTITY_PRICE,
                $product->price,
                (float)$attributes['price'],
                UpdatedDataType::PRICE_CHANGE_SOURCE_MERCHANT_PORTAL
            );

            ChangeLog::log($product->id, CHANNEL_MERCHANT_PORTAL, ChangeLog::ACTION_UPDATED, $data);
        }

        // Log if quantity changed.
        if ($product->quantity != $attributes['quantity']) {
            $data = new UpdatedDataType(
                UpdatedDataType::ENTITY_QUANTITY,
                $product->quantity,
                (int)$attributes['quantity'],
                UpdatedDataType::QUANTITY_CHANGE_SOURCE_MERCHANT_PORTAL
            );

            ChangeLog::log($product->id, CHANNEL_MERCHANT_PORTAL, ChangeLog::ACTION_UPDATED, $data);
        }

        $attributes['attributes'] = [];

        foreach ($request->get('attributes') ?: [] as $attribute) {
            if ($attribute['name'] !== '' && $attribute['value'] !== '') {
                $attributes['attributes'][$attribute['name']] = $attribute['value'];
            }
        }

        $product->update($attributes);

        session()->flash('success', 'Product successfully updated.');

        return redirect("/inventory/$product->id/edit");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function bulkUpdate(Request $request)
    {
        $this->validate($request, [
            'data.*.name'     => 'required',
            'data.*.price'    => 'required|numeric',
            'data.*.weight'   => 'numeric',
            'data.*.quantity' => 'numeric',
        ]);

        $updated = [];

        foreach ($request->get('data') as $id => $attributes) {
            $updated[] = $product = Product::findOrFail($id);
            $product->update($attributes);
        }

        return response(['data' => $updated]);
    }

    /**
     * Show page for importing csv.
     *
     * @return mixed
     */
    public function getImportCsv()
    {
        return view('pages.inventory.csv');
    }

    /**
     * Show page for importing csv.
     *
     * @param Request $request
     * @return mixed
     */
    public function getCategories(Request $request)
    {
        $parentIds = Category::pluck('parent_id')->toArray();
        $query = Category::whereNotIn('category_id', $parentIds)->select(['category_id', 'path_by_name']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('path_by_name', 'like', "%$search%");
            });
        }

        $categories = $query->paginate(20);

        return view('pages.inventory.csv-categories', compact('categories'));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function postImportCsv(Request $request)
    {
        try {
            $importer = CSVImporter::parse($request->file('file'));
        }
        catch (\Exception $e) {
            return api($e->getMessage(), 400);
        }

        foreach ($importer->getProducts() as $index => $product) {
            CSVImporter::validate($product, $index);
        }

        try {
            $importer->saveProducts();
        }
        catch (\Exception $e) {
            $importer->rollback();

            throw $e;
        }

        return response('success');
    }

    /**
     * @param array $categoryIds
     * @param $product
     */
    public function attachProductCategories(array $categoryIds, $product)
    {
        $ids = [];

        // Get all categories selected by user
        $categories = Category::whereIn('category_id', $categoryIds)->get();
        $product->categoryFee = 0;

        // Get all categories in the path (all parents).
        foreach ($categories as $category) {
            $pathById = explode(',', $category->path_by_category_id);
            $ids = array_merge($ids, $pathById);
            
            if($product->categoryFee <= $category->fee) {
                $product->categoryFee = $category->fee;
            }
        }

        $ids = array_unique($ids);

        // Attach to categories.
        CategoryProduct::attach($product->id, $ids);

        // Attach the same categories to the child products.
        foreach ($product->children as $child) {
            CategoryProduct::attach($child->id, $ids);
        }
    }
    
    public function detachProductCategories($product)
    {
        CategoryProduct::where('product_id', $product->id)->delete();
    }       
    
    public function disable($productId) {
        $product = Product::find($productId);
        if(isset($product->id)) {
            $product->previous_status = $product->status; // save current status
            $product->status = Product::STATUS_DISABLED;
            $product->save();    
        }
        
        return Redirect::to(URL::previous());
    }
    
    public function enable($productId) {
        $product = Product::find($productId);
        if(isset($product->id)) {
            $product->status = $product->previous_status; // restore old status
            $product->save();    
        }
        
        return Redirect::to(URL::previous());
    }
}
