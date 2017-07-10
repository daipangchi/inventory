<?php

namespace App\Models\Products\ChangeLogDataTypes;

class CreatedDataType extends BaseDataType
{
    public function __construct(string $channel, float $price, int $quantity)
    {
        $this->attributes['channel'] = $channel;
        $this->attributes['price'] = $price;
        $this->attributes['quantity'] = $quantity;
    }
}
