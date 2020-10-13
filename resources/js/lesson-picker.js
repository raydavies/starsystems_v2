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
			beforeSend: function() {
				$('#subject_select').prop('disabled', true);
			},
			success: function(response) {
				var i, option;
				if (response.subjects.length) {
					$('#subject_select').prop('disabled', false).empty();

					for (i in response.subjects) {
						option = $('<option/>').text(response.subjects[i].name).val(response.subjects[i].id);
						$('#subject_select').append(option);
					}
				}
			}
		});
	};
}
