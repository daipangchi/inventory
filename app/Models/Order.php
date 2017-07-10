<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property integer $id
 * @property string $order_number
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_telephone
 * @property string $country
 * @property string $city
 * @property string $street
 * @property string $pincode
 * @property float $weight
 * @property string $revenue
 * @property float $grand_total
 * @property string $tracking_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $status
 * @property-read mixed $total_quantity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderProduct[] $items
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCustomerName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCustomerEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCustomerTelephone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereStreet($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order wherePincode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereRevenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereGrandTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereTrackingNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    /**
     * @var string
     */
    public $table = 'orders';

    /**
     * @var array
     */
    public $fillable = [
        'merchant_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_telephone',
        'country',
        'city',
        'street',
        'pincode',
        'weight',
        'grand_total',
        'tracking_number',
    ];

    protected $appends = ['status', 'total_quantity', 'merchant_total'];

    public function getStatusAttribute()
    {
        $item = OrderProduct::whereOrderId($this->attributes['id'])->whereMerchantId(auth()->id())->first();

        return $item ? $item->status : 0;
    }

    public function getTotalQuantityAttribute()
    {
        return OrderProduct::whereOrderId($this->attributes['id'])->whereMerchantId(auth()->id())->sum('qty');
    }
    
    public function getMerchantTotalAttribute()
    {
        return OrderProduct::whereOrderId($this->attributes['id'])->whereMerchantId(auth()->id())->sum('purchase_price');
    }

    public function items()
    {
        return $this->hasMany(OrderProduct::class);
    }
    
    public function getShippingPrice($key='total') 
    {
        if($key != 'total') {
            $key = 'merchant_' . $key;
        }
        
        $prices = json_decode($this->shipping_prices, true);
        return isset($prices[$key]) ? $prices[$key] : 0;
    }
}
