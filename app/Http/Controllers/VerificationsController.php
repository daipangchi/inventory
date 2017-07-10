<?php

namespace App\Http\Controllers;

use App\CustomerPortal\Connection;
use App\Models\EmailVerification;
use App\Models\Merchants\Merchant;
use Auth;

class VerificationsController extends Controller
{
    /**
     * Show page to notify user that email verification is required.
     *
     * @return mixed
     */
    public function notify()
    {
        return view('pages.verification.notify');
    }

    /**
     * @return mixed
     */
    public function failure()
    {
        return view('pages.verification.failure');
    }

    /**
     * @param $email
     * @param $token
     * @return mixed
     */
    public function verify($email, $token)
    {
        if ($verification = EmailVerification::whereToken($token)->whereEmail($email)->first()) {
            $merchant = Merchant::whereEmail($email)->first();

            // Send request to magento to create new merchant
            (new \App\CustomerPortal\Connection())->createMerchant($merchant);

            $verification->delete();

            $merchant->is_verified = true;
            $merchant->save();

            Auth::login($merchant);

            session()->put('initial_login', true);
            session()->flash('success', 'Your email has been verified.');

            return redirect('/profile');
        }

        return redirect('/');
    }
}
