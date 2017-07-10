<?php

namespace App\CustomerPortal\Magmi;

use App\Models\Category;
use App\Models\Merchants\Merchant;
use App\Models\Products\Product;
use File;

class CsvGenerator
{
    /**
     * @var Merchant
     */
    protected $merchant;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $configurableAttributes = [];

    /**
     * @var array
     */
    protected $specs = [];

    /**
     * @var resource
     */
    protected $attributeStream;

    /**
     * @var resource
     */
    protected $attributeSetStream;

    /**
     * @var resource
     */
    protected $attributeSetAssociationStream;

    /**
     * @var resource
     */
    protected $productsStream;

    /**
     * Used to prevent duplicates.
     *
     * @var array
     */
    protected $existingAttrs = [];

    /**
     * Used to prevent duplicates.
     *
     * @var array
     */
    protected $existingAttrSets = [];

    /**
     * Used to prevent duplicates.
     *
     * @var array
     */
    protected $existingAttrSetAssocs = [];

    /**
     * Amount to chunk the products by to prevent memory overload.
     */
    const CHUNK_AMOUNT = 5000;

    /**
     * Products constructor.
     *
     * @param Merchant $merchant
     * @param string $action
     */
    public function __construct(Merchant $merchant, string $action)
    {
        ini_set('memory_limit', '-1');

        $dir = app()->environment('testing') ? 'testing' : 'magento';

        $this->writePath = storage_path("$dir/export_files");
        $this->merchant = $merchant;
        $this->action = $action;

        $this->attributeStream = $this->openFileStream('attribute.csv');
        $this->attributeSetStream = $this->openFileStream('attribute_set.csv');
        $this->attributeSetAssociationStream = $this->openFileStream('attribute_set_association.csv');
        $this->productsStream = $this->openFileStream('products.csv');
    }

    /**
     * Generate the csv files.
     *
     * @return void
     */
    function generate()
    {
        // Do not continue if empty
        if (! $this->getProductsQuery()->count()) {
            return;
        }

        if ($this->action == 'create') {
            $this->generateCreateCsv();
        }
        else if ($this->action == 'update') {
            $this->generateUpdateCsv();
        }
    }

    /**
     * Generate csv files for a magmi import create command.
     *
     * @return void
     */
    protected function generateCreateCsv()
    {
        fputcsv($this->attributeStream, $this->getAttrColumns());

        $this->getProductsQuery()->with('children', 'categories')
            ->chunk(static::CHUNK_AMOUNT, function ($products) {
                foreach ($products as $product) {
                    if (! $product->categories->count()) {
                        continue;
                    }

                    $this->processAttributes($product->attributes, 'select', 'int');
                    $this->processAttributes($product->specs, 'text', 'text');

                    foreach ($product->children as $child) {
                        $this->processAttributes($child->attributes, 'select', 'int');
                        $this->processAttributes($child->specs, 'text', 'text');
                    }
                }
            });

        fputcsv($this->attributeSetStream, $this->getAttributeSetColumns());
        fputcsv($this->attributeSetAssociationStream, $this->getAttributeSetAssociationColumns());
        fputcsv($this->productsStream, $this->getProductsColumns());

        $this->getProductsQuery()->with('categories', 'children.images', 'images')
            ->chunk(static::CHUNK_AMOUNT, function ($products) {
                /** @var Product $product */
                foreach ($products as $product) {
                    $this->handleAttributeSets($product);
                    $this->handleAttributeSetAssociations($product);
                    $this->handleProducts($product);
                }
            });

        fclose($this->attributeStream);
        fclose($this->attributeSetStream);
        fclose($this->attributeSetAssociationStream);
        fclose($this->productsStream);
    }

    /**
     * Generate csv files for a magmi import update command.
     *
     * @return void
     */
    protected function generateUpdateCsv()
    {
        $this->getProductsQuery()->with('children', 'categories')
            ->chunk(static::CHUNK_AMOUNT, function ($products) {
                foreach ($products as $product) {
                    if (! $product->categories->count()) {
                        continue;
                    }

                    $this->processAttributes($product->attributes, 'select', 'int');
                    $this->processAttributes($product->specs, 'text', 'text');

                    foreach ($product->children as $child) {
                        $this->processAttributes($child->attributes, 'select', 'int');
                        $this->processAttributes($child->specs, 'text', 'text');
                    }
                }
            });

        fputcsv($this->productsStream, $this->getProductsColumns());

        $this->getProductsQuery()->with('categories', 'children.images', 'images')
            ->chunk(static::CHUNK_AMOUNT, function ($products) {
                /** @var Product $product */
                foreach ($products as $product) {
                    $this->handleProducts($product);
                }
            });

        fclose($this->productsStream);
    }

    /**
     * Write to attribute_set.csv
     *
     * @param Product $product
     * @return void
     */
    protected function handleAttributeSets(Product $product)
    {
        $categories = $product->categories->where('level', 2);

        foreach ($categories as $category) {
            $name = $category->category_id.'_'.$category->name;

            if (! in_array($name, $this->existingAttrSets)) {
                $this->existingAttrSets[] = $name;

                fputcsv($this->attributeSetStream, [
                    $this->sanitizeAttributeCode($name),
                    'General:1,Prices:2,Meta Information:3,Images:4,Recurring Profile:5,Design:6,Gift Options:8,Specs:9',
                ]);
            }
        }
    }

    /**
     * Write to attribute_set_association.csv
     *
     * @param Product $product
     * @return void
     */
    protected function handleAttributeSetAssociations(Product $product)
    {
        $category = $product->categories->where('level', 2)->first();

        if (! $category) {
            return;
        }

        $categoryName = $category->category_id.'_'.$category->name;

        $this->processAttributeSetAssociations($product->attributes, $categoryName, 'General');
        $this->processAttributeSetAssociations($product->specs, $categoryName, 'Specs');

        foreach ($product->children as $child) {
            $this->processAttributeSetAssociations($child->attributes, $categoryName, 'General');
            $this->processAttributeSetAssociations($child->specs, $categoryName, 'Specs');
        }
    }

    /**
     * Write to products.csv
     *
     * @param Product $product
     * @return void
     */
    protected function handleProducts(Product $product)
    {
        $category = $product->categories->where('level', 3)->first()
            ?: $product->categories->where('level', 2)->first();

        if (! $category) {
            return;
        }

        $attributeSet = $this->getAttributeSet($product);

        foreach ($product->children as $child) {
            fputcsv($this->productsStream, $this->generateProductsRow($child, $attributeSet, $category));
        }

        fputcsv($this->productsStream, $this->generateProductsRow($product, $attributeSet, $category));
    }

    /**
     * @return array
     */
    protected function getAttrColumns()
    {
        return [
            'attribute_code',
            'is_global',
            'frontend_input',
            'default_value_text',
            'is_unique',
            'is_required',
            'is_searchable',
            'is_visible_in_advanced_search',
            'is_comparable',
            'is_filterable',
            'is_filterable_in_search',
            'is_used_for_promo_rules',
            'position',
            'is_html_allowed_on_front',
            'is_visible_on_front',
            'used_in_product_listing',
            'used_for_sort_by',
            'backend_type',
            'is_user_defined',
            'frontend_label',
        ];
    }

    /**
     * @return array
     */
    protected function getAttributeSetColumns()
    {
        return [
            'attribute_set_name',
            'magmi:groups',
        ];
    }

    /**
     * @return array
     */
    protected function getAttributeSetAssociationColumns()
    {
        return [
            'attribute_set_name',
            'attribute_code',
            'attribute_group_name',
        ];
    }

    /**
     * @return array
     */
    protected function getProductsColumns()
    {
        $map = function ($column) {
            $column = str_replace(' ', '_', $column);
            $column = strtolower($column);

            return '_'.preg_replace('/[^a-z0-9_]/', '', $column);
        };

        $columns = [
            'sku', 'attribute_set', 'type', 'simple_skus', 'store', 'price', 'qty', 'is_in_stock', 'manage_stock',
            'use_config_manage_stock', 'status', 'visibility', 'weight', 'categories', 'tax_class_id', 'thumbnail',
            'small_image', 'image', 'media_gallery', 'name', 'merchant_id', 'condition', 'configurable_attributes',
        ];

        $configurableAttributes = array_map($map, $this->configurableAttributes);
        $specs = array_map($map, $this->specs);

        return array_merge($columns, $configurableAttributes, $specs);
    }

    /**
     * @param $attributes
     * @param $categoryName
     * @param $groupName
     */
    protected function processAttributeSetAssociations($attributes, $categoryName, $groupName)
    {
        if (is_array($attributes)) {
            $attributeSetName = $this->sanitizeAttributeCode($categoryName);

            foreach ($attributes as $name => $value) {
                $row = [
                    'attribute_set_name'   => $attributeSetName,
                    'attribute_code'       => '_'.$this->sanitizeAttributeCode($name),
                    'attribute_group_name' => $groupName,
                ];

                $needle = implode('', $row);

                if (! in_array($needle, $this->existingAttrSetAssocs)) {
                    $this->existingAttrSetAssocs[] = $needle;

                    fputcsv($this->attributeSetAssociationStream, $row);
                }
            }

            $row = [
                'attribute_set_name'   => $attributeSetName,
                'attribute_code'       => '_merchant_id',
                'attribute_group_name' => 'General',
            ];

            $needle = implode('', $row);

            if (! in_array($needle, $this->existingAttrSetAssocs)) {
                $this->existingAttrSetAssocs[] = $needle;

                fputcsv($this->attributeSetAssociationStream, $row);
            }
        }
    }

    /**
     * @param Product $product
     * @param string $attributeSet
     * @param Category $category
     * @return array
     */
    protected function generateProductsRow(Product $product, string $attributeSet, Category $category)
    {
        $imageUrl = str_replace('/storage/products/', '', $product->images->first()->url ?? '');

        $row = array_values([
            'sku'                     => $product->id,
            'attribute_set'           => $attributeSet,
            'type'                    => $product->type,
            'simple_skus'             => $product->isParent() ? $product->children->implode('id', ',') : '',
            'store'                   => '',
            'price'                   => $product->price,
            'qty'                     => $product->quantity || 1,
            'is_in_stock'             => 1,
            'manage_stock'            => 1,
            'use_config_manage_stock' => '',
            'status'                  => 1,
            'visibility'              => $product->parent_id ? 1 : 'Catalog, Search',
            'weight'                  => $product->weight,
            'categories'              => str_replace(' > ', '/', $category->path_by_name_hebrew),
            'tax_class_id'            => 2,
            'thumbnail'               => $imageUrl,
            'small_image'             => $imageUrl,
            'image'                   => $imageUrl,
            'media_gallery'           => str_replace('/storage/products/', '', $product->images->implode('url', ';')),
            'name'                    => $product->name,
            'merchant_id'             => $product->merchant_id,
            'condition'               => $product->condition ?: 'new',
            'configurable_attributes' => $this->generateConfigurableAttributes($product),
        ]);

        foreach ($this->configurableAttributes as $attr) {
            $row[] = $product->attributes[$attr] ?? '';
        }

        foreach ($this->specs as $spec) {
            $row[] = implode('|', $product->specs[$spec] ?? []);
        }

        return $row;
    }

    /**
     * @param $attributes
     * @param $frontendInput
     * @param $backendType
     */
    protected function processAttributes($attributes, $frontendInput, $backendType)
    {
        if (is_array($attributes)) {
            foreach ($attributes as $name => $value) {
                $needle = strtolower($name.$frontendInput.$backendType);

                if (! in_array($needle, $this->existingAttrs)) {
                    $this->existingAttrs[] = $needle;

                    if ($frontendInput == 'select') {
                        $this->configurableAttributes[] = $name;
                    }
                    else {
                        $this->specs[] = $name;
                    }

                    if ($this->action == 'update') {
                        fputcsv($this->attributeStream, [
                            'attribute_code'                => '_'.$this->sanitizeAttributeCode($name),
                            'is_global'                     => 1,
                            'frontend_input'                => $frontendInput,
                            'default_value_text'            => $name,
                            'is_unique'                     => 0,
                            'is_required'                   => 0,
                            'is_searchable'                 => 1,
                            'is_visible_in_advanced_search' => 1,
                            'is_comparable'                 => 0,
                            'is_filterable'                 => 0,
                            'is_filterable_in_search'       => 0,
                            'is_used_for_promo_rules'       => 0,
                            'position'                      => 0,
                            'is_html_allowed_on_front'      => 0,
                            'is_visible_on_front'           => 1,
                            'used_in_product_listing'       => 0,
                            'used_for_sort_by'              => 0,
                            'backend_type'                  => $backendType,
                            'is_user_defined'               => 1,
                            'frontend_label'                => $name,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * @param Product $product
     * @return string
     */
    protected function getAttributeSet($product)
    {
        $attributeSetCategory = $product->categories->where('level', 2)->first();
        $attributeSet = $attributeSetCategory->category_id.'_'.$attributeSetCategory->name;

        return $this->sanitizeAttributeCode($attributeSet);
    }

    /**
     * @return mixed
     */
    protected function getProductsQuery()
    {
        $query = Product::whereMerchantId($this->merchant->id)->whereParentId(null);

        switch ($this->action) {
            case 'create':
                $query->where('exported_at', null);
                break;
            case 'update':
                $query->whereNotNull('exported_at')->whereRaw('updated_at > exported_at');
                break;
        }

        return $query;
    }

    /**
     * @param Product $product
     * @return string
     */
    protected function generateConfigurableAttributes(Product $product)
    {
        $configurable = array_keys($product->attributes ?: []);

        $configurable = array_map(function ($item) {
            $item = strtolower($item);
            $item = str_replace(' ', '_', $item);
            $item = preg_replace('/[^a-zA-Z0-9_]/', '', $item);

            return '_'.$item;
        }, $configurable);

        return implode(',', $configurable);
    }

    /**
     * @param $name
     * @return mixed|string
     */
    protected function sanitizeAttributeCode($name)
    {
        $attributeCode = str_replace(' & ', '_', $name);
        $attributeCode = str_replace(' ', '_', $attributeCode);
        $attributeCode = preg_replace('/[^a-zA-Z0-9_]/', '', $attributeCode);

        return strtolower($attributeCode);
    }

    /**
     * @param string $filename
     * @return resource
     */
    protected function openFileStream(string $filename)
    {
        $filename = preg_replace('/^\//', '', $filename);
        $path = "{$this->writePath}/{$this->merchant->id}/{$this->action}/{$filename}";
        $dirname = File::dirname($path);

        if (! File::exists($dirname)) {
            File::makeDirectory($dirname, 493, true);
        }

        return fopen($path, 'w');
    }
}
