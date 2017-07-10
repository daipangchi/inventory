<?php

namespace App\Channels\Amazon;

use SimpleXMLElement;

/**
 * Amazon Products API client.
 *
 * @link http://docs.developer.amazonservices.com/en_UK/products/Products_Overview.html
 * @package App\Channels\Amazon
 */
class Products extends Client
{
    /**
     * Returns a list of products and their attributes, based on a search query.
     */
    public function listMatchingProducts()
    {
        //
    }

    /**
     * Returns a list of products and their attributes, based on a list of ASIN values.
     */
    public function getMatchingProduct()
    {
        //
    }

    /**
     * Returns a list of products and their attributes, based on a list of
     * ASIN, GCID, SellerSKU, UPC, EAN, ISBN, and JAN values.
     *
     * @link http://docs.developer.amazonservices.com/en_US/products/Products_GetMatchingProductForId.html
     * @param array $identifiers
     * @param string $type ASIN, GCID, SellerSKU, UPC, EAN, ISBN, JAN
     * @return SimpleXMLElement
     */
    public function getMatchingProductForId(array $identifiers, string $type = 'ASIN') : SimpleXMLElement
    {
        $queryIds = [];

        foreach ($identifiers as $index => $id) {
            $queryIds['IdList.Id.'.++$index] = $id;
        }

        $url = $this->generateUrl('/Products/2011-10-01', array_merge([
            'Action'        => 'GetMatchingProductForId',
            'IdType'        => $type,
            'Version'       => '2011-10-01',
            'SellerId'      => $this->config['SellerId'],
            'MarketplaceId' => 'ATVPDKIKX0DER',
        ], $queryIds));

        $response = $this->sendRequest($url);

        sleep(1);

        return simplexml_load_string($response->getBody());
    }

    /**
     * Returns the current competitive price of a product, based on SellerSKU.
     */
    public function getImagesForASIN(string $asin)
    {
        
        $amazonECS  = new AmazonECS("AKIAJEMG74VXEKW2RTNA", "5TY4RM10BPASD8EFeC7GukzDszDqenWzF0iGn2JA", 'com', "cadabra-20");
        // Looking up multiple items
        $response = $amazonECS->responseGroup('Images')->optionalParameters(array('Condition' => 'New'))->lookup($asin);
        $images = Array();
        if(isset($response->Items->Item->ImageSets->ImageSet) && count($response->Items->Item->ImageSets->ImageSet) > 0){

            if(count($response->Items->Item->ImageSets->ImageSet) > 1){
                foreach($response->Items->Item->ImageSets->ImageSet as $set){
                   if(isset($set->LargeImage)){
                        $images[] = $set->LargeImage->URL; 
                   }
                }
            }else{

                if(isset($response->Items->Item->ImageSets->ImageSet->LargeImage)){
                    $images[] =$response->Items->Item->ImageSets->ImageSet->LargeImage->URL;
                }

            }
        }

        // Prevent Amazon throttling - sleep half a second
        usleep(500000);
        unset($amazonECS);
        return $images;
    }
    
    /**
     * Returns the current competitive price of a product, based on SellerSKU.
     */
    public function getCompetitivePricingForSKU()
    {
        //
    }

    /**
     * Returns the current competitive price of a product, based on ASIN.
     */
    public function getCompetitivePricingForASIN()
    {
        //
    }

    /**
     * Returns pricing information for the lowest-price active offer
     * listings for up to 20 products, based on SellerSKU.
     */
    public function getLowestOfferListingsForSKU()
    {
        //
    }

    /**
     * Returns pricing information for the lowest-price active offer listings for up to 20 products, based on ASIN.
     */
    public function getLowestOfferListingsForASIN()
    {
        //
    }

    /**
     *    Returns lowest priced offers for a single product, based on SellerSKU.
     */
    public function getLowestPricedOffersForSKU()
    {
        //
    }

    /**
     * Returns lowest priced offers for a single product, based on ASIN.
     */
    public function getLowestPricedOffersForASIN()
    {
        //
    }

    /**
     * Returns the estimated fees for a list of products.
     */
    public function getMyFeesEstimate()
    {
        //
    }

    /**
     * Returns pricing information for your own offer listings, based on SellerSKU.
     */
    public function getMyPriceForSKU()
    {
        //
    }

    /**
     * Returns pricing information for your own offer listings, based on ASIN.
     */
    public function getMyPriceForASIN()
    {
        //
    }

    /**
     * Returns the parent product categories that a product belongs to, based on SellerSKU.
     */
    public function getProductCategoriesForSKU()
    {
        //
    }

    /**
     * Returns the parent product categories that a product belongs to, based on ASIN.
     *
     * @link http://docs.developer.amazonservices.com/en_US/products/Products_GetProductCategoriesForASIN.html
     */
    public function getProductCategoriesForASIN($asin)
    {
        $url = $this->generateUrl('/Products/2011-10-01', [
            'Action'        => 'GetProductCategoriesForASIN',
            'Version'       => '2011-10-01',
            'SellerId'      => $this->config['SellerId'],
            'MarketplaceId' => 'ATVPDKIKX0DER',
            'ASIN'          => $asin,
        ]);

        $response = $this->sendRequest($url);

        // Prevent hitting Amazon throttle.
        sleep(5);

        return simplexml_load_string($response->getBody());
    }

    /**
     * Returns the operational status of the Products API section.
     */
    public function getServiceStatus()
    {
        //
    }
}
