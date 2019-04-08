$(document).ready(function() {
	$(document).on('show.bs.collapse', '#faq .question', function() {
		$(this).find('.caret').addClass('active');
	});

	$(document).on('hide.bs.collapse', '#faq .question', function() {
		$(this).find('.caret').removeClass('active');
	});
});
