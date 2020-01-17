<?php
/**
 * WP Courseware Page Quiz Modify.
 *
 * Functions relating to modifying a quiz.
 *
 * @package WPCW
 * @since 1.0.0
 */

/**
 * Modify Quiz Load.
 *
 * @since 1.0.0
 */
function WPCW_showPage_ModifyQuiz_load() {
	// Thickbox needed for random and quiz windows.
	add_thickbox();
	wp_enqueue_media();

	// Globals
	global $wpcwdb;

	// Vars
	$page         = new PageBuilder( true );
	$quizDetails  = false;
	$adding       = false;
	$quizID       = false;
	$canAddQuiz   = false;
	$canEditQuiz  = false;
	$current_user = wp_get_current_user();

	// Check POST and GET
	if ( isset( $_GET['quiz_id'] ) ) {
		$quizID = absint( $_GET['quiz_id'] );
	} else if ( isset( $_POST['quiz_id'] ) ) {
		$quizID = absint( $_POST['quiz_id'] );
	}

	if ( ! $quizID ) {
		$adding = true;
	}

	$quizzes_page_url = add_query_arg( array( 'page' => 'wpcw-quizzes' ), admin_url( 'admin.php' ) );
	$courses_page_url = add_query_arg( array( 'post_type' => 'wpcw_course' ), admin_url( 'edit.php' ) );
	$modules_page_url = add_query_arg( array( 'page' => 'wpcw-modules' ), admin_url( 'admin.php' ) );
	$units_page_url   = add_query_arg( array( 'post_type' => 'course_unit' ), admin_url( 'edit.php' ) );

	$quiz_page_title = $adding ? esc_html__( 'Add Quiz / Survey', 'wp-courseware' ) : esc_html__( 'Edit Quiz / Survey', 'wp-courseware' );

	// Generate the tabs.
	$tabList = array(
		'wpcw_section_break_quiz_general'         => array( 'label' => __( 'General Settings', 'wp-courseware' ) ),
		'wpcw_section_break_quiz_logic'           => array( 'label' => __( 'Quiz Behaviour Settings', 'wp-courseware' ) ),
		'wpcw_section_break_quiz_results'         => array(
			'label'    => __( 'Result Settings', 'wp-courseware' ),
			'cssclass' => 'wpcw_quiz_only_tab',
		),
		'wpcw_section_break_quiz_custom_feedback' => array(
			'label'    => __( 'Custom Feedback', 'wp-courseware' ),
			'cssclass' => 'wpcw_quiz_only_tab',
		),
		'wpcw_section_break_quiz_questions'       => array( 'label' => __( 'Manage Questions', 'wp-courseware' ) ),
	);

	$formDetails = array(
		'wpcw_section_break_quiz_general' => array(
			'type' => 'break',
			'html' => WPCW_forms_createBreakHTML_tab( false ),
		),

		'quiz_title' => array(
			'label'    => __( 'Quiz Title', 'wp-courseware' ),
			'type'     => 'text',
			'required' => true,
			'cssclass' => 'wpcw_quiz_title',
			'desc'     => __( 'The title of your quiz or survey. Your trainees will be able to see this quiz title.', 'wp-courseware' ),
			'validate' => array(
				'type'   => 'string',
				'maxlen' => 150,
				'minlen' => 1,
				'regexp' => '/^[^<>]+$/',
				'error'  => __( 'Please specify a name for your quiz or survey, up to a maximum of 150 characters, just no angled brackets (&lt; or &gt;).', 'wp-courseware' ),
			),
		),

		'quiz_desc' => array(
			'label'    => __( 'Quiz/Survey Description', 'wp-courseware' ),
			'type'     => 'textarea',
			'required' => false,
			'cssclass' => 'wpcw_quiz_desc',
			'rows'     => 2,
			'desc'     => __( '(Optional) The description of this quiz. Your trainees won\'t see this description. It\'s just for your reference.', 'wp-courseware' ),
			'validate' => array(
				'type'   => 'string',
				'maxlen' => 5000,
				'minlen' => 1,
				'error'  => __( 'Please limit the description of your quiz to 5000 characters.', 'wp-courseware' ),
			),
		),

		'quiz_type' => array(
			'label'    => __( 'Quiz Type', 'wp-courseware' ),
			'type'     => 'radio',
			'required' => true,
			'cssclass' => 'wpcw_quiz_type wpcw_quiz_type_hide_pass',
			'data'     => array(
				'survey'       => __( '<b>Survey Mode</b> - No correct answers, just collect information.', 'wp-courseware' ),
				'quiz_block'   => __( '<b>Quiz Mode - Blocking</b> - require trainee to correctly answer questions before proceeding. Trainee must achieve <b>minimum pass mark</b> to progress to the next unit.', 'wp-courseware' ),
				'quiz_noblock' => __( '<b>Quiz Mode - Non-blocking</b> - require trainee to answer a number of questions before proceeding, but allow them to progress to the next unit <b>regardless of their pass mark</b>.', 'wp-courseware' ),
			),
		),

		'wpcw_section_break_quiz_logic' => array(
			'type' => 'break',
			'html' => WPCW_forms_createBreakHTML_tab( false ),
		),

		'quiz_pass_mark' => array(
			'label'    => __( 'Pass Mark', 'wp-courseware' ),
			'type'     => 'select',
			'required' => true,
			'cssclass' => 'wpcw_quiz_block_only wpcw_quiz_only',
			'data'     => WPCW_quizzes_getPercentageList( __( '-- Select a pass mark --', 'wp-courseware' ) ),
			'desc'     => __( 'The minimum pass mark that your trainees need to achieve to progress on to the next unit.', 'wp-courseware' ),
		),

		'quiz_recommended_score' => array(
			'label'           => __( 'Recommended Score', 'wp-courseware' ),
			'type'            => 'radio',
			'required'        => true,
			'cssclass'        => 'wpcw_quiz_noblock_only wpcw_quiz_only',
			'data'            => array(
				'show_recommended' => __( '<b>Show Recommended Score</b> - This setting will allow you to provide students with a recommended passing score for the quiz and give the trainee the opportunity to retake the quiz. The trainee will <b>still be able to progress</b> to the next unit if they do not want to retake the quiz.', 'wp-courseware' ),
				'no_recommended'   => __( '<b>No Recommended Score</b> - This setting will not display a recommended passing score for the quiz, and students will still be able to progress to the next unit.', 'wp-courseware' ),
			),

			// The list of subitems for each radio button
			'suffix_subitems' => array(

				// Show these options when 'show_recommended' is selected
				'show_recommended' => array(

					'show_recommended_percentage' => array(
						'type'     => 'select',
						'required' => false,
						'cssclass' => 'wpcw_quiz_recommended_score_percentages',
						'data'     => WPCW_quizzes_getPercentageList( __( '-- Select a pass mark --', 'wp-courseware' ) ),
					),
				), // end of quiz_paginate_questions array
			), // end suffix_subitems
		),

		'quiz_attempts_allowed' => array(
			'label'    => __( 'Number of Attempts Allowed?', 'wp-courseware' ),
			'type'     => 'select',
			'required' => true,
			'cssclass' => 'wpcw_quiz_only',
			'data'     => WPCW_quizzes_getAttemptList(),
			'desc'     => __( 'The maximum number of attempts that a trainee is given to complete a quiz.', 'wp-courseware' ),
		),

		'quiz_show_survey_responses' => array(
			'label'    => __( 'Show Survey Responses?', 'wp-courseware' ),
			'type'     => 'radio',
			'required' => true,
			'cssclass' => 'wpcw_survey_only',
			'data'     => array(
				'show_responses' => __( '<b>Show Responses</b> - Show the trainee their survey responses.', 'wp-courseware' ),
				'no_responses'   => __( '<b>No Responses</b> - Don\'t show the trainee their survey responses.', 'wp-courseware' ),
			),
			'desc'     => __( 'This setting allows you to choose whether or not you want students to be able to review their past survey responses when they return to units.', 'wp-courseware' ),
		),

		'quiz_paginate_questions' => array(
			'label'           => __( 'Paginate Questions?', 'wp-courseware' ),
			'type'            => 'radio',
			'required'        => true,
			'cssclass'        => 'wpcw_quiz_paginate_questions',
			'data'            => array(
				'use_paging' => __( '<b>Use Paging</b> - This setting will display quiz questions one at a time allowing students to progress through questions individually within a frame in your unit page.', 'wp-courseware' ),
				'no_paging'  => __( '<b>No Paging</b> - Don\'t use any paging. Show all quiz questions at once on the unit page.', 'wp-courseware' ),
			),

			// The list of subitems for each radio button
			'suffix_subitems' => array(

				// Show these options when 'use_paging' is selected
				'use_paging' => array(

					'quiz_paginate_questions_settings' => array(
						'type'     => 'checkboxlist',
						'required' => false,
						'cssclass' => 'wpcw_quiz_paginate_questions_group',
						'data'     => array(
							'allow_review_before_submission' => '<b>' . __( 'Allow Review Before Final Submission', 'wp-courseware' ) . '</b> - ' . __( 'If selected, students will be presented with an opportunity to review an editable list of all answers before final submission.', 'wp-courseware' ),
							'allow_students_to_answer_later' => '<b>' . __( 'Allow Students to Answer Later', 'wp-courseware' ) . '</b> - ' . __( 'If selected, the student will be able to click the "Answer Later" button and progress to the next question without answering. The question will be presented again at the end of the quiz.', 'wp-courseware' ),
							'allow_nav_previous_questions'   => '<b>' . __( 'Allow Navigation to Previous Questions', 'wp-courseware' ) . '</b> - ' . __( 'If selected, a "Previous Question" button will be displayed, allowing students to freely navigate backwards and forwards through questions.', 'wp-courseware' ),
						),
					),
				), // end of quiz_paginate_questions array
			), // end suffix_subitems
		),

		'quiz_timer_mode' => array(
			'label'    => __( 'Set Time Limit for Quiz?', 'wp-courseware' ),
			'type'     => 'radio',
			'required' => true,
			'cssclass' => 'wpcw_quiz_timer_mode wpcw_quiz_only',
			'data'     => array(
				'use_timer' => __( '<b>Specify a Quiz Time Limit</b> - Give the trainee a fixed amount of time to complete the quiz.', 'wp-courseware' ),
				'no_timer'  => __( '<b>No Time Limit</b> - the trainee can take as long as they wish to complete the quiz.', 'wp-courseware' ),
			),
		),

		'quiz_timer_mode_limit' => array(
			'label'     => __( 'Time Limit (in minutes)', 'wp-courseware' ),
			'type'      => 'text',
			'required'  => false,
			'cssclass'  => 'wpcw_quiz_timer_mode_limit wpcw_quiz_timer_mode_active_only wpcw_quiz_block_only',
			'extrahtml' => __( 'Minutes', 'wp-courseware' ),
			'validate'  => array(
				'type'  => 'number',
				'max'   => 1000,
				'min'   => 1,
				'error' => 'Please choose time limit between 1 and 1000 minutes.',
			),
		),

		'wpcw_section_break_quiz_results' => array(
			'type' => 'break',
			'html' => WPCW_forms_createBreakHTML_tab( false ),
		),

		'quiz_show_answers' => array(
			'label'           => __( 'Show Answers?', 'wp-courseware' ),
			'type'            => 'radio',
			'required'        => true,
			'cssclass'        => 'wpcw_quiz_show_answers wpcw_quiz_only',
			'data'            => array(
				'show_answers' => __( '<b>Show Answers</b> - Show the trainee the correct answers before they progress.', 'wp-courseware' ),
				'no_answers'   => __( '<b>No Answers</b> - Don\'t show the trainee any answers before they progress.', 'wp-courseware' ),
			),
			'extrahtml'       => '<div class="wpcw_msg_info wpcw_msg wpcw_msg_in_form wpcw_msg_error_no_answers_selected" style="display: none">' . __( 'If this option is selected, students will not be able to view correct or incorrect answers.', 'wp-courseware' ) . '</div>',

			// The list of subitems for each radio button
			'suffix_subitems' => array(

				// Show these options when 'show_answers' is selected
				'show_answers' => array(

					'show_answers_settings' => array(
						'type'      => 'checkboxlist',
						'required'  => false,
						'cssclass'  => '',
						'errormsg'  => __( 'Please choose at least one option when showing correct answers.', 'wp-courseware' ),
						'data'      => array(
							'show_correct_answer'         => '<b>' . __( 'Show correct answer', 'wp-courseware' ) . '</b> - ' . __( 'Show the trainee the correct answers before they progress.', 'wp-courseware' ),
							'show_user_answer'            => '<b>' . __( 'Show user\'s answer', 'wp-courseware' ) . '</b> - ' . __( 'Show the trainee the answer they submitted before they progress.', 'wp-courseware' ),
							'show_explanation'            => '<b>' . __( 'Show explanation', 'wp-courseware' ) . '</b> - ' . __( 'Show the trainee an explanation for the correct answer (if there is one).', 'wp-courseware' ),
							'show_other_possible_answers' => '<b>' . __( 'Show All Possible Answers', 'wp-courseware' ) . '</b> - ' . __( 'This option allows the trainee to view all of the possible answers (excluding the correct answer) which were presented for each question.', 'wp-courseware' ),
							'mark_answers'                => '<b>' . __( 'Mark Answers', 'wp-courseware' ) . '</b> - ' . __( 'This option will show correct answers with a green check mark, and incorrect answers with a red "X".', 'wp-courseware' ),
							'show_results_later'          => '<b>' . __( 'Leave quiz results available for later viewing?', 'wp-courseware' ) . '</b> - ' . __( 'This setting allows you to choose whether or not you want students to be able to review their past quiz answers when they return to units.', 'wp-courseware' ),
						),
						'extrahtml' => '<div class="wpcw_msg_error wpcw_msg wpcw_msg_in_form wpcw_msg_error_show_answers_none_selected" style="display: none">' . __( 'To make use of this showing answers setting, at least one of the above settings should be ticked. Otherwise, no answers are actually shown.', 'wp-courseware' ) . '</div>',
					),
				), // end of show_answers array
			),
		),

		'quiz_results_downloadable' => array(
			'label'      => __( 'Downloadable Quiz Results?', 'wp-courseware' ),
			'type'       => 'checkbox',
			'required'   => false,
			'cssclass'   => 'wpcw_quiz_only',
			'extralabel' => __( 'Enabling this option will provide a button for students to download their quiz results in PDF format upon completing a quiz.', 'wp-courseware' ),
		),

		'quiz_results_by_tag' => array(
			'label'      => __( 'Show Results by Tag?', 'wp-courseware' ),
			'type'       => 'checkbox',
			'required'   => false,
			'cssclass'   => 'wpcw_quiz_only',
			'extralabel' => __( '<b>Display results by question tag</b> - In addition to the overall score, indicate a breakdown of the results for each question tag.', 'wp-courseware' ),
		),

		'quiz_results_by_timer' => array(
			'label'      => __( 'Show Completion Time?', 'wp-courseware' ),
			'type'       => 'checkbox',
			'required'   => false,
			'cssclass'   => 'wpcw_quiz_timer_mode_active_only wpcw_quiz_block_only',
			'extralabel' => __( '<b>Display completion time for timed quiz</b> - If the quiz has been timed, this option will display the student\'s total time used for completing the quiz.', 'wp-courseware' ),
		),

		'quiz_author' => array(
			'label' => __( 'Quiz Author', 'wp-courseware' ),
			'type'  => 'hidden',
		),
	);

	$form = new RecordsForm(
		$formDetails,               // List of form elements
		$wpcwdb->quiz,              // Table for main details
		'quiz_id',                  // Primary key column name
		false,
		'wpcw_quiz_details_modify'  // Name of the form.
	);

	$form->customFormErrorMsg = __( 'Sorry, but unfortunately there were some errors saving the quiz details. Please fix the errors and try again.', 'wp-courseware' );
	$form->setAllTranslationStrings( WPCW_forms_getTranslationStrings() );

	// Got to summary of quizzes
	$directionMsg = ' ' . sprintf( __( '<a href="%s">Back to Quizzes / Surveys</a>', 'wp-courseware' ), admin_url( 'admin.php?page=wpcw-quizzes' ) );

	// Override success messages
	$form->msg_record_created = __( 'Quiz created successfully.', 'wp-courseware' ) . $directionMsg;
	$form->msg_record_updated = __( 'Quiz updated successfully.', 'wp-courseware' ) . $directionMsg;

	$form->setPrimaryKeyValue( $quizID );
	$form->setSaveButtonLabel( __( 'Save All Quiz Settings &amp; Questions', 'wp-courseware' ) );

	// Do default checking based on quiz type.
	$form->filterBeforeSaveFunction = 'WPCW_actions_quizzes_beforeQuizSaved';
	$form->afterSaveFunction        = 'WPCW_actions_quizzes_afterQuizSaved';

	// Set defaults when creating a new one
	if ( $adding ) {
		$form->loadDefaults( array(
			'quiz_pass_mark'                   => 50,
			'quiz_type'                        => 'quiz_noblock',
			'quiz_show_survey_responses'       => 'no_responses',

			// Show answers
			'quiz_show_answers'                => 'show_answers',
			'show_answers_settings'            => array(
				'show_correct_answer'         => 'on',
				'show_user_answer'            => 'on',
				'show_explanation'            => 'on',
				'mark_answers'                => 'on',
				'show_results_later'          => 'on',
				'show_other_possible_answers' => 'off',
			),

			// Paging
			'quiz_paginate_questions'          => 'no_paging',
			'quiz_paginate_questions_settings' => array(
				'allow_review_before_submission' => 'on',
				'allow_students_to_answer_later' => 'on',
				'allow_nav_previous_questions'   => 'on',
			),

			// Recommended Score
			'quiz_recommended_score'           => 'no_recommended',
			'show_recommended_percentage'      => 50,

			// Time Limit
			'quiz_timer_mode'                  => 'no_timer',
			'quiz_timer_mode_limit'            => '15',

			// Result settings
			'quiz_results_downloadable'        => 'on',
			'quiz_results_by_tag'              => 'on',
			'quiz_results_by_timer'            => 'on',

			'quiz_author' => $current_user->ID,
		) );
	}

	// Get the rendered form, extract the start and finish form tags so that
	// we can use the form across multiple panes, and submit other data such
	// as questions along with the quiz itself. We're doing this because the RecordsForm
	// object actually does a really good job, so we don't want to refactor to remove it.
	$formHTML = $form->getHTML();
	$formHTML = preg_replace( '/^(\s*?)(<form(.*?>))/', '', $formHTML );
	$formHTML = preg_replace( '/<\/form>(\s*?)$/', '', $formHTML );

	// Edit a quiz, or add a new one.
	if ( $quizDetails = WPCW_quizzes_getQuizDetails( $quizID, false, false, false ) ) {

		// Abort if quiz not found.
		if ( ! $quizDetails ) {
			$page->showPageHeader( $quiz_page_title, '70%' );
			$page->showMessage( __( 'Sorry, but that quiz / survey could not be found.', 'wp-courseware' ), true );
			$page->showPageFooter();

			return;
		} else {
			// Start form prolog - with quiz ID
			printf( '<form method="POST" action="%s&quiz_id=%d" name="wpcw_quiz_details_modify" id="wpcw_quiz_details_modify">', admin_url( 'admin.php?page=WPCW_showPage_ModifyQuiz' ), $quizDetails->quiz_id );

			$quiz_page_title .= sprintf(
				' <a class="page-title-action" href="%s">%s</a>',
				esc_url( $quizzes_page_url ),
				esc_html__( 'Back to Quizzes', 'wp-courseware' )
			);

			$quiz_page_title .= sprintf(
				' <a class="page-title-action" href="%s">%s</a>',
				esc_url( add_query_arg( array( 'page' => 'WPCW_showPage_ModifyQuiz' ), admin_url( 'admin.php' ) ) ),
				esc_html__( 'Add New Quiz', 'wp-courseware' )
			);

			$quiz_page_title .= sprintf(
				' <a class="page-title-action" href="%s">%s</a>',
				$courses_page_url,
				esc_html__( 'View Courses', 'wp-courseware' )
			);

			$quiz_page_title .= sprintf(
				' <a class="page-title-action" href="%s">%s</a>',
				$modules_page_url,
				esc_html__( 'View Modules', 'wp-courseware' )
			);

			$quiz_page_title .= sprintf(
				'<a class="page-title-action" href="%s">%s</a>',
				$units_page_url,
				esc_html__( 'View Units', 'wp-courseware' )
			);

			$page->showPageHeader( $quiz_page_title, '70%' );

			// Check permissions, this condition allows admins to view all modules even if they are not the author.
			if ( user_can( $current_user->ID, 'manage_wpcw_settings' ) ) {
				$canEditQuiz = true;
			}

			// Check Author
			if ( $quizDetails->quiz_author == $current_user->ID ) {
				$canEditQuiz = true;
			}

			// Back compat filter
			$canEditQuiz = apply_filters( 'wpcw_back_permissions_user_can_edit_quiz', $canEditQuiz, $current_user->ID, $quizDetails );

			// Add filters to override
			$canEditQuiz     = apply_filters( 'wpcw_permissions_user_can_edit_quiz', $canEditQuiz, $current_user, $quizDetails );
			$cantEditQuizMsg = apply_filters( 'wpcw_permissions_user_can_edit_quiz_msg', esc_attr__( 'You are not permitted to edit this quiz.', 'wp-courseware' ), $current_user, $quizDetails );

			// Display message if no access.
			if ( ! $canEditQuiz ) {
				$page->showMessage( $cantEditQuizMsg, true );
				$page->showPageFooter();

				return;
			}
		}
	} else {
		// Start form prolog - no quiz ID
		printf( '<form method="POST" action="%s" name="wpcw_quiz_details_modify" id="wpcw_quiz_details_modify">', admin_url( 'admin.php?page=WPCW_showPage_ModifyQuiz' ) );

		$quiz_page_title .= sprintf(
			' <a class="page-title-action" href="%s">%s</a>',
			esc_url( $quizzes_page_url ),
			esc_html__( 'Back to Quizzes', 'wp-courseware' )
		);

		if ( $form->primaryKeyValue > 0 ) {
			$quiz_page_title .= sprintf(
				' <a class="page-title-action" href="%s">%s</a>',
				esc_url( add_query_arg( array( 'page' => 'WPCW_showPage_ModifyQuiz' ), admin_url( 'admin.php' ) ) ),
				esc_html__( 'Add New Quiz', 'wp-courseware' )
			);
		}

		$quiz_page_title .= sprintf(
			' <a class="page-title-action" href="%s">%s</a>',
			$courses_page_url,
			esc_html__( 'View Courses', 'wp-courseware' )
		);

		$quiz_page_title .= sprintf(
			' <a class="page-title-action" href="%s">%s</a>',
			$modules_page_url,
			esc_html__( 'View Modules', 'wp-courseware' )
		);

		$quiz_page_title .= sprintf(
			'<a class="page-title-action" href="%s">%s</a>',
			$units_page_url,
			esc_html__( 'View Units', 'wp-courseware' )
		);

		if ( $form->primaryKeyValue > 0 ) {
			$edit_page_title = esc_html__( 'Edit Quiz / Survey', 'wp-courseware' );
			$quiz_page_title = str_replace( 'Add Quiz / Survey', $edit_page_title, $quiz_page_title );
			$page->showPageHeader( $quiz_page_title, '70%' );
		} else {
			$page->showPageHeader( $quiz_page_title, '70%' );
		}

		// Check permissions
		if ( user_can( $current_user->ID, 'view_wpcw_courses' ) ) {
			$canAddQuiz = true;
		}

		// Add filter to override
		$canAddQuiz     = apply_filters( 'wpcw_permissions_user_can_add_quiz', $canAddQuiz, $current_user );
		$cantAddQuizMsg = apply_filters( 'wpcw_permissions_user_can_add_quiz_msg', esc_attr__( 'You are not permitted to add a new quiz.', 'wp-courseware' ), $current_user );

		// Legacy Fitler
		$canAddQuiz     = apply_filters( 'wpcw_back_permissions_user_can_add_quiz', $canAddQuiz, $current_user->ID );
		$cantAddQuizMsg = apply_filters( 'wpcw_back_msg_permissions_user_can_add_quiz', esc_attr__( 'You are not permitted to add a new quiz.', 'wp-courseware' ), $current_user->ID );

		// Check
		if ( ! $canAddQuiz ) {
			$page->showMessage( $cantAddQuizMsg, true );
			$page->showPageFooter();

			return;
		}
	}

	// Need to move the submit button to before the closing form tag to allow it
	// to render on the page as expected after the questions drag-n-drop section.
	// Don't bother if we're not showing any questions yet though.
	$buttonHTML = false;
	if ( $form->primaryKeyValue > 0 ) {
		$pattern = '/<p class="submit">(\C*?)<\/p>/';
		if ( preg_match( $pattern, $formHTML, $matches ) ) {
			// Found it, so add to variable to show later, and strip
			// it from the HTML so far.
			$buttonHTML = $matches[0];
			$formHTML   = str_replace( $buttonHTML, false, $formHTML );
		}
	}

	// Not got any questions to show yet, so hide questions tab.
	if ( $form->primaryKeyValue <= 0 ) {
		unset( $tabList['wpcw_section_break_quiz_questions'] );
		unset( $tabList['wpcw_section_break_quiz_custom_feedback'] );
	}

	// Show a placeholder for an error message that may occur within tabs.
	printf( '<div class="wpcw_msg wpcw_msg_error wpcw_section_error_within_tabs">%s</div>', __( 'Unfortunately, there are a few missing details that need to be added before this quiz can be saved. Please resolve them and try again.', 'wp-courseware' ) );

	// Render the tabs
	echo WPCW_tabs_generateTabHeader( $tabList, 'wpcw_quizzes_tabs' );

	// The main quiz settings
	echo $formHTML;

	// Try to see if we've got an ID having saved the form from a first add
	// or we're editing the form
	if ( $form->primaryKeyValue > 0 ) {
		$quizID = $form->primaryKeyValue;

		// Top for jumps.
		printf( '<a name="top"></a>' );

		// 1) Custom Feedback Messages
		printf( '<div class="wpcw_form_break_tab"></div>' );

		printf( '<div class="form-table" id="wpcw_section_break_quiz_custom_feedback">' );
		WPCW_showPage_customFeedback_showEditForms( $quizID, $page );
		printf( '</div>' );

		// 2) Question Settings
		printf( '<div class="wpcw_form_break_tab"></div>' );

		printf( '<div class="form-table" id="wpcw_section_break_quiz_questions">' );
		WPCW_showPage_ModifyQuiz_showQuestionEntryForms( $quizID, $page );
		printf( '</div>' );
	}

	// Reshow the button here
	echo $buttonHTML;

	// The closing form tag
	echo '</form>';

	// .wpcw_tab_wrapper
	echo '</div>';

	// The thickboxes for the page
	WPCW_showPage_thickbox_questionPool();
	WPCW_showPage_thickbox_randomQuestion();

	$page->showPageFooter();
}

/**
 * Render the placeholder for the AJAX-loading
 * Thickbox that allows the user to add questions
 * to a quiz directly from a thickbox.
 *
 * @since 1.0.0
 */
function WPCW_showPage_thickbox_questionPool() {
	printf( '<div id="wpcw_tb_question_pool" style="display: none">' );
	printf( '<div id="wpcw_tb_question_pool_inner">' );

	echo WPCW_questionPool_showPoolTable( 20, $_GET, 'ajax' );

	printf( '</div>' ); // #wpcw_tb_question_pool_inner
	printf( '</div>' ); // #wpcw_tb_question_pool
}

/**
 * Render the placeholder for the AJAX-loading
 * Thickbox that allows the user to add a
 * selection of random questions using a tag.
 *
 * @since 1.0.0
 */
function WPCW_showPage_thickbox_randomQuestion() {
	$defaultQuestions = 10;

	printf( '<div id="wpcw_tb_random_question" style="display: none">' );
	printf( '<div id="wpcw_tb_random_question_inner">' );

	// Choice A - Whole Quiz Pool
	printf( '<div class="wpcw_tb_option_wrap" id="wpcw_tb_option_wrap_whole_pool">' );

	// Label
	printf( '<label class="wpcw_bold"><input type="radio" name="wpcw_tb_random_question_type" value="whole_pool" /> %s</label>',
		__( 'Randomly Select from Entire Quiz Pool', 'wp-courseware' )
	);
	printf( '<div class="wpcw_tb_description">%s</div>', __( 'If this option is selected, then number of questions you choose wil be randomly chosen from the entire quiz pool, regardless of question tag.', 'wp-courseware' ) );

	// How many questions are there?
	$questionCount = WPCW_questions_getQuestionCount();

	// Wraps the selection in a grey box
	printf( '<div class="wpcw_tb_option_selection">' );
	printf( '<label>%s&nbsp;&nbsp;</label>', __( 'Show a total of ', 'wp-courseware' ) );
	printf( '<input type="text" class="wpcw_spinner" value="%d" data-wpcw-max="%d"/>', $defaultQuestions, $questionCount );
	printf( '<label>&nbsp;&nbsp;%s</label>', __( 'questions', 'wp-courseware' ) );

	// Shows how many questions are available.
	printf( '<div class="wpcw_tb_random_question_count">%s <b>%d</b> %s</div>',
		__( 'There are a total of ', 'wp-courseware' ),
		$questionCount,
		__( 'questions available in the question pool.', 'wp-courseware' )
	);
	printf( '</div>' );

	printf( '</div>' ); // .wpcw_tb_option_wrap

	// Choice B - Select using question tags...
	printf( '<div class="wpcw_tb_option_wrap wpcw_tb_option_wrap_active" id="wpcw_tb_option_wrap_question_tags">' );

	// Label
	printf( '<label class="wpcw_bold"><input type="radio" name="wpcw_tb_random_question_type" value="question_tags" checked/> %s</label>',
		__( 'Randomly Select using Question Tags', 'wp-courseware' )
	);
	printf( '<div class="wpcw_tb_description">%s</div>', __( 'If this option is selected, then number of questions you choose for each tag will be randomly displayed.', 'wp-courseware' ) );

	// Wraps the selection in a grey box
	printf( '<div class="wpcw_tb_option_selection">' );

	// List the main ones.
	printf( '<div id="wpcw_tb_option_wrap_question_tags_list">' );
	WPCW_showPage_thickbox_randomQuestion_tagSelectionLine( 10, true );
	printf( '</div>' );

	// Create the add new line
	printf( '<hr/>' );
	printf( '<a href="#" id="wpcw_tb_option_wrap_question_tags_add">%s</a>', __( '+ Add Another', 'wp-courseware' ) );

	printf( '</div>' );

	printf( '</div>' ); // .wpcw_tb_option_wrap

	// Insert button
	printf( '<br/><div class="wpcw_button_group">' );
	printf( '<a href="#new_question" class="button-primary" id="wpcw_tb_random_question_inner_insert">%s</a>', __( 'Insert Random Question Selection', 'wp-courseware' ) );
	printf( '</div>' );

	printf( '</div>' ); // #wpcw_tb_random_question_inner
	printf( '</div>' ); // #wpcw_tb_random_question
}

/**
 * Creates a simple spinner line with delete/tag selection.
 *
 * @since 1.0.0
 *
 * @param integer $defaultCount The default count on the spinner.
 */
function WPCW_showPage_thickbox_randomQuestion_tagSelectionLine( $defaultCount = 10, $isFirst = false ) {
	printf( '<div class="wpcw_tb_option_wrap_question_tags_row">' );

	// Label prefix
	printf( '<label>%s&nbsp;&nbsp;</label>', __( 'Select  ', 'wp-courseware' ) );

	// Input box
	printf( '<input type="text" class="wpcw_spinner" value="%d" />', $defaultCount );

	// Label suffix
	printf( '<label>&nbsp;&nbsp;%s&nbsp;&nbsp;</label>', __( 'questions from', 'wp-courseware' ) );

	// The tag dropdown
	echo WPCW_questions_tags_getTagDropdown( __( '--- Select Tag ---', 'wp-courseware' ), 'tag_selection', false, 'wpcw_tb_option_tag_select', true );

	// The deletion link.
	printf( '<a href="#" class="wpcw_delete_icon" rel="%s" %s>%s</a>',
		__( 'Are you sure you wish to delete this selection?', 'wp-courseware' ),
		( $isFirst ? 'style="display: none;"' : false ), // Hide deletion link if this is the first item
		__( 'Delete', 'wp-courseware' )
	);

	// Marker to show that a tag needs selection.
	printf( '<div class="wpcw_missing_tag">%s</div>',
		__( 'Please select a tag', 'wp-courseware' )
	);

	printf( '</div>' );
}

/**
 * Show the forms where the quiz answers can be edited.
 *
 * @since 1.0.0
 *
 * @param integer $quizID the ID of the quiz to be edited.
 * @param object $page The associated page object for showing messages.
 */
function WPCW_showPage_ModifyQuiz_showQuestionEntryForms( $quizID, $page ) {
	global $wpdb, $wpcwdb;
	$wpdb->show_errors();

	// Work out if we need correct answers or not. And what the pass mark is.
	$quizDetails        = WPCW_quizzes_getQuizDetails( $quizID, true, false, false );
	$needCorrectAnswers = ( 'survey' != $quizDetails->quiz_type );

	// Show the existing quiz questions as a series of forms.
	$quizItems = WPCW_quizzes_getListOfQuestions( $quizID );

	// Show the number of correct answers the user must get in order to pass.
	if ( 'quiz_block' == $quizDetails->quiz_type ) {
		$totalQs = WPCW_quizzes_calculateActualQuestionCount( $quizID );
		$passQs  = ceil( ( $quizDetails->quiz_pass_mark / 100 ) * $totalQs );

		printf( '<div class="wpcw_msg wpcw_msg_info">' );
		printf( __( 'The trainee will be required to correctly answer at least <b>%1$d of the %2$d</b> following questions (<b>at least %3$d%%</b>) to progress.', 'wp-courseware' ), $passQs, $totalQs, $quizDetails->quiz_pass_mark );

		printf( '</div>' );
	}

	// Got a  quiz, and trainer is requiring to show answers. Tell them we can't show answers
	// as this quiz contains open-ended questions that need grading.
	if ( $needCorrectAnswers && 'show_answers' == $quizDetails->quiz_show_answers && WPCW_quizzes_containsQuestionsNeedingManualGrading( $quizItems ) ) {
		printf( '<div class="wpcw_msg wpcw_msg_error">' );

		printf(
			__( 'This quiz contains questions that need <b>manual grading</b>, and you\'ve selected \'<b>Show Answers</b>\' when the user completes this quiz. ', 'wp-courseware' ) . '<br/><br/>' .
			__( 'Since answers cannot be shown to the user because they are not known at that stage, <b>answers cannot be shown</b>. To hide this message, select \'<b>No Answers</b>\' above.', 'wp-courseware' )
		);
		printf( '</div>' );
	}

	$errorCount = 0;
	global $errorCount;

	// Wrapper for questions
	printf( '<ol class="wpcw_dragable_question_holder">' );

	if ( $quizItems ) {
		// Render edit form for each of the quizzes that already exist
		foreach ( $quizItems as $quizItem ) {
			switch ( $quizItem->question_type ) {
				case 'multi':
					$quizObj = new WPCW_quiz_MultipleChoice( $quizItem );
					break;

				case 'truefalse':
					$quizObj = new WPCW_quiz_TrueFalse( $quizItem );
					break;

				case 'open':
					$quizObj = new WPCW_quiz_OpenEntry( $quizItem );
					break;

				case 'upload':
					$quizObj = new WPCW_quiz_FileUpload( $quizItem );
					break;

				case 'random_selection':
					$quizObj = new WPCW_quiz_RandomSelection( $quizItem );
					break;

				default:
					die( __( 'Unknown quiz type: ', 'wp-courseware' ) . $quizItem->question_type );
					break;
			}

			$quizObj->showErrors         = true;
			$quizObj->needCorrectAnswers = $needCorrectAnswers;

			// Keep track of errors
			if ( $quizObj && $quizObj->gotError ) {
				$errorCount ++;
			}

			echo $quizObj->editForm_toString();
		}
	}

	printf( '</ol>' );

	// Do any of the questions have residual errors? Tell the user.
	if ( $errorCount > 0 ) {
		$page->showMessage( sprintf( __( '%d of the questions below have errors. Please make corrections and then save the changes.', 'wp-courseware' ), $errorCount ), true );
	}

	$page->showPageMiddle( '35%' );

	// Show the menu for saving and adding new items.
	WPCW_showPage_ModifyQuiz_FloatMenu( $page );

	// Flag to indicate that questions have been updated.
	printf( '<input type="hidden" name="survey_updated" value="survey_updated" />' );

	printf( '<a name="new_question"></a>' );

	// The empty forms for adding a new question
	$quizItemDummy                              = new stdClass();
	$quizItemDummy->question_question           = '';
	$quizItemDummy->question_correct_answer     = false;
	$quizItemDummy->question_order              = 0;
	$quizItemDummy->question_answer_type        = false;
	$quizItemDummy->question_answer_hint        = false;
	$quizItemDummy->question_answer_explanation = false;
	$quizItemDummy->question_answer_file_types  = 'doc, pdf, jpg, png, jpeg, gif';
	$quizItemDummy->question_image              = false;
	$quizItemDummy->question_usage_count        = 0;
	$quizItemDummy->question_author             = get_current_user_id();

	$quizItemDummy->question_multi_random_enable = 0;
	$quizItemDummy->question_multi_random_count  = 5;

	// Create some dummy answers.
	$quizItemDummy->question_data_answers = serialize( array(
		1 => array( 'answer' => '' ),
		2 => array( 'answer' => '' ),
		3 => array( 'answer' => '' ),
	) );

	$quizFormsToCreate = array(
		'new_multi'            => 'WPCW_quiz_MultipleChoice',
		'new_tf'               => 'WPCW_quiz_TrueFalse',
		'new_open'             => 'WPCW_quiz_OpenEntry',
		'new_upload'           => 'WPCW_quiz_FileUpload',
		'new_random_selection' => 'WPCW_quiz_RandomSelection',
	);

	// Create the dummy quiz objects
	foreach ( $quizFormsToCreate as $dummyid => $objClass ) {
		// Set placeholder class
		$quizItemDummy->question_id = $dummyid;

		// Create new object and set it up with defaults
		$quizObj = new $objClass( $quizItemDummy );

		$quizObj->cssClasses                   .= ' wpcw_question_template';
		$quizObj->showErrors                   = false;
		$quizObj->needCorrectAnswers           = $needCorrectAnswers;
		$quizObj->editForm_questionNotSavedYet = true;

		echo $quizObj->editForm_toString();
	}
}

/**
 * Creates the floating menu for adding quiz items.
 *
 * @since 1.0.0
 *
 * @param string $page The admin page to modify the quiz.
 */
function WPCW_showPage_ModifyQuiz_FloatMenu( $page ) {
	?>
    <div class="wpcw_floating_menu" id="wpcw_add_quiz_menu">
        <div class="wpcw_add_quiz_block">
            <div class="wpcw_add_quiz_title"><?php _e( 'Question Tools', 'wp-courseware' ); ?></div>
            <div class="wpcw_add_quiz_options">
                <ul>
                    <li><a href="#new_question" class="button-secondary" id="wpcw_add_question_multi"><?php _e( 'Add Multiple Choice', 'wp-courseware' ); ?></a></li>
                    <li><a href="#new_question" class="button-secondary" id="wpcw_add_question_truefalse"><?php _e( 'Add True/False', 'wp-courseware' ); ?></a></li>
                    <li><a href="#new_question" class="button-secondary" id="wpcw_add_question_open"><?php _e( 'Add Open Ended Question', 'wp-courseware' ); ?></a></li>
                    <li><a href="#new_question" class="button-secondary" id="wpcw_add_question_upload"><?php _e( 'Add File Upload Question', 'wp-courseware' ); ?></a></li>

                    <li class="wpcw_add_quiz_spacer">
                        <hr/>
                    </li>

                    <li><a href="#TB_inline?width=1200&height=800&inlineId=wpcw_tb_question_pool" title="<?php _e( 'Insert question from Question Pool', 'wp-courseware' ); ?>"
                           class="button-secondary thickbox" id="wpcw_add_question_from_pool"><?php _e( 'Add Questions from Pool', 'wp-courseware' ); ?></a></li>
                    <li><a href="#TB_inline?width=1200&height=800&inlineId=wpcw_tb_random_question" title="<?php _e( 'Insert Random Questions', 'wp-courseware' ); ?>" class="button-secondary thickbox"
                           id="wpcw_add_question_random"><?php _e( 'Add Random Questions', 'wp-courseware' ); ?></a></li>

                    <li class="wpcw_add_quiz_spacer">
                        <hr/>
                    </li>

                    <li class="wpcw_quiz_tool_compact">
                        <a href="#top" class="wpcw_quiz_tool_compact_compact"
                           title="<?php _e( 'Use this to compact the questions so that they are easier to organise.', 'wp-courseware' ); ?>">[-] <?php _e( 'Compact Questions', 'wp-courseware' ); ?></a>
                        <a href="#top" class="wpcw_quiz_tool_compact_expand"
                           title="<?php _e( 'Use this to expand the questions for editing.', 'wp-courseware' ); ?>">[+] <?php _e( 'Expand Questions', 'wp-courseware' ); ?></a>
                    </li>
                </ul>
            </div>
        </div>

		<?php // Keep track of new questions so that they all get a new ID. ?>
		<?php printf( '<div id="wpcw_question_template_count" class="wpcw_question_template">0</div>' ); ?>

        <div class="wpcw_add_quiz_save">
            <input type="submit" class="button-primary" value="<?php _e( 'Save Quiz &amp; Questions', 'wp-courseware' ); ?>"/>
        </div>

        <div class="wpcw_quiz_tool_compact"></div>
    </div>
	<?php
}

/**
 * Show the forms where the quiz custom feedback can be edited.
 *
 * @since 1.0.0
 *
 * @param integer $quizID the ID of the quiz to be edited.
 * @param object $page The associated page object for showing messages.
 */
function WPCW_showPage_customFeedback_showEditForms( $quizID, $page ) {
	// 1 - Heading Messages
	printf( '<h3>%s</h3>', __( 'Custom Feedback Messages', 'wp-courseware' ) );
	printf( '<p>(%s) %s</p>',
		__( 'Optional', 'wp-courseware' ),
		__( 'If you are using question tags within your quiz, you can create custom messages to display upon submission of the quiz based on a student\'s results for a particular tag.', 'wp-courseware' )
	);

	printf( '<p><em>%s</em></p>',
		__( 'Please note: The custom feedback messages do not display any grade information. Use the settings under <b>Result Settings</b> tab, then <b>Show Answers</b> - to customise the display of results.', 'wp-courseware' )
	);

	// 2 - Button to add a new message
	printf( '<div class="wpcw_button_group"><a href="#" class="button-secondary" id="wpcw_quiz_custom_feedback_add_new">%s</a></div><br/>', __( 'Add New Feedback Message', 'wp-courseware' ) );

	// 3 - Keep track of new messages and deletions
	printf( '<div id="wpcw_quiz_custom_feedback_add_new_count">0</div>' );
	printf( '<div id="wpcw_quiz_custom_feedback_deletion_holder"></div>' );

	// 4 - Holder for new messages
	printf( '<div id="wpcw_quiz_custom_feedback_holder">' );

	// 5 - Render the existing forms to modify the custom messages.
	$feedbackList = WPCW_quizzes_feedback_getFeedbackMessagesForQuiz( $quizID );
	if ( ! empty( $feedbackList ) ) {
		// Show an edit form for each custom feedback item.
		foreach ( $feedbackList as $feedbackItem ) {
			$fb = new WPCW_quiz_CustomFeedback( $quizID, $feedbackItem );
			echo $fb->generate_editForm();
		}
	}

	// 6 - Close holder
	printf( '</div>' );

	// 7 - Render a hidden form that's used for a placeholder.
	$fb = new WPCW_quiz_CustomFeedback( $quizID, false );
	echo $fb->generate_editForm();
}

/**
 * Handle saving a feedback message to the database.
 *
 * @since 1.0.0
 *
 * @param integer $quizID The quiz for which the questions apply to.
 */
function WPCW_showPage_customFeedback_processSave( $quizID ) {
	global $wpdb, $wpcwdb;
	$wpdb->show_errors();

	$msgToSave     = array();
	$msgToSave_New = array();

	// Check $_POST data for the
	foreach ( $_POST as $key => $value ) {

		// 1) - Check if we're deleting a custom feedback message
		if ( preg_match( '/^delete_wpcw_qcfm_sgl_wrapper_([0-9]+)$/', $key, $matches ) ) {
			// Delete the message from the message table
			$SQL = $wpdb->prepare( "
				DELETE FROM $wpcwdb->quiz_feedback
				WHERE qfeedback_id = %d
			", $matches[1] );

			$wpdb->query( $SQL );
		}

		// 2 - See if we have a custom feedback message to add or update
		// Checking for wpcw_qcfm_sgl_wrapper_1 or wpcw_qcfm_sgl_wrapper_new_message_1
		if ( preg_match( '/^wpcw_qcfm_sgl_summary(_new_message)?_([0-9]+)$/', $key, $matches ) ) {
			// Got the ID of the message we're updating or adding.
			$messageID = $matches[2];

			// Store the extra string if we're adding a new message.
			$newMessagePrefix = $matches[1];

			$fieldSuffix = $newMessagePrefix . '_' . $messageID;

			// Fetch each field we need that will be saved
			$messageFields = array(
				// Already have this, so not fetching from POST
				'qfeedback_quiz_id'     => $quizID,

				// Risk of slashes, hence removing them
				'qfeedback_summary'     => stripslashes( WPCW_arrays_getValue( $_POST, 'wpcw_qcfm_sgl_' . 'summary' . $fieldSuffix ) ),
				'qfeedback_message'     => stripslashes( WPCW_arrays_getValue( $_POST, 'wpcw_qcfm_sgl_' . 'message' . $fieldSuffix ) ),

				// Numbers
				'qfeedback_tag_id'      => intval( WPCW_arrays_getValue( $_POST, 'wpcw_qcfm_sgl_' . 'tag' . $fieldSuffix ) ),
				'qfeedback_score_grade' => intval( WPCW_arrays_getValue( $_POST, 'wpcw_qcfm_sgl_' . 'score_grade' . $fieldSuffix ) ),

				// Fixed-width strings
				'qfeedback_score_type'  => WPCW_arrays_getValue( $_POST, 'wpcw_qcfm_sgl_' . 'score_type' . $fieldSuffix ),
			);

			// Check we have a valid score type.
			if ( 'below' != $messageFields['qfeedback_score_type'] && 'above' != $messageFields['qfeedback_score_type'] ) {
				$messageFields['qfeedback_score_type'] = 'below';
			}

			// 3) - Not a new message - so add to list of new messages to add.
			if ( $newMessagePrefix ) {
				$msgToSave_New[] = $messageFields;
			} else { // Existing message - so keep the message ID we have so we can update it.
				$messageFields['qfeedback_id'] = $messageID;
				$msgToSave[]                   = $messageFields;
			}
		}
	}

	// 4) Add new messages
	if ( ! empty( $msgToSave_New ) ) {
		foreach ( $msgToSave_New as $messageDetails ) {
			$wpdb->query( arrayToSQLInsert( $wpcwdb->quiz_feedback, $messageDetails ) );
		}
	}

	// 5) Update existing messages
	if ( ! empty( $msgToSave ) ) {
		foreach ( $msgToSave as $messageDetails ) {
			$wpdb->query( arrayToSQLUpdate( $wpcwdb->quiz_feedback, $messageDetails, 'qfeedback_id' ) );
		}
	}
}

/**
 * Function called before a quiz is being saved.
 *
 * @since 1.0.0
 *
 * @param array $originalFormValues The raw form values.
 * @param RecordsForm $thisObject The reference to the form object doing the saving.
 */
function WPCW_actions_quizzes_beforeQuizSaved( $originalFormValues, $thisObject ) {
	// Ensure if survey is selected, that no answers are set up.
	if ( 'survey' == $originalFormValues['quiz_type'] ) {
		$originalFormValues['quiz_show_answers'] = 'no_answers';
	}

	// Ensure that if we're not in quiz mode, that we can't use the timer.
	if ( 'survey' == $originalFormValues['quiz_type'] ) {
		$originalFormValues['quiz_timer_mode'] = 'no_timer';
	}

	// Ensure if no timer is selected, that the time is set to a useful time other than 0.
	if ( 'no_timer' == $originalFormValues['quiz_timer_mode'] && intval( $originalFormValues['quiz_timer_mode_limit'] ) <= 0 ) {
		$originalFormValues['quiz_timer_mode_limit'] = '15';
	}

	return $originalFormValues;
}

/**
 * Function called after a quiz is being saved.
 *
 * @since 1.0.0
 *
 * @param array $formValues The form values after filtering that were used to save.
 * @param array $originalFormValues The raw form values that were unfiltered.
 * @param RecordsForm $thisObject The reference to the form object doing the saving.
 */
function WPCW_actions_quizzes_afterQuizSaved( $formValues, $originalFormValues, $thisObject ) {
	// Handle the saving of quiz questions
	WPCW_handler_questions_processSave( $thisObject->primaryKeyValue );

	// Handle the saving of custom feedback
	WPCW_showPage_customFeedback_processSave( $thisObject->primaryKeyValue );
}

/**
 * Fetch a list of the attempts that a user is allowed for completing a quiz.
 *
 * @since 1.0.0
 *
 * @return array The list of attempts as Counts => Names.
 *
 * @return array $attemptList The attempt list array.
 */
function WPCW_quizzes_getAttemptList() {
	$attemptList = array(
		'-1' => __( 'Unlimited Attempts', 'wp-courseware' ),
		'1'  => __( '1 Attempt', 'wp-courseware' ),
	);

	for ( $i = 2; $i <= 30; $i ++ ) {
		$attemptList[ $i ] = sprintf( __( '%d Attempts', 'wp-courseware' ), $i );
	}

	return $attemptList;
}