@extends('layout.page')

@section('title', 'Contact Us')

@section('headscripts')
	@parent
	<script>
        $(document).ready(function() {
            var form = new FormManager($('#contact_form'), {
				'first_name': 'required|validName',
				'last_name': 'required|validName',
				'email': 'required|validEmail',
				'subject': 'required|validAlphaNum',
				'message': 'required'
			});
            form.init();
        });
    </script>
@stop

@section('page_content')
	<div class="text-center">
		<header class="header header-xl">Contact Us</header>
		<p class="font-plus">For more information, or to leave a comment, please call us or fill out the form below!</p>
		<div class="row">
			<address class="contact_info col-sm-6 col-sm-offset-3">
				<a href="tel:7086757640">(708) 675-7640</a><br>
				Toll-Free <a href="tel:8448951124">(844) 895-1124</a>
			</address>
		</div>
	</div>

	<div id="form_wrapper" class="row">
		<div class="col-md-12">
			<form method="post" action={{ route('contact') }} accept-charset="UTF-8" id="contact_form" data-validate-on-start="{{ !empty($errors->all()) }}" novalidate="novalidate">
				@csrf
				<div class="row clearfix">
					<div class="form-group col-md-3 col-md-offset-3 col-xs-12 has-feedback">
						<label for="firstname" class="control-label">First Name</label>
						<div class="control-input">
							<input required type="text" name="first_name" id="firstname" class="form-control" aria-describedby="firstname_status">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="firstname_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('first_name') }}</span>
						</div>
					</div>
					<div class="form-group has-feedback col-md-3 col-xs-12">
						<label for="lastname" class="control-label">Last Name</label>
						<div class="control-input">
							<input required type="text" name="last_name" id="lastname" class="form-control" aria-describedby="lastname_status">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="lastname_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('last_name') }}</span>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="form-group col-md-6 col-md-offset-3 col-xs-12 has-feedback">
					<label for="email" class="control-label">Email</label>
						<div class="control-input">
							<input required type="email" name="email" id="email" class="form-control" placeholder="e.g. john.smith@example.com" aria-describedby="email_status">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="email_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('email') }}</span>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="form-group col-md-6 col-md-offset-3 col-xs-12 has-feedback">
					<label for="subject" class="control-label">Subject</label>
						<div class="control-input">
							<input required type="text" name="subject" id="subject" class="form-control" aria-describedby="subject_status">
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="subject_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('subject') }}</span>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="form-group col-md-6 col-md-offset-3 col-xs-12 has-feedback">
					<label for="message" class="control-label">Message</label>
						<div class="control-input">
							<textarea required name="message" id="message" class="form-control" aria-describedby="message_status" cols="50" rows="10"></textarea>
							<i class="fa form-control-feedback hidden" aria-hidden="true"></i>
							<span id="message_status" class="sr-only hidden"></span>
						</div>
						<div class="control-message">
							<span class="errormsg">{{ $errors->first('message') }}</span>
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
