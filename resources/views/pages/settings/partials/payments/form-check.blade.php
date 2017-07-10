<fieldset>
    <legend>Account Information</legend>

    <div class="form-group row">
        <p class="col-sm-2"><strong>Account type:</strong></p>

        <div class="col-sm-10">
            <div class="radio-inline">
                <input type="radio" name="attributes[account_type]" id="check_type_check" value="checking"
                        {{ (old('account_type') ?: $paymentCheck['account_type'] ?? '') == 'checking' ? 'checked' : '' }}>
                <label for="check_type_check">Checking</label>
            </div>
            <div class="radio-inline">
                <input type="radio" name="attributes[account_type]" id="check_type_savings" value="savings"
                        {{ (old('account_type') ?: $paymentCheck['account_type'] ?? '') == 'savings' ? 'checked' : '' }}>
                <label for="check_type_savings">Savings</label>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label for="check" class="col-sm-2 form-control-label">Business Name</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="check" name="attributes[business_name]" class="form-control" placeholder="Business Name"
                       value="{{ old('business_name') ?: $paymentCheck['business_name'] ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="bank_name" class="col-sm-2 form-control-label">Bank Name</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="bank_name" name="attributes[bank_name]" class="form-control" placeholder="Bank Name"
                       value="{{ old('bank_name') ?: $paymentCheck['bank_name'] ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="account_number" class="col-sm-2 form-control-label">Account #</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="account_number" name="attributes[account_number]" class="form-control" placeholder="Account #"
                       value="{{ old('account_number') ?: $paymentCheck['account_number'] ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="routing_number" class="col-sm-2 form-control-label">Bank #</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="routing_number" name="attributes[routing_number]" class="form-control" placeholder="Bank #"
                       value="{{ old('routing_number') ?: $paymentCheck['routing_number'] ?? '' }}">
            </p>
        </div>
    </div>
</fieldset>