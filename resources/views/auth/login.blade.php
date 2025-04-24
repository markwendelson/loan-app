@extends('layouts.auth')

@section('content')
    <div class="login-card login-dark">
        <div>
            <div>
                <a class="logo"href="#">
                    <img class="img-fluid for-dark" src="{{url('assets/images/logo/guiuan-logo.png')}}" alt="looginpage"><img class="img-fluid for-light" src="{{url('assets/images/logo/guiuan-logo.png')}}" alt="looginpage">
                </a>
            </div>


            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="login-main">
                <form class="theme-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <h4>Sign in to account </h4>
                    <p>Enter your email & password to login</p>

                    <div class="form-group">
                        <label class="col-form-label" for="email">Email Address</label>
                        <input class="form-control" id="email" name="email" type="email" required placeholder="Test@gmail.com" value="{{old('email')}}" />
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="password">Password </label>
                        <div class="form-input position-relative">
                            <input class="form-control" type="password" id="password" name="password" value = "" required placeholder="*********" />
                            <div class="show-hide">
                                <span class="show"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <div class="checkbox p-0">
                            <input id="remember" type="checkbox" name = "remember">
                            <label class="text-muted" for="remember">Remember password</label>
                        </div>
                        <a class="link" href="#">Forgot password?</a>
                        <div class="text-end mt-3">
                            <button class="btn btn-primary btn-block w-100" type="submit">Sign in</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection