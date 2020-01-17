<?php
/**
 * WP Courseware Admin Ajax Functions.
 *
 * @package WPCW
 * @since 1.0.0
 */

/**
 * Function called when adding a question to a quiz from the thickbox.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_handleThickboxAction_QuestionPool_addQuestion() {
	$questionID      = WPCW_arrays_getValue( $_POST, 'questionnum' );
	$questionDetails = WPCW_questions_getQuestionDetails( $questionID );

	if ( $questionDetails ) {
		switch ( $questionDetails->question_type ) {
			case 'multi':
				$quizObj = new WPCW_quiz_MultipleChoice( $questionDetails );
				break;
			case 'truefalse':
				$quizObj = new WPCW_quiz_TrueFalse( $questionDetails );
				break;
			case 'open':
				$quizObj = new WPCW_quiz_OpenEntry( $questionDetails );
				break;
			case 'upload':
				$quizObj = new WPCW_quiz_FileUpload( $questionDetails );
				break;
			case 'random_selection':
				$quizObj = new WPCW_quiz_RandomSelection( $questionDetails );
				break;
			default:
				die( __( 'Unknown quiz type: ', 'wp-courseware' ) . $questionDetails->question_type );
				break;
		}

		$quizObj->showErrors         = true;
		$quizObj->needCorrectAnswers = true;

		echo $quizObj->editForm_toString();
	}

	die();
}

/**
 * Function called when any filtering occurs
 * within the thickbox window for the Question Pool.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_handleThickboxAction_QuestionPool() {
	$args = wp_parse_args( $_POST, array(
		'pagenum' => 1,
	) );

	// Create URL from parameters to use for building the question pool table
	echo WPCW_questionPool_showPoolTable( 20, $args, 'ajax' );

	die();
}

/**
 * Handle Question Remove Tag.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_handleQuestionRemoveTag() {
	$ajaxResults = array(
		'success' => true,
	);

	$tagID      = intval( WPCW_arrays_getValue( $_POST, 'tagid' ) );
	$questionID = intval( WPCW_arrays_getValue( $_POST, 'questionid' ) );

	WPCW_questions_tags_removeTag( $questionID, $tagID );

	header( 'Content-Type: application/json' );
	echo json_encode( $ajaxResults );

	die();
}

/**
 * Handle Question New Tag.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_handleQuestionNewTag() {
	$ajaxResults = array(
		'success'  => true,
		'errormsg' => __( 'Unfortunately there was a problem adding the tag.', 'wp-courseware' ),
		'html'     => false,
	);

	// Assume that we may have multiple tags, separated by commas.
	$potentialTagList = explode( ',', WPCW_arrays_getValue( $_POST, 'tagtext' ) );
	$cleanTagList     = array();

	// Check if question is expected to have been saved.
	$hasQuestionBeenSaved = 'yes' == WPCW_arrays_getValue( $_POST, 'isquestionsaved' );

	// Got potential tags
	if ( ! empty( $potentialTagList ) ) {
		// Clean up each tag, and add to a list.
		foreach ( $potentialTagList as $potentialTag ) {
			$cleanTagList[] = sanitize_text_field( stripslashes( $potentialTag ) );
		}

		// Check that cleaned tags are ok too
		if ( ! empty( $cleanTagList ) ) {
			// Do this if the question exists and we're adding tags.
			if ( $hasQuestionBeenSaved ) {
				// Get the ID of the question we're adding this tag to.
				$questionID = intval( WPCW_arrays_getValue( $_POST, 'questionid' ) );

				// Validate that the question exists before we tag it.
				$questionDetails = WPCW_questions_getQuestionDetails( $questionID );
				if ( ! $questionDetails ) {
					$ajaxResults['errormsg'] = __( 'Unfortunately that question could not be found, so the tag could not be added.', 'wp-courseware' );
					$ajaxResults['success']  = false;
				} // Question Found - carry on
				else {
					// Add the tag to the database, get a list of the tag details now that they have been added.
					$tagDetailList = WPCW_questions_tags_addTags( $questionID, $cleanTagList );
					foreach ( $tagDetailList as $tagAddedID => $tagAddedText ) {
						// Create the HTML to show the new tag.
						$ajaxResults['html'] .= sprintf( '<span><a data-questionid="%d" data-tagid="%d" class="ntdelbutton">X</a>&nbsp;%s</span>',
							$questionID, $tagAddedID, $tagAddedText
						);
					}
				}
			} else { // We expect the question not to exist, hence we don't try to add to a question.
				$tagDetailList = WPCW_questions_tags_addTags_withoutQuestion( $cleanTagList );

				// For a new question, the ID is a string, not a value.
				$questionIDStr = WPCW_arrays_getValue( $_POST, 'questionid' );

				// Create a hidden form entry plus the little tag, so that we can add the tag to the question when we save.
				foreach ( $tagDetailList as $tagAddedID => $tagAddedText ) {
					// Create the HTML to show the new tag. We'll add the full string to the hidden field so that we can
					// add the tags later.
					$ajaxResults['html'] .= sprintf(
						'<span>
							<a data-questionid="%d" data-tagid="%d" class="ntdelbutton">X</a>&nbsp;%s
							<input type="hidden" name="tags_to_add%s[]" value="%s" />
						</span>',
						0,
						$tagAddedID,
						$tagAddedText,
						$questionIDStr,
						addslashes( $tagAddedText )
					);
				}
			}
		}
	}

	header( 'Content-Type: application/json' );
	echo json_encode( $ajaxResults );

	die();
}

/**
 * Handle Uniit Duplication.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_handleUnitDuplication() {
	if ( ! wp_verify_nonce( WPCW_arrays_getValue( $_POST, 'security_id' ), 'wpcw_ajax_unit_change' ) ) {
		die ( __( 'Security check failed!', 'wp-courseware' ) );
	}

	global $wpdb, $wpcwdb;

	// See if we can get the post that we've asked to duplicate
	$sourcePostID = WPCW_arrays_getValue( $_POST, 'source_id', false );
	$newUnit      = get_post( $sourcePostID, 'ARRAY_A' );

	$ajaxResults = array(
		'success'  => true,
		'errormsg' => false,
	);

	// Got the new unit
	if ( $newUnit ) {
		// Modify the post title to add ' Copy' to the end.
		$newUnit['post_title'] .= ' ' . __( 'Copy', 'wp-courseware' );

		// Adjust date to today
		$newUnit['post_date'] = current_time( 'mysql' );

		// Remove some of the keys relevant to the other post so that they are generated
		// automatically.
		unset( $newUnit['ID'] );
		unset( $newUnit['guid'] );
		unset( $newUnit['comment_count'] );
		unset( $newUnit['post_name'] );
		unset( $newUnit['post_date_gmt'] );

		// Insert the post into the database
		$newUnitID = wp_insert_post( $newUnit );

		// Duplicate all the taxonomies/terms
		$taxonomies = get_object_taxonomies( $newUnit['post_type'] );
		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms = wp_get_post_terms( $sourcePostID, $taxonomy, array( 'fields' => 'names' ) );
				wp_set_object_terms( $newUnitID, $terms, $taxonomy );
			}
		}

		// Duplicate all the custom fields
		$custom_fields = get_post_custom( $sourcePostID );
		if ( ! empty( $custom_fields ) ) {
			foreach ( $custom_fields as $key => $value ) {
				add_post_meta( $newUnitID, $key, maybe_unserialize( $value[0] ) );
			}
		}

		// See if there's an entry in the courseware table
		$SQL = $wpdb->prepare( "
			SELECT *
			FROM $wpcwdb->units_meta
			WHERE unit_id = %d
		", $newUnitID );

		// Ensure there's a blank entry in the database for this post.
		if ( ! $wpdb->get_row( $SQL ) ) {
			$SQL = $wpdb->prepare( "
				INSERT INTO $wpcwdb->units_meta (unit_id, parent_module_id, unit_author)
				VALUES (%d, 0, %d)
			", $newUnitID, get_current_user_id() );

			$wpdb->query( $SQL );
		}
	} else { // Post not found, show relevant error
		$ajaxResults['success']  = false;
		$ajaxResults['errormsg'] = __( 'Post could not be found.', 'wp-courseware' );
	}

	header( 'Content-Type: application/json' );
	echo json_encode( $ajaxResults );

	die();
}

/**
 * Handle Unit Ordering Saving.
 *
 * This function will save the order of the modules, units and any unassigned units.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_handleUnitOrderingSaving() {
	if ( ! wp_verify_nonce( WPCW_arrays_getValue( $_POST, 'order_nonce' ), 'wpcw-order-nonce' ) ) {
		die ( __( 'Security check failed!', 'wp-courseware' ) );
	}

	// Get list of modules to save, check IDs are what we expect, and abort if nothing to do.
	$moduleList = WPCW_arrays_getValue( $_POST, 'moduleList' );
	if ( ! $moduleList || count( $moduleList ) < 1 ) {
		die();
	}

	global $wpdb, $wpcwdb;
	$wpdb->show_errors();

	$parentCourseID = 0;

	// Save new module ordering to database
	$moduleOrderCount = 0;

	// Ordering of units is absolute to the whole course
	$unitOrderCount = 0;

	// Need a course ID for resetting the ordering.
	foreach ( $moduleList as $moduleID ) {
		// Validate we have an actual module
		if ( preg_match( '/^wpcw_mod_(\d+)$/', $moduleID, $matches ) ) {
			// Get course ID from module
			$moduleDetails = WPCW_modules_getModuleDetails( $matches[1] );
			if ( $moduleDetails ) {
				$parentCourseID = $moduleDetails->parent_course_id;
				break;
			}
		}
	}

	// If there's no associated parent course, there's an issue.
	if ( ! $parentCourseID ) {
		error_log( 'WPCW_AJAX_handleUnitOrderingSaving(). No associated parent course ID, so aborting.' );
		die();
	}

	// 2013-05-01 - Bug with orphan modules being left in the units_meta
	// Fix - Clean out existing units in this course, resetting them.
	// Then update the ordering using the loops below.
	$SQL = $wpdb->prepare( "
		UPDATE $wpcwdb->units_meta
		   SET unit_order = 0, parent_module_id = 0,
		   	   parent_course_id = 0, unit_number = 0
		WHERE parent_course_id = %d
	", $parentCourseID );

	$wpdb->query( $SQL );

	foreach ( $moduleList as $moduleID ) {
		// Check module name matches expected format.
		if ( preg_match( '/^wpcw_mod_(\d+)$/', $moduleID, $matches ) ) {
			$moduleOrderCount++;
			$moduleIDClean = $matches[1];

			// Update module list with new ordering
			$SQL = $wpdb->prepare( "
				UPDATE $wpcwdb->modules
				   SET module_order = %d, module_number = %d
				WHERE module_id = %d
			", $moduleOrderCount, $moduleOrderCount, $moduleIDClean );

			$wpdb->query( $SQL );

			// Check units associated with this module
			$unitList = WPCW_arrays_getValue( $_POST, $moduleID );
			if ( $unitList && count( $unitList ) > 0 ) {
				$unitNumber = 0;
				foreach ( $unitList as $unitID ) {
					$unitNumber++;

					// Check unit name matches expected format.
					if ( preg_match( '/^wpcw_unit_(\d+)$/', $unitID, $matches ) ) {
						$unitOrderCount += 10;
						$unitIDClean    = $matches[1];

						// Update database with new association and ordering.
						$SQL = $wpdb->prepare( "
							UPDATE $wpcwdb->units_meta
							   SET unit_order = %d, parent_module_id = %d,
							   	   parent_course_id = %d, unit_number = %d
							WHERE unit_id = %d
						", $unitOrderCount, $moduleIDClean,
							$parentCourseID, $unitNumber,
							$unitIDClean );

						$wpdb->query( $SQL );

						// 2013-05-01 - Updated to use the module ID, rather than the module order.
						update_post_meta( $unitIDClean, 'wpcw_associated_module', $moduleIDClean );
					}
				}// end foreach
			} // end of $unitList check
		}
	}

	// Check for any units that have associated quizzes
	foreach ( $_POST as $key => $value ) {
		// Check any post value that has a unit in it
		if ( preg_match( '/^wpcw_unit_(\d+)$/', $key, $matches ) ) {
			$unitIDClean = $matches[1];

			// Try to extract the unit ID
			// [wpcw_unit_71] => Array
			// (
			//	[0] => wpcw_quiz_2
			//)
			$quizIDRaw = false;
			if ( $value && is_array( $value ) ) {
				$quizIDRaw = $value[0];
			}

			// Got a matching quiz ID
			if ( preg_match( '/^wpcw_quiz_(\d+)$/', $quizIDRaw, $matches ) ) {
				$quizIDClean = $matches[1];

				// Grab parent course ID from unit. Can't assume all units are in same course.
				$parentData     = WPCW_units_getAssociatedParentData( $unitIDClean );
				$parentCourseID = $parentData->parent_course_id;

				// Update database with new association and ordering.
				$SQL = $wpdb->prepare( "
					UPDATE $wpcwdb->quiz
					   SET parent_unit_id = %d, parent_course_id = %d
					WHERE quiz_id = %d
				", $unitIDClean, $parentCourseID, $quizIDClean );

				$wpdb->query( $SQL );

				// Add new associated unit information to the user quiz progress,
				// keeping any existing quiz results.
				$SQL = $wpdb->prepare( "
					UPDATE $wpcwdb->user_progress_quiz
					   SET unit_id = %d
					WHERE quiz_id = %d
				", $unitIDClean, $quizIDClean );

				$wpdb->query( $SQL );
			}
		}
	}

	// Check for any unassigned units, and ensure they're de-associated from modules.
	$unitList = WPCW_arrays_getValue( $_POST, 'unassunits' );
	if ( $unitList && count( $unitList ) > 0 ) {
		foreach ( $unitList as $unitID ) {
			// Check unit name matches expected format.
			if ( preg_match( '/^wpcw_unit_(\d+)$/', $unitID, $matches ) ) {
				$unitIDClean = $matches[1];

				// Remove notifications
				WPCW_queue_dripfeed::updateQueueItems_unitRemovedFromCourse( $unitIDClean );

				// Update database with new association and ordering.
				$SQL = $wpdb->prepare( "
					UPDATE $wpcwdb->units_meta
					   SET unit_order = 0, parent_module_id = 0, parent_course_id = 0, unit_number = 0
					WHERE unit_id = %d
				", $unitIDClean );

				$wpdb->query( $SQL );

				// Update post meta to remove associated module detail
				update_post_meta( $unitIDClean, 'wpcw_associated_module', 0 );

				// Remove progress for this unit, as likely to be associated with something else.
				$SQL = $wpdb->prepare( "
					DELETE FROM $wpcwdb->user_progress
					WHERE unit_id = %d
				", $unitIDClean );

				$wpdb->query( $SQL );
			}
		}
	}

	// Check for any unassigned quizzes, and ensure they're de-associated from units.
	$quizList = WPCW_arrays_getValue( $_POST, 'unassquizzes' );
	if ( $quizList && count( $quizList ) > 0 ) {
		foreach ( $quizList as $quizID ) {
			// Check unit name matches expected format.
			if ( preg_match( '/^wpcw_quiz_(\d+)$/', $quizID, $matches ) ) {
				$quizIDClean = $matches[1];

				// Update database with new association and ordering.
				$SQL = $wpdb->prepare( "
					UPDATE $wpcwdb->quiz
					   SET parent_unit_id = 0, parent_course_id = 0
					WHERE quiz_id = %d
				", $quizIDClean );

				$wpdb->query( $SQL );

				// Remove the associated unit information from the user quiz progress.
				// But keep the quiz results for now.
				$SQL = $wpdb->prepare( "
					UPDATE $wpcwdb->user_progress_quiz
					   SET unit_id = 0
					WHERE quiz_id = %d
				", $quizIDClean );

				$wpdb->query( $SQL );
			}
		} // end foreach ($quizList as $quizID)
	}

	// Update course details
	$courseDetails = WPCW_courses_getCourseDetails( $parentCourseID );
	if ( $courseDetails ) {
		do_action( 'wpcw_course_details_updated', $courseDetails );
	}

	die();
}

/**
 * Handle Quiz Retake.
 *
 * Lots of checking needs to go on here for security reasons
 * to ensure that they don't manipulate their own progress (or somebody elses).
 *
 * @since 1.0.0
 */
function WPCW_AJAX_units_handleQuizRetakeRequest() {
	if ( ! wp_verify_nonce( WPCW_arrays_getValue( $_POST, 'progress_nonce' ), 'wpcw-progress-nonce' ) ) {
		die ( __( 'Security check failed!', 'wp-courseware' ) );
	}

	// Get unit and quiz ID
	$unitID = intval( WPCW_arrays_getValue( $_POST, 'unitid' ) );
	$quizID = intval( WPCW_arrays_getValue( $_POST, 'quizid' ) );

	// Get the post object for this quiz item.
	$post = get_post( $unitID );
	if ( ! $post ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not request a retake for the quiz.', 'wp-courseware' ) . ' ' . __( 'Could not find training unit.', 'wp-courseware' ) );
		die();
	}

	// Initalise the unit details
	$fe = new WPCW_UnitFrontend( $post );

	// Get associated data for this unit. No course/module data, then it's not a unit
	if ( ! $fe->check_unit_doesUnitHaveParentData() ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not request a retake for the quiz.', 'wp-courseware' ) . ' ' . __( 'Could not find course details for unit.', 'wp-courseware' ) );
		die();
	}

	// User not allowed access to content
	if ( ! $fe->check_user_canUserAccessCourse() ) {
		echo $fe->message_user_cannotAccessCourse();
		die();
	}

	// See if we're in a position to retake this quiz?
	if ( ! $fe->check_quizzes_canUserRequestRetake() ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not request a retake for the quiz.', 'wp-courseware' ) . ' ' . __( 'You are not permitted to retake this quiz.', 'wp-courseware' ) );
		die();
	}

	// Trigger the upgrade to progress so that we're allowed to retake this quiz.
	$fe->update_quizzes_requestQuizRetake();

	// Only complete if allowed to continue.
	echo apply_filters( 'the_content', $fe->render_detailsForUnit( false, true ) );

	die();
}

/**
 * Handle Unit User Progress.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_units_handleUserProgress() {
	if ( ! wp_verify_nonce( WPCW_arrays_getValue( $_POST, 'progress_nonce' ), 'wpcw-progress-nonce' ) ) {
		die ( __( 'Security check failed!', 'wp-courseware' ) );
	}

	$unitID = WPCW_arrays_getValue( $_POST, 'id' );

	// Validate the course ID
	if ( ! preg_match( '/unit_complete_(\d+)/', $unitID, $matches ) ) {
		echo WPCW_UnitFrontend::message_error_getCompletionBox_error();
		die();
	}
	$unitID = $matches[1];

	// Get the post object for this quiz item.
	$post = get_post( $unitID );
	if ( ! $post ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not save your progress.', 'wp-courseware' ) . ' ' . __( 'Could not find training unit.', 'wp-courseware' ) );
		die();
	}

	// Initalise the unit details
	$fe = new WPCW_UnitFrontend( $post );

	// Get associated data for this unit. No course/module data, then it's not a unit
	if ( ! $fe->check_unit_doesUnitHaveParentData() ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not save your progress.', 'wp-courseware' ) . ' ' . __( 'Could not find course details for unit.', 'wp-courseware' ) );
		die();
	}

	// User not allowed access to content
	if ( ! $fe->check_user_canUserAccessCourse() ) {
		echo $fe->message_user_cannotAccessCourse();
		die();
	}

	WPCW_units_saveUserProgress_Complete( $fe->fetch_getUserID(), $fe->fetch_getUnitID(), 'complete' );

	// Unit complete, check if course/module is complete too.
	do_action( 'wpcw_user_completed_unit', $fe->fetch_getUserID(), $fe->fetch_getUnitID(), $fe->fetch_getUnitParentData() );

	// Only complete if allowed to continue.
	echo apply_filters( 'the_content', $fe->render_detailsForUnit( false, false ) );

	die();
}

/**
 * Handle Quiz Response.
 *
 * Called when a user is submitting quiz answers via the frontend form.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_units_handleQuizResponse() {
	if ( ! wp_verify_nonce( WPCW_arrays_getValue( $_POST, 'progress_nonce' ), 'wpcw-progress-nonce' ) ) {
		die ( __( 'Security check failed!', 'wp-courseware' ) );
	}

	// Quiz ID and Unit ID are combined in the single CSS ID for validation.
	// So validate both are correct and that user is allowed to access quiz.
	$quizAndUnitID = WPCW_arrays_getValue( $_POST, 'id' );

	// e.g. quiz_complete_69_1 or quiz_complete_17_2 (first ID is unit, 2nd ID is quiz)
	if ( ! preg_match( '/quiz_complete_(\d+)_(\d+)/', $quizAndUnitID, $matches ) ) {
		echo WPCW_UnitFrontend::message_error_getCompletionBox_error();
		die();
	}

	// Use the extracted data for further validation
	$unitID = $matches[1];
	$quizID = $matches[2];

	// Get the post object for this quiz item.
	$post = get_post( $unitID );
	if ( ! $post ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not save your quiz results.', 'wp-courseware' ) . ' ' . __( 'Could not find training unit.', 'wp-courseware' ) );
		die();
	}

	// Initalise the unit details
	$fe = new WPCW_UnitFrontend( $post );
	$fe->setTriggeredAfterAJAXRequest();

	// Get associated data for this unit. No course/module data, then it's not a unit
	if ( ! $fe->check_unit_doesUnitHaveParentData() ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not save your quiz results.', 'wp-courseware' ) . ' ' . __( 'Could not find course details for unit.', 'wp-courseware' ) );
		die();
	}

	// User not allowed access to content
	if ( ! $fe->check_user_canUserAccessCourse() ) {
		echo $fe->message_user_cannotAccessCourse();
		die();
	}

	// Check that the quiz is valid and belongs to this unit
	if ( ! $fe->check_quizzes_isQuizValidForUnit( $quizID ) ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not save your quiz results.', 'wp-courseware' ) . ' ' . __( 'Quiz data does not match quiz for this unit.', 'wp-courseware' ) );
		die();
	}

	$canContinue = false;

	// Do we have all the answers that we need so that we can grade the quiz?
	// Answer Check Variation A - Paging
	if ( $fe->check_paging_areWePagingQuestions() ) {
		// If this is false, then we keep checking for more answers.
		$readyForMarking = $fe->check_quizzes_canWeContinue_checkAnswersFromPaging( $_POST );
	} else { // Answer Check Variation B - All at once (no paging)
		// If this is false, then the form is represented asking for fixes.
		$readyForMarking = $fe->check_quizzes_canWeContinue_checkAnswersFromOnePageQuiz( $_POST );
	}

	// Now checks are done, $this->unitQuizProgress contains the latest questions so that we can mark them.
	if ( $readyForMarking || $fe->check_timers_doWeHaveAnActiveTimer_thatHasExpired() ) {
		$canContinue = $fe->check_quizzes_gradeQuestionsForQuiz();
	}

	// Validate the answers that we have, which determines if we can carry on to the next
	//      unit, or if the user needs to do something else.
	if ( $canContinue ) {
		WPCW_units_saveUserProgress_Complete( $fe->fetch_getUserID(), $fe->fetch_getUnitID(), 'complete' );

		// Unit complete, check if course/module is complete too.
		do_action( 'wpcw_user_completed_unit', $fe->fetch_getUserID(), $fe->fetch_getUnitID(), $fe->fetch_getUnitParentData() );
	}

	// Show the appropriate messages/forms for the user to look at. This is common for all execution
	// paths.
	// DJH 2015-09-09 - Added capability for next button to show when a user completes a quiz.
	echo apply_filters( 'the_content', $fe->render_detailsForUnit( false, ! $canContinue ) );

	die();
}

/**
 * Handle a user wanting to go to the previous question or
 * jump a question without saving the question details.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_units_handleQuizJumpQuestion() {
	if ( ! wp_verify_nonce( WPCW_arrays_getValue( $_POST, 'progress_nonce' ), 'wpcw-progress-nonce' ) ) {
		die ( __( 'Security check failed!', 'wp-courseware' ) );
	}

	// Get unit and quiz ID
	$unitID = intval( WPCW_arrays_getValue( $_POST, 'unitid' ) );
	$quizID = intval( WPCW_arrays_getValue( $_POST, 'quizid' ) );

	$jumpMode  = 'previous';
	$msgPrefix = __( 'Error - could not load the previous question.', 'wp-courseware' ) . ' ';

	// We're skipping ahead.
	if ( 'next' == WPCW_arrays_getValue( $_POST, 'qu_direction' ) ) {
		$jumpMode  = 'next';
		$msgPrefix = __( 'Error - could not load the next question.', 'wp-courseware' ) . ' ';
	}

	// Get the post object for this quiz item.
	$post = get_post( $unitID );
	if ( ! $post ) {
		echo WPCW_UnitFrontend::message_createMessage_error( $msgPrefix . __( 'Could not find training unit.', 'wp-courseware' ) );
		die();
	}

	// Initalise the unit details
	$fe = new WPCW_UnitFrontend( $post );

	// Get associated data for this unit. No course/module data, then it's not a unit
	if ( ! $fe->check_unit_doesUnitHaveParentData() ) {
		echo WPCW_UnitFrontend::message_createMessage_error( $msgPrefix . __( 'Could not find course details for unit.', 'wp-courseware' ) );
		die();
	}

	// User not allowed access to content
	if ( ! $fe->check_user_canUserAccessCourse() ) {
		echo $fe->message_user_cannotAccessCourse();
		die();
	}

	// Check that the quiz is valid and belongs to this unit
	if ( ! $fe->check_quizzes_isQuizValidForUnit( $quizID ) ) {
		echo WPCW_UnitFrontend::message_createMessage_error( $msgPrefix . __( 'Quiz data does not match quiz for this unit.', 'wp-courseware' ) );
		die();
	}

	$canContinue = false;

	// If we're paging, then do what we need next.
	if ( $fe->check_paging_areWePagingQuestions() ) {
		$fe->fetch_paging_getQuestion_moveQuestionMarker( $jumpMode );
	}

	echo apply_filters( 'the_content', $fe->render_detailsForUnit( false, true ) );

	die();
}

/**
 * Function called when user starting a quiz
 * and needs to kick off the timer.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_units_handleQuizTimerBegin() {
	if ( ! wp_verify_nonce( WPCW_arrays_getValue( $_POST, 'progress_nonce' ), 'wpcw-progress-nonce' ) ) {
		die ( __( 'Security check failed!', 'wp-courseware' ) );
	}

	// Get unit and quiz ID
	$unitID = intval( WPCW_arrays_getValue( $_POST, 'unitid' ) );
	$quizID = intval( WPCW_arrays_getValue( $_POST, 'quizid' ) );

	// Get the post object for this quiz item.
	$post = get_post( $unitID );
	if ( ! $post ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not start the timer for the quiz.', 'wp-courseware' ) . ' ' . __( 'Could not find training unit.', 'wp-courseware' ) );
		die();
	}

	// Initalise the unit details
	$fe = new WPCW_UnitFrontend( $post );

	// Get associated data for this unit. No course/module data, then it's not a unit
	if ( ! $fe->check_unit_doesUnitHaveParentData() ) {
		echo WPCW_UnitFrontend::message_createMessage_error( __( 'Error - could not start the timer for the quiz.', 'wp-courseware' ) . ' ' . __( 'Could not find course details for unit.', 'wp-courseware' ) );
		die();
	}

	// User not allowed access to content
	if ( ! $fe->check_user_canUserAccessCourse() ) {
		echo $fe->message_user_cannotAccessCourse();
		die();
	}

	// See if we're in a position to retake this quiz?
	// if (!$fe->check_quizzes_canUserRequestRetake())
	// {
	// 	echo WPCW_UnitFrontend::message_createMessage_error(__('Error - could not start the timer for the quiz.', 'wp-courseware') . ' ' . __('You are not permitted to retake this quiz.', 'wp-courseware'));
	// 	die();
	// }

	// Trigger the upgrade to progress so that we can start the quiz, and trigger the timer.
	$fe->update_quizzes_beginQuiz();

	// Only complete if allowed to continue.
	echo apply_filters( 'the_content', $fe->render_detailsForUnit( false, true ) );

	die();
}

/**
 * Function called when the user is
 * enrolling via enrollment shortcode.
 *
 * @since 1.0.0
 */
function WPCW_AJAX_course_handleEnrollment_button() {
	if ( ! wp_verify_nonce( WPCW_arrays_getValue( $_POST, 'enrollment_nonce' ), 'wpcw-enrollment-nonce' ) ) {
		die ( __( 'Security check failed!', 'wp-courseware' ) );
	}

	$courseList = WPCW_arrays_getValue( $_POST, 'id' );

	// Validate the course ID
	if ( ! preg_match( '/(\d+)(_\s*\d+)*/', $courseList, $matches ) ) {
		//if (!preg_match('/enroll-(\d+)(,\s*\d+)*/', $courseList, $matches)) {
		//if (!preg_match('/enroll-([^-]+)-([\d+,\d]+)/', $courseList, $matches)) {
		echo WPCW_UnitFrontend::message_error_getCompletionBox_error();
		die();
	}

	$courseIDs = explode( "_", $matches[0] );
	$user_id   = get_current_user_id();

	//enroll user into course(s)
	WPCW_courses_syncUserAccess( $user_id, $courseIDs, 'add' );

	// verify enrollment
	foreach ( $courseIDs as $courseID ) {
		// Back Compat
		$course_id = $courseID;

		// Get course details to fetch course title
		$courseDetails = WPCW_courses_getCourseDetails( $courseID );

		// Can the student access the course now?
		$userCourses = WPCW_courses_canUserAccessCourse( $courseID, $user_id );

		if ( $userCourses ) {
			$course_title = sprintf( __( '%s', 'wp-courseware' ), $courseDetails->course_title );
			/**
			 * Filter: Course Enrollment Success Message.
			 *
			 * @since 4.3.0
			 *
			 * @param string $message The success message.
			 * @param int $course_id The course id.
			 * @param int $user_id The user id.
			 *
			 * @return string $message The success message modified.
			 */
			$success_message = apply_filters( 'wpcw_course_enrollment_success_message', sprintf( __( 'Success! You have been enrolled into %s', 'wp-courseware' ), $course_title ), $course_id, $user_id );
			echo WPCW_UnitFrontend::message_createMessage_success( $success_message );
		} else {
			/**
			 * Filter: Course Enrollment Error Message.
			 *
			 * @since 4.3.0
			 *
			 * @param string $message The success message.
			 * @param int $course_id The course id.
			 * @param int $user_id The user id.
			 *
			 * @return string $message The success message modified.
			 */
			$error_message = apply_filters( 'wpcw_course_enrollment_error_message', __( 'Oops! Something went wrong. Please contact the course instructor for more information.', 'wp-courseware' ), $course_id, $user_id );
			echo WPCW_UnitFrontend::message_createMessage_error( $error_message );
		}
	}

	die();
}