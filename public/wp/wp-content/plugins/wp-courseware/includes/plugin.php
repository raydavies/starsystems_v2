<?php
/**
 * Main WP Courseware Plugin Class.
 *
 * Main class plugin file used to
 * bootstrap the rest of the plugin.
 *
 * @package WPCW
 * @since 4.3.0
 */

use WPCW\Core\Admin;
use WPCW\Core\Ajax;
use WPCW\Core\Api;
use WPCW\Core\Blocks;
use WPCW\Core\Cache;
use WPCW\Core\Countries;
use WPCW\Core\Cron;
use WPCW\Core\Database;
use WPCW\Core\Deactivate;
use WPCW\Core\Extensions;
use WPCW\Core\Form;
use WPCW\Core\Frontend;
use WPCW\Core\HTTPS;
use WPCW\Core\i18n;
use WPCW\Core\Install;
use WPCW\Core\Legacy;
use WPCW\Core\License;
use WPCW\Core\Privacy;
use WPCW\Core\Query;
use WPCW\Core\Roles;
use WPCW\Core\Session;
use WPCW\Core\Settings;
use WPCW\Core\Shortcodes;
use WPCW\Core\Support;
use WPCW\Core\Tools;
use WPCW\Core\Tracker;
use WPCW\Core\Widgets;
use WPCW\Controllers\Cart;
use WPCW\Controllers\Certificates;
use WPCW\Controllers\Checkout;
use WPCW\Controllers\Courses;
use WPCW\Controllers\Coupons;
use WPCW\Controllers\Emails;
use WPCW\Controllers\Enrollment;
use WPCW\Controllers\Gateways;
use WPCW\Controllers\Logs;
use WPCW\Controllers\Membership;
use WPCW\Controllers\Modules;
use WPCW\Controllers\Notes;
use WPCW\Controllers\Orders;
use WPCW\Controllers\Questions;
use WPCW\Controllers\Quizzes;
use WPCW\Controllers\Reports;
use WPCW\Controllers\Students;
use WPCW\Controllers\Styles;
use WPCW\Controllers\Subscriptions;
use WPCW\Controllers\Units;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WP Courseware plugin class.
 *
 * The main plugin handler class is responsible for initializing WP Courseware.
 * The class registers and all the components required to run the plugin.
 *
 * @since 4.3.0
 */
final class WPCW_Plugin {

	/**
	 * @var i18n The core i18n object.
	 * @since 4.3.0
	 */
	public $i18n;

	/**
	 * @var Legacy The core legacy object.
	 * @since 4.1.0
	 */
	public $legacy;

	/**
	 * @var Settings The core settings object.
	 * @since 4.1.0
	 */
	public $settings;

	/**
	 * @var Database The core database object.
	 * @since 4.3.0
	 */
	public $database;

	/**
	 * @var Admin The core admin object.
	 * @since 4.1.0
	 */
	public $admin;

	/**
	 * @var Frontend The core frontend object.
	 * @since 4.1.0
	 */
	public $frontend;

	/**
	 * @var Shortcodes The shortcodes object.
	 * @since 4.3.0
	 */
	public $shortcodes;

	/**
	 * @var Blocks The Blocks object.
	 * @since 4.5.1
	 */
	public $blocks;

	/**
	 * @var Widgets The widgets object.
	 * @since 4.3.0
	 */
	public $widgets;

	/**
	 * @var Roles The core roles object.
	 * @since 4.3.0
	 */
	public $roles;

	/**
	 * @var Query The core query object.
	 * @since 4.3.0
	 */
	public $query;

	/**
	 * @var Api The core api object.
	 * @since 4.1.0
	 */
	public $api;

	/**
	 * @var Ajax The core ajax object.
	 * @since 4.3.0
	 */
	public $ajax;

	/**
	 * @var Cache The core cache object.
	 * @since 4.3.0
	 */
	public $cache;

	/**
	 * @var Cron The cron object.
	 * @since 4.3.0
	 */
	public $cron;

	/**
	 * @var Session The core session object.
	 * @since 4.3.0
	 */
	public $session;

	/**
	 * @var Countries The core countries object.
	 * @since 4.3.0
	 */
	public $countries;

	/**
	 * @var HTTPS The core https class.
	 * @since 4.3.0
	 */
	public $https;

	/**
	 * @var License The core license object.
	 * @since 4.1.0
	 */
	public $license;

	/**
	 * @var Tools The core tools object.
	 * @since 4.1.0
	 */
	public $tools;

	/**
	 * @var Privacy the core privacy object.
	 * @since 4.3.0
	 */
	public $privacy;

	/**
	 * @var Tracker The core tracker object.
	 * @since 4.4.0
	 */
	public $tracker;

	/**
	 * @var Support The core support information object.
	 * @since 4.1.0
	 */
	public $support;

	/**
	 * @var Install The core install object.
	 * @since 4.3.0
	 */
	public $install;

	/**
	 * @var Deactivate The core deactivate object.
	 * @since 4.3.0
	 */
	public $deactivate;

	/**
	 * @var Extensions The extensions object.
	 * @since 4.3.0
	 */
	public $extensions;

	/**
	 * @var Courses The courses controller.
	 * @since 4.1.0
	 */
	public $courses;

	/**
	 * @var Modules The modules controller.
	 * @since 4.1.0
	 */
	public $modules;

	/**
	 * @var Units The units controller.
	 * @since 4.1.0
	 */
	public $units;

	/**
	 * @var Questions The questions controller.
	 * @since 4.1.0
	 */
	public $questions;

	/**
	 * @var Quizzes The quizzed controller.
	 * @since 4.1.0
	 */
	public $quizzes;

	/**
	 * @var Students The students controller.
	 * @since 4.1.0
	 */
	public $students;

	/**
	 * @var Certificates The certificates controller.
	 * @since 4.3.0
	 */
	public $certificates;

	/**
	 * @var Enrollment The enrollment controller.
	 * @since 4.3.0
	 */
	public $enrollment;

	/**
	 * @var Styles The styles controller.
	 * @since 4.3.0
	 */
	public $styles;

	/**
	 * @var Gateways The gateways controller.
	 * @since 4.3.0
	 */
	public $gateways;

	/**
	 * @var Cart The cart controller.
	 * @since 4.3.0
	 */
	public $cart;

	/**
	 * @var Checkout The checkout controller.
	 * @since 4.3.0
	 */
	public $checkout;

	/**
	 * @var Orders The orders controller.
	 * @since 4.3.0
	 */
	public $orders;

	/**
	 * @var Subscriptions The subscriptions controller.
	 * @since 4.3.0
	 */
	public $subscriptions;

	/**
	 * @var Coupons The coupons controller.
	 * @since 4.5.0
	 */
	public $coupons;

	/**
	 * @var Emails The emails controller.
	 * @since 4.3.0
	 */
	public $emails;

	/**
	 * @var Reports The reports contoller.
	 * @since 4.3.0
	 */
	public $reports;

	/**
	 * @var Logs The logs controller.
	 * @since 4.3.0
	 */
	public $logs;

	/**
	 * @var Notes The notes controller.
	 * @since 4.3.0
	 */
	public $notes;

	/**
	 * @var Plugin Plugin Singleton Instance.
	 * @since 4.1.0
	 */
	private static $instance = null;

	/**
	 * Plugin Singleton Instance.
	 *
	 * @since 4.1.0
	 *
	 * @return null|Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->includes();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Plugin Includes.
	 *
	 * @since 4.1.0
	 */
	protected function includes() {
		spl_autoload_register( array( $this, 'autoloader' ) );

		require_once WPCW_PATH . 'includes/common/constants.php';
		require_once WPCW_PATH . 'includes/common/globals.php';

		require_once WPCW_PATH . 'includes/functions/core.php';
		require_once WPCW_PATH . 'includes/functions/backcompat.php';
		require_once WPCW_PATH . 'includes/functions/deprecated.php';
		require_once WPCW_PATH . 'includes/functions/utilities.php';
		require_once WPCW_PATH . 'includes/functions/template.php';
		require_once WPCW_PATH . 'includes/functions/conditional.php';
		require_once WPCW_PATH . 'includes/functions/formatting.php';
		require_once WPCW_PATH . 'includes/functions/admin.php';
		require_once WPCW_PATH . 'includes/functions/notices.php';
		require_once WPCW_PATH . 'includes/functions/cart.php';
		require_once WPCW_PATH . 'includes/functions/checkout.php';
		require_once WPCW_PATH . 'includes/functions/validation.php';
		require_once WPCW_PATH . 'includes/functions/ajax.php';
		require_once WPCW_PATH . 'includes/functions/logs.php';
		require_once WPCW_PATH . 'includes/functions/courses.php';
		require_once WPCW_PATH . 'includes/functions/modules.php';
		require_once WPCW_PATH . 'includes/functions/units.php';
		require_once WPCW_PATH . 'includes/functions/quizzes.php';
		require_once WPCW_PATH . 'includes/functions/orders.php';
		require_once WPCW_PATH . 'includes/functions/coupons.php';
		require_once WPCW_PATH . 'includes/functions/students.php';
		require_once WPCW_PATH . 'includes/functions/subscriptions.php';
		require_once WPCW_PATH . 'includes/functions/enrollment.php';
		require_once WPCW_PATH . 'includes/functions/reports.php';

		require_once WPCW_PATH . 'includes/core/addons.php';

		require_once WPCW_LEGACY_PATH . 'functions.php';
		require_once WPCW_LEGACY_PATH . 'builder/formbuilder.php';
		require_once WPCW_LEGACY_PATH . 'builder/pagebuilder.php';
		require_once WPCW_LEGACY_PATH . 'builder/tablebuilder.php';
		require_once WPCW_LEGACY_PATH . 'builder/easyform.php';
		require_once WPCW_LEGACY_PATH . 'builder/recordsform.php';
		require_once WPCW_LEGACY_PATH . 'builder/settingsform.php';
		require_once WPCW_LEGACY_PATH . 'classes/userprogress.php';
		require_once WPCW_LEGACY_PATH . 'classes/courseprogress.php';
		require_once WPCW_LEGACY_PATH . 'classes/quizcustomfeedback.php';
		require_once WPCW_LEGACY_PATH . 'classes/dripfeed.php';
		require_once WPCW_LEGACY_PATH . 'quiz/base.php';
		require_once WPCW_LEGACY_PATH . 'quiz/multi.php';
		require_once WPCW_LEGACY_PATH . 'quiz/truefalse.php';
		require_once WPCW_LEGACY_PATH . 'quiz/open.php';
		require_once WPCW_LEGACY_PATH . 'quiz/upload.php';
		require_once WPCW_LEGACY_PATH . 'quiz/random.php';
		require_once WPCW_LEGACY_PATH . 'classes/quizresults.php';
		require_once WPCW_LEGACY_PATH . 'classes/unitfrontend.php';

		require_once WPCW_LEGACY_PATH . 'classes/coursemap.php';
		require_once WPCW_LEGACY_PATH . 'classes/import.php';
		require_once WPCW_LEGACY_PATH . 'classes/export.php';
		require_once WPCW_LEGACY_PATH . 'admin/functions.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-course-dashboard.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-course-modify.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-course-ordering.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-gradebook.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-import-export.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-module-modify.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-question-modify.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-question-pool.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-quiz-modify.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-quiz-summary.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-unit-convert.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-course-access.php';
		require_once WPCW_LEGACY_PATH . 'pages/page-user-progress.php';
		require_once WPCW_LEGACY_PATH . 'frontend/functions.php';
		require_once WPCW_LEGACY_PATH . 'frontend/templates.php';

		require_once WPCW_LEGACY_PATH . 'classes/certificate.php';
		require_once WPCW_LEGACY_PATH . 'pdf/certificates.php';
		require_once WPCW_LEGACY_PATH . 'pdf/results.php';
		require_once WPCW_LEGACY_PATH . 'ajax.php';
		require_once WPCW_LEGACY_PATH . 'shortcodes.php';
		require_once WPCW_LEGACY_PATH . 'setup.php';
	}

	/**
	 * Plugin Setup.
	 *
	 * @since 4.1.0
	 */
	protected function setup() {
		// Set Up Early Objects.
		$this->i18n       = new i18n();
		$this->settings   = new Settings();
		$this->database   = new Database();
		$this->install    = new Install();
		$this->deactivate = new Deactivate();

		// Setup Core Objects.
		$this->legacy     = new Legacy();
		$this->admin      = new Admin();
		$this->frontend   = new Frontend();
		$this->shortcodes = new Shortcodes();
		$this->blocks     = new Blocks();
		$this->widgets    = new Widgets();
		$this->roles      = new Roles();
		$this->query      = new Query();
		$this->api        = new Api();
		$this->ajax       = new Ajax();
		$this->cache      = new Cache();
		$this->cron       = new Cron();
		$this->session    = new Session(); // x
		$this->countries  = new Countries();
		$this->https      = new HTTPS();
		$this->license    = new License();
		$this->tools      = new Tools();
		$this->tracker    = new Tracker();
		$this->privacy    = new Privacy();
		$this->support    = new Support();
		$this->extensions = new Extensions();

		// Setup Controller Objects.
		$this->courses       = new Courses();
		$this->modules       = new Modules();
		$this->units         = new Units();
		$this->quizzes       = new Quizzes();
		$this->questions     = new Questions();
		$this->students      = new Students();
		$this->certificates  = new Certificates();
		$this->enrollment    = new Enrollment();
		$this->styles        = new Styles();
		$this->gateways      = new Gateways();
		$this->cart          = new Cart();
		$this->checkout      = new Checkout();
		$this->orders        = new Orders();
		$this->subscriptions = new Subscriptions();
		$this->coupons       = new Coupons();
		$this->emails        = new Emails();
		$this->reports       = new Reports();
		$this->logs          = new Logs();
		$this->notes         = new Notes();

		// Objects that need to be loaded immediately.
		$this->i18n->load();
		$this->settings->load();
		$this->install->load();
		$this->deactivate->load();

		// All other objects will be loaded late on 'plugins_loaded'
		add_action( 'plugins_loaded', array( $this, 'load' ), 12 );

		// Hook onto init to register items later in the process.
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Plugin Load.
	 *
	 * @since 4.1.0
	 */
	public function load() {
		// Core.
		$this->legacy->load();
		$this->admin->load();
		$this->frontend->load();
		$this->shortcodes->load();
		$this->blocks->load();
		$this->widgets->load();
		$this->roles->load();
		$this->api->load();
		$this->ajax->load();
		$this->cache->load();
		$this->cron->load();
		$this->session->load();
		$this->query->load();
		$this->https->load();
		$this->license->load();
		$this->tools->load();
		$this->tracker->load();
		$this->privacy->load();
		$this->support->load();
		$this->extensions->load();

		// Controllers.
		$this->courses->load();
		$this->modules->load();
		$this->units->load();
		$this->quizzes->load();
		$this->questions->load();
		$this->students->load();
		$this->certificates->load();
		$this->enrollment->load();
		$this->styles->load();
		$this->gateways->load();
		$this->cart->load();
		$this->checkout->load();
		$this->orders->load();
		$this->subscriptions->load();
		$this->coupons->load();
		$this->emails->load();
		$this->reports->load();
		$this->logs->load();
		$this->notes->load();

		/**
		 * Action: WP Courseware is fully loaded.
		 *
		 * @since 4.3.0
		 */
		do_action( 'wpcw_loaded' );
	}

	/**
	 * Plugin Init.
	 *
	 * @since 4.3.0
	 */
	public function init() {
		/**
		 * Action: WP Courseware is initialized.
		 *
		 * @since 4.3.0
		 */
		do_action( 'wpcw_init' );
	}

	/**
	 * Get Plugin Name.
	 *
	 * @since 4.3.0
	 *
	 * @return string
	 */
	public function get_name() {
		return 'WP Courseware';
	}

	/**
	 * Get Plugin Company Name.
	 *
	 * @since 4.3.0
	 *
	 * @return string The plugin company name.
	 */
	public function get_company_name() {
		return esc_attr( apply_filters( 'wpcw_company_name', 'Fly Plugins' ) );
	}

	/**
	 * Get Plugin Company Url.
	 *
	 * @since 4.1.0
	 *
	 * @return string The plugin company url.
	 */
	public function get_company_url() {
		return esc_url( apply_filters( 'wpcw_company_url', 'https://flyplugins.com/' ) );
	}

	/**
	 * Get Plugin Company Member Portal Url.
	 *
	 * @since 4.1.0
	 *
	 * @return string The plugin company member portal url.
	 */
	public function get_member_portal_url() {
		return esc_url( apply_filters( 'wpcw_company_member_portal_url', 'https://flyplugins.com/member-portal/' ) );
	}

	/**
	 * Get Plugin Company Member Portal Support Url.
	 *
	 * @since 4.1.0
	 *
	 * @return string The plugin company member portal support url.
	 */
	public function get_member_portal_support_url() {
		return esc_url( apply_filters( 'wpcw_company_member_portal_support_url', 'https://flyplugins.com/member-portal/support/' ) );
	}

	/**
	 * Member Portal License Url.
	 *
	 * @since 4.3.0
	 *
	 * @return string The plugin company member portal license url.
	 */
	public function get_member_portal_license_url() {
		return esc_url( apply_filters( 'wpcw_company_member_portal_license_url', 'https://flyplugins.com/member-portal/license-keys/' ) );
	}

	/**
	 * Get the plugin template path.
	 *
	 * @since 4.3.0
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'wpcw_template_path', trailingslashit( 'wp-courseware' ) );
	}

	/**
	 * Plugin Class Autoloader.
	 *
	 * @since 4.1.0
	 *
	 * @param string $class The class name.
	 *
	 * @return bool|mixed|void
	 */
	public function autoloader( $class ) {
		if ( 0 !== strpos( $class, 'WPCW\\', 0 ) ) {
			return;
		}

		static $loaded = array();

		if ( isset( $loaded[ $class ] ) ) {
			return $loaded[ $class ];
		}

		$class = str_replace( 'WPCW\\', '', $class );
		$class = strtolower( $class );
		$class = str_replace( '_', '-', $class );
		$class = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, __DIR__ . DIRECTORY_SEPARATOR . $class . '.php' );

		if ( false === ( $class = realpath( $class ) ) ) {
			return false;
		}

		return $loaded[ $class ] = ( bool ) require( $class );
	}

	/**
	 * Plugin Constructor.
	 *
	 * @since 4.1.0
	 */
	private function __construct() { /* Do Nothing */ }

	/**
	 * Disable plugin cloning.
	 *
	 * @since 4.1.0
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-courseware' ), '4.1.0' ); }

	/**
	 * Disable plugin unserializing.
	 *
	 * @since 4.1.0
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-courseware' ), '4.1.0' ); }

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since 4.1.0
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong( "WPCW::{$method}", esc_html__( 'WP Courseware class method does not exist.', 'wp-courseware' ), '4.1.0' );
		unset( $method, $args );

		return null;
	}
}
