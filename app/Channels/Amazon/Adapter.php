<?php

namespace App\Channels\Amazon;

use App\Models\AmazonCategory;
use App\Models\AmazonProductCategoryMapping;
use App\Models\Merchants\Merchant;
use App\Policies\ProductsAdapter;

/**
 * Class Adapter
 *
 * Used to transform Amazon product structure into
 * Cadabra product structure.
 *
 * @package App\Channels\Amazon
 */
class Adapter implements ProductsAdapter
{
    /**
     * @var Merchant
     */
    private $merchant;

    /**
     * @var Products
     */
    protected $productsClient;

    /**
     * Adapter constructor.
     *
     * @param Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
        $this->productsClient = new Products($this->merchant->amazon_seller_id, $this->merchant->amazon_auth_token);
    }

    /**
     * Adapt product attributes to fit into the Cadabra database.
     *
     * @param array $data
     * @return array|mixed
     */
    public function extractAttributes($data) : array
    {
        return [
            'merchant_id'             => $this->merchant->id,
            'name'                    => $data['item-name'],
            'description'             => $data['item-description'],
            'sku'                     => $data['seller-sku'],
            'price'                   => $data['price'],
            'quantity'                => $data['quantity'],
            'condition'               => $this->adaptCondition($data['item-condition']),
            'amazon_asin'             => $data['asin1'],
            'product_identifier'      => $data['product-id'],
            'weight'                  => $data['weight'] ?? 0,
            'weight_unit'             => $data['weight_unit'] ?? '',
            'product_identifier_type' => $this->adaptProductIdentifierType($data['product-id-type']),
            'channel'                 => CHANNEL_AMAZON,
            'specs'                   => (isset($data['specs']))? $data['specs'] : '',
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
        $asin = $data['asin1'];
        $nodeIds = null;

        if ($productMappings = AmazonProductCategoryMapping::cache()->where('product_asin', $asin)->first()) {
            $nodeIds = array_unique(explode(',', $productMappings->node_ids));
        }
        else {
            $response = $this->productsClient->getProductCategoriesForASIN($asin);
            $nodeIds = array_unique($this->getNodeIds($response->GetProductCategoriesForASINResult->Self));

            // Save for later use so we don't have to send a request when we don't need to.
            AmazonProductCategoryMapping::create([
                'product_asin' => $asin,
                'node_ids'     => implode(',', $nodeIds),
            ]);
        }

        return AmazonCategory::cache(['node_id', 'cadabra_category_id'])
            ->whereInLoose('node_id', $nodeIds)
            ->pluck('cadabra_category_id')
            ->toArray();
    }

    /**
     * Extract image urls
     *
     * @param $data
     * @return array
     */
    public function extractImageUrls($data) : array
    {   
        if(is_array($data['image-url'])){
            return array_filter($data['image-url']);
        }else{
            return Array();
        }
        
    }

    /**
     * Adapt the product condition.
     *
     * @param $condition
     * @return string
     */
    protected function adaptCondition($condition) : string
    {
        if ($condition <= 4) {
            return 'used';
        }
        else {
            return 'new';
        }
    }

    /**
     * Adapt the product identifier type.
     *
     * @param $amazonProductIdType
     * @return string
     */
    protected function adaptProductIdentifierType($amazonProductIdType) : string
    {
        switch ($amazonProductIdType) {
            case 1:
                return 'asin';
            case 2:
                return 'isbn';
            case 3:
                return 'upc';
            case 4:
                return 'ean';
        }
    }

    /**
     * @param $nodes
     * @return array
     */
    protected function getNodeIds($nodes) : array
    {
        $nodeIds = [];

        if (isset($nodes[0])) {
            $node = $nodes[0];
            $nodeIds[] = (int)$node->ProductCategoryId;

            while ($node->Parent) {
                $nodeIds[] = (int)$node->Parent->ProductCategoryId;
                $node = $node->Parent;
            }
        }

        return $nodeIds;
    }

    /**
     * @param $node
     * @return int
     */
    protected function extractNodeId($node) : int
    {
        if (isset($node->Parent) && $node->Parent) {
            return $this->extractNodeId($node->Parent);
        }

        if (!is_array($node)) {
            return (int)$node;
        }

        return $node->ProductCategoryId;
    }
}
