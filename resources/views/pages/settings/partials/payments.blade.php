<h3>Payments</h3>

<hr>

<div class="form-group row">
    <p class="col-sm-2"><strong>Payment type:</strong></p>
                                   
    <div class="col-sm-10 payment-types-container">
        <div class="radio-inline">                                                              
            <input type="radio" name="payment_type" id="type_ach" value="ach" {{ old('method') == 'ach' ? 'checked' : '' }}>
            <label for="type_ach">ACH</label>
        </div>
        <div class="radio-inline">
            <input type="radio" name="payment_type" id="type_check" value="check" {{ old('method') == 'check' ? 'checked' : '' }}>
            <label for="type_check">Check</label>
        </div>
        <div class="radio-inline">
            <input type="radio" name="payment_type" id="type_paypal" value="paypal" {{ old('method') == 'paypal' ? 'checked' : '' }}>
            <label for="type_paypal">PayPal</label>
        </div>
    </div>
</div>

@include('common.errors')

<form action="/profile?_type=payment" method="POST" class="payment form-ach" <?php echo (old('method') === 'ach' || old('method') == '') ? '' : 'style="display:none;"'; ?>>
    <input type="hidden" name="method" value="ach">
    {{--The number is to differentiate the checkbox for address_same so the checkboxes work correctly--}}
    @include('pages.settings.partials.payments.address', ['number' => 1])
    @include('pages.settings.partials.payments.form-ach')
    @include('pages.settings.partials.payments.common')
</form>
<form action="/profile?_type=payment" method="POST" class="payment form-check" <?php echo old('method') === 'check' ? '' : 'style="display:none;"'; ?>>
    <input type="hidden" name="method" value="check">
    @include('pages.settings.partials.payments.address', ['number' => 2])
    @include('pages.settings.partials.payments.form-check')
    @include('pages.settings.partials.payments.common')
</form>
<form action="/profile?_type=payment" method="POST" class="payment form-paypal" <?php echo old('method') === 'paypal' ? '' : 'style="display:none;"'; ?>>
    <input type="hidden" name="method" value="paypal">
    @include('pages.settings.partials.payments.address', ['number' => 1])
    @include('pages.settings.partials.payments.form-paypal')
    @include('pages.settings.partials.payments.common')
</form>
