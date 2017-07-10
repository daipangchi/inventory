<form action="/profile?_type=password" method="post">
    <h3>Change Your Password</h3>

    <hr>

    <div class="form-group row">
        <div class="col-sm-6">
            <fieldset class="form-group">
                <label class="form-label" for="current_password">Verify Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Verify Current Password">
            </fieldset>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-6">
            <fieldset class="form-group">
                <label class="form-label" for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password">
            </fieldset>
        </div>
        <div class="col-sm-6">
            <fieldset class="form-group">
                <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm New Password">
            </fieldset>
        </div>
    </div>

    <div class="text-xs-right">
        <button class="btn btn-primary">Update Password</button>
    </div>

    {!! csrf_field().method_field('patch') !!}
</form>
