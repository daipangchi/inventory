<?php

namespace App\Models\Merchants;

use App\Models\Products\ChangeLog;
use App\Models\Products\Product;
use App\Models\ShippingDeduction;
use App\Models\SyncJobLog;
use App\PasswordHasher;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanReset;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Swap;

/**
 * App\Models\Merchants\Merchant
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $amazon_seller_id
 * @property string $amazon_auth_token
 * @property string $amazon_marketplace_id
 * @property string $amazon_import_options
 * @property string $ebay_auth_token
 * @property string $ebay_auth_token_expiration
 * @property string $tax_id_number
 * @property string $ebay_import_options
 * @property boolean $is_verified
 * @property boolean $is_disabled
 * @property boolean $is_admin
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Merchants\MerchantAddress[] $addresses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Merchants\MerchantPayment[] $paymentInformation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShippingDeduction[] $shippingDeductions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShippingDeduction[] $importDeductions
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereAmazonSellerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereAmazonAuthToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereAmazonMarketplaceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereAmazonImportOptions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereEbayAuthToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereEbayAuthTokenExpiration($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereTaxIdNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereEbayImportOptions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereIsVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereIsDisabled($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereIsAdmin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\Merchant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Merchant extends Authenticatable implements CanReset
{
    use PasswordHasher, CanResetPassword;

    /**
     * @var array
     */
    protected $guarded = ['id', 'is_verified', 'is_disabled', 'is_admin'];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'amazon_seller_id',
        'amazon_auth_token',
        'ebay_auth_token',
        'ebay_auth_token_expiration',
        'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'merchant_id');
    }
    
    public function totalProducts()
    {
        return Product::where('merchant_id', $this->id)
            ->where('parent_id', NULL)
            ->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(MerchantAddress::class, 'merchant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentInformation()
    {
        return $this->hasMany(MerchantPayment::class, 'merchant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingDeductions()
    {
        return $this->hasMany(ShippingDeduction::class, 'merchant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function importDeductions()
    {
        return $this->hasMany(ShippingDeduction::class, 'merchant_id');
    }

    /**
     * @param $options
     * @return string
     */
    public function setEbayImportOptionsAttribute($options)
    {
        if (! is_string($options)) {
            foreach ($options as $key => $o) {
                if ($key == 'discount_amount') {
                    continue;
                }

                $options[$key] = $o == 'false' ? false : true;
            }

            $this->attributes['ebay_import_options'] = \GuzzleHttp\json_encode($options);
        }
    }

    /**
     * @param $options
     * @return array
     */
    public function getEbayImportOptionsAttribute($options)
    {
        return \GuzzleHttp\json_decode($options, true);
    }

    /**
     * @param $options
     * @return string
     */
    public function setAmazonImportOptionsAttribute($options)
    {
        if (! is_string($options)) {
            foreach ($options as $key => $o) {
                if ($key == 'discount_amount') {
                    continue;
                }

                //$options[$key] = filter_var($options, FILTER_VALIDATE_BOOLEAN);;
                $options[$key] = $o == 'false' ? false : true;
            }

            $this->attributes['amazon_import_options'] = \GuzzleHttp\json_encode($options);
        }
    }

    /**
     * @param $options
     * @return array
     */
    public function getAmazonImportOptionsAttribute($options)
    {
        return \GuzzleHttp\json_decode($options, true);
    }

    /**
     * Check if this merchant is integrated with a third-party service
     *
     * @param string $channel
     * @return bool
     * @throws \Exception
     */
    public function isConnectedTo(string $channel) : bool
    {
        switch (strtolower($channel)) {
            case CHANNEL_EBAY:
                if ($this->ebay_auth_token) {
                    $date = Carbon::parse($this->ebay_auth_token_expiration);

                    return $date->isPast() ? false : true;
                }

                return false;
            case CHANNEL_AMAZON:
                return (bool)$this->amazon_seller_id;
            default:
                throw new \Exception('Invalid channel.');
        }
    }

    /**
     * Check if merchant is the owner of this product.
     *
     * @param Product $product
     * @return bool
     */
    public function owns(Product $product) : bool
    {
        return $this->id === $product->merchant_id;
    }

    /**
     * @param string $sku
     * @param string $channel
     * @param bool $failIfNotFound
     * @return Product|null
     */
    public function getProductBySku(string $sku, string $channel = '', bool $failIfNotFound = false)
    {
        $query = Product::whereMerchantId($this->id)->whereSku($sku);

        if ($channel) {
            $query->whereChannel($channel);
        }

        if ($failIfNotFound) {
            return $query->firstOrFail();
        }

        return $query->first();
    }

    /**
     * @param string $ebayid
     * @param string $channel
     * @param bool $failIfNotFound
     * @return Product|null
     */
    public function getProductByEbayId(int $ebayid, bool $failIfNotFound = false)
    {
        $query = Product::whereMerchantId($this->id)->whereEbay_id($ebayid);

        if ($failIfNotFound) {
            return $query->firstOrFail();
        }

        return $query->first();
    }
}
