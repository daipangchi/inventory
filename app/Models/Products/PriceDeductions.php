<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Products\Image
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $url
 * @property string $original_url
 * @property boolean $is_main
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Image whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Image whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Image whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Image whereOriginalUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Image whereIsMain($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Image whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PriceDeductions extends Model
{
    public $table = 'products_price_deductions';
    
    public $fillable = [
        'product_id',
        'reason'
    ];
}
