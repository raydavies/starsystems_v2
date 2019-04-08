@extends('layout.page')

@section('title', 'Enter New Password')

@section('page_content')
    <div class="row clearfix">
        <div class="col-md-6 col-md-offset-3">
            @include('partial.alert_box')

            {!! Form::open(array('route' => 'password-reset-form', 'method' => 'post', 'id' => 'password_reset_form', 'novalidate' => 'novalidate')) !!}
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    {!! Form::label('email', 'Email', array('class' => 'control-label')) !!}
                    <div class="control-input">
                        {!! Form::email('email', null, ['required', 'id' => 'email', 'class' => 'form-control', 'aria-describedby' => 'email_status']) !!}
                        <span id="email_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('password', 'Password', array('class' => 'control-label')) !!}
                    <div class="control-input">
                        {!! Form::password('password', ['required', 'id' => 'password', 'class' => 'form-control', 'aria-describedby' => 'password_status']) !!}
                        <span id="password_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('password_confirmation', 'Confirm Password', array('class' => 'control-label')) !!}
                    <div class="control-input">
                        {!! Form::password('password_confirmation', ['required', 'id' => 'password_confirmation', 'class' => 'form-control', 'aria-describedby' => 'password_confirmation_status']) !!}
                        <span id="password_confirmation_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="text-center margin-top-15">
                    <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
