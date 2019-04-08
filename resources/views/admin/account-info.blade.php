@extends('layout.dashboard')

@section('title', 'Account Information')

@section('content')
    <div class="row clearfix">
        <div class="col-md-6 col-md-offset-3">
            <header class="header header-xl">Account Information</header>
            <div class="form-group">
                <label for="name" class="control-label">Name</label>
                <div class="input-group control-input">
                    <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                    <input disabled type="text" name="name" id="name" class="form-control" aria-describedby="name_status" value="{{ $user->first_name }} {{ $user->last_name }}">
                    <span id="name_status" class="sr-only hidden"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="control-label">Email</label>
                <div class="input-group control-input">
                    <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                    <input disabled type="email" name="email" id="email" class="form-control" aria-describedby="email_status" value="{{ $user->email }}">
                    <span id="email_status" class="sr-only hidden"></span>
                </div>
            </div>
        </div>
    </div>
@stop
