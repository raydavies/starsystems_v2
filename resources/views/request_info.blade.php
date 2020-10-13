@extends('layout.page')

@section('title', 'Request More Information')

@section('headscripts')
	@parent
	<script>
        $(document).ready(function() {
            var form = new FormManager($('#request_info_form'), {
				'name': 'required|validName',
				'city': 'validName',
				'email': 'required|validEmail',
				'phone_home': 'required',
				'child_name': 'validName',
			});
            form.init();
        });
    </script>
@stop

@section('page_content')
	<div class="text-center">
		<header class="header header-xl">Request More Information</header>
		<p class="font-plus">Would you like to find out more about the Interactive Curriculum? Fill out the form below!</p>
		<p class="alert alert-info col-md-6 col-md-offset-3">All fields marked with an asterisk (*) are required.</p>
	</div>

	<div id="form_wrapper" class="row">
		<div class="col-md-12">
			<form method="post" action={{ route('request_info') }} accept-charset="UTF-8" id="request_info_form" novalidate="novalidate">
				@csrf
				<div class="row clearfix">
					<div class="form-group col-md-6 col-md-offset-3 col-xs-12 has-feedback">
						<label for="name" class="control-label">Name</label>
						<span class="required-field-marker">*</span>
						<div class="control-input">
							<input required type="text" name="name" id="name" class="form-control" aria-describedby="name_status" value="{{ $customer->name }}">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="name_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('name') }}</span>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="form-group col-md-6 col-md-offset-3 col-xs-12 has-feedback">
						<label for="street_address" class="control-label">Street Address</label>
						<div class="control-input">
							<input type="text" name="street_address" id="street_address" class="form-control" aria-describedby="street_address_status" value="{{ $customer->street_address }}">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="street_address_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('street_address') }}</span>
						</div>
					</div>
				</div>


				<div class="row clearfix">
                    <div class="form-group col-md-3 col-md-offset-3 col-xs-12 has-feedback">
                        <label for="city" class="control-label">City</label>
                        <div class="control-input">
                            <input type="text" name="city" id="city" class="form-control" aria-describedby="city_status" value="{{ $customer->city }}">
                            <i class="fa form-control-feedback hidden" aria-hidden="true"></i>
                            <span id="city_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('city') }}</span>
                        </div>
                    </div>
                    <div class="form-group col-md-1 col-xs-12">
                        <label for="state_province" class="control-label">State</label>
                        <div class="control-input">
                            <select name="state_province" id="state_province" class="form-control" aria-describedby="state_province_status">
								<option value="" />
                                @foreach ($states as $state)
                                    <option value="{{ $state->abbreviation }}" @if ($customer->state_province === $state->abbreviation) selected="selected" @endif>{{ $state->abbreviation }}</option>
                                @endforeach
                            </select>
                            <span id="state_province_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('state_province') }}</span>
                        </div>
                    </div>
					<div class="form-group col-md-2 col-xs-12 has-feedback">
                        <label for="zip_code" class="control-label">Zip Code</label>
                        <div class="control-input">
							<input type="text" name="zip_code" id="zip_code" class="form-control" aria-describedby="zip_code_status" value="{{ $customer->zip_code }}">
                            <i class="fa form-control-feedback hidden" aria-hidden="true"></i>
                            <span id="zip_code_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('zip_code') }}</span>
                        </div>
                    </div>
                </div>

				<div class="row clearfix">
					<div class="form-group col-md-6 col-md-offset-3 col-xs-12 has-feedback">
						<label for="email" class="control-label">Email</label>
						<span class="required-field-marker">*</span>
						<div class="control-input">
							<input required type="email" name="email" id="email" class="form-control" placeholder="e.g. john.smith@example.com" aria-describedby="email_status" value="{{ $customer->email }}">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="email_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('email') }}</span>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="form-group col-md-3 col-md-offset-3 col-xs-12 has-feedback">
						<label for="phone_home" class="control-label">Home Phone Number</label>
						<span class="required-field-marker">*</span>
						<div class="control-input">
							<input required type="text" name="phone_home" id="phone_home" class="form-control" aria-describedby="phone_home_status" value="{{ $customer->phone_home }}">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="phone_home_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('phone_home') }}</span>
						</div>
					</div>
					<div class="form-group col-md-3 col-xs-12 has-feedback">
						<label for="phone_work" class="control-label">Work Phone Number</label>
						<div class="control-input">
							<input type="text" name="phone_work" id="phone_work" class="form-control" aria-describedby="phone_work_status" value="{{ $customer->phone_work }}">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="phone_home_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('phone_work') }}</span>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="form-group col-md-offset-3 col-md-3 col-xs-12 has-feedback">
						<label for="child_name" class="control-label">Child's Name</label>
						<div class="control-input">
							<input type="text" name="child_name" id="child_name" class="form-control" aria-describedby="child_name_status" value="{{ $customer->child_name }}">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="child_name_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('child_name') }}</span>
						</div>
					</div>
					<div class="form-group col-md-3 col-xs-12 has-feedback">
						<label for="grade" class="control-label">Child's Grade Level</label>
						<div class="control-input">
							<select name="grade" id="grade" class="form-control" aria-describedby="grade_status">
								<option value="" />
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->level }}" @if ($customer->grade === $grade->level) selected="selected" @endif>{{ $grade->name }}</option>
                                @endforeach
                            </select>
							<span id="grade_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('grade') }}</span>
						</div>
					</div>
				</div>

				<div class="text-center">
					<button type="submit" class="btn btn-success btn-lg">Send</button>
				</div>
			</form>
		</div>
	</div>
@stop
