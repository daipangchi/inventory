<fieldset>
    <legend>Paypal</legend>

    <div class="form-group row{{ $errors->has('email') ? ' error' : '' }}">
        <label class="col-sm-2 form-control-label">Paypal Email</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="email" class="form-control" name="attributes[email]" placeholder="Paypal Email"
                       value="{{ old('attributes.email') ?: $paymentPaypal['email'] }}">
            </p>
        </div>
    </div>
</fieldset>
