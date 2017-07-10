<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShippingDeduction
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property float $from_weight
 * @property float $to_weight
 * @property float $amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShippingDeduction whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShippingDeduction whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShippingDeduction whereFromWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShippingDeduction whereToWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShippingDeduction whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShippingDeduction whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShippingDeduction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ShippingDeduction extends Model
{
    protected $table = 'merchants_shipping_deductions';

    protected $fillable = ['merchant_id', 'from_weight', 'to_weight', 'amount'];
    
    public function compareText() {
        return $this->from_weight . '-' . $this->to_weight . ':' . $this->amount;
    }
}
