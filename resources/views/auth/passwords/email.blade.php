@extends('layout.page')

@section('title', 'Forgot Password')

@section('page_content')
    <div class="row clearfix">
        <div class="col-md-6 col-md-offset-3">
            @include('partial.alert_box')

            <form method="post" action="{{ route('password-reset-email') }}" id="send_reset_email_form" accept-charset="UTF-8" novalidate="novalidate">
                @csrf
                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <div class="control-input">
                        <input required type="email" name="email" id="email" class="form-control" aria-describedby="email_status">
                        <span id="email_status" class="sr-only hidden"></span>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Send Password Reset Link</button>
                </div>
            </form>
        </div>
    </div>
@stop
