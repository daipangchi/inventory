<?php

namespace App\Http\Controllers;

use App\Jobs\Integrations\AmazonIntegrationJob;
use App\Jobs\Integrations\EbayIntegrationJob;
use App\Models\Merchants\Merchant;
use App\Models\Merchants\MerchantAddress;
use App\Models\Merchants\MerchantPayment;
use App\Models\ShippingDeduction;
use DTS\eBaySDK\Trading\Services\TradingService;
use DTS\eBaySDK\Trading\Types\GetSessionIDRequestType;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class SettingsController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $ebayOptions = auth()->user()->ebay_import_options;
        $amazonOptions = auth()->user()->amazon_import_options;
        $importDeductions = auth()->user()->importDeductions;

        return view('pages.settings.index', compact('ebayOptions', 'amazonOptions', 'importDeductions'));
    }

    /**
     * @return mixed
     */
    public function profile()
    {
        $id = auth()->id();
        $paymentAch = MerchantPayment::whereMerchantId($id)->whereMethod('ach')->first()->attributes ?? null;
        $paymentCheck = MerchantPayment::whereMerchantId($id)->whereMethod('check')->first()->attributes ?? null;
        $paymentPaypal = MerchantPayment::whereMerchantId($id)->whereMethod('paypal')->first()->attributes ?? null;
        $addressBilling = MerchantAddress::whereMerchantId($id)->whereAddressType('billing')->first();
        $addressMailing = MerchantAddress::whereMerchantId($id)->whereAddressType('mailing')->first();

        session()->put('initial_login', session('initial_login'));

        return view('pages.settings.profile', compact(
            'paymentAch',
            'paymentCheck',
            'paymentPaypal',
            'addressBilling',
            'addressMailing'
        ));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $merchant = Merchant::find(auth()->id());

        switch ($request->get('_type')) {
            case 'password':
                $currentPass = $request->get('current_password');
                $newPass = $request->get('new_password');
                $newPassConfirmation = $request->get('new_password_confirmation');

                if (! Hash::check($currentPass, $merchant->password)) {
                    session()->flash('error', 'Your current password is incorrect.');
                }
                else if (strlen($newPass < 6)) {
                    session()->flash('error', 'Your password must be at least 6 characters long.');
                }
                else if ($newPass !== $newPassConfirmation) {
                    session()->flash('error', 'Your passwords do not match.');
                }
                else {
                    $merchant->password = $newPass;
                    $merchant->save();

                    session()->flash('success', 'Your password has been successfully changed.');
                }

                break;
            case 'payment':
                $id = auth()->id();
                $merchantId = ['merchant_id' => auth()->id()];
                $addressMailing = array_filter($request->get('address_mailing'));
                
                /*$messageBag = $this->validateAddressAndPayment($request);
                if ($messageBag->count()) {
                    return redirect()->back()->withInput()->withErrors($messageBag);
                }*/
                
                // check if there is exist row
                $addressMailingRow = MerchantAddress::whereMerchantId($id)->whereAddressType('mailing')->first();
                $addressBillingRow = MerchantAddress::whereMerchantId($id)->whereAddressType('billing')->first();
                $paymentRow = MerchantPayment::whereMerchantId($id)->whereMethod($request->get('method'))->first();
                
                // - address
                if(!isset($addressBillingRow->id)) {
                    MerchantAddress::create(array_merge(
                        $request->get('address_billing'),
                        $merchantId,
                        ['address_type' => MerchantAddress::ADDRESS_TYPE_BILLING]
                    ));
                } else {
                    $addressBillingRow->update($addressMailing);  
                }

                if (! empty($addressMailing)) {
                    if(!isset($addressMailingRow->id)) {
                        MerchantAddress::create(array_merge(
                            $addressMailing,
                            $merchantId,
                            ['address_type' => MerchantAddress::ADDRESS_TYPE_MAILING]
                        ));
                    } else {
                        $addressMailingRow->update($addressMailing);  
                    }
                }
                               
                // - payment
                if(!isset($paymentRow->id)) {
                    MerchantPayment::create([
                        'merchant_id' => auth()->id(),
                        'method'      => $request->get('method'),
                        'attributes'  => $request->get('attributes'),
                    ]);
                } else {
                    $paymentRow->update(['attributes'  => $request->get('attributes')]);  
                }

                // - tax 
                if ($taxNumber = $request->get('tax_identification_number')) {
                    auth()->user()->update(['tax_id_number' => $taxNumber]);
                }

                session()->flash('success', 'Your payment information has been saved.');

                break;
            case 'shipping':
                if($request->has('shipping_username') &&
                    $request->has('shipping_password')) {
                    $shippingCredentials = new \stdClass();
                    $shippingCredentials->username = $request->get('shipping_username');
                    $shippingCredentials->password = $request->get('shipping_password');
                    
                    $merchant->shipping_credential = json_encode($shippingCredentials);
                    $merchant->save();
                    
                    session()->flash('success', 'Your shipping information has been saved.');
                }                
                
                break;
        }

        if (session('initial_login')) {
            session()->put('initial_login', '');
            return redirect('/');
        }

        return redirect()->back()->withInput($request->input());
    }

    /**
     * @return mixed
     */
    public function accepted()
    {
        $service = new TradingService([
            'sandbox'     => env('EBAY_SANDBOX_MODE', true),
            'apiVersion'  => '903',
            'siteId'      => '3',
            'credentials' => [
                'appId'  => env('EBAY_APP_ID'),
                'certId' => env('EBAY_CERT_ID'),
                'devId'  => env('EBAY_DEV_ID'),
            ],
        ]);

        $request = new \DTS\eBaySDK\Trading\Types\FetchTokenRequestType();
        $request->SessionID = session()->get('sessionid', '');
        $response = $service->fetchToken($request);

        auth()->user()->update([
            'ebay_auth_token'            => $response->eBayAuthToken,
            'ebay_auth_token_expiration' => $response->HardExpirationTime,
        ]);

        $merchant = Merchant::find(auth()->id());

        dispatch(new EbayIntegrationJob($merchant, 'Initial Integration'));

        session()->flash('success', 'Currently integrating eBay products. You will be notified by email when complete.');

        return redirect('/settings');
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('pages.settings.create');
    }

    /**
     * @param Request $request
     */
    public function disconnect(Request $request)
    {
        switch ($request->get('channel')) {
            case CHANNEL_AMAZON:
                auth()->user()->update([
                    'amazon_seller_id'      => '',
                    'amazon_auth_token'     => '',
                    'amazon_marketplace_id' => '',
                ]);
                break;
            case CHANNEL_EBAY:
                auth()->user()->update([
                    'ebay_auth_token'            => '',
                    'ebay_auth_token_expiration' => '',
                ]);
                break;
            default:
                abort(422);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connectAmazon(Request $request)
    {
        $this->validate($request, [
            'amazon_seller_id'  => 'required',
            'amazon_auth_token' => 'required',
        ]);

        auth()->user()->update($request->only('amazon_seller_id', 'amazon_auth_token', 'amazon_import_options'));

        dispatch(new AmazonIntegrationJob(auth()->user(), 'Initial Integration'));

        session()->flash('success', 'Currently integrating Amazon products. You will be notified by email when complete.');

        return redirect()->back();
    }

    /**
     * @return string
     */
    public function getEbaySessionId()
    {
        $service = new TradingService(array_except(config('channels.ebay'), 'ruName'));
        $request = new GetSessionIDRequestType();

        $request->RuName = config('channels.ebay.ruName');

        $response = $service->getSessionID($request);

        session()->put('sessionid', $response->SessionID);

        return $response->SessionID;
    }

    /**
     * @return string
     */
    protected function getEbayAuthUrl()
    {
        $sessionId = $this->getEbaySessionId();
        $runame = config('channels.ebay.ruName');
        $sandbox = config('channels.ebay.sandbox') ? '.sandbox' : '';

        return "https://signin$sandbox.ebay.com/ws/eBayISAPI.dll?SignIn&runame=$runame&SessID=$sessionId";
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function storeDeduction(Request $request)
    {
        $this->validate($request, [
            'from_weight' => 'required|numeric',
            'to_weight'   => 'required|numeric|different:from_weight|min:'.$request->get('from_weight'), // hack to make it so that they can't be equal
            'amount'      => 'required|numeric',
        ]);

        $deductions = auth()->user()->importDeductions;

        // prevent overlap
        foreach ($deductions as $existing) {
            $overlaps = overlaps(
                $request->get('from_weight'),
                $request->get('to_weight'),
                $existing->from_weight,
                $existing->to_weight,
                //$canTouch = true
                $canTouch = false
            );

            if ($overlaps) {
                return response(['error' => ['There is a deduction weight range overlap.']], 422);
            }
        }

        $attributes = $request->only('from_weight', 'to_weight', 'amount');
        $attributes['merchant_id'] = auth()->id();

        $deduction = ShippingDeduction::create($attributes);

        return response(['data' => $deduction]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroyDeduction(Request $request)
    {
        $deduction = ShippingDeduction
            ::whereMerchantId(auth()->id())
            ->whereId($request->get('id'))
            ->firstOrFail();

        $deduction->delete();

        return response(['data' => ['message' => 'good']]);
    }

    /**
     * @param Request $request
     * @return MessageBag
     */
    protected function validateAddressAndPayment(Request $request)
    {
        $messageBag = new MessageBag();
        $messageBag->merge(MerchantAddress::validate($request->get('address_mailing'), 'mailing'));

        if ($request->get('address_billing')) {
            $messageBag->merge(MerchantAddress::validate($request->get('address_billing'), 'billing'));
        }

        $messageBag->merge(MerchantPayment::validate($request));
        return $messageBag;
    }
}
