$(document).ready(function() {
	var testimonialsManager = new TestimonialsManager($('#testimonial_manager'));
	testimonialsManager.init();
});

function TestimonialsManager(context) {
	var self = this,
		filters = context.find('#testimonial_filters'),
		list = context.find('#testimonial_list');

	this.init = function () {
		$('[data-toggle="tooltip"]').tooltip();

		filters.on('click.filter', 'a[role="tab"]', self.filterTestimonials);

		list.on('click.toggle', '.activation-toggle-switch input', function () {
			var id = $(this).closest('.testimonial').attr('id');
			var testimonial_id = parseInt(id.substring(12), 10);
			self.toggleActiveStatus(testimonial_id);
		});

		list.on('click.delete', '.delete-button', function() {
			var id = $(this).closest('.testimonial').attr('id');
			var testimonial_id = parseInt(id.substring(12), 10);
			self.deleteTestimonial(testimonial_id);
		});
	};

	this.toggleActiveStatus = function (testimonial_id) {
		var testimonial, toggle;

		$.ajax({
			url: '/admin/testimonials/' + testimonial_id + '/toggle-status',
			dataType: 'json',
			type: 'post',
			error: function(obj, error, msg) {
				console.log(msg);
			},
			success: function (response) {
				if (response.data) {
					testimonial = $('#testimonial-' + testimonial_id);
					toggle = testimonial.find('.activation-toggle-switch');

					if (parseInt(response.data.flag_active, 10)) {
						testimonial.removeClass('panel-danger').addClass('panel-success');
					} else {
						testimonial.removeClass('panel-success').addClass('panel-danger');
					}

					//hide any testimonials that no longer match active filter status
					filters.find('li.active > a').trigger('click.filter');
				}
			}
		});
	};

	this.filterTestimonials = function() {
		var filter = $(this).data('status');
		var testimonials = list.find('.testimonial');

		if (filter === 1) {
			testimonials.each(function(id, testimonial) {
				var testimonialObj = $(testimonial),
					wrapper = testimonialObj.closest('.testimonial-wrapper'),
					input = testimonialObj.find('input[name="flag_active"]');

				if (input.prop('checked')) {
					wrapper.removeClass('hidden');
				} else {
					wrapper.addClass('hidden');
				}
			});
		} else if (filter === 0) {
			testimonials.each(function(id, testimonial) {
				var testimonialObj = $(testimonial),
					wrapper = testimonialObj.closest('.testimonial-wrapper'),
					input = testimonialObj.find('input[name="flag_active"]');

				if (!input.prop('checked')) {
					wrapper.removeClass('hidden');
				} else {
					wrapper.addClass('hidden');
				}
			});
		} else {
			testimonials.each(function(id, testimonial) {
				$(testimonial).closest('.testimonial-wrapper').removeClass('hidden');
			});
		}
	};

	this.deleteTestimonial = function(testimonial_id) {
		var testimonial, wrapper;

		if (!confirm('Are you sure you want to delete this testimonial?')) {
			return;
		}

		$.ajax({
			url: '/admin/testimonials/' + testimonial_id + '/delete',
			dataType: 'json',
			type: 'post',
			error: function(err, obj, msg) {
				console.log(msg);
			},
			success: function (response) {
				if (response.data && response.data.deleted === testimonial_id) {
					testimonial = $('#testimonial-' + testimonial_id);
					wrapper = testimonial.closest('.testimonial-wrapper');
					wrapper.remove();
				}
			}
		});
	};
}

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

$(document).ready(function() {
	$(window).bind('scroll', function() {
		showScrollButton();
	});

	$('a[href=#top]').bind('click', function(event) {
		event.preventDefault();
		scrollToTop(1000);
	});

	showScrollButton();
});

/**
 * Determines whether or not to display the scroll to top
 * button based on the window position.
 */
function showScrollButton()
{
	var breakpoint = ($('body').height()) / 4;

	if ($(document).scrollTop() >= breakpoint) {
		$('.scroll-btn').removeClass('hidden');
	} else {
		$('.scroll-btn').addClass('hidden');
	}
}

/**
 * Scrolls to the top of the window at the specified pace.
 * @param {int} time
 */
function scrollToTop(time)
{
	var time = parseInt(time, 10);
	$('html, body').animate({scrollTop: 0}, time);
}
