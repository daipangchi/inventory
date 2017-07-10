<fieldset class="mailing-address-container m-t-1">
    <legend>Mailing Address</legend>

    <div class="form-group row">
        <label for="address_line_1" class="col-sm-2 form-control-label">Address Line 1</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="address_line_1" name="address_mailing[address_line_1]" class="form-control" placeholder="Address Line 1"
                       value="{{ old('address_mailing.address_line_1') ?: $addressMailing->address_line_1 ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="address_line_2" class="col-sm-2 form-control-label">Address Line 2</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="address_line_2" name="address_mailing[address_line_2]" class="form-control" placeholder="Address Line 2"
                       value="{{ old('address_mailing.address_line_2') ?: $addressMailing->address_line_2 ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="city" class="col-sm-2 form-control-label">City</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="city" name="address_mailing[city]" class="form-control"
                       placeholder="City" value="{{ old('address_mailing.city') ?: $addressMailing->city ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="state" class="col-sm-2 form-control-label">State</label>
        <div class="col-sm-10">
            <select id="state" name="address_mailing[state_code]" class="form-control" aria-label="Select State">
                <option value="">Select a state</option>
                @foreach(US_STATES as $code => $state)
                    <option value="{{ $code }}" {{ (old('address_mailing.state') ?: $addressMailing->state_code ?? '') == $code ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="form-group row">
        <label for="city" class="col-sm-2 form-control-label">Zipcode</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="zip_code" name="address_mailing[zip_code]" class="form-control"
                       placeholder="Zipcode" value="{{ old('address_mailing.zip_code') ?: $addressMailing->zip_code ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-10 col-sm-offset-2">
            <div class="checkbox">
                <input type="checkbox" id="address_same{{$number}}" name="address_same{{$number}}" checked>
                <label for="address_same{{$number}}">Is your mailing address the same as your billing address?</label>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="billing-address-container m-t-1">
    <legend>Billing Address</legend>

    <div class="form-group row">
        <label for="address_line_1" class="col-sm-2 form-control-label">Address Line 1</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="address_line_1" name="address_billing[address_line_1]" class="form-control" placeholder="Address Line 1"
                       value="{{ old('address_billing.address_line_1') ?: $addressBilling->address_line_1 ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="address_line_2" class="col-sm-2 form-control-label">Address Line 2</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="address_line_2" name="address_billing[address_line_2]" class="form-control" placeholder="Address Line 2"
                       value="{{ old('address_billing.address_line_2') ?: $addressBilling->address_line_2 ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="city" class="col-sm-2 form-control-label">City</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="city" name="address_billing[city]" class="form-control"
                       placeholder="City" value="{{ old('address_billing.city') ?: $addressBilling->city ?? '' }}">
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label for="state" class="col-sm-2 form-control-label">State</label>
        <div class="col-sm-10">
            <select id="state" name="address_billing[state_code]" class="form-control" aria-label="Select State">
                <option value="">Select a state</option>
                @foreach(US_STATES as $code => $state)
                    <option value="{{ $code }}" {{ (old('address_billing.state') ?: $addressBilling->state_code ?? '') == $code ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
        </div>
    </div>
</fieldset>
