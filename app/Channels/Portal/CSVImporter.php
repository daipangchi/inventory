<?php

namespace App;

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Products\Image;
use App\Models\Products\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Validator;

class CSVImporter
{
    /**
     * @var Collection
     */
    protected $products;

    /**
     * @var array
     */
    protected $savedProducts = [];

    /**
     * Used to rollback updated products to their old attributes
     *
     * @var array
     */
    protected $updatedProductsOldAttributes = [];

    /**
     * @var array
     */
    protected $savedProductImages = [];

    /**
     * @var array
     */
    protected $savedCategoryProducts = [];

    /**
     * @var array
     */
    protected $currentRow = [];

    /**
     * CSVImporter constructor.
     *
     * @param array $products
     */
    public function __construct(array $products)
    {
        array_splice($products, 0, 2); // Remove header rows

        $this->columns = array_map(function ($column) {
            return str_replace('*', '', $column);
        }, array_splice($products, 0, 1)[0]);

        $products = $this->filterEmptyRows($products);

        $products = collect($this->transform($products))
            ->sort(function ($a, $b) {
                // Move parent products to top

                if ($a['parent_sku'] == '' && $b['parent_sku'] != '') {
                    return -1;
                }

                if ($b['parent_sku'] == '' && $a['parent_sku'] != '') {
                    return 1;
                }

                return 0;
            });

        // Apply the correct type to product.
        $this->products = $products->map(function ($product) use ($products) {
            $hasChildren = $products->where('parent_sku', $product['sku'])->count();

            // If product has child products, set to 'configurable' else set to 'simple'
            $product['type'] = $hasChildren ? Product::TYPE_CONFIGURABLE : Product::TYPE_SIMPLE;

            return $product;
        });
    }

    /**
     *
     */
    public function saveProducts()
    {
        foreach ($this->products as $index => $product) {
            $imageUrls = $product['image_urls'];
            $categoryId = $product['category_id'];
            unset($product['image_urls'], $product['category_id']);

            $savedProduct = $this->saveProduct($product);
            $this->saveProductImages($savedProduct, $imageUrls);
            $this->saveProductCategories($savedProduct, $categoryId);
        }
    }

    /**
     * @return Collection
     */
    public function getProducts() : Collection
    {
        return $this->products;
    }

    /**
     * @return CSVImporter
     */
    public function rollback() : CSVImporter
    {
        Product::whereIn('id', $this->savedProducts)->delete();
        Image::whereIn('id', $this->savedProductImages)->delete();
        CategoryProduct::whereIn('id', $this->savedCategoryProducts)->delete();

        foreach ($this->updatedProductsOldAttributes as $old) {
            $product = Product::find($old['id']);

            if ($product) {
                $product->update($old);
            }
        }

        return $this;
    }

    /**
     * @param array $products
     * @return array
     */
    protected function filterEmptyRows(array $products)
    {
        return array_filter(array_map(function ($product) {
            if (! empty(array_filter($product))) {
                return $product;
            }
        }, $products));
    }

    /**
     * @param $products
     * @return array
     */
    protected function transform($products) : array
    {
        foreach ($products as $index => $product) {
            $transformed = [];

            foreach ($product as $i => $cell) {
                $transformed[$this->columns[$i]] = $cell;
            }

            $products[$index] = $this->adaptProductAttributes($transformed);
        }

        $products = array_filter($products, function ($product) {
            $isEmpty = true;

            foreach ($product as $value) {
                if ($value !== '') {
                    $isEmpty = false;
                    continue;
                }
            }

            if ($isEmpty) {
                return false;
            }
            else {
                return true;
            }
        });

        return $products;
    }

    /**
     * @param $product
     * @return array
     */
    protected function adaptProductAttributes(array $product) : array
    {
        $attributes = [
            'merchant_id'             => auth()->id(),
            'sku'                     => $product['sku'],
            'parent_sku'              => $product['parent_sku'],
            'name'                    => $product['name'],
            'description'             => $product['description'],
            'brand'                   => $product['brand'],
            'quantity'                => $product['quantity'],
            'condition'               => strtolower($product['condition'] ?: 'new'),
            'price'                   => $product['price'],
            'msrp'                    => $product['msrp'],
            'manufacturer'            => $product['manufacturer'],
            'model_number'            => $product['model_number'],
            'product_identifier'      => $product['product_identifier'],
            'product_identifier_type' => strtolower($product['product_identifier_type']),
            'weight'                  => $product['weight'],
            'weight_unit'             => strtolower($product['weight_unit']),
            'height'                  => $product['height'],
            'width'                   => $product['width'],
            'length'                  => $product['length'],
            'dimensions_unit'         => strtolower($product['dimensions_unit']),
            'amazon_asin'             => $product['amazon_asin'],
            'ebay_id'                 => $product['ebay_id'],
            'is_published'            => strtolower($product['published']),
            'channel'                 => CHANNEL_CSV_IMPORT,

            'attributes'  => array_filter([
                $product['option_1_name'] => $product['option_1_value'],
                $product['option_2_name'] => $product['option_2_value'],
                $product['option_3_name'] => $product['option_3_value'],
                $product['option_4_name'] => $product['option_4_value'],
                $product['option_5_name'] => $product['option_5_value'],
            ]),
            'features'    => array_filter([
                $product['feature_1'],
                $product['feature_2'],
                $product['feature_3'],
            ]),

            // these will be unset when saving on the product
            'image_urls'  => $product['image_urls'],
            'category_id' => $product['category_id'],
        ];

        return $attributes;
    }

    /**
     * @param $file
     * @return CSVImporter
     */
    public static function parse(\Illuminate\Http\UploadedFile $file) : CSVImporter
    {
        $products = array_map('str_getcsv', file($file->getRealPath()));

        unlink($file->getRealPath());

        return new static($products);
    }

    /**
     * @param $product
     * @return Product
     */
    protected function saveProduct($product) : Product
    {
        // Update if it exists.
        if ($saved = auth()->user()->getProductBySku($product['sku'])) {
            // Save old attributes for rollback
            $this->updatedProductsOldAttributes[] = $saved->toArray();
            $saved->update($product);
        }
        else {
            if ($product['parent_sku']) {
                if ($parent = auth()->user()->getProductBySku($product['parent_sku'])) {
                    $product['parent_id'] = $parent->id;
                    Product::updateParentVariations($parent, $product);
                }
            }

            $saved = Product::create($product);
            $this->savedProducts[] = $saved->id;
        }

        return $saved;
    }

    /**
     * @param Product $product
     * @param $imageUrls
     * @return CSVImporter
     */
    protected function saveProductImages(Product $product, $imageUrls) : CSVImporter
    {
        $product->downloadAndAttachImages(explode('|', $imageUrls));

        return $this;
    }

    /**
     * @param $product
     * @param $categoryId
     * @return CSVImporter
     */
    protected function saveProductCategories($product, $categoryId) : CSVImporter
    {
        if ($category = Category::whereCategoryId($categoryId)->first()) {
            foreach (explode(',', $category->path_by_category_id) as $categoryId) {
                CategoryProduct::create([
                    'category_id' => $categoryId,
                    'product_id'  => $product->id,
                ]);
            }
        }

        return $this;
    }

    /**
     * @param $product
     * @param $index
     * @throws ValidationException
     */
    public static function validate($product, $index)
    {
        $validator = Validator::make($product, [
            'sku'       => 'required',
            'name'      => 'required',
            'condition' => 'required|in:new,used,reconditioned,refurbished',
            'quantity'  => 'numeric',
            'price'     => 'required|numeric',
            'msrp'      => 'numeric',
            'published' => 'in:true,false',

            'product_identifier'      => 'required_with:product_identifier_type',
            'product_identifier_type' => 'in:upc,isbn,ean',

            'weight'      => 'numeric|required_with:weight_unit',
            'weight_unit' => 'required_with:weight|in:ounces,grams',

            'height'          => 'numeric|required_with:dimensions_unit,width,length',
            'width'           => 'numeric|required_with:dimensions_unit,height,length',
            'length'          => 'numeric|required_with:dimensions_unit,height,width',
            'dimensions_unit' => 'in:inches,centimeters',

            'option_1_name'  => 'required_with:option_1_value',
            'option_1_value' => 'required_with:option_1_name',

            'option_2_name'  => 'required_with:option_2_value',
            'option_2_value' => 'required_with:option_2_name',

            'option_3_name'  => 'required_with:option_3_value',
            'option_3_value' => 'required_with:option_3_name',

            'option_4_name'  => 'required_with:option_4_value',
            'option_4_value' => 'required_with:option_4_name',

            'option_5_name'  => 'required_with:option_5_value',
            'option_5_value' => 'required_with:option_5_name',

        ]);

        if ($validator->fails()) {
            // Add 4 because the top 3 lines were removed rows and csv is 1 indexed
            // and we want to show the user which line on *their* csv file caused the issue
            $index = $index + 4;
            throw new ValidationException($validator, "Error at row $index");
        }
    }
}
