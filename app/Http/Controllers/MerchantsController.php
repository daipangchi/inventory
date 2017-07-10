<?php

namespace App\Http\Controllers;

use App\Models\Merchants\Merchant;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Mail;

class MerchantsController extends Controller
{
    /**
     * MerchantsController constructor.
     */
    public function __construct()
    {
        $this->middleware('admin', ['except' => ['settings', 'edit', 'update']]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $query = Merchant::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('id', $search);
            });
        }

        $merchants = $query->paginate(15);

        return view('pages.merchants.index', compact('merchants'));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('pages.merchants.create');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|max:255',
            'email' => 'required|email|max:255|unique:merchants',
        ]);

        $attributes = $request->all();
        $attributes['password'] = $tempPassword = str_random(10);

        $merchant = Merchant::create($attributes);

        Mail::send('emails.temp-password', compact('merchants', 'tempPassword'), function (Message $m) use ($merchant) {
            $m->to($merchant->email);
            $m->subject('Temporary password for your Cadabra Express account.');
            $m->from('noreply@cadabraexpress.com', 'Cadabra Express');
        });

        //return redirect("/merchants/$merchant->id/edit")->withInput($merchant->toArray());
        return redirect("/merchants/");
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $merchant = Merchant::findOrFail($id);

        return view('pages.merchants.edit', compact('merchant'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    /*public function edit(Request $request, $id)
    {
        if ($id !== auth()->id()) {
            return redirect('/');
        }

        $merchant = Merchant::findOrFail($id);
        $attributes = $request->only([
            'name',
            'amazon_seller_id',
            'amazon_auth_token',
            'amazon_import_options',
            'ebay_seller_id',
            'ebay_auth_token',
            'ebay_import_options',
        ]);

        $merchant->update($attributes);

        if ($request->ajax()) {
            return response(['data' => $merchant]);
        }

        session()->put('_old_input', $merchant->toArray());

        return view('pages.merchants.edit', compact('merchant'))
            ->with('_old_input', $merchant->toArray());
    }*/

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'                                  => 'string|max:255',
            'email'                                 => 'email|unique:merchants,email',
            'ebay_import_options.discount_amount'   => 'numeric|between:0,100',
            'amazon_import_options.discount_amount' => 'numeric|between:0,100',
        ]);

        $merchant = Merchant::findOrFail($id);

        if (auth()->user()->is_admin) Merchant::unguard();

        $attributes = $request->only('name', 'email', 'is_disabled', 'is_verified', 'ebay_import_options', 'amazon_import_options');
        $attributes = array_filter($attributes, function ($item) {
            return ! is_null($item);
        });

        $merchant->update($attributes);

        if (auth()->user()->is_admin) Merchant::reguard();

        if ($request->ajax()) {
            return response(['data' => $merchant]);
        }

        $request->session()->flash('_old_input', $merchant->fresh()->toArray());
        $request->session()->flash('success', 'Updated merchant.');

        return redirect()->back();
    }

    /**
     * Admin impersonates (logs in as) another merchant.
     *
     * @param $id
     * @return mixed
     */
    public function impersonate($id)
    {
        $merchant = Merchant::findOrFail($id);

        // Later, when the admin logs out (stops impersonating) a merchant
        // This session variable will be checked. If there is a value,
        // it will log that user back into the admin account.
        session()->put('impersonated_by', auth()->id());

        auth()->login($merchant);

        return redirect('/');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return response(['message' => 'You cannot do this to yourself'], 400);
        }

        Merchant::find($id)->delete();

        return response('good');
    }
}
