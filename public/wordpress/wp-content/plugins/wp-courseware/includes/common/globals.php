<?php
/**
 * WP Courseware Globals
 *
 * @package WPCW
 * @subpackage Common
 * @since 4.3.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Define globals
global $wpcwdb, $fieldsToProcess_course, $fieldsToProcess_modules, $fieldsToProcess_units, $fieldsToProcess_quizzes, $fieldsToProcess_quiz_questions, $fieldsToProcess_quiz_custom_feedback, $fieldsToProcess_quizzes_inner__show_answers_settings, $fieldsToProcess_quizzes_inner__quiz_paginate_questions_settings;

// Legacy: Database
$wpcwdb = new \WPCW\Core\WPCWDB();

// Courses
$fieldsToProcess_course = array(
	'course_title',
	'course_desc',
	'course_opt_completion_wall',
	'course_opt_user_access',
	'course_from_name',
	'course_from_email',
	'course_to_email',
	'course_message_unit_complete',
	'course_message_unit_not_logged_in',
	'course_message_unit_pending',
	'course_message_unit_no_access',
	'course_message_unit_not_yet',
	'email_complete_module_option_admin',
	'email_complete_module_option',
	'email_complete_module_subject',
	'email_complete_module_body',
	'email_complete_course_option_admin',
	'email_complete_course_option',
	'email_complete_course_subject',
	'email_complete_course_body',

	// Added in V2.60
	'course_message_course_complete',
	'course_opt_use_certificate',

	// Added in V2.70
	'course_message_quiz_open_grading_blocking',
	'course_message_quiz_open_grading_non_blocking',
	'email_quiz_grade_option',
	'email_quiz_grade_subject',
	'email_quiz_grade_body',
	'email_complete_course_grade_summary_subject',
	'email_complete_course_grade_summary_body',

	// Added in 3.7
	'course_message_unit_not_yet_dripfeed',
	'email_unit_unlocked_subject',
	'email_unit_unlocked_body',

	// Added in 3.8.5
	'course_message_prerequisite_not_met',

	// Added in 4.0
	'course_author',

	// Added in 4.3.0
	'payments_type',
	'payments_price',
	'payments_interval',

	// Added in 4.4.0
	'course_status',

	// Added in 4.6.0
	'email_complete_unit_subject',
	'email_complete_unit_body',
	'email_complete_unit_option_admin',
	'email_complete_unit_option',
);

// Modules
$fieldsToProcess_modules = array(
	'module_title',
	'module_desc',
	'module_order',
	'module_number',
	'module_author',
);

// Units
$fieldsToProcess_units = array(
	'post_title',
	'post_content',
	'post_name',
	'post_author',
	'comment_status',
	'ping_status',
	'template',
	'unit_drip_type',
	'unit_drip_date',
	'unit_drip_interval',
	'unit_drip_interval_type',
	'unit_drip_date_ts',
	'unit_teaser',
);

// Quizzes
$fieldsToProcess_quizzes = array(
	'quiz_title',
	'quiz_desc',
	'quiz_type',
	'quiz_pass_mark',
	'quiz_show_answers',

	// @since V2.90
	'quiz_show_survey_responses',

	// @since V3.00
	'quiz_attempts_allowed',
	'quiz_paginate_questions',
	'quiz_timer_mode',
	'quiz_timer_mode_limit',
	'quiz_results_by_tag',
	'quiz_results_by_timer',
	'quiz_results_downloadable',

	// @since v4.0
	'quiz_author',

	// Not these fields, as we handle them separately as they are arrays
	// show_answers_settings
	// quiz_paginate_questions_settings
);

// Quiz Questions
$fieldsToProcess_quiz_questions = array(
	'question_type',
	'question_question',
	'question_correct_answer',
	'question_order',
	'question_answer_type',
	'question_answer_hint',
	'question_answer_file_types',
	'question_answer_explanation',
	'question_image',

	// @since V3.00
	'question_hash',
	'question_multi_random_enable',
	'question_multi_random_count',

	// @since V4.0
	'question_author',

	// Not this field, as we handle it separately as it's an array.
	//'question_data_answers'

	// Legacy fields - not used
	//'question_answers'
);

// Quiz Custom Feedback
$fieldsToProcess_quiz_custom_feedback = array(
	'qfeedback_score_type',
	'qfeedback_score_grade',
	'qfeedback_message',
	'qfeedback_summary',
	'qfeedback_tag_name',
);

// Quizzes answer settings
$fieldsToProcess_quizzes_inner__show_answers_settings = array(
	'show_correct_answer'         => array( 'on', 'off' ),
	'show_user_answer'            => array( 'on', 'off' ),
	'show_explanation'            => array( 'on', 'off' ),
	'mark_answers'                => array( 'on', 'off' ),
	'show_results_later'          => array( 'on', 'off' ),
	'show_other_possible_answers' => array( 'on', 'off' ),
);

// Quizzes paginate settings
$fieldsToProcess_quizzes_inner__quiz_paginate_questions_settings = array(
	'allow_review_before_submission' => array( 'on', 'off' ),
	'allow_students_to_answer_later' => array( 'on', 'off' ),
	'allow_nav_previous_questions'   => array( 'on', 'off' ),
);
