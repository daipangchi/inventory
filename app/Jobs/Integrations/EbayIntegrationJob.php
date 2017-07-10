<?php

namespace App\Jobs\Integrations;

use App\Channels\Ebay\Adapter;
use App\Channels\Ebay\Client;
use App\Jobs\MagmiImportJob;
use App\Models\CategoryProduct;
use App\Models\Products\ChangeLog;
use App\Models\Products\ChangeLogDataTypes\CreatedDataType;
use App\Models\Products\Product;
use App\Models\SyncJobLog;
use Carbon\Carbon;
use DateTime;
use DTS\eBaySDK\Trading\Types\GetItemRequestType;
use DTS\eBaySDK\Trading\Types\ItemType;
use DTS\eBaySDK\Trading\Types\VariationType;
use Mail;

class EbayIntegrationJob extends BaseIntegrationJob
{
    /**
     * @var string
     */
    public $connection = 'integrations';

    /**
     * @var string
     */
    public $queue = 'integrations';

    /**
     * @var \App\Models\SyncJobLog
     */
    protected $syncJobLog;

    /**
     * @var \App\Channels\Ebay\Client
     */
    protected $ebay;

    /**
     * @var \DTS\eBaySDK\Trading\Services\TradingService
     */
    protected $service;

    /**
     * @var \App\Channels\Ebay\Adapter
     */
    protected $adapter;

    /**
     * Used to remove products that are no longer listed.
     *
     * @var array
     */
    protected $skus = [];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->setupJob();

        $initial = time();
        $fourYearsAgo = 365 * 4; //temp
        $day = 0;
        $interval = 120;
        $fmt = 'Y-m-d H:i:s';
        
        // loop through 120 day intervals for past 4 years
        while ($day < $fourYearsAgo) {
            $start = Carbon::createFromTimestampUTC($initial)->subDays($day + $interval)->format($fmt);
            $end = Carbon::createFromTimestampUTC($initial)->subDays($day)->format($fmt);
            $page = 0;

            $request = $this->ebay->buildGetSellerListRequestObject(
                DateTime::createFromFormat($fmt, $start),
                DateTime::createFromFormat($fmt, $end)
            );

            // Loop through all pages in this 120 day interval.
            do {
                $request->Pagination->PageNumber = ++$page;
                $response = $this->service->getSellerList($request);

                if ($response->ItemArray && $response->ItemArray->Item) {
                    $this->processItems($response->ItemArray->Item);
                }

                $isNotLastPage = $page < $response->PaginationResult->TotalNumberOfPages;
            } while ($response->ItemArray && $response->PaginationResult && $isNotLastPage);

            $day = $day + $interval;
        }

        $this->removeUnlistedProducts(CHANNEL_EBAY, $this->skus);

        $this->syncJobLog->ended_at = Carbon::now();
        $this->syncJobLog->save();

        dispatch(new MagmiImportJob($this->merchant, $this->syncJobLog));

        Mail::send('emails.integration-complete', ['log' => $this->syncJobLog], function ($m) {
            $m->to($this->merchant->email);
            $m->subject("Integration with eBay complete.");
            $m->from('noreply@cadabraexpress.com', 'Cadabra Express');
        });
    }

    /**
     * @param $itemArray
     * @return void
     */
    public function processItems($itemArray)
    {
        /* @var $item ItemType */
        foreach ($itemArray as $item) {
            // Get additional details
            $this->preprocessItem($item);

            // // Extract attributes
            $attributes = $this->adapter->extractAttributes($item);
            $categoryIds = $this->adapter->extractCategoryIds($item);
            $images = $this->adapter->extractImageUrls($item);

            //Add Status to processing
            $attributes['status'] = Product::STATUS_PROCESSING;

            $endDate = $item->ListingDetails->EndTime;
            // Create or update product.
            if ($existingProduct = $this->merchant->getProductByEbayId($attributes['ebay_id'])) {
                
                if ($this->checkIfRemoved(CHANNEL_EBAY, $existingProduct, $attributes, $endDate)) {
                    // echo "Skipping Phase 1:". $attributes['ebay_id'] . " -- ";
                    continue;
                }

                $saved = $this->updateProduct($attributes, $existingProduct);
            }
            else {

                // Preventing adding a product with an end date in the past and quantity 0
                if(($endDate <= new \DateTime()) || ($attributes['quantity'] == 0)){
                    // echo "Skipping Phase 2:". $attributes['ebay_id'] . " -- ";
                    continue;
                }

                $saved = $this->createProduct($attributes);
            }

            //$saved->attachCategories($categoryIds);
            //$saved->downloadAndAttachImages($images);
            
            // added by andrey
            // attach images and update status to active
            $saved->downloadAndAttachImages($images);
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

            // If there are variations, create child products.
            if ($item->Variations) {
                $this->processChildProducts($item, $saved);
            }

            $this->skus[] = $saved->sku;
        }
    }

    /**
     * Set up job.
     */
    protected function setupJob()
    {
        ini_set('memory_limit', '-1');

        $this->syncJobLog = SyncJobLog::create([
            'merchant_id' => $this->merchant->id,
            'channel'     => CHANNEL_EBAY,
            'data'        => ['caller' => $this->caller],
        ]);

        $this->ebay = new Client($this->merchant->ebay_auth_token);
        $this->service = $this->ebay->sdk->createTrading();
        $this->adapter = new Adapter($this->merchant);
    }

    /**
     * @param $itemId
     * @return \DTS\eBaySDK\Trading\Types\GetItemResponseType
     */
    protected function getItem($itemId)
    {
        $itemSpecsRequest = new GetItemRequestType();
        $itemSpecsRequest->ItemID = $itemId;
        $itemSpecsRequest->DetailLevel = ['ReturnAll'];
        $itemSpecsRequest->IncludeItemSpecifics = true;

        return $this->service->getItem($itemSpecsRequest);
    }

    /**
     * @param $attributes
     * @param Product $product
     * @return Product
     */
    protected function updateProduct($attributes, Product $product)
    {
        /*$requirePriceUpdate = $this->isRequirePriceUpdate(
            $product,
            (float)$attributes['weight'],
            $this->merchant->ebay_import_options['discount_amount'],
            CHANNEL_EBAY);*/
        //$originalPrice = $product->price;
        $originalPrice = $product->original_price;
        $newPrice = (float)$attributes['price'];

        $originalQuantity = $product->quantity;
        $newQuantity = $attributes['quantity'];

        /*if(!$requirePriceUpdate) {
            unset($attributes['price']);
        }*/
        // save original price for later use
        $attributes['original_price'] = $attributes['price'];
        unset($attributes['price']);
        $product->update($attributes);

        $changed = $this->logIfChanged(
            $product,
            $originalPrice,
            $newPrice,
            $newQuantity,
            $originalQuantity,
            CHANNEL_EBAY
        );

        if ($changed) {
            $this->syncJobLog->products_updated++;
        }

        // check if price is changed fro ebay or deduction settings are changed
        /*if($requirePriceUpdate) {
            // remove deduction history
            $product->removeDeductions();

            $this->manipulatePrice(
                $product,
                (float)$attributes['weight'],
                $this->merchant->ebay_import_options['discount_amount'],
                CHANNEL_EBAY
            );
        }*/

        return $product;
    }

    /**
     * @param $attributes
     * @return Product
     */
    protected function createProduct($attributes)
    {
        // save original price for later use
        $attributes['original_price'] = $attributes['price'];
        $attributes['price'] = "0";

        /* @var $product Product */
        $product = Product::create($attributes);

        $data = new CreatedDataType(CHANNEL_EBAY, $product->price, $product->quantity);
        ChangeLog::log($product->id, CHANNEL_EBAY, ChangeLog::ACTION_CREATED, $data, $this->syncJobLog->id);

        $this->syncJobLog->products_created++;

        /*$this->manipulatePrice(
            $product,
            (float)$attributes['weight'],
            $this->merchant->ebay_import_options['discount_amount'],
            CHANNEL_EBAY
        );*/

        return $product;
    }

    /**
     * @param ItemType $item
     * @param $parent
     */
    protected function processChildProducts(ItemType $item, Product $parent)
    {
        if (count($item->Variations->Variation) > 0) {
            $parent->type = Product::TYPE_CONFIGURABLE;
        }

        $parentAttributes = $this->getParentAttributes($parent);

        foreach ($item->Variations->Variation as $i => $variation) {
            $childSku = $parent->sku.'-'.$i;
            $this->skus[] = $childSku;

            $productAttributes = $this->processAttributes($variation);

            $child = Product::createOrFirst([
                'merchant_id' => $parent->merchant_id,
                'sku'         => $childSku,
            ]);

            $child->update(array_merge($parentAttributes, [
                'parent_id'  => $parent->id,
                'parent_sku' => $parent->sku,
                'type'       => Product::TYPE_SIMPLE,
                'quantity'   => $variation->Quantity,
                'attributes' => $productAttributes,
            ]));

            foreach ($parent->categories as $category) {
                CategoryProduct::create([
                    'product_id'  => $child->id,
                    'category_id' => $category->category_id,
                ]);
            }

            $parent->mergeChildAttributes($child->attributes);
        }

        $parent->save();
    }

    /**
     * @param VariationType $variation
     * @return array
     */
    protected function processAttributes(VariationType $variation)
    {
        $productAttributes = [];

        foreach ($variation->VariationSpecifics as $spec) {
            foreach ($spec->NameValueList as $item) {
                foreach ($item->Value as $value) {
                    if (is_array($value)) {
                        $value = implode(' ', $value);
                    }

                    $productAttributes[$item->Name] = $value;
                }
            }
        }

        return $productAttributes;
    }

    /**
     * @param Product $parent
     * @return array
     */
    protected function getParentAttributes(Product $parent)
    {
        $exclude = [
            'id',
            'sku',
            'images',
            'image',
            'parent',
            'variations',
            'variants',
            'exported_at',
            'updated_at',
            'created_at',
        ];

        return array_except($parent->toArray(), $exclude);
    }

    /**
     * @param $item
     * @return \DTS\eBaySDK\Trading\Types\GetItemResponseType
     */
    protected function preprocessItem(ItemType $item)
    {
        $itemDetails = $this->getItem($item->ItemID);
        
        $item->SKU = $item->SKU ?? $item->ItemID;        
        $item->ItemSpecifics = $itemDetails->Item->ItemSpecifics ? $itemDetails->Item->ItemSpecifics : array();
        $item->ConditionID = $itemDetails->Item->ConditionID ? $itemDetails->Item->ConditionID : 0;
    }
}
