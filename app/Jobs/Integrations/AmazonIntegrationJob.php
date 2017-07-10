<?php

namespace App\Jobs\Integrations;

use App\Channels\Amazon\Adapter;
use App\Channels\Amazon\Products;
use App\Channels\Amazon\Reports;
use App\Jobs\MagmiImportJob;
use App\Models\Products\ChangeLog;
use App\Models\Products\ChangeLogDataTypes\CreatedDataType;
use App\Models\Products\Product;
use App\Models\SyncJobLog;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class AmazonIntegrationJob extends BaseIntegrationJob
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    public $connection = 'integration_amazon';

    /**
     * @var string
     */
    public $queue = 'integration_amazon';

    /**
     * @var \App\Models\SyncJobLog
     */
    protected $syncJobLog;

    /**
     * @var \App\Channels\Amazon\Reports
     */
    protected $reportsClient;

    /**
     * @var \App\Channels\Amazon\Adapter
     */
    protected $adapter;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->setupJob();

        $products = $this->getAmazonProducts();
        $products = $this->getAmazonProductDetails($products);

        foreach ($products as $product) {
            $attributes = $this->adapter->extractAttributes($product);
            $categoryIds = $this->adapter->extractCategoryIds($product);
            $imageUrls = $this->adapter->extractImageUrls($product);

            // Add Status to processing
            $attributes['status'] = Product::STATUS_PROCESSING;
            
            $saved = $this->createProductFromAmazon($attributes);

            //$saved->attachCategories($categoryIds);
            //$saved->downloadAndAttachImages($imageUrls);
            
            // added by andrey
            // attach images and update status to active
            $saved->downloadAndAttachImages($imageUrls);
            $saved->status = Product::STATUS_ACTIVE;
            
            // if there is no categories
            if(count($categoryIds) > 0) {
                $saved->attachCategories($categoryIds);
                
                // set publish price 
                //$saved->publish_price = $saved->price + $saved->categoryFee;
            } else {
                //$saved->status = Product::STATUS_PENDING_NO_CATEGORY;
            }
            
            // if there is no weight
            /*if($attributes['weight'] == 0) {
                $saved->status = Product::STATUS_PENDING_NO_WEIGHT;
            }*/
            
            // save product with status
            $saved->save();              
            // end by andrey
        }

        $this->removeUnlistedProducts(CHANNEL_AMAZON, array_map(function ($product) {
            return $product['seller-sku'];
        }, $products));

        $this->syncJobLog->update(['ended_at' => (string)Carbon::now()]);

        dispatch(new MagmiImportJob($this->merchant, $this->syncJobLog));

        Mail::send('emails.integration-complete', ['log' => $this->syncJobLog], function ($m) {
            $m->to($this->merchant->email);
            $m->subject("Integration with Amazon complete.");
            $m->from('noreply@cadabraexpress', 'Cadabra Express');
        });
    }

    /**
     *
     */
    protected function setupJob()
    {
        ini_set('memory_limit', '-1');

        $this->syncJobLog = SyncJobLog::create([
            'merchant_id' => $this->merchant->id,
            'channel'     => CHANNEL_AMAZON,
            'data'        => ['caller' => $this->caller],
        ]);

        $this->reportsClient = new Reports($this->merchant->amazon_seller_id, $this->merchant->amazon_auth_token);
        $this->adapter = new Adapter($this->merchant);
    }

    /**
     * @param array $attributes
     * @return Product
     */
    protected function createProductFromAmazon(array $attributes)
    {
        /* @var $existingProduct Product */
        $existingProduct = $this->merchant->getProductBySku($attributes['sku'], CHANNEL_AMAZON);

        // Remove/don't create if certain conditions are met
        if ($existingProduct) {
            if ($this->checkIfRemoved(CHANNEL_AMAZON, $existingProduct, $attributes)) {
                return null;
            }
        }

        if ($existingProduct) {
            /*$requirePriceUpdate = $this->isRequirePriceUpdate(
                $product,
                (float)$attributes['weight'],
                $this->merchant->amazon_import_options['discount_amount'],
                CHANNEL_AMAZON);*/
            //$originalPrice = $existingProduct->price;
            $originalPrice = $existingProduct->original_price;
            $newPrice = $attributes['price'];

            $originalQuantity = $existingProduct->quantity;
            $newQuantity = $attributes['quantity'];

            /*if(!$requirePriceUpdate) {
                unset($attributes['price']);
            }*/
            // save original price for later use
            $attributes['original_price'] = $attributes['price'];
            $attributes['price'] = "0";
            
            $existingProduct->update($attributes);

            $updated = $this->logIfChanged(
                $existingProduct,
                $newPrice,
                $originalPrice,
                $newQuantity,
                $originalQuantity,
                CHANNEL_AMAZON
            );

            if ($updated) {
                $this->syncJobLog->products_updated++;
                $this->syncJobLog->save();
            }

            $product = $existingProduct;
        }
        else {
            // save original price for later use
            $attributes['original_price'] = $attributes['price'];
            $attributes['price'] = "0";

            // create product
            $product = Product::create($attributes);

            $this->syncJobLog->products_created++;
            $this->syncJobLog->save();

            $data = new CreatedDataType(CHANNEL_AMAZON, $product->price, $product->quantity);
            ChangeLog::log($product->id, CHANNEL_AMAZON, ChangeLog::ACTION_CREATED, $data);
        }

        // check if price is changed fro ebay or deduction settings are changed
        /*if($requirePriceUpdate) {
            // remove deduction history
            $product->removeDeductions();

            $this->manipulatePrice(
                $product,
                $attributes['weight'],
                $this->merchant->amazon_import_options['discount_amount'],
                CHANNEL_AMAZON
            );
        }*/

        return $product;
    }

    /**
     * @return array
     */
    protected function getAmazonProducts()
    {
        $response = $this->reportsClient->requestReport('_GET_MERCHANT_LISTINGS_DATA_');
        $requestIds = ['ReportRequestIdList.Id.1' => $response->RequestReportResult->ReportRequestInfo->ReportRequestId];
        $reportRequestList = $this->reportsClient->getReportRequestList($requestIds);
        $generatedReportId = $reportRequestList->GetReportRequestListResult->ReportRequestInfo->GeneratedReportId;
        $products = $this->reportsClient->parseGeneratedReport($this->reportsClient->getReport($generatedReportId));
    
        return $products;
    }

    /**
     * @param array $products
     * @return array
     */
    protected function getAmazonProductDetails(array $products) : array
    {
        $client    = new Products($this->merchant->amazon_seller_id, $this->merchant->amazon_auth_token);
        $sellerSku = array_column($products, 'seller-sku');
        $sellerSku = array_chunk($sellerSku, 5);

        //$length = count($products);
        $counter = 0;

        // Send request to Amazon in chunks of 5 since that is the limit.
        foreach ($sellerSku as $i => $sellerSkuRow) {
            
            try {
                $response = $client->getMatchingProductForId($sellerSkuRow, 'SellerSKU');
            } catch (ClientException $e) {
                \Log::info($e->getResponse()->getBody()->getContents());
                throw $e;
            }

            // Loop through the response and all the products and match the weight to
            // the correct products.
            foreach ($response->GetMatchingProductForIdResult as $index => $result) {

                $attributes = $result->Products->Product->AttributeSets->children('http://mws.amazonservices.com/schema/Products/2011-10-01/default.xsd');
                $asin = (string)$result->Products->Product->Identifiers->MarketplaceASIN->ASIN;
                $images = $client->getImagesForASIN($asin);

                $products[$counter]['image-url'] = $images;

                $disallowed_url = Array("Label", "SmallImage", "Title");
                foreach (json_decode(json_encode($attributes), true)['ItemAttributes'] as $name => $value) {
                    if(!in_array($name, $disallowed_url)){
                        $products[$counter]['specs'][$name] = (array)$value;
                    }
                }

                // todo choose `ItemDimensions` or `PackageDimensions`
                $itemDimensions = $attributes->ItemAttributes->ItemDimensions;
                $packageDimensions = $attributes->ItemAttributes->ItemDimensions;

                if (isset($itemDimensions->Weight) && $itemDimensions->Weight) {
                    $products[$counter]['weight'] = (float)$itemDimensions->Weight;
                    $products[$counter]['weight_unit'] = (string)$itemDimensions->Weight['Unit'];
                }
                else {
                    $products[$counter]['weight'] = (float)$packageDimensions->Weight;
                    $products[$counter]['weight_unit'] = (string)$packageDimensions->Weight['Unit'];
                }

                $counter++;

                unset($images);
                unset($attributes);
                unset($response);

            }
            
        }

        return $products;
    }
}
