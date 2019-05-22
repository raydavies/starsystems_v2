<?php
/**
 * WP Courseware Units Functions.
 *
 * @package WPCW
 * @subpackage Functions
 * @since 4.3.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Get Unit.
 *
 * @since 4.4.0
 *
 * @param int|bool $unit_id The Unit Id.
 *
 * @return \WPCW\Models\Unit|bool An unit object or false.
 */
function wpcw_get_unit( $unit_id = false ) {
	if ( ! did_action( 'wpcw_loaded' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, 'wpcw_get_unit should not be called before the unit object is setup.', '4.4.0' );

		return false;
	}

	return new \WPCW\Models\Unit( $unit_id );
}

/**
 * Insert Unit.
 *
 * @since 4.4.0
 *
 * @param array $data The unit data.
 *
 * @return \WPCW\Models\Unit|bool The unit object or false on failure.
 */
function wpcw_insert_unit( $data = array() ) {
	if ( ! did_action( 'wpcw_loaded' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, 'wpcw_insert_unit should not be called before the unit object is setup.', '4.4.0' );

		return false;
	}

	$unit    = new \WPCW\Models\Unit();
	$unit_id = $unit->create( $data );

	return $unit_id ? $unit : $unit_id;
}

/**
 * Get Units.
 *
 * @since 4.4.0
 *
 * @param array $args The courses query args.
 *
 * @return array The array of Course objects.
 */
function wpcw_get_units( $args = array() ) {
	return wpcw()->units->get_units( $args );
}

/**
 * Can Student Access Unit?
 *
 * @since 4.3.0
 *
 * @param int $unit_id The unit id.
 * @param int $student_id The student id.
 *
 * @return bool True if the student can access unit.
 */
function wpcw_can_student_access_unit( $unit_id, $student_id = 0 ) {
	$can_access = false;

	if ( ! $unit_id ) {
		return $can_access;
	}

	$unit_data = WPCW_units_getUnitMetaData( $unit_id );

	if ( empty( $unit_data ) || empty( $unit_data->parent_course_id ) ) {
		return $can_access;
	}

	if ( $unit_data->unit_teaser ) {
		return true;
	}

	if ( ! $student_id && ! is_user_logged_in() ) {
		return $can_access;
	}

	if ( ! $student_id ) {
		$student_id = get_current_user_id();
	}

	// Admin and Teachers Capability.
	$admins_can_access   = user_can( $student_id, apply_filters( 'wpcw_units_accessible_admin_capability', 'manage_wpcw_settings' ) );
	$teachers_can_access = user_can( $student_id, apply_filters( 'wpcw_units_accessible_minimum_capability', 'view_wpcw_courses' ) );

	// If teachers and not admins, we need to check for authorship.
	if ( ! $admins_can_access && $teachers_can_access ) {
		$unit_post = get_post( $unit_id );
		if ( absint( $student_id ) !== absint( $unit_post->post_author ) ) {
			$teachers_can_access = false;
		}
	}

	// Admin and Teachers Access.
	if ( $admins_can_access || $teachers_can_access ) {
		$can_access = true;
	}

	if ( WPCW_courses_canUserAccessCourse( $unit_data->parent_course_id, $student_id ) ) {
		$can_access = true;
	}

	return $can_access;
}

/**
 * Is Unit Admin or Teacher?
 *
 * @since 4.6.0
 *
 * @param int $unit_id The unit id.
 * @param int $user_id The user id.
 *
 * @return bool $is_admin_or_teacher True if the user is a unit admin or teacher.
 */
function wpcw_is_unit_admin_or_teacher( $unit_id_or_object, $user_id = 0 ) {
	$is_admin_or_teacher = false;

	if ( ! $unit_id_or_object ) {
		return $is_admin_or_teacher;
	}

	if ( ! $user_id && ! is_user_logged_in() ) {
		return $is_admin_or_teacher;
	}

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	/**
	 * Filter: Disable Admin or Teacher Check.
	 *
	 * @since 4.6.0
	 *
	 * @param bool True or False to disable the is admin or teacher check. Default is false.
	 *
	 * @return bool True or False to disable the is admin or teacher check.
	 */
	if ( apply_filters( 'wpcw_disable_is_admin_or_teacher_check', false ) ) {
		return $is_admin_or_teacher;
	}

	// Admin and Teachers Capability.
	$is_admin   = user_can( $user_id, apply_filters( 'wpcw_units_accessible_admin_capability', 'manage_wpcw_settings' ) );
	$is_teacher = user_can( $user_id, apply_filters( 'wpcw_units_accessible_minimum_capability', 'view_wpcw_courses' ) );

	// If is an admin or teacher.
	if ( $is_admin || $is_teacher ) {
		$is_admin_or_teacher = true;
	}

	// If teachers and not admins, we need to check for authorship.
	if ( ! $is_admin && $is_teacher ) {
		if ( is_object( $unit_id_or_object ) && ! empty( $unit_id_or_object->post_author ) ) {
			$unit_author = $unit_id_or_object->post_author;
		} else {
			$unit_post   = get_post( $unit_id_or_object );
			$unit_author = $unit_post->post_author;
		}

		if ( absint( $user_id ) === absint( $unit_author ) ) {
			$is_admin_or_teacher = false;
		}
	}

	return $is_admin_or_teacher;
}

/**
 * Get Unit Label.
 *
 * @since 4.4.4
 *
 * @param bool $plural True if plural is needed.
 *
 * @return string The unit label.
 */
function wpcw_get_unit_label( $plural = false ) {
	$unit_label_setting = wpcw_get_setting( 'unit_label', 'unit' );

	$default   = esc_html__( 'Unit', 'wp-courseware' );
	$default_p = esc_html__( 'Units', 'wp-courseware' );

	$unit_label   = $default;
	$unit_label_p = $default_p;

	switch ( $unit_label_setting ) {
		case 'lesson':
			$unit_label   = esc_html__( 'Lesson', 'wp-courseware' );
			$unit_label_p = esc_html__( 'Lessons', 'wp-courseware' );
			break;
		case 'lecture':
			$unit_label   = esc_html__( 'Lecture', 'wp-courseware' );
			$unit_label_p = esc_html__( 'Lectures', 'wp-courseware' );
			break;
	}

	if ( 'custom' === $unit_label_setting ) {
		$unit_label   = wpcw_get_setting( 'unit_label_custom', $unit_label );
		$unit_label_p = wpcw_get_setting( 'unit_label_custom_plural', $unit_label_p );
	}

	return $plural ? $unit_label_p : $unit_label;
}

/**
 * Convert Unit Drip Interval.
 *
 * @since 4.6.0
 *
 * @param int    $interval
 * @param string $type
 *
 * @return int
 */
function wpcw_unit_convert_drip_interval( $interval, $type = 'interval_days' ) {
	$drip_interval = 0;

	switch ( $type ) {
		case 'interval_hours':
			$drip_interval = $interval * WPCW_TIME_HR_IN_SECS;
			break;

		case 'interval_days':
			$drip_interval = $interval * WPCW_TIME_DAY_IN_SECS;
			break;

		case 'interval_weeks':
			$drip_interval = $interval * WPCW_TIME_WEEK_IN_SECS;
			break;

		case 'interval_months':
			$drip_interval = $interval * WPCW_TIME_MONTH_IN_SECS;
			break;

		case 'interval_years':
			$drip_interval = $interval * WPCW_TIME_YEAR_IN_SECS;
			break;
	}

	return absint( $drip_interval );
}
