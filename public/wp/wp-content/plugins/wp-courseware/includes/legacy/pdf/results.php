<?php
/**
 * WP Courseware Create PDF Results.
 *
 * @package WPCW
 * @since 1.0.0
 */

/**
 * Create PDF Results
 *
 * @since 1.0.0
 *
 * @param object $wp The WordPress object.
 */
function WPCW_create_pdf_results( $wp ) {
	if ( ! array_key_exists( 'page', $wp->query_vars ) || $wp->query_vars['page'] != 'wpcw_pdf_create_results' ) {
		return;
	}

	// Define Current User
	$current_user = wp_get_current_user();

	// Get unit and quiz ID
	$unitID = intval( WPCW_arrays_getValue( $_GET, 'unitid' ) );
	$quizID = intval( WPCW_arrays_getValue( $_GET, 'quizid' ) );

	// Get the post object for this quiz item.
	$post = get_post( $unitID );
	if ( ! $post ) {
		WPCW_export_results_notFound( __( 'Could not find training unit.', 'wp-courseware' ) );
	}

	// Initalise the unit details
	$fe = new WPCW_UnitFrontend( $post );

	// Get associated data for this unit. No course/module data, then it's not a unit
	if ( ! $fe->check_unit_doesUnitHaveParentData() ) {
		WPCW_export_results_notFound( __( 'Could not find course details for unit.', 'wp-courseware' ) );
	}

	// User not allowed access to content.
	if ( ! $fe->check_user_canUserAccessCourse() ) {
		WPCW_export_results_notFound( $fe->fetch_message_user_cannotAccessCourse() );
	}

	$parentData  = $fe->fetch_getUnitParentData();
	$quizDetails = $fe->fetch_getUnitQuizDetails();

	$qrpdf = new WPCW_QuizResults( $parentData );

	// Set values for use in the results
	$qrpdf->setTraineeName( WPCW_users_getUsersName( $current_user ) );
	$qrpdf->setCourseName( $parentData->course_title );
	$qrpdf->setQuizName( $quizDetails->quiz_title );

	// Render status messages
	$qrpdf->setQuizMessages( $fe->check_quizzes_workoutQuizPassStatusDetails() );

	// Render feedback messages
	$qrpdf->setQuizFeedback( $fe->fetch_customFeedbackMessage_calculateMessages() );

	// Render the results
	$qrpdf->setQuizResults( $fe->render_quizzes_showAllCorrectAnswers( true ) );

	// Generate pdf
	$qrpdf->generatePDF( 'browser' );

	die();
}

/**
 * Export Results Not Found Error.
 *
 * @since 1.0.0
 */
function WPCW_export_results_notFound( $extraMessage = false ) {
	printf( __( 'Could not export your results. %s', 'wp-courseware' ), $extraMessage );
	die();
}