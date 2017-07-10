<?php

namespace App\Models\Merchants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;

/**
 * App\Models\Merchants\MerchantPayment
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property string $method
 * @property string $attributes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-write mixed $address_mailing
 * @property-write mixed $address_billing
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantPayment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantPayment whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantPayment whereMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantPayment whereAttributes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Merchants\MerchantPayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantPayment extends Model
{
    public $table = 'merchants_payments';

    public $fillable = [
        'merchant_id',
        'method',
        'address_mailing',
        'address_billing',
        'attributes',
    ];

    /**
     * @param $attributes
     * @return string
     */
    public function setAttributesAttribute($attributes)
    {
        return $this->attributes['attributes'] = json_encode($attributes);
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function getAttributesAttribute($attributes)
    {
        return json_decode($attributes, true);
    }

    /**
     * @param $mailing
     * @return string
     */
    public function setAddressMailingAttribute($mailing)
    {
        return $this->attributes['address_mailing'] = json_encode($mailing);
    }

    /**
     * @param $billing
     * @return string
     */
    public function setAddressBillingAttribute($billing)
    {
        return $this->attributes['address_mailing'] = json_encode($billing);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\MessageBag|void
     */
    public static function validate(Request $request)
    {
        $common = static::getCommonRules();
        $mailing = static::getAddressRules('mailing');

        if (! $request->get('address_same1') || $request->get('address_same2') || $request->get('address_same3')) {
            $billing = static::getAddressRules('billing');
        }

        switch ($request->get('method')) {
            case 'ach':
                $rules = static::getAchRules();
                break;
            case 'check':
                $rules = static::getCheckRules();
                break;
            case 'paypal':
                $rules = static::getPaypalRules();
                break;
            default:
                return abort(500);
        }

        $merged = array_merge($common, $rules, $mailing);

        if (isset($billing)) {
            $merged = array_merge($merged, $billing);
        }

        return Validator::make($request->all(), $merged)->errors();
    }

    /**
     * @param string $mailingOrBilling
     * @return array
     */
    public static function getAddressRules(string $mailingOrBilling)
    {
        $states = implode(',', array_keys(US_STATES));

        return [
            "address_$mailingOrBilling.address_line_1" => 'required|max:255',
            "address_$mailingOrBilling.address_line_2" => 'max:255',
            "address_$mailingOrBilling.city"           => 'required|max:255',
            "address_$mailingOrBilling.state"          => "required|in:$states",
        ];
    }

    /**
     * @return array
     */
    public static function getCommonRules()
    {
        return [
            //
        ];
    }

    /**
     * @return array
     */
    public static function getAchRules()
    {
        return [
            'ach.business_name'  => 'required|max:255',
            'ach.bank_name'      => 'required|max:255',
            'ach.account_number' => 'required|numeric',
            'ach.routing_number' => 'required|numeric',
            'ach.bank_location'  => 'required|max:255',
        ];
    }

    /**
     * @return array
     */
    public static function getCheckRules()
    {
        return [
            'check.business_name'  => 'required|max:255',
            'check.bank_name'      => 'required|max:255',
            'check.account_number' => 'required|numeric',
            'check.routing_number' => 'required|numeric',
        ];
    }

    /**
     * @return array
     */
    public static function getPaypalRules()
    {
        return [
            'paypal.email' => 'required|email|max:255',
        ];
    }
}
