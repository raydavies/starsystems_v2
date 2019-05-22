jQuery(document).ready(function($) {
	jQuery('.ewd-feup-main-dashboard-review-ask').css('display', 'block');

	jQuery('.ewd-feup-main-dashboard-review-ask').on('click', function(event) {
		if (jQuery(event.srcElement).hasClass('notice-dismiss')) {
			var data = 'Ask_Review_Date=3&action=ewd_feup_hide_review_ask';
        	jQuery.post(ajaxurl, data, function() {});
        }
	});

	jQuery('.ewd-feup-review-ask-yes').on('click', function() {
		jQuery('.ewd-feup-review-ask-feedback-text').removeClass('feup-hidden');
		jQuery('.ewd-feup-review-ask-starting-text').addClass('feup-hidden');

		jQuery('.ewd-feup-review-ask-no-thanks').removeClass('feup-hidden');
		jQuery('.ewd-feup-review-ask-review').removeClass('feup-hidden');

		jQuery('.ewd-feup-review-ask-not-really').addClass('feup-hidden');
		jQuery('.ewd-feup-review-ask-yes').addClass('feup-hidden');

		var data = 'Ask_Review_Date=7&action=ewd_feup_hide_review_ask';
    	jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.ewd-feup-review-ask-not-really').on('click', function() {
		jQuery('.ewd-feup-review-ask-review-text').removeClass('feup-hidden');
		jQuery('.ewd-feup-review-ask-starting-text').addClass('feup-hidden');

		jQuery('.ewd-feup-review-ask-feedback-form').removeClass('feup-hidden');
		jQuery('.ewd-feup-review-ask-actions').addClass('feup-hidden');

		var data = 'Ask_Review_Date=1000&action=ewd_feup_hide_review_ask';
    	jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.ewd-feup-review-ask-no-thanks').on('click', function() {
		var data = 'Ask_Review_Date=1000&action=ewd_feup_hide_review_ask';
        jQuery.post(ajaxurl, data, function() {});

        jQuery('.ewd-feup-main-dashboard-review-ask').css('display', 'none');
	});

	jQuery('.ewd-feup-review-ask-review').on('click', function() {
		jQuery('.ewd-feup-review-ask-feedback-text').addClass('feup-hidden');
		jQuery('.ewd-feup-review-ask-thank-you-text').removeClass('feup-hidden');

		var data = 'Ask_Review_Date=1000&action=ewd_feup_hide_review_ask';
        jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.ewd-feup-review-ask-send-feedback').on('click', function() {
		var Feedback = jQuery('.ewd-feup-review-ask-feedback-explanation textarea').val();
		var EmailAddress = jQuery('.ewd-feup-review-ask-feedback-explanation input[name="feedback_email_address"]').val();
		var data = 'Feedback=' + Feedback + '&EmailAddress=' + EmailAddress + '&action=ewd_feup_send_feedback';
        jQuery.post(ajaxurl, data, function() {});

        var data = 'Ask_Review_Date=1000&action=ewd_feup_hide_review_ask';
        jQuery.post(ajaxurl, data, function() {});

        jQuery('.ewd-feup-review-ask-feedback-form').addClass('feup-hidden');
        jQuery('.ewd-feup-review-ask-review-text').addClass('feup-hidden');
        jQuery('.ewd-feup-review-ask-thank-you-text').removeClass('feup-hidden');
	});
});