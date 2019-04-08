var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-44796661-1']);
_gaq.push(['_setDomainName', 'starlearningsystems.com']);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

$(document).ready(function() {
	$(document).on('show.bs.collapse', '#faq .question', function() {
		$(this).find('.caret').addClass('active');
	});

	$(document).on('hide.bs.collapse', '#faq .question', function() {
		$(this).find('.caret').removeClass('active');
	});
});

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
	var lessonPicker = new LessonPicker();
	lessonPicker.init();
});

function LessonPicker() {
	var self = this;

	this.init = function() {
		$('#lesson_select_form').on('change.level', '#level_select', function() {
			var level_id = parseInt($(this).val(), 10);
			self.loadSubjects(level_id);
		});
	};

	this.loadSubjects = function(level_id) {
		$.ajax({
			url: '/lesson-topics/' + level_id,
			dataType: 'json',
			success: function(response) {
				var i, option;
				if (response.subjects.length) {
					$('#subject_select').empty();

					for (i in response.subjects) {
						option = $('<option/>').text(response.subjects[i].name).val(response.subjects[i].id);
						$('#subject_select').append(option);
					}
				}
			}
		});
	};
}

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

$(document).ready(function() {
	$(".carousel-inner").swipe( {
		swipeLeft: function(event, direction, distance, duration, fingerCount) {
			$(this).parent().carousel('next');
		},
		swipeRight: function() {
			$(this).parent().carousel('prev');
		},
		threshold: 50
	});
});
