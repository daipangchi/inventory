@extends('master')

@section('content')
    <div class="page-merchants-edit">
        <div class="container">
            <h1>Edit Merchant</h1>

            <form action="/merchants/{{ $merchant->id }}" method="post">
                {{ csrf_field() }}
                {{ method_field('patch') }}

                <div class="form-group row{{ $errors->has('name') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Merchant Name</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="text"
                                   class="form-control"
                                   name="name"
                                   placeholder="Merchant Name"
                                   value="{{ old('name') }}">
                        </p>
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('email') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Email</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="email"
                                   class="form-control"
                                   name="email"
                                   placeholder="Email"
                                   value="{{ old('email') }}">
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <p class="col-sm-10 col-sm-offset-2">An email will be sent to the merchant with a temporary
                        password.</p>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10 col-sm-offset-2">
                        <div class="form-check">
                            <input type="hidden" name="is_verified" value="0">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="is_verified"
                                       value="1" {{ old('is_verified') ? 'checked' : '' }}>
                                Verified
                            </label>
                        </div>
                        <div class="form-check m-t-1">
                            <input type="hidden" name="is_disabled" value="0">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="is_disabled"
                                       value="1" {{ old('is_disabled') ? 'checked' : '' }}>
                                Disabled
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection