<fieldset class="m-t-1">
    <legend>Account Type</legend>

    <div class="form-group row">
        <label for="business_name" class="col-sm-2 form-control-label">Name on Account</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="business_name" name="attributes[business_name]" class="form-control"
                       placeholder="Name on Account"
                       value="{{ old('attributes.business_name') ?: $paymentAch['business_name'] ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="bank_name" class="col-sm-2 form-control-label">Bank Name</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="bank_name" name="attributes[bank_name]" class="form-control" placeholder="Bank Name"
                       value="{{ old('attributes.bank_name') ?: $paymentAch['bank_name'] ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="account_number" class="col-sm-2 form-control-label">Account Number</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="account_number" name="attributes[account_number]" class="form-control"
                       placeholder="Account Number"
                       value="{{ old('attributes.account_number') ?: $paymentAch['account_number'] ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="routing_number" class="col-sm-2 form-control-label">Bank Routing Number</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="routing_number" name="attributes[routing_number]" class="form-control"
                       placeholder="Bank Routing Number"
                       value="{{ old('attributes.routing_number') ?: $paymentAch['routing_number'] ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="bank_location" class="col-sm-2 form-control-label">Bank City, State</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="bank_location" name="attributes[bank_location]" class="form-control"
                       placeholder="City, State"
                       value="{{ old('attributes.bank_location') ?: $paymentAch['bank_location'] ?? '' }}">
            </p>
        </div>
    </div>

</fieldset>
