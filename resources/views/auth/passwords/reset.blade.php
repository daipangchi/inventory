@extends('master')

@section('content')
    <div class="page-reset-password">

        <div class="container" style="background: none;">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 p-a-2" style="background: #fff;">
                    <header class="sign-title"
                            style="font-size: 1.25rem; text-align: center; margin: 0 0 15px; line-height: normal;">
                        Reset Password
                    </header>

                    @include('common.errors')

                    <form class="form-horizontal" role="form" method="POST" action="/password/reset">
                        {{ csrf_field() }}
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Email</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="text" id="email" name="email" class="form-control" placeholder="Email"
                                           value="{{ $email or old('email') }}">
                                </p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-sm-2 form-control-label">Password</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="password" id="password" name="password" class="form-control"
                                           placeholder="Password">
                                </p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password_confirmation" class="col-sm-2 form-control-label">Confirm
                                Password</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="form-control" placeholder="Confirm Password">
                                </p>
                            </div>
                        </div>

                        <div class="text-right m-t-2">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>


    {{--<div class="container">--}}
    {{--<div class="row">--}}
    {{--<div class="col-md-8 col-md-offset-2">--}}
    {{--<div class="panel panel-default">--}}

    {{--<div class="panel-body">--}}
    {{--<form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">--}}
    {{--{{ csrf_field() }}--}}

    {{--<input type="hidden" name="token" value="{{ $token }}">--}}

    {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">--}}
    {{--<label for="email" class="col-md-4 control-label">E-Mail Address</label>--}}

    {{--<div class="col-md-6">--}}
    {{--<input id="email" type="email" class="form-control" name="email"--}}
    {{--value="{{ $email or old('email') }}">--}}


    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">--}}
    {{--<label for="password" class="col-md-4 control-label">Password</label>--}}

    {{--<div class="col-md-6">--}}
    {{--<input id="password" type="password" class="form-control" name="password">--}}

    {{--@if ($errors->has('password'))--}}
    {{--<span class="help-block">--}}
    {{--<strong>{{ $errors->first('password') }}</strong>--}}
    {{--</span>--}}
    {{--@endif--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">--}}
    {{--<label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>--}}
    {{--<div class="col-md-6">--}}
    {{--<input id="password-confirm" type="password" class="form-control"--}}
    {{--name="password_confirmation">--}}

    {{--@if ($errors->has('password_confirmation'))--}}
    {{--<span class="help-block">--}}
    {{--<strong>{{ $errors->first('password_confirmation') }}</strong>--}}
    {{--</span>--}}
    {{--@endif--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="form-group">--}}
    {{--<div class="col-md-6 col-md-offset-4">--}}
    {{--<button type="submit" class="btn btn-primary">--}}
    {{--<i class="fa fa-btn fa-refresh"></i> Reset Password--}}
    {{--</button>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</form>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
@endsection
