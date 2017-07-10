<?php

namespace App\Models;

use App\Models\Products\Product;
use App\Models\Category;
use App\Models\CategoryProduct;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderProduct
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $item_id
 * @property string $name
 * @property string $sku
 * @property float $price
 * @property integer $qty
 * @property integer $merchant_id
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $product
 * @property-read \App\Models\Order $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[] $products
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereItemId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereSku($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereQty($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderProduct extends Model
{
    /**
     * @var string
     */
    public $table = 'orders_products';

    /**
     * @var array
     */
    public $fillable = [
        'order_id',
        'item_id',
        'name',
        'sku',
        'price',
        'purchase_price',
        'qty',
        'merchant_id',
        'status'
    ];

    public $appends = ['product'];

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_PROCESSING = 3;

    /**
     * @return Product|null
     */
    public function getProductAttribute()
    {
        return Product::whereMerchantId($this->merchant_id)->whereSku($this->sku)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    public function categories()
    {
        return CategoryProduct::select('categories.*')
            ->join('categories', 'categories.category_id', '=', 'categories_products.category_id')
            ->where('product_id', $this->sku)
            ->orderBy('categories.level', 'desc')
            ->get();        
    }
    
    public function getCustomCode() 
    {
        foreach($this->categories() as $cat) {
            if($cat->custom_code != '') {
                return $cat->custom_code;
            }
        }
        
        return '';
    }
    
    static public function productPrice($productId)
    {
        $product = Product::find($productId);
        if(isset($product->id)) {
            return $product->price;
        }
        
        return 0;
    }
}
