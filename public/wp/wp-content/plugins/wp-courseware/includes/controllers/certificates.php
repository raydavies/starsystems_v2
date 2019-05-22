<?php
/**
 * WP Courseware Certificates Controller.
 *
 * @package WPCW
 * @subpackage Controllers
 * @since 4.3.0
 */
namespace WPCW\Controllers;

// Exit if accessed directly
defined( 'ABSPATH' ) || die;

/**
 * Class Certificates.
 *
 * @since 4.3.0
 */
class Certificates extends Controller {

	/**
	 * Load Certificates.
	 *
	 * @since 4.3.0
	 */
	public function load() { /* Do nothing yet */ }

	/**
	 * Get Certificate Settings Fields.
	 *
	 * @since 4.3.0
	 *
	 * @return array The certificates settings fields.
	 */
	public function get_settings_fields() {
		return apply_filters( 'wpcw_certificate_settings_feilds', array(
			array(
				'type'    => 'hidden',
				'key'     => 'cert_signature_type',
				'default' => 'text',
			),
			array(
				'type'    => 'hidden',
				'key'     => 'cert_sig_text',
				'default' => esc_attr( get_bloginfo( 'name' ) ),
			),
			array(
				'type'    => 'hidden',
				'key'     => 'cert_sig_image_url',
				'default' => '',
			),
			array(
				'type'    => 'hidden',
				'key'     => 'cert_logo_enabled',
				'default' => 'no_cert_logo',
			),
			array(
				'type'    => 'hidden',
				'key'     => 'cert_logo_url',
				'default' => '',
			),
			array(
				'type'    => 'hidden',
				'key'     => 'cert_background_type',
				'default' => 'use_default',
			),
			array(
				'type'    => 'hidden',
				'key'     => 'cert_background_custom_url',
				'default' => '',
			),
			array(
				'type'    => 'hidden',
				'key'     => 'certificate_encoding',
				'default' => 'ISO-8859-1',
			),
		) );
	}
}