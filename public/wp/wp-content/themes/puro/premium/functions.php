<?php
/**
 * Puro Premium functions and definitions.
 *
 * @package puro
 * @since puro 1.3
 * @license GPL 2.0
 */

define( 'SITEORIGIN_IS_PREMIUM', true );

// Include all the premium extras
include get_template_directory() . '/premium/extras/ajax-comments/ajax-comments.php';
include get_template_directory() . '/premium/extras/share/share.php';

// Theme specific files
include get_template_directory() . '/premium/inc/settings.php';
include get_template_directory() . '/premium/inc/customizer.php';

function puro_premium_setup(){
	if ( siteorigin_setting( 'comments_ajax_comments' ) ) siteorigin_ajax_comments_activate();
	if ( siteorigin_setting( 'social_share_post' ) ) siteorigin_share_activate();
}
add_action( 'after_setup_theme', 'puro_premium_setup', 15 );

/**
 * Set the Retina logo.
 */
function puro_premium_logo_retina( $attr ) {
	$logo = siteorigin_setting( 'header_image_retina' );
	if ( $logo ) {
		$image = wp_get_attachment_image_src( $logo, 'full' );

		// Ignore empty images
		if ( empty( $image ) ) return $attr;
		list ( $src, $height, $width ) = $image;

		$attr['data-retina-image'] = $src;
	}

	return $attr;
}
add_filter( 'puro_logo_image_attributes', 'puro_premium_logo_retina' );

if ( ! function_exists( 'puro_premium_show_social_share' ) ) :
/**
 * Show the social share icons on posts.
 */
function puro_premium_show_social_share() {
	if ( siteorigin_setting( 'social_share_post' ) && is_single() ) {
		siteorigin_share_render( array(
			'twitter' => siteorigin_setting( 'social_twitter' ),
		) );
	}
}
add_action( 'puro_entry_main_bottom', 'puro_premium_show_social_share' );
endif;

if ( ! function_exists( 'puro_premium_show_page_social_share' ) ) :
/**
 * Show the social share icons on pages.
 */
function puro_premium_show_page_social_share() {
	$is_wc_shop = puro_is_woocommerce_active() && ( is_woocommerce() || is_shop() || is_cart() || is_checkout() || is_account_page() );
	if ( siteorigin_setting( 'social_share_page' ) && is_page() && ! $is_wc_shop ) {
		siteorigin_share_render( array(
			'twitter' => siteorigin_setting( 'social_twitter' ),
		) );
	}
}
add_action( 'puro_entry_main_bottom', 'puro_premium_show_page_social_share' );
endif;

/**
 * Handle the new settings teaser field.
 */
class SiteOrigin_Theme_Premium {

	function __construct(){
		if ( ! class_exists( 'SiteOrigin_Premium_Manager' ) ) {
			add_filter( 'siteorigin_settings_display_teaser', '__return_false' );
			add_action( 'siteorigin_settings_add_teaser_field', array( $this, 'handle_teaser_field' ), 10, 6 );
		}
	}

	static function single(){
		static $single;
		if( empty( $single ) ) {
			$single = new self();
		}
		return $single;
	}

	/**
	 * Change the teaser field to display the full field
	 *
	 * @param $settings
	 * @param $section
	 * @param $id
	 * @param $type
	 * @param $label
	 * @param $args
	 */
	function handle_teaser_field( $settings, $section, $id, $type, $label, $args ){
		if( method_exists( $settings, 'add_field' ) ) {
			$settings->add_field( $section, $id, $type, $label, $args );
		}
	}

}

SiteOrigin_Theme_Premium::single();