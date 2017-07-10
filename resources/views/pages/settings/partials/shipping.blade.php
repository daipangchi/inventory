<?php
$merchant = auth()->user();
$shippingCredentials = json_decode($merchant->shipping_credential);
?>

<form action="/profile?_type=shipping" method="post">
    <h3>Shipping Credentials</h3>

    <hr>

    <div class="form-group row">
        <div class="col-sm-6">
            <fieldset class="form-group">
                <label class="form-label" for="shipping_username">Username</label>
                <input type="text" class="form-control" id="shipping_username" name="shipping_username" placeholder="Username" value="<?php echo isset($shippingCredentials->username) ? $shippingCredentials->username : ''; ?>">
            </fieldset>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-6">
            <fieldset class="form-group">
                <label class="form-label" for="shipping_password">Password</label>
                <input type="text" class="form-control" id="shipping_password" name="shipping_password" placeholder="Password" value="<?php echo isset($shippingCredentials->password) ? $shippingCredentials->password : ''; ?>">
            </fieldset>
        </div>
    </div>

    <div class="text-xs-right">
        <button class="btn btn-primary">Save</button>
    </div>

    {!! csrf_field().method_field('patch') !!}
</form>
