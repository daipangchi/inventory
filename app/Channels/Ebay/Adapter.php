<?php

namespace App\Channels\Ebay;

use App\Models\EbayCategory;
use App\Models\Merchants\Merchant;
use App\Policies\ProductsAdapter;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use Swap;

/**
 * Class Adapter
 *
 * Used to transform eBay product structure into
 * Cadabra product structure.
 *
 * @package App\Channels\Ebay
 */
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
     * Adapt product attributes to fit into the Cadabra database.
     *
     * @param $data
     * @return array|mixed
     */
    public function extractAttributes($data) : array
    {
        /** @var \DTS\eBaySDK\Trading\Types\ItemType $data */
        $rate = Swap::quote("{$data->Currency}/USD")->getValue();
        $calculatedShippingRate = $data->ShippingDetails->CalculatedShippingRate;

        $merchant_id = $this->merchant->id;
        $sku = $data->SKU;
        $name = $data->Title;
        $description = $data->Description;
        $ebay_id = $data->ItemID;
        $price = ($data->ReservePrice->value ?: $data->BuyItNowPrice->value ?: $data->StartPrice->value) * $rate;
        $quantity = ($data->Quantity - $data->SellingStatus->QuantitySold);
        $condition = $this->adaptCondition($data->ConditionID);
        $channel = CHANNEL_EBAY;
        list($weight, $weight_unit) = $this->getWeight($data, $calculatedShippingRate);
        $variations = $this->generateVariations($data);
        $specs = $this->extractItemSpecs($data->ItemSpecifics);

        return compact(
            'merchant_id',
            'sku',
            'name',
            'description',
            'ebay_id',
            'price',
            'quantity',
            'condition',
            'weight',
            'weight_unit',
            'channel',
            'variations',
            'specs'
        );
    }

    /**
     * Extract category ids.
     *
     * @param $data
     * @return array
     */
    public function extractCategoryIds($data) : array
    {
        /** @var \DTS\eBaySDK\Trading\Types\ItemType $data */
        $ebayCategoryIds = array_filter([
            $data->PrimaryCategory->CategoryID ?? '',
            $data->SecondaryCategory->CategoryID ?? '',
            $data->FreeAddedCategory->CategoryID ?? '',
        ]);

        return EbayCategory::cache(['ebay_category_id', 'cadabra_category_id'])
            ->whereInLoose('ebay_category_id', $ebayCategoryIds)
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
        $urls = [];

        /** @var \DTS\eBaySDK\Trading\Types\ItemType $data */
        foreach ($data->PictureDetails->PictureURL as $url) {
            $urls[] = $url;
        }

        return array_filter($urls);
    }

    /**
     * @param \DTS\eBaySDK\Trading\Types\ItemType $attributes
     * @return array
     */
    public function generateVariations($attributes)
    {
        $variations = [];

        if ($attributes->Variations) {
            foreach ($attributes->Variations->Variation as $variation) {
                foreach ($variation->VariationSpecifics as $item) {
                    foreach ($item->NameValueList as $v) {
                        $values = [];

                        foreach ($v->Value as $value) {
                            $values[] = $value;
                        }

                        if (isset($variations[$v->Name])) {
                            $variations[$v->Name] = array_merge($variations[$v->Name], $values);
                        }
                        else {
                            $variations[$v->Name] = $values;
                        }
                    }
                }
            }
        }

        foreach ($variations as $key => $value) {
            $variations[$key] = array_unique($value);
        }

        return $variations;
    }

    /**
     * @param \DTS\eBaySDK\Trading\Types\NameValueListArrayType $specs
     * @return array
     */
    public function extractItemSpecs($specs = null)
    {
        if (! $specs) return null;

        $extracted = [];

        foreach ($specs->NameValueList as $item) {
            $extracted[$item->Name] = [];

            foreach ($item->Value as $value) {
                $extracted[$item->Name][] = $value;
            }
        }

        return $extracted;
    }

    /**
     * @param $attributes
     * @param $calculatedShippingRate
     * @return array
     */
    public function getWeight($attributes, $calculatedShippingRate)
    {
        if ($calculatedShippingRate) {
            $weightMajor = $calculatedShippingRate->WeightMajor;
            $weightMinor = $calculatedShippingRate->WeightMinor;
            $weight = $weightMajor->value;
            $weight += (new Mass($weightMinor->value, $weightMinor->unit))->toUnit($weightMajor->unit);
            $weight_unit = $attributes->ShippingDetails->CalculatedShippingRate->WeightMajor->unit;
            return [$weight, $weight_unit];
        }
        else {
            $weight = 0;
            $weight_unit = '';
            return [$weight, $weight_unit];
        }
    }

    /**
     * @param $conditionId
     * @return string
     */
    public function adaptCondition($conditionId)
    {
        switch ($conditionId) {
            case 1000:
            case 1500:
            case 1750:
                return 'new';
            case 2000:
            case 2500:
            case 2750:
                return 'reconditioned';
            case 3000:
            case 4000:
            case 5000:
            case 6000:
                return 'used';
            case 7000:
                return 'broken'; // todo
        }

        // return new by default
        return 'new';
    }
}
