Stripe.setPublishableKey(stripe_vars.publishable_key);
function stripeResponseHandler(status, response) {
    if (response.error) {
		// show errors returned by Stripe
        jQuery(".payment-errors").html(response.error.message);
		// re-enable the submit button
		jQuery('#stripe-submit').attr("disabled", false);
    } else {
        var form$ = jQuery("#stripe-payment-form");
        // token contains id, last4, and card type
        var token = response['id'];
        // insert the token into the form so it gets submitted to the server
        form$.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
        // and submit
        form$.get(0).submit();
    }
}
jQuery(document).ready(function($) {
	$("#stripe-payment-form").submit(function(event) {
		// disable the submit button to prevent repeated clicks
		$('#stripe-submit').attr("disabled", "disabled");
 
		// send the card details to Stripe
		Stripe.createToken({
			number: $('[data-stripe="card_number"]').val(),
			cvc: $('[data-stripe="card_cvc"]').val(),
			exp_month: $('.[data-stripe="exp_month"]').val(),
			exp_year: $('.[data-stripe="exp_year"]').val(),
			currency: $('.[data-stripe="currency"]').val()
		}, stripeResponseHandler);
 
		// prevent the form from submitting with the default action
		return false;
	});
});