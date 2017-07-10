<?php

namespace App\Models\Products\ChangeLogDataTypes;

class UpdatedDataType extends BaseDataType
{
    // The entity that was updated.
    const ENTITY_PRICE = 'price';
    const ENTITY_QUANTITY = 'quantity';

    // Where the source came
    const PRICE_CHANGE_SOURCE_AUTOMATIC = 'Automatic import deduction.';
    const PRICE_CHANGE_SOURCE_SHIPPING = 'Shipping deduction.';
    const PRICE_CHANGE_SOURCE_CATEGORY = 'Category deduction.';
    const PRICE_CHANGE_SOURCE_MERCHANT_PORTAL = 'Price changed from Merchant Portal.';
    const PRICE_CHANGE_SOURCE_AMAZON = 'Price changed from Amazon';
    const PRICE_CHANGE_SOURCE_EBAY = 'Price changed from eBay.';

    const QUANTITY_CHANGE_SOURCE_MERCHANT_PORTAL = 'Quantity changed from Merchant Portal.';
    const QUANTITY_CHANGE_SOURCE_AMAZON = 'Quantity changed from Amazon';
    const QUANTITY_CHANGE_SOURCE_EBAY = 'Quantity changed from from eBay';

    /**
     * @param string $entity
     * @param float $before
     * @param float $after
     * @param string $reason
     * @throws \Exception
     */
    public function __construct(string $entity, float $before, float $after, string $reason)
    {
        if (!in_array($entity, $this->getPossibleEntityTypes())) {
            throw new \Exception();
        }

        $this->attributes['entity'] = $entity;
        $this->attributes['before'] = $before;
        $this->attributes['after'] = $after;
        $this->attributes['reason'] = $reason;

        switch ($entity) {
            case static::ENTITY_PRICE:
                $this->attributes['changed'] = $after - $before;
                break;
            case static::ENTITY_QUANTITY:
                $this->attributes['changed'] = (int)($after - $before);
                break;
            default:
                throw new \Exception('Invalid argument.');
                break;
        }
    }

    private function getPossibleEntityTypes()
    {
        return [
            static::ENTITY_PRICE,
            static::ENTITY_QUANTITY,
        ];
    }
}
