<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package puro
 * @since puro 1.0
 * @license GPL 2.0
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function puro_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'render'    => 'puro_infinite_scroll_render',
		'footer'    => 'page',
		'posts_per_page' => 8
	) );

	/*
	 * Enable support for Responsive Videos.
	 * See: https://jetpack.com/support/responsive-videos/
	 */
	add_theme_support( 'jetpack-responsive-videos' );	
}
add_action( 'after_setup_theme', 'puro_jetpack_setup' );

if ( ! function_exists( 'puro_infinite_scroll_render' ) ) :
/**
 * Custom render function for Infinite Scroll.
 */
function puro_infinite_scroll_render() {
	if ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_woocommerce() ) ) {
		echo '<ul class="products">';
		while ( have_posts() ) {
			the_post();
			wc_get_template_part( 'content', 'product' );
		}
		echo '</ul>';
	} else {	
		while ( have_posts() ) {
			the_post();
			if ( is_search() ) :
				get_template_part( 'content', 'search' );
			else :
				get_template_part( 'content', get_post_format() );
			endif;
		}
	}
}
endif;