<?php

namespace App\Models\Products\ChangeLogDataTypes;

class RemovedDataType extends BaseDataType
{
    const REASON_SOLD = 'Product is sold out.';
    const REASON_REMOVED = 'Product no longer exists.';
    const REASON_ENDED = 'Product listing has ended.';

    public function __construct(string $reason)
    {
        $this->attributes['reason'] = $reason;
    }
}
