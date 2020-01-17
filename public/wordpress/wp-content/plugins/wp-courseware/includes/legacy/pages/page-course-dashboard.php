<?php
/**
 * WP Courseware Page Course Dashboard.
 *
 * Functions relating to showing the course summary page.
 *
 * @package WPCW
 * @since 1.0.0
 */

/**
 * Function that show a summary of the training courses.
 *
 * @since 1.0.0
 */
function WPCW_showPage_Dashboard_load() {
	global $wpcwdb, $wpdb;
	$user_id = get_current_user_id();

	// Check permissions
	if ( ! user_can( $user_id, 'view_wpcw_courses' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

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

	// Was a search string specified? Or a specific item?
	$searchString = WPCW_arrays_getValue( $_GET, 's' );

	// Create WHERE string based search - Title or Description of Quiz
	$SQL_WHERE_CONDITIONS = array();
	$SQL_WHERE            = false;
	if ( $searchString ) {
		$SQL_WHERE_CONDITIONS[] = $wpdb->prepare( 'course_title LIKE %s', '%' . $searchString . '%' );
	}

	// Check if admin
	if ( ! user_can( $user_id, 'manage_wpcw_settings' ) ) {
		$SQL_WHERE_CONDITIONS[] = $wpdb->prepare( 'course_author = %d', $user_id );
	}

	// Build Where Query
	if ( is_array( $SQL_WHERE_CONDITIONS ) && ! empty( $SQL_WHERE_CONDITIONS ) ) {
		$SQL_WHERE       = 'WHERE ';
		$counter         = 1;
		$condition_count = count( $SQL_WHERE_CONDITIONS );
		foreach ( $SQL_WHERE_CONDITIONS as $SQL_CONDITION ) {
			$SQL_WHERE .= ( $counter !== $condition_count ) ? $SQL_CONDITION . ' AND ' : $SQL_CONDITION;
			$counter ++;
		}
	}

	// Get the page count for the query
	$SQL_PAGING = "
			SELECT COUNT(*) as course_count
			FROM $wpcwdb->courses c
			$SQL_WHERE
		";

	$paging_resultsPerPage = ( get_user_meta( $user_id, 'wpcw_courses_per_page', true ) != false ) ? get_user_meta( $user_id, 'wpcw_courses_per_page', true ) : 20;
	$paging_totalCount     = $wpdb->get_var( $SQL_PAGING );
	$paging_recordStart    = ( ( $paging_pageWanted - 1 ) * $paging_resultsPerPage ) + 1;
	$paging_recordEnd      = ( $paging_pageWanted * $paging_resultsPerPage );
	$paging_pageCount      = ceil( $paging_totalCount / $paging_resultsPerPage );
	$paging_sqlStart       = $paging_recordStart - 1;

	// Generate paging code
	$summaryPageURL = admin_url( 'admin.php?page=wpcw-courses' );
	$baseURL        = WPCW_urls_getURLWithParams( $summaryPageURL ) . "&pagenum=";
	$paging         = WPCW_tables_showPagination( $baseURL, $paging_pageWanted, $paging_pageCount, $paging_totalCount, $paging_recordStart, $paging_recordEnd );

	$page = new PageBuilder( false );

	$page_title = __( 'Courses', 'wp-courseware' );
	$page_title .= sprintf(
		'&nbsp;<a class="page-title-action" href="%s">%s</a>',
		add_query_arg( array( 'page' => 'WPCW_showPage_ModifyCourse' ), admin_url( 'admin.php' ) ),
		esc_html__( 'Add New', 'wp-courseware' )
	);

	$page->showPageHeader( $page_title, '75%', WPCW_icon_getPageIconURL() );

	// Handle any deletion
	// WPCW_handler_processDeletion( $page );

	// Handle the sorting and filtering
	$orderBy  = WPCW_arrays_getValue( $_GET, 'orderby' );
	$ordering = WPCW_arrays_getValue( $_GET, 'order' );

	// Validate ordering
	switch ( $orderBy ) {
		case 'course_title':
		case 'course_id':
			break;

		default:
			$orderBy = 'course_title';
			break;
	}

	// Create opposite ordering for reversing it.
	$ordering_opposite = false;
	switch ( $ordering ) {
		case 'desc':
			$ordering_opposite = 'asc';
			break;

		case 'asc':
			$ordering_opposite = 'desc';
			break;

		default:
			$ordering          = 'asc';
			$ordering_opposite = 'desc';
			break;
	}

	// This data has been validated, so ok to use without prepare
	$SQL = "
			SELECT *
			FROM $wpcwdb->courses
			$SQL_WHERE
			ORDER BY $orderBy $ordering
			LIMIT $paging_sqlStart, $paging_resultsPerPage
			";

	$courses = $wpdb->get_results( $SQL );
	if ( $courses || $searchString ) {

		// Show search message - that a search has been tried.
		if ( $searchString ) {
			printf( '<div class="wpcw_search_count">%s "%s" (%s %s) (<a href="%s">%s</a>)</div>',
				__( 'Search results for', 'wp-courseware' ),
				htmlentities( $searchString ),
				$paging_totalCount,
				_n( 'result', 'results', $paging_totalCount, 'wp-courseware' ),
				$summaryPageURL,
				__( 'reset', 'wp-courseware' )
			);
		}

		// Show the form for searching
		?>
        <form id="wpcw_courses_search_box" method="get" action="<?php echo $summaryPageURL; ?>">
            <p class="search-box">
                <label class="screen-reader-text" for="wpcw_courses_search_input"><?php _e( 'Search Courses', 'wp-courseware' ); ?></label>
                <input id="wpcw_courses_search_input" type="text" value="<?php echo $searchString ?>" name="s"/>
                <input class="button" type="submit" value="<?php _e( 'Search Courses', 'wp-courseware' ); ?>"/>
                <input type="hidden" name="page" value="wpcw-courses"/>
            </p>
        </form>
		<?php

		// Show paging
		echo $paging;

		$tbl             = new TableBuilder();
		$tbl->attributes = array(
			'id'    => 'wpcw_tbl_course_summary',
			'class' => 'widefat wpcw_tbl',
		);

		// ID - sortable
		$sortableLink = sprintf( '<a href="%s&order=%s&orderby=course_id"><span>%s</span><span class="sorting-indicator"></span></a>',
			admin_url( 'admin.php?page=wpcw-courses' ),
			( 'course_id' == $orderBy ? $ordering_opposite : 'asc' ),
			__( 'ID', 'wp-courseware' )
		);

		if ( $paging_totalCount == 0 ) {
			$tbl->addRowObj( new RowDataSimple( 'wpcw_center wpcw_none_found', __( 'There are currently no courses to show.', 'wp-courseware' ), 7 ) );
		}

		// ID - render
		$tblCol              = new TableColumn( $sortableLink, 'course_id' );
		$tblCol->cellClass   = "course_id";
		$tblCol->headerClass = ( 'course_id' == $orderBy ? 'sorted ' . $ordering : 'sortable' );
		$tbl->addColumn( $tblCol );

		// Title - sortable
		$sortableLink = sprintf( '<a href="%s&order=%s&orderby=course_title"><span>%s</span><span class="sorting-indicator"></span></a>',
			admin_url( 'admin.php?page=wpcw-courses' ),
			( 'course_title' == $orderBy ? $ordering_opposite : 'asc' ),
			__( 'Course Title', 'wp-courseware' )
		);

		// Title - render
		$tblCol              = new TableColumn( $sortableLink, 'course_title' );
		$tblCol->headerClass = ( 'course_title' == $orderBy ? 'sorted ' . $ordering : 'sortable' );
		$tblCol->cellClass   = "course_title";
		$tbl->addColumn( $tblCol );

		$tblCol            = new TableColumn( __( 'Description', 'wp-courseware' ), 'course_desc' );
		$tblCol->cellClass = "course_desc";
		$tbl->addColumn( $tblCol );

		$tblCol            = new TableColumn( __( 'Settings', 'wp-courseware' ), 'course_settings' );
		$tblCol->cellClass = "course_settings";
		$tbl->addColumn( $tblCol );

		$tblCol            = new TableColumn( __( 'Total Units', 'wp-courseware' ), 'total_units' );
		$tblCol->cellClass = "total_units";
		$tbl->addColumn( $tblCol );

		$tblCol            = new TableColumn( __( 'Modules', 'wp-courseware' ), 'course_modules' );
		$tblCol->cellClass = "course_modules";
		$tbl->addColumn( $tblCol );

		$tblCol            = new TableColumn( __( 'Actions', 'wp-courseware' ), 'actions' );
		$tblCol->cellClass = "actions";
		$tbl->addColumn( $tblCol );

		// Links
		$editURL       = admin_url( 'admin.php?page=WPCW_showPage_ModifyCourse' );
		$url_addModule = admin_url( 'admin.php?page=WPCW_showPage_ModifyModule' );
		$url_ordering  = admin_url( 'admin.php?page=WPCW_showPage_CourseOrdering' );
		$url_gradeBook = admin_url( 'admin.php?page=WPCW_showPage_GradeBook' );
		$url_classroom = admin_url( 'admin.php?page=wpcw-course-classroom' );

		// Format row data and show it.
		$odd = false;
		foreach ( $courses as $course ) {
			$data = array();

			// Basic Details
			$data['course_id']   = $course->course_id;
			$data['course_desc'] = $course->course_desc;

			// Editing Link
			$data['course_title'] = sprintf( '<a href="%s&course_id=%d">%s</a>', $editURL, $course->course_id, $course->course_title );

			// Actions
			$data['actions'] = '<ul>';
			$data['actions'] .= sprintf( '<li><a href="%s&course_id=%d" class="button-primary">%s</a></li>', $url_addModule, $course->course_id, __( 'Add Module', 'wp-courseware' ) );
			$data['actions'] .= sprintf( '<li><a href="%s&course_id=%d" class="button-secondary">%s</a></li>', $editURL, $course->course_id, __( 'Edit Course Settings', 'wp-courseware' ) );
			$data['actions'] .= sprintf( '<li><a href="%s&course_id=%d" class="button-secondary">%s</a></li>', $url_ordering, $course->course_id, __( 'Modules, Units &amp; Quiz Ordering', 'wp-courseware' ) );
			$data['actions'] .= sprintf( '<li><a href="%s&course_id=%d" class="button-secondary">%s</a></li>', $url_gradeBook, $course->course_id, __( 'Access Grade Book', 'wp-courseware' ) );
			$data['actions'] .= sprintf( '<li><a href="%s&course_id=%d" class="button-secondary">%s</a></li>', $url_classroom, $course->course_id, __( 'Classroom', 'wp-courseware' ) );
			$data['actions'] .= '</ul>';

			// Settings Summary - to allow user to see a quick overview of the current settings.
			$data['course_settings'] = '<ul class="wpcw_tickitems">';

			// Access control - filtered if membership plugin
			$data['course_settings'] .= apply_filters( 'wpcw_extensions_access_control_override',
				sprintf( '<li class="wpcw_%s">%s</li>', ( 'default_show' == $course->course_opt_user_access ? 'enabled' : 'disabled' ), __( 'Give new users access by default', 'wp-courseware' ) )
			);

			// Completion wall
			$data['course_settings'] .= sprintf( '<li class="wpcw_%s">%s</li>', ( 'completion_wall' == $course->course_opt_completion_wall ? 'enabled' : 'disabled' ),
				__( 'Require unit completion before showing next', 'wp-courseware' ) );

			// Certificate handling
			$data['course_settings'] .= sprintf( '<li class="wpcw_%s">%s</li>', ( 'use_certs' == $course->course_opt_use_certificate ? 'enabled' : 'disabled' ),
				__( 'Generate certificates on course completion', 'wp-courseware' ) );

			$data['course_settings'] .= '</ul>';


			// Module list
			$data['course_modules'] = false;
			$moduleList             = WPCW_courses_getModuleDetailsList( $course->course_id );
			$moduleIDList           = array();

			if ( $moduleList ) {
				foreach ( $moduleList as $item_id => $moduleObj ) {
					$modName = sprintf( '%s %d - %s', __( 'Module', 'wp-courseware' ), $moduleObj->module_number, $moduleObj->module_title );

					// Create each module item with an edit link.
					$modEditURL             = admin_url( 'admin.php?page=WPCW_showPage_ModifyModule&module_id=' . $item_id );
					$data['course_modules'] .= sprintf( '<li><a href="%s" title="%s \'%s\'">%s</a></li>',
						$modEditURL,
						__( 'Edit Module', 'wp-courseware' ),
						$modName, $modName
					);

					// Just want module IDs
					$moduleIDList[] = $item_id;
				}
			} else {
				$data['course_modules'] = __( 'No modules yet.', 'wp-courseware' );
			}


			// Unit Count
			if ( count( $moduleIDList ) > 0 ) {
				$data['total_units'] = $wpdb->get_var( "
					SELECT COUNT(*)
					FROM $wpcwdb->units_meta
					WHERE parent_module_id IN (" . implode( ",", $moduleIDList ) . ")" );
			} // No modules, so can't be any units.
			else {
				$data['total_units'] = '0';
			}


			// Odd/Even row colouring.
			$odd      = ! $odd;
			$rowClass = ( $odd ? 'alternate' : '' );


			// Check if we want to show quiz grading notifications
			$quiz_notify = get_user_meta( $user_id, 'wpcw_course_dashboard_quiz_notification_hide', true );
			if ( $quiz_notify == 'show' ) {

				// Get a list of all quizzes for the specified parent course.
				$listOfQuizzes = $wpdb->get_col( $wpdb->prepare( "
					SELECT quiz_id
					FROM $wpcwdb->quiz
					WHERE parent_course_id = %d
				", $course->course_id ) );

				$countOfQuizzesNeedingGrading    = false;
				$countOfQuizzesNeedingManualHelp = false;

				// Determine if there are any quizzes that need marking. If so, how many?
				if ( ! empty( $listOfQuizzes ) ) {
					$quizIDList = '(' . implode( ',', $listOfQuizzes ) . ')';

					$countOfQuizzesNeedingGrading = $wpdb->get_var( "
						SELECT COUNT(*)
						FROM $wpcwdb->user_progress_quiz
						WHERE quiz_id IN $quizIDList
						  AND quiz_needs_marking > 0
						  AND quiz_is_latest = 'latest'
					" );

					$countOfQuizzesNeedingManualHelp = $wpdb->get_var( "
						SELECT COUNT(*)
						FROM $wpcwdb->user_progress_quiz
						WHERE quiz_id IN $quizIDList
						  AND quiz_next_step_type = 'quiz_fail_no_retakes'
						  AND quiz_is_latest = 'latest'
					" );
				}


				// Have we got any custom data for this row?
				$tblCustomRowStr = false;

				// Show the status message about quizzes needing marking.
				if ( $countOfQuizzesNeedingGrading ) {
					// Create message that quizzes need marking.
					$tblCustomRowStrTmp =
						__( 'This course has ', 'wp-courseware' ) .
						_n( '1 quiz that requires', '%d quizzes that require', $countOfQuizzesNeedingGrading, 'wp-courseware' ) .
						__( ' manual grading.', 'wp-courseware' );

					$tblCustomRowStr .= '<span>' . sprintf( $tblCustomRowStrTmp, $countOfQuizzesNeedingGrading ) . '</span>';
				}

				// Show the status message about quizzes needing manual intervention.
				if ( $countOfQuizzesNeedingManualHelp ) {
					// Create message that quizzes need marking.
					$tblCustomRowStrTmp =
						__( 'This course has ', 'wp-courseware' ) .
						_n( '1 user that is', '%d users that are', $countOfQuizzesNeedingManualHelp, 'wp-courseware' ) .
						__( ' blocked due to too many failed attempts.', 'wp-courseware' );

					$tblCustomRowStr .= '<span>' . sprintf( $tblCustomRowStrTmp, $countOfQuizzesNeedingManualHelp ) . '</span>';
				}
			} else {
				$tblCustomRowStr = false;
			}


			// Add a row for the status data, hiding the border above it.
			if ( $tblCustomRowStr ) {
				// Create a row that also hides the border below it.
				$tbl->addRow( $data, 'wpcw_tbl_row_status_pre ' . $rowClass );

				$tblRow = new RowDataSimple( 'wpcw_tbl_row_status ' . $rowClass, $tblCustomRowStr, 7 );
				$tbl->addRowObj( $tblRow );
			}


			// Normal course row. No status information below the course detail row.
			// So don't modify the row borders at all.
			else {
				$tbl->addRow( $data, $rowClass );
			}
		}

		// Finally show table
		echo $tbl->toString();
		// Show paging
		echo $paging;
	} else {
		printf( '<p>%s</p>', __( 'There are currently no courses to show. Why not create one?', 'wp-courseware' ) );
	}

	$page->showPageFooter();
}

/**
 * Handle any deletion if any has been requested.
 *
 * @since 1.0.0
 *
 * @param object $page The reference to the object showing the page content
 */
function WPCW_handler_processDeletion( $page ) {
	// Check for deletion command
	if ( ! isset( $_GET['action'] ) ) {
		return;
	}

	$action = WPCW_arrays_getValue( $_GET, 'action' );

	switch ( $action ) {
		// Deleting a module
		case 'delete_module':
			$module_id     = WPCW_arrays_getValue( $_GET, 'module_id' );
			$moduleDetails = WPCW_modules_getModuleDetails( $module_id );
			if ( $moduleDetails ) {
				// Actually delete the module from the system
				WPCW_modules_deleteModule( $moduleDetails );

				$page->showMessage( sprintf( __( 'Successfully deleted module "<em>%s</em>".', 'wp-courseware' ), $moduleDetails->module_title ) );
			}
			break;

		// Deleting a course
		case 'delete_course':
			$course_id     = WPCW_arrays_getValue( $_GET, 'course_id' );
			$courseDetails = WPCW_courses_getCourseDetails( $course_id );
			if ( $courseDetails ) {
				// What deletion method?
				$deleteMethod = 'complete';
				if ( 'course_and_module' == WPCW_arrays_getValue( $_POST, 'delete_course_type' ) ) {
					$deleteMethod = 'course_and_module';
				}

				// Actually delete the course from the system
				WPCW_modules_deleteCourse( $courseDetails, $deleteMethod );

				$page->showMessage( sprintf( __( 'Successfully deleted training course "<em>%s</em>".', 'wp-courseware' ), $courseDetails->course_title ) );
			}
			break;
	}
}

/**
 * Delete a module and disassociating of the units that it contains.
 *
 * @since 1.0.0
 *
 * @param object $moduleDetails The details of the module to delete.
 */
function WPCW_modules_deleteModule( $moduleDetails ) {
	if ( ! $moduleDetails ) {
		return;
	}

	global $wpdb, $wpcwdb;
	$wpdb->show_errors();

	// Remove association with all units for this module
	$unitList = WPCW_units_getListOfUnits( $moduleDetails->module_id );
	if ( $unitList ) {
		// Unassociate units from this module
		$SQL = $wpdb->prepare( "
			UPDATE $wpcwdb->units_meta
			   SET unit_order = 0, parent_module_id = 0, parent_course_id = 0, unit_number = 0
			WHERE parent_module_id = %d
		", $moduleDetails->module_id );

		// Update database with new association and ordering.
		foreach ( $unitList as $unitID => $unitObj ) {
			$wpdb->query( $SQL );

			// Update post meta to remove associated module detail
			update_post_meta( $unitID, 'wpcw_associated_module', 0 );

			// Remove progress for this unit, since unit is now unassociated.
			$SQL = $wpdb->prepare( "DELETE FROM $wpcwdb->user_progress WHERE unit_id = %d", $unitID );
			$wpdb->query( $SQL );

			// Quiz - Unconnect it from the quiz that it's associated with.
			$SQL = $wpdb->prepare( "UPDATE $wpcwdb->quiz SET parent_unit_id = 0, parent_course_id = 0 WHERE parent_unit_id = %d", $unitID );
			$wpdb->query( $SQL );
		}
	}

	// Perform module deletion here.
	$wpdb->query( $SQL = $wpdb->prepare( "
				DELETE FROM $wpcwdb->modules
				WHERE module_id = %d
			", $moduleDetails->module_id ) );

	// Modules have changed for this course, update numbering
	do_action( 'wpcw_modules_modified', $moduleDetails->parent_course_id );

	// Course has been updated, update the progress details
	$courseDetails = WPCW_courses_getCourseDetails( $moduleDetails->parent_course_id );
	if ( $courseDetails ) {
		do_action( 'wpcw_course_details_updated', $courseDetails );
	}

	// Trigger event that module has been deleted
	do_action( 'wpcw_module_deleted', $moduleDetails );

	return true;
}

/**
 * Delete a course, its modules and disassociating of the units that it contains.
 *
 * @since 1.0.0
 *
 * @param object $courseDetails The details of the course to delete.
 * @param string $deleteMethod The deletion method. Either 'complete' or 'course_and_module'.
 */
function WPCW_modules_deleteCourse( $courseDetails, $deleteMethod ) {
	if ( ! $courseDetails ) {
		return;
	}

	global $wpdb, $wpcwdb;
	$wpdb->show_errors();

	// Get a list of units for this course.
	$unitList = $wpdb->get_col( $wpdb->prepare( "
    	SELECT unit_id
    	FROM $wpcwdb->units_meta
    	WHERE parent_course_id = %d
    ", $courseDetails->course_id ) );

	switch ( $deleteMethod ) {
		// Disassociate quizzes, units, etc.
		case 'course_and_module':
			if ( $unitList ) {
				foreach ( $unitList as $unitID ) {
					// Unit Meta
					// Remove course info. Update database with new association and ordering.
					$SQL = $wpdb->prepare( "
						UPDATE $wpcwdb->units_meta
						   SET unit_order = 0, parent_module_id = 0, parent_course_id = 0, unit_number = 0
						WHERE unit_id = %d
					", $unitID );

					$wpdb->query( $SQL );

					// Unit Post Meta
					// Update post meta to remove associated module detail
					update_post_meta( $unitID, 'wpcw_associated_module', 0 );

					// User Progress
					// Remove progress for this unit, since unit is now unassociated.
					$SQL = $wpdb->prepare( "
						DELETE FROM $wpcwdb->user_progress
						WHERE unit_id = %d
					", $unitID );

					$wpdb->query( $SQL );

					// User Quiz Progress
					// Progress is linked to a course, hence wanting to remove it.
					$SQL = $wpdb->prepare( "
						DELETE FROM $wpcwdb->user_progress_quiz
						WHERE unit_id = %d
					", $unitID );
					$wpdb->query( $SQL );

					// Quiz - Unconnect it from the quiz that it's associated with.
					$SQL = $wpdb->prepare( "UPDATE $wpcwdb->quiz SET parent_unit_id = 0, parent_course_id = 0 WHERE parent_unit_id = %d", $unitID );
					$wpdb->query( $SQL );
				}
			}

			// Quiz Association
			// Remove course info for all quizzes
			$SQL = $wpdb->prepare( "
				UPDATE $wpcwdb->quiz
				   SET parent_course_id = 0
				WHERE parent_course_id = %d
			", $courseDetails->course_id );
			$wpdb->query( $SQL );

			break;

		// Complete delete - delete absolutely everything...
		default:
			// Quiz Deletion - need a list of quiz IDs to delete the
			// question mappings
			$quizList = $wpdb->get_col( $wpdb->prepare( "
		    	SELECT quiz_id
		    	FROM $wpcwdb->quiz
		    	WHERE parent_course_id = %d
		    ", $courseDetails->course_id ) );

			if ( $quizList ) {
				// Reduce SQL queries - do an ARRAY search on the WHERE.
				$quizIDList = implode( ",", $quizList );

				// Get a list of question IDs first
				$questionIDList = $wpdb->get_col( "
					SELECT question_id
					FROM $wpcwdb->quiz_qs_mapping
					WHERE parent_quiz_id IN ($quizIDList)
					GROUP BY question_id
				" );

				// Remove the mappings between Quiz and their Questions
				$wpdb->query( "
					DELETE FROM $wpcwdb->quiz_qs_mapping
					WHERE parent_quiz_id IN ($quizIDList)
				" );

				// Update usage count for questions
				if ( ! empty( $questionIDList ) ) {
					foreach ( $questionIDList as $questionID ) {
						WPCW_questions_updateUsageCount( $questionID );
					}
				}
			}

			// Quizzes themselves
			$SQL = $wpdb->prepare( "DELETE FROM $wpcwdb->quiz WHERE parent_course_id = %d", $courseDetails->course_id );

			$wpdb->query( $SQL );

			if ( $unitList ) {
				// Reduce SQL queries - do an ARRAY search on the WHERE.
				$unitIDList = implode( ",", $unitList );

				// Unit Meta
				$wpdb->query( "
					DELETE FROM $wpcwdb->units_meta
					WHERE unit_id IN ($unitIDList)
				" );

				// User Progress
				$wpdb->query( "
					DELETE FROM $wpcwdb->user_progress
					WHERE unit_id IN ($unitIDList)
				" );

				// User Quiz Progress
				$wpdb->query( "
					DELETE FROM $wpcwdb->user_progress_quiz
					WHERE unit_id IN ($unitIDList)
				" );

				// Use WordPress delete for deleting the unit.
				foreach ( $unitList as $unitID ) {
					wp_delete_post( $unitID, true );
				}
			} // end unit check
			break;
	}

	// Module deletion here.
	$wpdb->query( $SQL = $wpdb->prepare( "
				DELETE FROM $wpcwdb->modules
				WHERE parent_course_id = %d
			", $courseDetails->course_id ) );

	// Certificate deletion for this course
	$wpdb->query( $SQL = $wpdb->prepare( "
				DELETE FROM $wpcwdb->certificates
				WHERE cert_course_id = %d
			", $courseDetails->course_id ) );

	// Perform course deletion here.
	$wpdb->query( $SQL = $wpdb->prepare( "
				DELETE FROM $wpcwdb->courses
				WHERE course_id = %d
			", $courseDetails->course_id ) );

	// Course progress summary for each user needs to be removed.
	$wpdb->query( $SQL = $wpdb->prepare( "
				DELETE FROM $wpcwdb->user_courses
				WHERE course_id = %d
			", $courseDetails->course_id ) );

	// Trigger event that course has been deleted
	do_action( 'wpcw_course_deleted', $courseDetails );

	return true;
}