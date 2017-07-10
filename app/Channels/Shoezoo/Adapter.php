<?php

namespace App\Channels\Shoezoo;

use App\Models\Category;
use App\Models\Merchants\Merchant;
use App\Policies\ProductsAdapter;

class Adapter implements ProductsAdapter
{
    /**
     * @var Merchant
     */
    protected $merchant;

    /**
     * ProductsAdapter constructor.
     *
     * @param Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;

        $this->categories = Category::all()->map(function (Category $category) {
            // Since collection querying is case sensitive.
            $category->path_by_name = strtolower($category->path_by_name);

            return $category;
        });
    }

    /**
     * Adapt product attributes to fit into the Cadabra database.
     *
     * @param $data
     * @return array
     */
    public function extractAttributes($data) : array
    {
        $adapted = [
            'merchant_id'             => $this->merchant->id,
            'sku'                     => $data['sku'],
            'parent_sku'              => $data['product.type'] == 'simple' ? str_replace(' ', '_', $data['mfg_id']) : null,
            'name'                    => $data['name'],
            'description'             => $data['description'],
            'type'                    => $data['product.type'],
            'manufacturer'            => $data['manufacturer'],
            'quantity'                => $data['stock.qty'],
            'original_price'          => $data['price'],
            'product_identifier'      => $data['upc'],
            'product_identifier_type' => 'upc',
            'condition'               => 'new',
            'attributes'              => $this->extractProductAttributes($data),
        ];

        if ($data['product.type'] == 'simple') {
            $adapted['parent_sku'] = $data['mfg_id'];
        }

        return $adapted;
    }

    /**
     * Extract category ids.
     *
     * @param $data
     * @return array
     */
    public function extractCategoryIds($data) : array
    {
        $categories = explode(';', $data[0]);
        $subCategories = explode(';', $data[1]);
        $paths = [];

        foreach ($categories as $category) {
            foreach ($subCategories as $subCategory) {
                // Since collection querying is case sensitive.
                $paths[] = strtolower("Shoes > $category > $subCategory");
            }
        }

        $categories = $this->categories->whereIn('path_by_name', $paths)->pluck('path_by_category_id');

        return $categories->map(function ($pathById) {
            return explode(',', $pathById);
        })->collapse()->unique()->toArray();
    }

    /**
     * Extract image urls
     *
     * @param $data
     * @return array
     */
    public function extractImageUrls($data) : array
    {
        return array_filter(explode(';', $data));
    }

    /**
     * @param $images
     * @return mixed
     */
    public function parseImages($images)
    {
        return explode(';', $images);
    }

    /**
     * @param $attributes
     * @return array
     */
    public function extractProductAttributes($attributes)
    {
        return array_filter([
            'Color'        => $attributes['color_raw'],
            'Shoe Size'    => $attributes['shoe_size_display']
            // 'Size Men'     => $attributes['shoe_size_men'],
            // 'Size Women'   => $attributes['shoe_size_women'],
            // 'Size Kids'    => $attributes['shoe_size_kids'],
            // 'Size Infants' => $attributes['shoe_size_infants'],
        ], function ($item) {
            return $item !== '';
        });
    }
}
