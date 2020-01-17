<?php
/**
 * WP Courseware Notices.
 *
 * @package WPCW
 * @subpackage Functions
 * @since 4.3.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Notice Count.
 *
 * @since 4.3.0
 *
 * @param string $notice_type Optional. The name of the notice type - either error, success or notice.
 *
 * @return int The number of notices.
 */
function wpcw_notice_count( $notice_type = '' ) {
	if ( ! did_action( 'wpcw_init' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before wpcw_init.', 'wp-courseware' ), '4.3.0' );
		return;
	}

	$notice_count = 0;
	$all_notices  = wpcw()->session->get( 'wpcw_notices', array() );

	if ( isset( $all_notices[ $notice_type ] ) ) {
		$notice_count = count( $all_notices[ $notice_type ] );
	} elseif ( empty( $notice_type ) ) {
		foreach ( $all_notices as $notices ) {
			$notice_count += count( $notices );
		}
	}

	return $notice_count;
}

/**
 * Check if a notice has already been added.
 *
 * @since 4.3.0
 *
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The name of the notice type - either error, success or notice.
 *
 * @return bool True if the notice is found.
 */
function wpcw_has_notice( $message, $notice_type = 'success' ) {
	if ( ! did_action( 'wpcw_init' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before wpcw_init.', 'wp-courseware' ), '4.3.0' );
		return false;
	}

	$notices = wpcw()->session->get( 'wpcw_notices', array() );
	$notices = isset( $notices[ $notice_type ] ) ? $notices[ $notice_type ] : array();

	return array_search( $message, $notices, true ) !== false;
}

/**
 * Add Notice.
 *
 * @since 4.3.0
 *
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The name of the notice type - either error, success or notice.
 */
function wpcw_add_notice( $message, $notice_type = 'success' ) {
	if ( ! did_action( 'wpcw_init' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before wpcw_init.', 'wp-courseware' ), '4.3.0' );
		return;
	}

	$notices = wpcw()->session->get( 'wpcw_notices', array() );

	$notices[ $notice_type ][] = apply_filters( 'wpcw_notices_add_' . $notice_type, $message );

	wpcw()->session->set( 'wpcw_notices', $notices );
}

/**
 * Set All Notices.
 *
 * @since 4.3.0
 *
 * @param mixed $notices Array of notices.
 */
function wpcw_set_notices( $notices ) {
	if ( ! did_action( 'wpcw_init' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before wpcw_init.', 'wp-courseware' ), '4.3.0' );
		return;
	}

	wpcw()->session->set( 'wpcw_notices', $notices );
}

/**
 * Clear All Notices.
 *
 * @since 4.3.0
 */
function wpcw_clear_notices() {
	if ( ! did_action( 'wpcw_init' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before wpcw_init.', 'wp-courseware' ), '4.3.0' );
		return;
	}

	wpcw()->session->set( 'wpcw_notices', null );
}

/**
 * Print All Notices.
 *
 * Prints messages and errors which are
 * stored in the session, then clears them.
 *
 * @since 4.3.0
 */
function wpcw_print_notices() {
	if ( ! did_action( 'wpcw_init' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before wpcw_init.', 'wp-courseware' ), '4.3.0' );
		return;
	}

	$all_notices  = wpcw()->session->get( 'wpcw_notices', array() );
	$notice_types = apply_filters( 'wpcw_notice_types', array( 'error', 'success', 'info' ) );

	foreach ( $notice_types as $notice_type ) {
		if ( wpcw_notice_count( $notice_type ) > 0 ) {
			wpcw_get_template( "notices/{$notice_type}.php", array(
				'messages' => array_filter( $all_notices[ $notice_type ] ),
			) );
		}
	}

	wpcw_clear_notices();
}

/**
 * Print a Notice.
 *
 * @since 4.3.0
 *
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 */
function wpcw_print_notice( $message, $notice_type = 'success' ) {
	wpcw_get_template( "notices/{$notice_type}.php", array(
		'messages' => array( apply_filters( 'wpcw_add_' . $notice_type, $message ) ),
	) );
}

/**
 * Get a Notice.
 *
 * @since 4.3.0
 *
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 *
 * @return string The notice.
 */
function wpcw_get_notice( $message, $notice_type = 'success' ) {
	ob_start();
	wpcw_print_notice( $message, $notice_type );
	return ob_get_clean();
}

/**
 * Get All Notices.
 *
 * @since 4.3.0
 *
 * @param string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 *
 * @return array|mixed
 */
function wpcw_get_notices( $notice_type = '' ) {
	if ( ! did_action( 'wpcw_init' ) ) {
		wpcw_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before wpcw_init.', 'wp-courseware' ), '4.3.0' );
		return;
	}

	$all_notices = wpcw()->session->get( 'wpcw_notices', array() );

	if ( empty( $notice_type ) ) {
		$notices = $all_notices;
	} elseif ( isset( $all_notices[ $notice_type ] ) ) {
		$notices = $all_notices[ $notice_type ];
	} else {
		$notices = array();
	}

	return $notices;
}

/**
 * Add notices for WP Errors.
 *
 * @since 4.3.0
 *
 * @param WP_Error $errors Errors.
 */
function wpcw_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			wpcw_add_notice( $error, 'error' );
		}
	}
}
