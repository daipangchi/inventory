<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\Merchants\Merchant;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Validator;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Where to redirect users / merchants after logout.
     *
     * @var string
     */
    protected $redirectAfterLogout = '/login';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => [
            'logout',
            'showPaymentRegistrationForm',
            'registerPayment',
        ]]);
    }

    /**
     * @return mixed
     */
    public function showReset()
    {
        return view('auth.passwords.email');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $this->createMerchantAndVerification($request->all());

        return redirect('/verify');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        if (property_exists($this, 'registerView')) {
            return view($this->registerView);
        }

        return view('auth.register.initial');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::guard($this->getGuard())->logout();

        $impersonatorId = session()->get('impersonated_by');

        if ($impersonatorId) {
            session()->remove('impersonated_by');
            auth()->login(Merchant::find($impersonatorId));
            return redirect('/merchants');
        }

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:merchants',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return Merchant
     */
    protected function createMerchantAndVerification(array $data)
    {
        $merchant = Merchant::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        $token = str_random();
        $email = $data['email'];

        EmailVerification::create(compact('token', 'email'));

        \Mail::send('emails.verification', compact('token', 'email'), function ($m) use ($merchant) {
            $m->to($merchant->email);
            $m->from('noreply@cadabraexpress.com', 'Cadabra Express');
            $m->subject('Verify your Cadabra Express email.');
        });

        return $merchant;
    }
    
    public function authenticated(Request $request, $user)
    {
        if (!$user->is_verified) {
            auth()->logout();
            return back()->with('warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        }
        
        return redirect()->intended($this->redirectPath());
    }
}
