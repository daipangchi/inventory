<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Mail\Message;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectPath = '/';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Reset the given merchant's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword $merchant
     * @param  string $password
     * @return void
     */
    protected function resetPassword($merchant, $password)
    {
        $merchant->forceFill([
            'password'       => $password,
            'remember_token' => Str::random(60),
        ])->save();

        Auth::guard($this->getGuard())->login($merchant);
    }
    
    protected function resetEmailBuilder()
    {
        return function (Message $message) {
            $message->subject($this->getEmailSubject());
            $message->from('noreply@cadabraexpress.com', 'Cadabra Express');
        };
    }
}
