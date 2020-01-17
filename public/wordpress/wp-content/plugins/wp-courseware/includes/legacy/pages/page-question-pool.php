<?php
/**
 * WP Courseware Page Question Pool.
 *
 * Functions relating to showing the question pool page.
 *
 * @package WPCW
 * @since 1.0.0
 */

/**
 * Shows the main Question Pool table.
 *
 * @since 1.0.0
 */
function WPCW_showPage_QuestionPool_load() {
	global $wpcwdb, $wpdb;
	$wpdb->show_errors();

	// Get the requested page number
	$paging_pageWanted = absint( WPCW_arrays_getValue( $_GET, 'pagenum' ) );
	if ( $paging_pageWanted == 0 ) {
		$paging_pageWanted = 1;
	}

	// Title for page with page number
	$titlePage = false;
	if ( $paging_pageWanted > 1 ) {
		$titlePage = sprintf( ' - %s %s', __( 'Page', 'wp-courseware' ), $paging_pageWanted );
	}

	$page = new PageBuilder( false );
	$page->showPageHeader( __( 'Question Pool', 'wp-courseware' ) . $titlePage, '75%', WPCW_icon_getPageIconURL() );


	// Handle the question deletion before showing remaining questions
	WPCW_quizzes_handleQuestionDeletion( $page );

	// Show the main pool table
	echo WPCW_questionPool_showPoolTable( 50, $_GET, 'std', $page );

	$page->showPageFooter();
}

/**
 * Handle the question deletion from the question pool page.
 *
 * @since 1.0.0
 *
 * @param PageBuilder $page The page rendering object.
 */
function WPCW_quizzes_handleQuestionDeletion( $page ) {
	global $wpcwdb, $wpdb;
	$wpdb->show_errors();

	// Vars
	$canDeleteQuestion = false;
	$current_user      = wp_get_current_user();

	// Check that the question exists and deletion has been requested
	if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['question_id'] ) ) {
		$questionID      = $_GET['question_id'];
		$questionDetails = WPCW_questions_getQuestionDetails( $questionID );

		// Only do deletion if question details are valid.
		if ( $questionDetails ) {
			// Check permissions, this condition allows admins to view all modules even if they are not the author.
			if ( user_can( $current_user->ID, 'manage_wpcw_settings' ) ) {
				$canDeleteQuestion = true;
			}

			// Check Author
			if ( $questionDetails->question_author == $current_user->ID ) {
				$canDeleteQuestion = true;
			}

			// Back compat filter
			$canDeleteQuestion = apply_filters( 'wpcw_back_permissions_user_can_delete_question', $canDeleteQuestion, $current_user->ID, $questionDetails );

			// Add filters to override
			$canDeleteQuestion   = apply_filters( 'wpcw_permissions_user_can_edit_question', $canDeleteQuestion, $current_user, $questionDetails );
			$cantEditQuestionMsg = apply_filters( 'wpcw_permissions_user_can_edit_question_msg', esc_attr__( 'You are not permitted to delete this question.', 'wp-courseware' ), $current_user, $questionDetails );

			// Display message if no access.
			if ( ! $canDeleteQuestion ) {
				$page->showMessage( $cantEditQuestionMsg, true );

				return;
			}

			// Get tags for questions
			$question_tags = WPCW_questions_getQuestionDetails( $questionID, $getTagsToo = true );

			// Remove tags for each question
			foreach ( $question_tags->tags as $question_tag ) {
				WPCW_questions_tags_removeTag( $questionID, $question_tag->question_tag_id );
			}

			// Delete question from question map
			$wpdb->query( $wpdb->prepare( "
				DELETE FROM $wpcwdb->quiz_qs_mapping
				WHERE question_id = %d
			", $questionDetails->question_id ) );


			// Finally delete question itself
			$wpdb->query( $wpdb->prepare( "
				DELETE FROM $wpcwdb->quiz_qs
				WHERE question_id = %d
			", $questionDetails->question_id ) );


			$page->showMessage( sprintf( __( 'The question \'%s\' was successfully deleted.', 'wp-courseware' ), $questionDetails->question_question ) );

		} // end of if $questionDetails

	} // end of check for deletion action
}

/**
 * Fetch the form for showing actions at the bottom of the page for the QuestionPool.
 *
 * @since 1.0.0
 */
function WPCW_showPage_QuestionPool_actionForm( $class = 'default' ) {
	// Start wrapper for bulk actions
	$formWrapper_end = sprintf( '<div id="wpcw_tbl_question_pool_bulk_actions" class="%s">', $class );

	// Error messages - if no questions or tags have been selected.
	$formWrapper_end .= sprintf( '<div id="wpcw_bulk_action_message_no_questions" class="wpcw_msg_error">%s</div>', __( 'Please select <b>at least 1 question</b> before continuing...', 'wp-courseware' ) );
	$formWrapper_end .= sprintf( '<div id="wpcw_bulk_action_message_no_tag_first" class="wpcw_msg_error">%s</div>', __( 'Please select the <b>first tag</b> before continuing...', 'wp-courseware' ) );
	$formWrapper_end .= sprintf( '<div id="wpcw_bulk_action_message_no_tag_second" class="wpcw_msg_error">%s</div>', __( 'Please select the <b>second tag</b> before continuing...', 'wp-courseware' ) );

	// Label - saying these are actions
	$formWrapper_end .= sprintf( '<label>%s</label>', __( 'Tag actions for selected questions?', 'wp-courseware' ) );

	// Dropdown of actions
	$formWrapper_end .= sprintf( WPCW_forms_createDropdown( 'wpcw_bulk_action_actions', array(
		''            => __( '--- Select a tag action ---', 'wp-courseware' ),
		'add_tag'     => __( 'Add tag to selected questions', 'wp-courseware' ),
		'remove_tag'  => __( 'Remove tag from selected questions', 'wp-courseware' ),
		'replace_tag' => __( 'Replace all instances of tag', 'wp-courseware' ),
	), false, 'wpcw_tbl_question_pool_bulk_actions_chooser', false ) );

	// #### The starting labels for all 3 actions.
	$formWrapper_end .= sprintf( '<label class="wpcw_bulk_action_label wpcw_bulk_action_add_tag">%s:</label>', __( 'Add Tag', 'wp-courseware' ) );
	$formWrapper_end .= sprintf( '<label class="wpcw_bulk_action_label wpcw_bulk_action_remove_tag">%s:</label>', __( 'Remove Tag', 'wp-courseware' ) );
	$formWrapper_end .= sprintf( '<label class="wpcw_bulk_action_label wpcw_bulk_action_replace_tag">%s:</label>', __( 'Replace Tag', 'wp-courseware' ) );

	// #### All 3 - Selector for Add/Remove/Replace tag - first box
	$formWrapper_end .= WPCW_questions_tags_getTagDropdown( __( 'Select a tag', 'wp-courseware' ),
		'wpcw_bulk_action_select_tag_a',    // Name
		WPCW_arrays_getValue( $_POST, 'wpcw_bulk_action_select_tag_a' ),
		'wpcw_bulk_action_select_tag_a wpcw_bulk_action_select_tag wpcw_bulk_action_add_tag wpcw_bulk_action_remove_tag wpcw_bulk_action_replace_tag' // CSS Classes
	);

	// ### Just 'Replace Tag' - the second label
	$formWrapper_end .= sprintf( '<label class="wpcw_bulk_action_label wpcw_bulk_action_replace_tag">%s:</label>', __( 'With', 'wp-courseware' ) );

	// Just 'Replace Tag' - the second dropdown
	$formWrapper_end .= WPCW_questions_tags_getTagDropdown( __( 'Select a tag', 'wp-courseware' ),
		'wpcw_bulk_action_select_tag_b',    // Name
		WPCW_arrays_getValue( $_POST, 'wpcw_bulk_action_select_tag_b' ),
		'wpcw_bulk_action_select_tag_b wpcw_bulk_action_select_tag wpcw_bulk_action_replace_tag'
	);

	// Button - submit
	$formWrapper_end .= sprintf( '<input type="submit" class="button-primary" value="%s">', __( 'Update Questions', 'wp-courseware' ) );

	// End wrapper for bulk actions
	$formWrapper_end .= '</div>';

	return $formWrapper_end;
}

/**
 * Process the action form to change tags for the selected questions.
 *
 * @since 1.0.0
 */
function WPCW_showPage_QuestionPool_processActionForm( $page ) {
	global $wpdb, $wpcwdb;
	$wpdb->show_errors();

	if ( ! isset( $_POST['wpcw_bulk_action_actions'] ) ) {
		return;
	}

	// #### #1 - Get a list of the questions to update
	$questionListToUpdate = array();
	foreach ( $_POST as $key => $value ) {
		// We're looking for these to get the question ID
		// 		[question_162] => on
		//		[question_149] => on
		if ( preg_match( '/^question_([0-9]+)$/', $key, $matches ) ) {
			$questionListToUpdate[] = $matches[1];
		}
	}

	// Appears there's nothing to do.
	if ( empty( $questionListToUpdate ) ) {
		$page->showMessage( __( 'Error. Please select some questions to update.', 'wp-courseware' ), true );

		return;
	}


	// #### #2 - Validate that the questions do indeed exist
	// Direct SQL is ok here, as IDs have been validated with the regex previously.
	$questionListStr    = implode( ',', $questionListToUpdate );
	$validatedQuestions = $wpdb->get_col( "
		SELECT *
		FROM $wpcwdb->quiz_qs
		WHERE question_id IN ($questionListStr)
	" );

	// Appears there's nothing to do, as questions do not validate.
	if ( empty( $questionListToUpdate ) ) {
		$page->showMessage( __( 'Error. Those questions no longer exist. Please select some more questions to update.', 'wp-courseware' ), true );

		return;
	}

	// #### #3 - Check that the action is what we're expecting.
	$actionToProcess = WPCW_arrays_getValue( $_POST, 'wpcw_bulk_action_actions' );
	switch ( $actionToProcess ) {
		case 'add_tag':
		case 'remove_tag':
		case 'replace_tag':
			break;

		default:
			$page->showMessage( __( 'Error. Did not recognise action to apply to selected questions.', 'wp-courseware' ), true );

			return;
			break;
	}

	// #### #4 - check permissions and unset what questions should be updated.
	$bulk_question_counter = 0;
	foreach ( $validatedQuestions as $questionId ) {

		// Vars
		$canBulkEditQuestion = false;
		$current_user        = wp_get_current_user();

		// Check permissions, this condition allows admins to view all modules even if they are not the author.
		if ( user_can( $current_user->ID, 'manage_wpcw_settings' ) ) {
			$canBulkEditQuestion = true;
		}

		// Question author
		$question_author_id = $wpdb->get_var( $wpdb->prepare( "SELECT question_author FROM $wpcwdb->quiz_qs WHERE question_id = %d", $questionId ) );

		// Check Author
		if ( $question_author_id == $current_user->ID ) {
			$canBulkEditQuestion = true;
		}

		// Back compat filter
		$canBulkEditQuestion = apply_filters( 'wpcw_back_permissions_user_can_bulkedit_question', $canBulkEditQuestion, $current_user->ID, $questionId );

		// Add filters to override
		$canBulkEditQuestion     = apply_filters( 'wpcw_permissions_user_can_bulkedit_question', $canBulkEditQuestion, $current_user, $questionId );
		$cantBulkEditQuestionMsg = apply_filters( 'wpcw_permissions_user_can_bulkedit_question_msg',
			sprintf( esc_attr__( 'You are not permitted to bulk edit Question ID: %s.', 'wp-courseware' ), $questionId ),
			$current_user,
			$questionId
		);

		// Display message if no access.
		if ( ! $canBulkEditQuestion ) {
			$page->showMessage( $cantBulkEditQuestionMsg, true );
			unset( $validatedQuestions[ $bulk_question_counter ] );
		}

		// Increment Counter
		$bulk_question_counter ++;
	}

	// #### #4 - Check that we have the tags that we're expecting.
	$tagID_first  = WPCW_arrays_getValue( $_POST, 'wpcw_bulk_action_select_tag_a', 0 );
	$tagID_second = WPCW_arrays_getValue( $_POST, 'wpcw_bulk_action_select_tag_b', 0 );

	$tagDetails_first  = false;
	$tagDetails_second = false;

	if ( ! $tagDetails_first = WPCW_questions_tags_getTagDetails( $tagID_first ) ) {
		$page->showMessage( __( 'Error. The first tag does not exist. Please select another tag.', 'wp-courseware' ), true );

		return;
	}

	// Check replace tag requirements
	if ( 'replace_tag' == $actionToProcess ) {
		// No 2nd tag
		if ( ! $tagDetails_second = WPCW_questions_tags_getTagDetails( $tagID_second ) ) {
			$page->showMessage( __( 'Error. The second tag does not exist. Please select another tag.', 'wp-courseware' ), true );

			return;
		}

		// 1st and 2nd tags match
		if ( $tagDetails_first->question_tag_id == $tagDetails_second->question_tag_id ) {
			$page->showMessage( __( 'Error. The first and second tag should be different.', 'wp-courseware' ), true );

			return;
		}
	}

	// #### #5 - By this point, everything is validated, so just execute the SQL.
	foreach ( $validatedQuestions as $questionID ) {
		switch ( $actionToProcess ) {
			case 'add_tag':
				$wpdb->query( $wpdb->prepare( "
					INSERT IGNORE $wpcwdb->question_tag_mapping
					(question_id, tag_id)
					VALUES (%d, %d)
				", $questionID, $tagDetails_first->question_tag_id ) );
				break;

			case 'remove_tag':
				$wpdb->query( $wpdb->prepare( "
					DELETE FROM $wpcwdb->question_tag_mapping
					WHERE question_id = %d
					  AND tag_id = %d
				", $questionID, $tagDetails_first->question_tag_id ) );
				break;

			case 'replace_tag':
				$wpdb->query( $wpdb->prepare( "
					UPDATE $wpcwdb->question_tag_mapping
					  SET tag_id = %d
					WHERE question_id = %d
					  AND tag_id = %d
				",
					$tagDetails_second->question_tag_id,
					$questionID, $tagDetails_first->question_tag_id
				) );
				break;
		}
	}

	// Need to update tag counts
	WPCW_questions_tags_updatePopularity( $tagDetails_first->question_tag_id );

	// 2nd is optional, so just need to check it exists first before trying update to prevent
	// an error message.
	if ( $tagDetails_second ) {
		WPCW_questions_tags_updatePopularity( $tagDetails_second->question_tag_id );
	}

	// #### #6 Finally show message
	$page->showMessage( __( 'Questions successfully updated.', 'wp-courseware' ) );
}