<?php
/**
 * WP Courseware Unit Converter Page.
 *
 * @package WPCW
 * @subpackage Admin\Pages
 * @since 4.1.0
 */
namespace WPCW\Admin\Pages;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class Page_Unit_Converter.
 *
 * @since 4.1.0
 */
class Page_Unit_Converter extends Page {

	/**
	 * Get Unit Converter Menu Title.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public function get_menu_title() {
		return esc_html__( 'Convert to Course Unit', 'wp-courseware' );
	}

	/**
	 * Get Unit Converter Page Title.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public function get_page_title() {
		return esc_html__( 'Convert to Course Unit', 'wp-courseware' );
	}

	/**
	 * Get Unit Converter Page Capability.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public function get_capability() {
		return apply_filters( 'wpcw_admin_page_units_capability', 'view_wpcw_courses' );
	}

	/**
	 * Get Unit Converter Page Slug.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public function get_slug() {
		return 'WPCW_showPage_ConvertPage';
	}

	/**
	 * Get Unit Converter Page Callback.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	protected function get_callback() {
		return 'WPCW_showPage_ConvertPage';
	}

	/**
	 * Is Unit Converter Page Hidden?
	 *
	 * @since 4.1.0
	 *
	 * @return bool
	 */
	public function is_hidden() {
		return true;
	}
}