@extends('master')

@section('content')
    <div class="page-merchants-settings">
        <div class="container">
            <h1>Enter Payment Information</h1>

            <div class="form-group row payment-type-container">
                <p class="col-sm-2"><strong>Payment type:</strong></p>

                <div class="col-sm-10">
                    <div class="radio-inline">
                        <input type="radio" name="payment_type" id="type_ach" value="ach" {{ old('payment_type') == 'ach' ? 'checked' : !old('payment_type') ? 'checked' : '' }}>
                        <label for="type_ach">ACH</label>
                    </div>
                    <div class="radio-inline">
                        <input type="radio" name="payment_type" id="type_check" value="check" {{ old('payment_type') == 'check' ? 'checked' : '' }}>
                        <label for="type_check">Check</label>
                    </div>
                    <div class="radio-inline">
                        <input type="radio" name="payment_type" id="type_paypal" value="paypal" {{ old('payment_type') == 'paypal' ? 'checked' : '' }}>
                        <label for="type_paypal">PayPal</label>
                    </div>
                </div>
            </div>

            @include('common.errors')

            <form action="/register/payment" method="POST" class="form-ach">
                <input type="hidden" name="type" value="ach">
                @include('pages.merchants.partials.settings.address')
                @include('pages.merchants.partials.settings.form-ach')
                @include('pages.merchants.partials.settings.common')
            </form>
            <form action="/register/payment" method="POST" class="form-check">
                <input type="hidden" name="type" value="check">
                @include('pages.merchants.partials.settings.address')
                @include('pages.merchants.partials.settings.form-check')
                @include('pages.merchants.partials.settings.common')
            </form>
            <form action="/register/payment" method="POST" class="form-paypal">
                <input type="hidden" name="type" value="paypal">
                @include('pages.merchants.partials.settings.address')
                @include('pages.merchants.partials.settings.form-paypal')
                @include('pages.merchants.partials.settings.common')
            </form>
        </div>
    </div>
@endsection
