<?php

namespace App\Jobs\Integrations;

use App\Jobs\Job;
use App\Models\Merchants\Merchant;
use App\Models\Products\ChangeLog;
use App\Models\Products\ChangeLogDataTypes\RemovedDataType;
use App\Models\Products\ChangeLogDataTypes\UpdatedDataType;
use App\Models\Products\Product;
use App\Models\ShippingDeduction;
use App\Models\SyncJobLog;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseIntegrationJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var \App\Models\Merchants\Merchant
     */
    protected $merchant;

    /**
     * @var string
     */
    protected $caller;

    /**
     * @var SyncJobLog
     */
    protected $syncJobLog;

    /**
     * Create a new job instance.
     *
     * @param Merchant $merchant
     * @param string $caller (to be used used for debugging) Possible values: Artisan, Queue, Scheduler_auto, Scheduler_custom
     */
    public function __construct(Merchant $merchant, string $caller = 'unknown')
    {
        $this->merchant = $merchant;
        $this->caller = $caller;
    }

    /**
     * @param $channel
     * @param $existingProduct
     * @param $attributes
     * @param Carbon $endDate
     * @return bool
     */
    protected function checkIfRemoved($channel, $existingProduct, $attributes, $endDate = null)
    {
        if($channel == "ebay"){
            $importOptions = $this->merchant->ebay_import_options;
        }elseif($channel == "amazon"){
            $importOptions = $this->merchant->amazon_import_options;
        }

        if ($importOptions['remove_sold_items'] && $attributes['quantity'] === 0) {
            $data = new RemovedDataType(RemovedDataType::REASON_SOLD);
            ChangeLog::log($existingProduct->id, $channel, ChangeLog::ACTION_REMOVED, $data, $this->syncJobLog->id);
            $this->syncJobLog->products_removed++;
            $this->syncJobLog->save();

            return true;
        }
        else if (($importOptions['remove_ended_items']) && ($endDate <= new \DateTime()) && ($endDate != NULL)) {
            $data = new RemovedDataType(RemovedDataType::REASON_ENDED);
            ChangeLog::log($existingProduct->id, $channel, ChangeLog::ACTION_REMOVED, $data, $this->syncJobLog->id);
            $this->syncJobLog->products_removed++;
            $this->syncJobLog->save();

            return true;
        }

        // We will run the check to see if the item was
        // removed by being unlisted  in another area.

        return false;
    }

    /**
     * @param Product $product
     * @param $weight
     * @param $discount
     * @param $channel
     */
    protected function isRequirePriceUpdate(Product $product, $weight, $discount, $channel)
    {
        // check price is changed from ebay or amazon
        if($product->price != $product->original_price) {
            return true;
        }

        // check channel deduction is changed
        if($product->channel_deduction != $discount) {
            return true;
        }

        // check shipping deduction is changed
        $shippingDiscount = 0;
        $shippingDeduction = ShippingDeduction::whereMerchantId($product->merchant_id)
            ->where('to_weight', '>', $weight)
            ->where('from_weight', '<=', $weight)
            ->first();
        if($shippingDeduction) {
            $shippingDiscount = $shippingDeduction->compareText();
        }
        if($product->shipping_deduction != $shippingDiscount) {
            return true;
        }

        return false;
    }

    /**
     * @param Product $product
     * @param float $weight
     * @param float $discount
     * @param string $channel
     * @return $this
     */
    public function manipulatePrice(Product $product, float $weight, float $discount, string $channel)
    {
        // add original price to deduction list
        $product->addDeduction("{$channel}: " . money_format('$%i', $product->price));
        
        $this->applyDiscount($product, $discount, $channel);
        $this->applyShippingDeductions($product, $weight, $channel);

        return $this;
    }

    /**
     * @param Product $product
     * @param float $percentage
     * @param string $channel
     */
    protected function applyDiscount(Product $product, float $percentage, string $channel)
    {
        if ($percentage) {
            $oldPrice = $product->price;
            $discount = $product->price * ($percentage / 100);

            $product->price -= $discount;

            // save channel discount to product  for later use
            $product->channel_deduction = $percentage;

            $data = new UpdatedDataType(
                UpdatedDataType::ENTITY_PRICE,
                $oldPrice,
                $product->price, // new price
                UpdatedDataType::PRICE_CHANGE_SOURCE_AUTOMATIC
            );

            ChangeLog::log($product->id, $channel, ChangeLog::ACTION_UPDATED, $data, $this->syncJobLog->id);
            
            // add channel deduction to list 
            $product->addDeduction("{$channel} Deduction: " . money_format('$%i', $discount) . "({$percentage}%)");
        }

        $product->save();
    }

    /**
     * @param Product $product
     * @param float $weight
     * @param string $channel
     */
    protected function applyShippingDeductions(Product $product, float $weight, string $channel)
    {
        if (! $weight) {
            return;
        }

        $shippingDeduction = ShippingDeduction::whereMerchantId($product->merchant_id)
            ->where('to_weight', '>', $weight)
            ->where('from_weight', '<=', $weight)
            ->first();

        if ($shippingDeduction) {
            $oldPrice = $product->price;
            $product->price -= $shippingDeduction->amount;

            // save shipping discount to product for later use
            $product->shipping_deduction = $shippingDeduction->compareText();

            $data = new UpdatedDataType(
                UpdatedDataType::ENTITY_PRICE,
                $oldPrice,
                $product->price, // new price
                UpdatedDataType::PRICE_CHANGE_SOURCE_SHIPPING
            );

            ChangeLog::log($product->id, $channel, ChangeLog::ACTION_UPDATED, $data, $this->syncJobLog->id);
            
            // add shipping deduction to list 
            $product->addDeduction("Weight Deduction: " . money_format('$%i', $shippingDeduction->amount) . "({$shippingDeduction->from_weight}lbs - {$shippingDeduction->to_weight}lbs) ");
        }
    }

    /**
     * @param Product $existingProduct
     * @param float $originalPrice
     * @param float $newPrice
     * @param int $newQuantity
     * @param int $originalQuantity
     * @param string $channel
     * @return bool
     */
    protected function logIfChanged(Product $existingProduct,
                                    float $originalPrice,
                                    float $newPrice,
                                    int $newQuantity,
                                    int $originalQuantity,
                                    string $channel)
    {
        $updated = false;

        $source = $channel == CHANNEL_AMAZON
            ? UpdatedDataType::PRICE_CHANGE_SOURCE_AMAZON
            : UpdatedDataType::PRICE_CHANGE_SOURCE_EBAY;

        // Log price change if there is one
        if ($newPrice != $originalPrice) {
            $data = new UpdatedDataType(
                UpdatedDataType::ENTITY_PRICE,
                $originalPrice,
                $newPrice,
                $source
            );

            ChangeLog::log($existingProduct->id, $channel, ChangeLog::ACTION_UPDATED, $data, $this->syncJobLog->id);
        }

        // Log quantity change if there is one.
        if ($newQuantity != $originalQuantity) {
            $data = new UpdatedDataType(
                UpdatedDataType::ENTITY_QUANTITY,
                //$originalPrice,
                //$newPrice,
                $originalQuantity,
                $newQuantity,
                $source
            );

            ChangeLog::log($existingProduct->id, $channel, ChangeLog::ACTION_UPDATED, $data, $this->syncJobLog->id);
            $updated = true;
            return $updated;
        }

        return $updated;
    }

    /**
     * @param string $channel
     * @param array $skus
     * @return void
     */
    protected function removeUnlistedProducts(string $channel, array $skus)
    {
        $products = Product::whereMerchantId($this->merchant->id)->whereChannel($channel)->whereNotIn('sku', $skus)->get();

        foreach ($products as $product) {
            $data = new RemovedDataType(RemovedDataType::REASON_REMOVED);
            ChangeLog::log($product->id, $channel, ChangeLog::ACTION_REMOVED, $data, $this->syncJobLog->id);

            //$product->delete();
            $product->status = PRODUCT::STATUS_REMOVED;
            $product->save();

            $this->syncJobLog->products_removed++;
        }
    }
}
