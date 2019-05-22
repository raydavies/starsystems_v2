<?php
/**
 * Plugin Name: WP Courseware
 * Plugin URI:  http://wpcourseware.com
 * Description: WordPress's leading Learning Management System (L.M.S.) plugin and is so simple you can create an online course in minutes.
 * Version:     4.6.0
 * Author:      Fly Plugins
 * Author URI:  http://flyplugins.com
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-courseware
 * Domain Path: /languages
 *
 * Copyright (c) 2018 Fly Plugins - Lighthouse Media, LLC (email : info@flyplugins.com)
 *
 * @package WPCW
 * @version 4.6.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Plugin Version
define( 'WPCW_VERSION', '4.6.0' );

// Plugin File
define( 'WPCW_FILE', __FILE__ );

// Plugin Path
defined( 'WPCW_PATH' ) || define( 'WPCW_PATH', plugin_dir_path( WPCW_FILE ) );

// Plugin Url
defined( 'WPCW_URL' ) || define( 'WPCW_URL', plugin_dir_url( WPCW_FILE ) );

// Requirements to run plugin
require_once WPCW_PATH . 'includes/common/requirements.php';

// Requirements check
if ( WPCW_Requirements::check() ) {
	require_once WPCW_PATH . 'includes/plugin.php';

	/**
	 * Main WP Courseware Function.
	 *
	 * The main function responsible for returning
	 * the singleton instance of \WPCW\Plugin.
	 *
	 * Example: <?php $wpcw = wpcw(); ?>
	 *
	 * @since 4.3.0
	 *
	 * @return WPCW_Plugin The WP Courseware plugin singleton instance.
	 */
	function wpcw() {
		return WPCW_Plugin::instance();
	}

	/**
	 * Start WP Courseware.
	 *
	 * Instead of hooking into the 'plugins_loaded' action
	 * we load the singleton instance immediately to load
	 * the necesary objects into memory and fire hooks
	 * at the appropriate time.
	 *
	 * @since 4.3.0
	 */
	wpcw();
}
