<?php

namespace App\Channels\Accuratime;

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
    }

    /**
     * Extract product attributes to fit into the Cadabra database.
     *
     * @param $data
     * @return array
     */
    public function extractAttributes($data) : array
    {
        return [
            'merchant_id'             => $this->merchant->id,
            'sku'                     => $data['Model'],
            'name'                    => $data['Name'],
            'description'             => $data['Description'],
            'quantity'                => $data['QTY'],
            'original_price'          => $data['Sales Price'],
            'product_identifier'      => $data['UPC'],
            'product_identifier_type' => 'upc',
            'brand'                   => $data['Brand Name'],
            'condition'               => 'new',
            'specs'                   => $this->extractSpecs($data),
        ];
    }

    /**
     * Extract category ids.
     *
     * @param $data
     * @return array
     */
    public function extractCategoryIds($data) : array
    {
        $gender = $data['Gender'];
        $pathByName = "Fashion Accessories > Watches > $gender";
        $category = Category::cache()->where('path_by_name', $pathByName)->first();

        return explode(',', $category->path_by_category_id);
    }

    /**
     * Extract image urls
     *
     * @param $data
     * @return array
     */
    public function extractImageUrls($data) : array
    {
        return [$data['Image Link']];
    }

    /**
     * @param $data
     * @return string
     */
    protected function extractSpecs($data)
    {
        return array_filter([
            'Collection'                  => $data['Collection'] ? [$data['Collection']] : '',
            'Dial'                        => $data['Dial'] ? [$data['Dial']] : '',
            'Movement'                    => $data['Movement'] ? [$data['Movement']] : '',
            'Clasp'                       => $data['Clasp'] ? [$data['Clasp']] : '',
            'Strap or Bracelet'           => $data['Strap or Bracelet'] ? [$data['Strap or Bracelet']] : '',
            'Strap or Bracelet Color'     => $data['Strap or Bracelet Color'] ? [$data['Strap or Bracelet Color']] : '',
            'Case Shape'                  => $data['Case Shape'] ? [$data['Case Shape']] : '',
            'Measurement'                 => $data['Measurement'] ? [$data['Measurement']] : '',
            'Bracelet Measurement'        => $data['Bracelet Measurement'] ? [$data['Bracelet Measurement']] : '',
            'Bracelet Length (in inches)' => $data['Bracelet Length (in inches)'] ? [$data['Bracelet Length (in inches)']] : '',
            'Crystal'                     => $data['Crystal'] ? [$data['Crystal']] : '',
            'Crown'                       => $data['Crown'] ? [$data['Crown']] : '',
            'Country of Origin'           => $data['Country of Origin'] ? [$data['Country of Origin']] : '',
            'Water Resistance'            => $data['Water Resistance'] ? [$data['Water Resistance']] : '',
            'Brand'                       => $data['Brand Name'] ? [$data['Brand Name']] : '',
        ]);
    }
}
