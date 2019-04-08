"use strict";

var FormManager = function(form, validationMap) {
	var self = this,
		validationMap = validationMap || {
			'first_name': 'validName',
			'last_name': 'validName',
			'email': 'validEmail',
			'subject': 'validAlphaNum',
			'message': 'validInput'
		},
		errorMsgMap = {
			'email:validEmail': 'The email field must contain a valid email address'
		},
		errors = {};

	this.init = function() {
		if (form.data('validateOnStart')) {
			self.validateForm();
		}

		form.on('change', '.form-control', function() {
			self.validateInput($(this));
		});

		form.on('submit', self.validateForm);
	};

	this.validateForm = function()
	{
		var isValid = true;

		form.find('.form-group').each(function() {
			if (!self.validateInput($(this).find('.form-control'))) {
				isValid = false;
			}
		});

		return isValid;
	};

	this.validateInput = function(input)
	{
		var inputName = input.attr('name'), callback, isValid, errorMsg, method;

		//clear error for this input
		errors[inputName] = '';

		if (validationMap[inputName]) {
			callback = 	validationMap[inputName];
		} else {
			callback = 'validInput';
		}
		if (self[callback](inputName, input.val())) {
			self.setSuccessStatus(input);
			isValid = true;
		} else {
			if (errors[inputName]) {
				method = errors[inputName];
				if (errorMsgMap[inputName + ':' + method]) {
					errorMsg = errorMsgMap[inputName + ':' + method];
				}
			}

			self.setErrorStatus(input, errorMsg);
			isValid = false;
		}

		return isValid;
	};

	this.setSuccessStatus = function(input)
	{
		input.siblings('.form-control-feedback').removeClass('fa-close hidden').addClass('fa-check');
		input.siblings('span.sr-only').removeClass('hidden').text('(success)');
		input.closest('.form-group').removeClass('has-error').addClass('has-success').find('.errormsg').empty();
	};

	this.setErrorStatus = function(input, errorMsg)
	{
		var fieldName = input.attr('name').replace(/[-_]+/, ' ');
		var errorMsg = errorMsg || 'The ' + fieldName + ' field is required';

		input.siblings('.form-control-feedback').removeClass('fa-check hidden').addClass('fa-close');
		input.siblings('span.sr-only').removeClass('hidden').text('(error)');
		input.closest('.form-group').removeClass('has-success').addClass('has-error').find('.errormsg').text(errorMsg);
	};

	this.validInput = function(inputName, value)
	{
		var cleanValue = self.trimSpace(value);

		return cleanValue != '';
	};

	this.validName = function(inputName, value)
	{
		if (self.validInput(inputName, value)) {
			if (value.length <= 255) {
				return true;
			} else {
				errors[inputName] = 'validName';
			}
		}
		return false;
	};

	this.validAlphaNum = function(inputName, value)
	{
		var alphaDash = /^[a-zA-Z0-9\-\_ ]+$/;

		if (self.validInput(inputName, value)) {
			if (value.match(alphaDash)) {
				return true;
			} else {
				errors[inputName] = 'validAlphaNum';
			}
		}
		return false;
	};

	this.validEmail = function(inputName, value)
	{
		var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;

		if (self.validInput(inputName, value)) {
			if (value.match(emailExp)) {
				return true;
			} else {
				errors[inputName] = 'validEmail';
			}
		}
		return false;
	};

	this.validStateAbbr = function(inputName, value)
	{
		var stateAbbrExp = /^[a-zA-Z]{2}$/;

		if (self.validInput(inputName, value)) {
			if (value.match(stateAbbrExp)) {
				return true;
			} else {
				errors[inputName] = 'validStateAbbr';
			}
		}
		return false;
	};

	this.noValidate = function(inputName, value)
	{
		return true;
	};

	this.trimSpace = function(string)
	{
		return string.replace(/^\s+|\s+$/g, '');
	};
};
