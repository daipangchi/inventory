<?php

namespace App\Policies;

use App\Models\Merchants\Merchant;

interface ProductsAdapter
{
    /**
     * ProductsAdapter constructor.
     *
     * @param Merchant $merchant
     */
    public function __construct(Merchant $merchant);

    /**
     * Extract product attributes to fit into the Cadabra database.
     *
     * @param $data
     * @return array
     */
    public function extractAttributes($data) : array;

    /**
     * Extract category ids.
     *
     * @param $data
     * @return array
     */
    public function extractCategoryIds($data) : array;

    /**
     * Extract image urls
     *
     * @param $data
     * @return array
     */
    public function extractImageUrls($data) : array;
}
