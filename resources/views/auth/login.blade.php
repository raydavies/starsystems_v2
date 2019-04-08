@extends('layout.page')

@section('title', 'Login')

@section('page_content')
    <div class="row clearfix">
        <div class="col-md-6 col-md-offset-3">
            @include('partial.alert_box')

            <form method="post" action="{{ route('login') }}" id="login_form" accept-charset="UTF-8" novalidate="novalidate">
                @csrf
                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <div class="control-input">
                        <input required type="email" name="email" id="email" class="form-control" aria-describedby="email_status">
                        <span id="email_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="control-label label-with-link">Password <a href="{{ route('password-reset-email') }}" class="pull-right">Forgot password?</a></label>
                    <div class="control-input">
                        <input required type="password" name="password" id="password" class="form-control" aria-describedby="password_status">
                        <span id="password_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="text-center">
                    <label class="font-normal">
                        <input type="checkbox" name="remember" value="1"> Remember Me
                    </label>
                </div>

                <div class="text-center margin-top-15">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                </div>
            </form>
        </div>
    </div>
@stop
