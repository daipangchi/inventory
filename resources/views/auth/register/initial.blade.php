@extends('master')

@section('content')
    <div class="page-register">
        <form class="sign-box" action="/register" method="POST">
            {{ csrf_field() }}

            @include('common.errors')

            <div class="sign-avatar no-photo">+</div>

            <header class="sign-title">Sign Up</header>

            <div class="form-group{{ $errors->has('name') ? ' error' : '' }}">
                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                       placeholder="Company Name" aria-label="Company Name">
            </div>

            <div class="form-group{{ $errors->has('email') ? ' error' : '' }}">
                <input type="text" name="email" class="form-control" value="{{ old('email') }}"
                       placeholder="E-Mail" aria-label="Email">
            </div>

            <div class="form-group{{ $errors->has('password') ? ' error' : '' }}">
                <input type="password" name="password" class="form-control" placeholder="Password"
                       aria-label="Password">
            </div>

            <div class="form-group{{ $errors->has('password_confirmation') ? ' error' : '' }}">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Comfirm Password"
                       aria-label="Confirm Password">
            </div>

            <button type="submit" class="btn btn-rounded btn-success sign-up">Sign up</button>

            <p class="sign-note">Already have an account? <a href="/login">Sign in</a></p>
            <!--<button type="button" class="close">
                <span aria-hidden="true">&times;</span>
            </button>-->
        </form>
    </div>
@endsection
