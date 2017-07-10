<?php

namespace App\Models\Merchants;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Merchants\MerchantAddress
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property string $name
 * @property string $address_type
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $city
 * @property string $zip_code
 * @property string $state_code
 * @property string $country_code
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereAddressType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereAddressLine1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereAddressLine2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereZipCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereStateCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereCountryCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantAddress whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantAddress extends Model
{
    const ADDRESS_TYPE_MAILING = 'mailing';
    const ADDRESS_TYPE_BILLING = 'billing';

    public $fillable = [
        'merchant_id',
        'name',
        'address_type',
        'address_line_1',
        'address_line_2',
        'city',
        'zip_code',
        'state_code',
        'country_code',
    ];

    public $table = 'merchants_addresses';

    /**
     * @param $address
     * @param $type
     * @return \Illuminate\Support\MessageBag
     */
    public static function validate($address, $type)
    {
        $address['type'] = $type;

        $validator = \Validator::make($address, [
            'name'           => 'string|max:255',
            'address_type'   => 'required|string|in:mailing,billing', // maybe also "both"
            'address_line_1' => 'required|max:255',
            'zip_code'       => 'required|numeric',
        ]);

        return $validator->errors();
    }
}
