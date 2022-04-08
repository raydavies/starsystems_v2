$(document).ready(function() {
	var customerManager = new CustomerManager($('#customer_manager'));
	customerManager.init();
});

function CustomerManager(context) {
	var self = this;

	this.init = function () {
		context.on('click.delete', '.delete-button', function() {
			var id = $(this).closest('.customer').attr('id');
			var customer_id = parseInt(id.split('-', 2).pop(), 10);
			self.deleteCustomer(customer_id);
		});
	};

    this.deleteCustomer = function(customer_id) {
		if (!confirm('Are you sure you want to delete this customer?')) {
			return;
		}

		$.ajax({
			url: '/admin/customer/' + customer_id + '/delete',
			dataType: 'json',
			type: 'post',
            beforeSend: function() {
                $('#overlay').removeClass('hidden');
                $('#customer_manager > .spinner').removeClass('hidden');
            },
			error: function(err, obj, msg) {
				console.log(msg);
			},
			success: function (response) {
				if (response.data && response.data.deleted === customer_id) {
					$('#customer-' + customer_id).remove();
				}
			},
            complete: function () {
                $('#overlay').addClass('hidden');
                $('#customer_manager > .spinner').addClass('hidden');
				location.reload();
            }
		});
	};
}