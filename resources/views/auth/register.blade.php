@extends('layout.page')

@section('title', 'Register')

@section('page_content')
    <div class="row clearfix">
        <div class="col-md-6 col-md-offset-3">
            @include('partial.alert_box')

            <form method="post" action="{{ route('register') }}" id="registration_form" accept-charset="UTF-8" novalidate="novalidate">
                @csrf
                <div class="row clearfix">
					<div class="form-group col-md-6 col-xs-12 has-feedback">
						<label for="firstname" class="control-label">First Name</label>
						<div class="control-input">
							<input required type="text" name="first_name" id="firstname" class="form-control" aria-describedby="firstname_status">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="firstname_status" class="sr-only hidden"></span>
						</div>
					</div>
					<div class="form-group has-feedback col-md-6 col-xs-12">
						<label for="lastname" class="control-label">Last Name</label>
						<div class="control-input">
							<input required type="text" name="last_name" id="lastname" class="form-control" aria-describedby="lastname_status">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="lastname_status" class="sr-only hidden"></span>
						</div>
					</div>
                </div>

                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <div class="control-input">
                        <input required type="email" name="email" id="email" class="form-control" aria-describedby="email_status">
                        <span id="email_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <div class="control-input">
                        <input required type="password" name="password" id="password" class="form-control" aria-describedby="password_status">
                        <span id="password_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password-confirm" class="control-label">Confirm Password</label>
                    <div class="control-input">
                        <input required type="password" name="password_confirmation" id="password-confirm" class="form-control" aria-describedby="password_confirm_status">
                        <span id="password_confirm_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="text-center margin-top-15">
                    <button type="submit" class="btn btn-primary btn-lg">Register</button>
                </div>
            </form>
        </div>
    </div>
@stop
