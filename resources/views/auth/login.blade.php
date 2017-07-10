@extends('master')

@section('content')
    <div class="page-login">

        <form action="/login" method="POST" class="sign-box">
            @include('common.errors')

            {{ csrf_field() }}

            <div class="sign-avatar">
                <img src="img/avatar-sign.png" alt="">
            </div>

            <header class="sign-title">Sign In</header>

            <div class="form-group">
                <input type="text" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}"
                       aria-label="email">
            </div>

            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>

            <div class="form-group">
                <div class="checkbox float-left">
                    <input type="checkbox" id="signed-in">
                    <label for="signed-in">Keep me signed in</label>
                </div>
                <div class="float-right reset">
                    <a href="/forgot-password">Reset Password</a>
                </div>
            </div>

            <button type="submit" class="btn btn-rounded">Sign in</button>

            <p class="sign-note">New to our website? <a href="/register">Sign up</a></p>
            <!--<button type="button" class="close">
                <span aria-hidden="true">&times;</span>
            </button>-->
        </form>
    </div>
@endsection
