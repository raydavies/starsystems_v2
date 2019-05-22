<?php

function puro_customizer_init(){

	$sections = apply_filters( 'puro_customizer_sections', array(
		'puro_general' => array(
			'title' => __('General', 'puro'),
			'priority' => 30,
		),		

		'puro_colors' => array(
			'title' => __('Colors', 'puro'),
			'priority' => 40,
		),

		'puro_header' => array(
			'title' => __('Header', 'puro'),
			'priority' => 50,
		),		

		'puro_menu' => array(
			'title' => __('Menu', 'puro'),
			'priority' => 60,
		),				

		'puro_fonts' => array(
			'title' => __('Fonts', 'puro'),
			'priority' => 70,
		),						

		'puro_sidebar' => array(
			'title' => __('Sidebar', 'puro'),
			'priority' => 80,
		),

		'puro_footer' => array(
			'title' => __('Footer', 'puro'),
			'priority' => 90,
		),						

	) );

	$settings = apply_filters( 'puro_premium_customizer_settings', array(

		'puro_general' => array(

			'site_width' => array(
				'type' => 'measurement',
				'title' => __('Site Max Width (px)', 'puro'),
				'callback' => 'puro_change_site_width',
				'default' => 1080,
			),	

		),

		'puro_colors' => array(

			'accent_color' => array(
				'type' => 'color',
				'title' => __('Accent Color', 'puro'),
				'default' => '#2ac176',
				'callback' => 'puro_accent_color',
			),

			'site_title_color' => array(
				'type' => 'color',
				'title' => __('Site Title Color', 'puro'),
				'default' => '#333333',
				'selector' => '.site-header, .site-branding h1.site-title, .menu-toggle',  
				'property' => 'color',
			),

			'site_tagline_color' => array(
				'type' => 'color',
				'title' => __('Site Tagline Color', 'puro'),
				'default' => '#999999',
				'selector' => '.site-header .site-branding h2.site-description',
				'property' => 'color',
			),	

			'heading_color' => array(
				'type' => 'color',
				'title' => __('Heading Color', 'puro'),
				'default' => '#333333',
				'selector' => '.site-content h1, .site-footer h1, .site-content h2, .site-content h3, .site-footer h3, .site-content h4, .site-footer h4, .site-content h5, .site-footer h5, .site-content h6, .site-footer h6, .entry-header h1.entry-title a, #secondary .widget h3.widget-title a, #colophon .widget h3.widget-title a, #infinite-footer .blog-info a',				
				'property' => 'color',
			),	

			'text_color' => array(
				'type' => 'color',
				'title' => __('Text Color', 'puro'),
				'default' => '#666666',
				'callback' => 'puro_text_color',
			),	

			'background_color' => array(
				'type' => 'color',
				'title' => __('Background Color', 'puro'),
				'default' => '#ffffff',
				'selector' => 'body',
				'property' => 'background',
			),		

			'border_color' => array(
				'type' => 'color',
				'title' => __('Border Color', 'puro'),
				'default' => '#e5e5e5',
				'callback' => 'puro_border_color',		
			),	

			'form_field_color' => array(
				'type' => 'color',
				'title' => __('Form Field Color', 'puro'),
				'default' => '#ffffff',
				'selector' => 'input[type="text"], input[type="email"], input[type="url"], input[type="tel"], input[type="number"], input[type="date"], input[type="password"], input[type="search"], select[multiple], textarea',	
				'property' => 'background',
			),																															

		),

		'puro_header' => array(

			'site_title_font' => array(
				'type' => 'font',
				'title' => __('Site Title Font', 'puro'),
				'default' => 'Arial',
				'selector' => '.site-header .site-branding h1.site-title',
			),	

			'site_title_font_size' => array(
				'type' => 'measurement',
				'title' => __('Site Title Font Size (em)', 'puro'),
				'default' => 2.5625,
				'unit' => 'em',
				'selector' => '.site-header .site-branding h1.site-title',
				'property' => 'font-size',
			),			

			'site_tagline_font' => array(
				'type' => 'font',
				'title' => __('Site Tagline Font', 'puro'),
				'default' => 'Arial',
				'selector' => '.site-header .site-branding h2.site-description',
			),

			'site_tagline_font_size' => array(
				'type' => 'measurement',
				'title' => __('Site Tagline Font Size (em)', 'puro'),
				'default' => 1,
				'unit' => 'em',
				'selector' => '.site-header .site-branding h2.site-description',
				'property' => 'font-size',
			),	

			'header_padding' => array(
				'type' => 'measurement',
				'title' => __('Header Padding (em)', 'puro'),
				'default' => 2.5313,
				'unit' => 'em',
				'selector' => '.site-header',
				'property' => array('padding-top', 'padding-bottom'),
			),												

		),	

		'puro_menu' => array(

			'menu_text_color' => array(
				'type' => 'color',
				'title' => __('Text Color', 'puro'),
				'default' => '#666666',
				'selector' => '.main-navigation a',
				'property' => 'color',
			),	

			'menu_text_hover_color' => array(
				'type' => 'color',
				'title' => __('Text Hover Color', 'puro'),
				'default' => '#2ac176',
				'selector' => '.main-navigation ul li:hover > a',
				'property' => 'color',
				'no_live' => true,
			),		

			'menu_current_page_color' => array(
				'type' => 'color',
				'title' => __('Current Page Color', 'puro'),
				'default' => '#2ac176',
				'selector' => '.main-navigation .current_page_item > a, .main-navigation .current-menu-item > a', 
				'property' => 'color',
			),									

			'menu_drop_down_background' => array(
				'type' => 'color',
				'title' => __('Drop Down Background', 'puro'),
				'default' => '#f2f2f2',
				'selector' => '.main-navigation ul ul li, .responsive-menu .main-navigation.toggled ul li a, .responsive-menu .main-navigation.toggled ul ul li:hover > a', 
				'property' => 'background',	
			),	

			'menu_drop_down_text_color' => array(
				'type' => 'color',
				'title' => __('Drop Down Text Color', 'puro'),
				'default' => '#666666',
				'selector' => '.main-navigation ul ul li > a, .responsive-menu .main-navigation.toggled ul li a, .responsive-menu .main-navigation.toggled ul ul li:hover > a, .responsive-menu .main-navigation.toggled ul > li[class*=children] > a ~ span',
				'property' => 'color',
			),		

			'menu_drop_down_hover_background' => array(
				'type' => 'color',
				'title' => __('Drop Down Hover Background', 'puro'),
				'default' => '#2ac176',
				'selector' => '.main-navigation ul ul li:hover > a, .responsive-menu .main-navigation.toggled ul li a:hover, .responsive-menu .main-navigation.toggled ul ul li a:hover', 
				'property' => 'background',
				'no_live' => true,
			),	

			'menu_drop_down_hover_text_color' => array(
				'type' => 'color',
				'title' => __('Drop Down Hover Text Color', 'puro'),
				'default' => '#ffffff',
				'selector' => '.main-navigation ul ul li:hover > a, .responsive-menu .main-navigation.toggled ul li a:hover, .responsive-menu .main-navigation.toggled ul ul li a:hover, .responsive-menu .main-navigation.toggled ul > li[class*=children] > a:hover ~ span', 
				'property' => 'color',
				'no_live' => true,
			),	

			'menu_font' => array(
				'type' => 'font',
				'title' => __('Font', 'puro'),
				'default' => 'Helvetica Neue',
				'selector' => '.main-navigation',
			),			

			'menu_font_size' => array(
				'type' => 'measurement',
				'title' => __('Font Size (em)', 'puro'),
				'default' => 0.875,
				'unit' => 'em',
				'selector' => '.main-navigation',
				'property' => 'font-size',
			),													

		),	

		'puro_fonts' => array(							

			'heading_font' => array(
				'type' => 'font',
				'title' => __('Heading Font', 'puro'),
				'default' => 'Arial',
				'selector' => 'h1, h2, h3, h4, h5, h6',
			),	

			'heading_font_size' => array(
				'type' => 'measurement',
				'title' => __('Heading Font Size (em)', 'puro'),
				'default' => 2,
				'unit' => 'em',
				'selector' => '.entry-header h1.entry-title',
				'property' => 'font-size',
			),						

			'body_font' => array(
				'type' => 'font',
				'title' => __('Body Font', 'puro'),
				'default' => 'Helvetica Neue',
				'selector' => 'body, button, input, select, textarea', 
			),

			'body_font_size' => array(
				'type' => 'measurement',
				'title' => __('Body Font Size (em)', 'puro'),
				'default' => 1,
				'unit' => 'em',
				'selector' => '#primary .entry-content label, #primary .entry-content button, #primary .entry-content input, #primary .entry-content select, #primary .entry-content textarea, #primary .entry-content p, #primary .entry-content > ul, #primary .entry-content > ol, #primary .entry-content > table, #primary .entry-content > dl, #primary .entry-content address, #primary .entry-content pre, #primary .paging-navigation, #primary .page-links', 
				'property' => 'font-size',
			),	

			'post_meta_font_size' => array(
				'type' => 'measurement',
				'title' => __('Post Meta Font Size (em)', 'puro'),
				'default' => 0.875,
				'unit' => 'em',
				'selector' => '.entry-header .entry-meta, .entry-footer', 
				'property' => 'font-size',
			),	

			'content_heading_one_size' => array(
				'type' => 'measurement',
				'title' => __('Content H1 Font Size (em)', 'puro'),
				'default' => 1.8125,
				'unit' => 'em',
				'selector' => '#primary .entry-content h1',
				'property' => 'font-size',
			),	

			'content_heading_two_size' => array(
				'type' => 'measurement',
				'title' => __('Content H2 Font Size (em)', 'puro'),
				'default' => 1.625,
				'unit' => 'em',
				'selector' => '#primary .entry-content h2',
				'property' => 'font-size',
			),	

			'content_heading_three_size' => array(
				'type' => 'measurement',
				'title' => __('Content H3 Font Size (em)', 'puro'),
				'default' => 1.4375,
				'unit' => 'em',
				'selector' => '#primary .entry-content h3',
				'property' => 'font-size',
			),	

			'content_heading_four_size' => array(
				'type' => 'measurement',
				'title' => __('Content H4 Font Size (em)', 'puro'),
				'default' => 1.25,
				'unit' => 'em',
				'selector' => '#primary .entry-content h4',
				'property' => 'font-size',
			),	

			'content_heading_five_size' => array(
				'type' => 'measurement',
				'title' => __('Content H5 Font Size (em)', 'puro'),
				'default' => 1.125,
				'unit' => 'em',
				'selector' => '#primary .entry-content h5',
				'property' => 'font-size',
			),	

			'content_heading_six_size' => array(
				'type' => 'measurement',
				'title' => __('Content H6 Font Size (em)', 'puro'),
				'default' => 1,
				'unit' => 'em',
				'selector' => '#primary .entry-content h6',
				'property' => 'font-size',
			),																			

		),		

		'puro_sidebar' => array(

			'sidebar_positon' => array(
				'type' => 'select',
				'title' => __('Position', 'puro'),
				'default' => 'right',
				'choices' => array(
					'right' => __('Right', 'puro'),
					'left' => __('Left', 'puro'),
				),
			),

			'sidebar_heading_color' => array(
				'type' => 'color',
				'title' => __('Heading Color', 'puro'),
				'default' => '#333333',
				'selector' => '#secondary .widget h3.widget-title',
				'property' => 'color',
			),				

			'sidebar_heading_size' => array(
				'type' => 'measurement',
				'title' => __('Heading Size (em)', 'puro'),
				'default' => 1,
				'unit' => 'em',
				'selector' => '#secondary .widget h3.widget-title',
				'property' => 'font-size',
			),

			'sidebar_text_color' => array(
				'type' => 'color',
				'title' => __('Text Color', 'puro'),
				'default' => '#666666',
				'selector' => '#secondary .widget',
				'property' => 'color',
			),	

			'sidebar_text_size' => array(
				'type' => 'measurement',
				'title' => __('Text Size (em)', 'puro'),
				'default' => 0.875,
				'unit' => 'em',
				'selector' => '#secondary .widget h3.widget-title ~ *',
				'property' => 'font-size',
			),									

		),

		'puro_footer' => array(

			'footer_heading_color' => array(
				'type' => 'color',
				'title' => __('Heading Color', 'puro'),
				'default' => '#333333',
				'selector' => '#colophon .widget h3.widget-title',
				'property' => 'color',
			),				

			'footer_heading_size' => array(
				'type' => 'measurement',
				'title' => __('Heading Size (em)', 'puro'),
				'default' => 1,
				'unit' => 'em',
				'selector' => '#colophon .widget h3.widget-title',
				'property' => 'font-size',
			),

			'footer_text_color' => array(
				'type' => 'color',
				'title' => __('Text Color', 'puro'),
				'default' => '#666666',
				'selector' => '#colophon .widget',
				'property' => 'color',
			),	

			'footer_text_size' => array(
				'type' => 'measurement',
				'title' => __('Text Size (em)', 'puro'),
				'default' => 0.875,
				'unit' => 'em',
				'selector' => '#colophon .widget h3.widget-title ~ *',
				'property' => 'font-size',
			),

			'footer_padding' => array(
				'type' => 'measurement',
				'title' => __('Footer Padding (em)', 'puro'),
				'default' => 3.375,
				'unit' => 'em',
				'selector' => '.site-footer',
				'property' => array('padding-top', 'padding-bottom'),
			),														

		),								
		
	) );

	// Include all the SiteOrigin customizer classes
	global $siteorigin_puro_customizer;
	$siteorigin_puro_customizer = new SiteOrigin_Customizer_Helper($settings, $sections, 'puro');
}
add_action('init', 'puro_customizer_init');

/**
 * @param WP_Customize_Manager $wp_customize
 */
function puro_customizer_register($wp_customize){
	global $siteorigin_puro_customizer;
	$siteorigin_puro_customizer->customize_register($wp_customize);
}
add_action( 'customize_register', 'puro_customizer_register', 15 );

/**
 * @param SiteOrigin_Customizer_CSS_Builder $builder
 * @param mixed $val
 * @param array $setting
 */
function puro_change_site_width( $builder, $val, $setting ) {

	if( empty($val) ) return;

	switch ($setting['id']) {
		case 'puro_general_site_width' :
			if( $val != 1080 ) {
				$builder->add_raw_css('#page{ max-width:'.intval($val).'px; }');
			}
			break;
	}
}

/**
 * @param SiteOrigin_Customizer_CSS_Builder $builder
 * @param mixed $val
 * @param array $setting
 */
function puro_accent_color($builder, $val, $setting){
	if(!empty($val) && $val != '#2ac176') {
		$accent_as_rgb = $builder->hex2rgb( esc_html($val) );
		$accent_darker = $builder->adjustBrightness( esc_html($val), -50 );
		$builder->add_raw_css( ' a, #secondary .widget h3.widget-title a:hover, #colophon .widget h3.widget-title a:hover, .entry-header h1.entry-title a:hover, .entry-header .entry-meta a:hover, .entry-footer .edit-link a:hover, .comment-metadata a:hover, .site-footer .site-info a:hover, .site-footer .theme-attribution a:hover, .social-links-menu ul li a:hover:before, #infinite-footer .blog-info a:hover, #infinite-footer .blog-credits a:hover { color: '.esc_html($val).'; }');
		$builder->add_raw_css( ' blockquote, abbr, acronym { border-color: '.esc_html($val).'; }');
		$builder->add_raw_css( ' .paging-navigation .page-numbers:hover, .paging-navigation .current, .page-links span, .page-links a span:hover, .wp-pagenavi a:hover { border-color: '.esc_html($val).'; color: '.esc_html($val).'; }');		
		$builder->add_raw_css( ' ::selection { background: '.esc_html($val).'; }');
		$builder->add_raw_css( ' button, input[type="button"], input[type="reset"], input[type="submit"], #infinite-handle span { background: rgba('.$accent_as_rgb.', 0.8); box-shadow: 0 1px 0 '.$accent_darker.'; }');
		$builder->add_raw_css( ' button:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover, #infinite-handle span:hover { background: rgba('.$accent_as_rgb.', 1); }');
	}

	return $builder;
}

/**
 * @param SiteOrigin_Customizer_CSS_Builder $builder
 * @param mixed $val
 * @param array $setting
 */
function puro_text_color($builder, $val, $setting){
	if(!empty($val) && $val != '#666666') {
		$color_lighter = $builder->adjustBrightness( esc_html($val), 51.5 );
		$builder->add_raw_css('body, button, input, select, textarea, .entry-footer .edit-link a, .page-links .page-links-title, #infinite-footer .blog-credits, #infinite-footer .blog-credits a, .wp-pagenavi a, .wp-pagenavi span { color: '.esc_html($val).'; }');
		$builder->add_raw_css('::-webkit-input-placeholder { color: '.esc_html($val).'; }');
		$builder->add_raw_css('.entry-header .entry-meta, .entry-header .entry-meta a, .entry-footer, .page-links a span, .comments-area .comment-metadata, .comments-area .comment-metadata a, .site-footer .site-info, .site-footer .theme-attribution, .site-footer .site-info a, .site-footer .theme-attribution a, div[id*="contact-form"] div label span, .paging-navigation .page-numbers, .menu-social-container ul li a:before { color: '.$color_lighter.'; }');
	}

	return $builder;
}

/**
 * @param SiteOrigin_Customizer_CSS_Builder $builder
 * @param mixed $val
 * @param array $setting
 */
function puro_border_color($builder, $val, $setting){
	if(!empty($val) && $val != '#e5e5e5') {
		$color_darker = $builder->adjustBrightness( esc_html($val), -26 );
		$builder->add_raw_css('hr, table, table th, table td, article, .page-links a span, .paging-navigation .page-numbers, .sidebar #primary, #secondary, .comments-area .comments-title, .comments-area .comment-respond, .site-header, .site-footer, input[type="text"], input[type="email"], input[type="url"], input[type="tel"], input[type="number"], input[type="date"], input[type="password"], input[type="search"], select[multiple], textarea, .infinite-wrap, #infinite-footer .container, .wp-pagenavi a, .wp-pagenavi span { border-color: '.esc_html($val).'; }');
		$builder->add_raw_css('input[type="text"]:focus, input[type="email"]:focus, input[type="url"]:focus, input[type="tel"]:focus, input[type="number"]:focus, input[type="date"]:focus, input[type="password"]:focus, input[type="search"]:focus, select[multiple]:focus, textarea:focus { border-color: '.$color_darker.'; }');
	}

	return $builder;
}

/**
 * Sidebar body class.
 */
function puro_customizer_change_body_class( $classes ) {
	$sidebar_position = get_theme_mod( 'puro_sidebar_sidebar_positon' );
	if ( ! empty ( $sidebar_position ) ) {
		$classes[] = 'sidebar-position-' . sanitize_html_class( $sidebar_position );
	}
	return $classes;
}
add_filter( 'body_class', 'puro_customizer_change_body_class' );

/**
 * Display the styles.
 */
function puro_customizer_style() {
	global $siteorigin_puro_customizer;
	if(empty($siteorigin_puro_customizer)) return;
	
	$builder = $siteorigin_puro_customizer->create_css_builder();

	// Add any extra CSS customizations
	echo $builder->css();
}
add_action('wp_head', 'puro_customizer_style', 20);