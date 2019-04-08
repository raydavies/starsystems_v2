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
