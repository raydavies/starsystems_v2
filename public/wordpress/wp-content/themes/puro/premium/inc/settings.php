<?php

/**
 * Setup all the premium settings.
 * 
 * @package puro
 * @since puro 1.0.3
 * @license GPL 2.0
 */
function puro_premium_theme_settings(){
	$settings = SiteOrigin_Settings::single();

	// Header
	$settings->add_field( 'logo', 'image_retina', 'media', __( 'Retina Logo', 'puro' ), array(
		'choose' => __( 'Choose Image', 'puro' ),
		'update' => __( 'Set Logo', 'puro' ),
		'description' => __( 'A double sized version of your logo for use on high pixel density displays. Must be used in addition to standard logo.', 'puro' ),
	) );		

	// Comments
	$settings->add_field('comments', 'ajax_comments', 'checkbox', __( 'AJAX Comments', 'puro' ), array(
		'description' => __( 'Allow users to submit comments without a page re-load on submit.', 'puro' ),
	) );

	// Social
	$settings->add_field('social', 'share_post', 'checkbox', __( 'Post Sharing', 'puro' ), array(
		'description' => __( 'Show icons to share your posts on Facebook, Twitter, Google+ and LinkedIn.', 'puro' ),
	) );

	$settings->add_field('social', 'share_page', 'checkbox', __( 'Page Sharing', 'puro' ), array(
		'description' => __( 'Show icons to share your pages on Facebook, Twitter, Google+ and LinkedIn.', 'puro' ),
	) );		

	$settings->add_field( 'social', 'twitter', 'text', __( 'Twitter Handle', 'puro' ), array(
		'description' => __( 'This handle will be recommended after a user shares one of your posts.', 'puro' ),
		'validator' => 'twitter',
	) );	

	// Footer
	$settings->add_field('footer', 'attribution', 'checkbox', __( 'Footer Attribution Link', 'puro' ), array(
		'description' => __( 'Remove the theme attribution link from your footer without editing any code.', 'puro' ),
	) );	
}
add_action( 'siteorigin_settings_init', 'puro_premium_theme_settings', 15 );
