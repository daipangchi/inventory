<?php

namespace App\Models\Products\ChangeLogDataTypes;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

abstract class BaseDataType implements Jsonable, Arrayable
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return \GuzzleHttp\json_encode($this->attributes);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return \GuzzleHttp\json_encode($this->attributes);
    }
}
