<?php

namespace App\Channels\Portal;

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
        // TODO: Implement extractAttributes() method.
    }

    /**
     * Extract category ids.
     *
     * @param $data
     * @return array
     */
    public function extractCategoryIds($data) : array
    {
        // TODO: Implement extractCategoryIds() method.
    }

    /**
     * Extract image urls
     *
     * @param $data
     * @return array
     */
    public function extractImageUrls($data) : array
    {
        // TODO: Implement extractImageUrls() method.
    }
}