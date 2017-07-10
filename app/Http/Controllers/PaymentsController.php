<?php

namespace App\Http\Controllers;

use App\Models\Merchants\MerchantPayment;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Show payment registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('auth.register.payment');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function store(Request $request)
    {
        $validator = MerchantPayment::validate($request);

        if ($validator->fails()) {
            $input = $request->except('password', 'password_confirmation');

            return back()->withErrors($validator)->withInput($input);
        }

        MerchantPayment::create($request->all());

        // todo show success message
//        $request->session()->flash('test', 'success');

        return redirect('/');
    }
}
