<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Products\Variation
 *
 * @property integer $id
 * @property string $name
 * @property string $attributes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Variation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Variation whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Variation whereAttributes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Variation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Variation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Variation extends Model
{
    public $fillable = ['name', 'attributes'];

    public function setAttributesAttribute($attributes)
    {
        if (is_array($attributes)) {
            return $this->attributes['attributes'] = json_encode($attributes);
        }

        return $this->attributes['attributes'] = $attributes;
    }

    public function getAttributesAttribute($attribute)
    {
        return $this->attributes['attributes'] = json_decode($attribute, true);
    }
}
