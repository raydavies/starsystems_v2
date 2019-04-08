@extends('layout.dashboard')

@section('title', 'Change Password')

@section('content')
    <div class="row clearfix">
        <div class="col-md-6 col-md-offset-3">
            <header class="header header-xl">Change Password</header>
            <div class="alert alert-info" role="alert">Use this widget to create a new password for your user account. All fields are required.</div>
            @include('partial.alert_box')

			<form method="post" action="{{ route('admin.password.change') }}" accept-charset="UTF-8" id="change_password_form" novalidate="novalidate">
                @csrf
                <div class="form-group">
                    <label for="old_password" class="control-label">Current Password</label>
                    <div class="control-input">
                        <input required type="password" name="old_password" id="old_password" class="form-control" aria-describedby="old_password_status">
                        <span id="old_password_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="control-label">New Password</label>
                    <div class="control-input">
                        <input required type="password" name="password" id="password" class="form-control" aria-describedby="password_status">
                        <span id="password_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="control-label">Confirm New Password</label>
                    <div class="control-input">
                        <input required type="password" name="password_confirmation" id="password_confirmation" class="form-control" aria-describedby="password_confirmation_status">
                        <span id="password_confirmation_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="text-center margin-top-15">
                    <button type="submit" class="btn btn-primary btn-lg">Change Password</button>
                </div>
            </form>
        </div>
    </div>
@stop
