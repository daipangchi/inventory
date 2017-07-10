<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\AmazonCategory;
use App\Models\Category;
use App\Models\CategoryDeduction;
use App\Models\CategoryTax;
use App\Models\EbayCategory;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * CategoriesController constructor.
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::with('subcategories');

            if ($parentId = $request->get('parent_id')) {
                $query->where('parent_id', (int)$parentId);
            }

            return api($query->get()->toArray());
        }

        $categories = Category::getAllNested();
        $countries = COUNTRIES;

        return view('pages.categories.index', compact('categories', 'countries'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validateCategory($request);

        $category = Category::create($request->only('parent_id', 'name', 'description', 'fee', 'custom_code'));

        if ($taxes = $request->get('taxes')) {
            $this->createOrUpdateCategoryTaxes($taxes, $category);
        }

        if ($deductions = $request->get('deduction')) {
            $this->createOrUpdateCategoryDeduction($deductions, $category);
        }

        return redirect("/categories/$category->category_id/edit");
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function show($id)
    {
        return Category::with('subcategories')->find($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $category = Category::with('taxes', 'ebayCategories', 'amazonCategories')->findOrFail($id);
        $categories = Category::getAllNested();
        $amazonCategories = AmazonCategory::whereCadabraCategoryId(0)->whereNodeLevel($category->level)->get();
        $ebayCategories = EbayCategory::whereCadabraCategoryId(0)->whereEbayCategoryLevel($category->level)->get();
        $countries = COUNTRIES;

        return view('pages.categories.edit', compact(
            'category',
            'categories',
            'countries',
            'amazonCategories',
            'ebayCategories'
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $this->validateCategory($request);

        $category = Category::with('parent')->findOrFail($id);                                                   
        //$category->update($request->only('parent_id', 'name', 'name_hebrew', 'description', 'fee', 'custom_code'));
        $category->update($request->only('name', 'name_hebrew', 'description', 'fee', 'custom_code'));

        if ($taxes = $request->get('taxes')) {
            $this->createOrUpdateCategoryTaxes($taxes, $category);
        }

        if ($deductions = $request->get('deduction')) {
            $this->createOrUpdateCategoryDeduction($deductions, $category);
        }

//        $this->mapCategories($request->get('category_mappings') ?? [], $category);

        return redirect("/categories/$category->category_id/edit");
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $category = Category::whereId($id)->firstOrFail();

        $category->delete();

        return api('Good');
    }

    /**
     * @param Request $request
     */
    protected function validateCategory(Request $request)
    {
        $this->validate($request, [
            'name'                    => 'required|max:255',
            'name_hebrew'             => 'required|max:255',
            'description'             => 'max:1024',
            'fee'                     => 'numeric',
            'custom_code'             => 'max:255',
            'taxes.*.country_code'    => 'required|in:'.implode(',', array_keys(COUNTRIES)),
            'taxes.*.percentage'      => 'required|numeric|min:0',
            'deductions.*.channel'    => 'required|in:amazon,ebay',
            'deductions.*.percentage' => 'required|numeric|min:0',
        ]);
    }

    /**
     * @param $taxes
     * @param $category
     */
    protected function createOrUpdateCategoryTaxes($taxes, $category)
    {
        if ($taxes) {
            foreach ($taxes as $tax) {
                if (! $tax['country_code'] && ! $tax['percentage']) continue;

                $existing = CategoryTax::whereCategoryId($category->category_id)->whereCountryCode($tax['country_code'])->first();

                if ($existing) {
                    $existing->update(['percentage' => $tax['percentage']]);
                }
                else {
                    CategoryTax::create([
                        'category_id'  => $category->category_id,
                        'country_code' => $tax['country_code'],
                        'percentage'   => $tax['percentage'],
                    ]);
                }
            }
        }
    }

    /**
     * @param $deduction
     * @param $category
     */
    protected function createOrUpdateCategoryDeduction($deduction, $category)
    {
        $existing = CategoryDeduction::whereCategoryId($category->category_id)->first();

        if ($existing) {
            $existing->update([
                'amazon_deduction' => $deduction['amazon_deduction'],
                'ebay_deduction'   => $deduction['ebay_deduction'],
            ]);
        }
        else {
            CategoryDeduction::create([
                'category_id'      => $category->category_id,
                'amazon_deduction' => $deduction['amazon_deduction'],
                'ebay_deduction'   => $deduction['ebay_deduction'],
            ]);
        }
    }

    /**
     * @param array $amazonCategoryIds
     * @param $category
     */
    protected function mapCategories(array $amazonCategoryIds, $category)
    {
//        if (empty($amazonCategoryIds)) return;
//
//        // Whatever is missing from the array means they unchecked it and want to remove it.
//        CategoryAmazonMapping::whereNotIn('amazon_node_id', $amazonCategoryIds)
//            ->where('cadabra_category_id', $category->id)
//            ->delete();
//
//        foreach ($amazonCategoryIds as $categoryId) {
//            CategoryAmazonMapping::firstOrCreate([
//                'mappable_type' => CHANNEL_AMAZON, // we will later map amazon categories through the ebay categories.
//                'mappable_id'   => $categoryId,
//                'category_id'   => $category->id,
//            ]);
//        }
    }
}
