@extends('master')

<!-- Main Content -->
@section('content')
    <div class="page-forgot-password">
        <form action="/password/email" method="post" class="sign-box reset-password-box">
            {{ csrf_field() }}

            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <header class="sign-title">Reset Password</header>

            <div class="form-group{{ $errors->has('email') ? ' error' : '' }}">
                <input type="text" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}">
            </div>

            <button type="submit" class="btn btn-rounded">Reset</button>
            or
            <a href="login">Sign in</a>
        </form>
    </div>
@endsection
