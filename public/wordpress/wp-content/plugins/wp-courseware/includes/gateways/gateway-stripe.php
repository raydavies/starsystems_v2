<?php
/**
 * WP Courseware Payment Gateway - Stripe
 *
 * @package WPCW
 * @subpackage Gateways
 * @since 4.3.0
 */
namespace WPCW\Gateways;

use WPCW\Gateways\Stripe\Stripe_Api;
use WPCW\Gateways\Stripe\Stripe_Customer;
use WPCW\Gateways\Stripe\Stripe_Exception;
use WPCW\Gateways\Stripe\Stripe_Subscription;
use WPCW\Gateways\Stripe\Stripe_Webhooks;
use WPCW\Models\Course;
use WPCW\Models\Order;
use WPCW\Models\Order_Item;
use WPCW\Models\Student;
use WPCW\Models\Subscription;

// Exit if accessed directly
defined( 'ABSPATH' ) || die;

/**
 * Class Gateway_Stripe.
 *
 * @since 4.3.0
 */
class Gateway_Stripe extends Gateway {

	/**
	 * @var string Test Mode.
	 * @since 4.3.0
	 */
	protected $test_mode = 'no';

	/**
	 * @var array Api Credentials.
	 * @since 4.3.0
	 */
	protected $api_creds = array(
		'live_publishable_key' => '',
		'live_secret_key'      => '',
		'test_publishable_key' => '',
		'test_secret_key'      => '',
	);

	/**
	 * @var string Stripe Checkout
	 * @since 4.3.0
	 */
	protected $checkout = 'no';

	/**
	 * @var string Stripe Checkout Image.
	 * @since 4.3.0
	 */
	protected $checkout_image = '';

	/**
	 * @var string Stripe Checkout Description.
	 * @since 4.3.0
	 */
	protected $checkout_desc = '';

	/**
	 * @var string Inline Credit Card Form.
	 * @since 4.3.0
	 */
	protected $inline_cc_form = 'no';

	/**
	 * @var string Statement Description.
	 * @since 4.3.0
	 */
	protected $statement_desc = '';

	/**
	 * @var string Logging.
	 * @since 4.3.0
	 */
	protected $logging = 'no';

	/**
	 * @var Stripe_Api The stripe api.
	 * @since 4.3.0
	 */
	protected $api;

	/**
	 * @var int The retry interval.
	 * @since 4.3.0
	 */
	protected $retry_interval;

	/**
	 * Gateway Stripe constructor.
	 *
	 * @since 4.3.0
	 */
	public function __construct() {
		$this->id                 = 'stripe';
		$this->method_title       = esc_html__( 'Stripe', 'wp-courseware' );
		$this->method_description = esc_html__( 'Stripe works by adding payment fields on the checkout and then sending the details to Stripe for verification.', 'wp-courseware' );
		$this->title              = esc_html__( 'Credit Card (Stripe)', 'wp-courseware' );
		$this->description        = esc_html__( 'Pay with your credit card via Stripe.', 'wp-courseware' );
		$this->has_fields         = true;
		$this->supports           = array( 'cc-form', 'courses', 'refunds', 'cancellations' );
		$this->retry_interval     = 1;

		parent::__construct();
	}

	/**
	 * Get Stripe Settings Fields.
	 *
	 * @since 4.3.0
	 *
	 * @return array The array of settings fields for Stripe.
	 */
	public function get_settings_fields() {
		$settings = parent::get_settings_fields();

		$stripe_settings = array(
			array(
				'type'     => 'heading',
				'key'      => $this->get_setting_key( 'api_keys_heading' ),
				'title'    => esc_html__( 'Stripe API', 'wp-courseware' ),
				'desc_tip' => esc_html__( 'Get your API keys from your Stripe Account.', 'wp-courseware' ),
				/* translators: %s The stripe account url */
				'desc'     => sprintf( __( 'Enter your Stripe API keys from your <a href="%s" target="_blank">Stripe API account settings</a>.', 'wp-courseware' ), esc_url_raw( 'https://dashboard.stripe.com/account/apikeys' ) ),
			),
			array(
				'type'     => 'checkbox',
				'key'      => $this->get_setting_key( 'test_mode' ),
				'title'    => esc_html__( 'Test Mode', 'wp-courseware' ),
				'label'    => esc_html__( 'Enable Stripe Test Mode', 'wp-coureware' ),
				'desc_tip' => esc_html__( 'Place the payment gateway in test mode using the Stripe test API keys.', 'wp-courseware' ),
				'default'  => 'no',
			),
			array(
				'type'        => 'password',
				'key'         => $this->get_setting_key( 'live_publishable_key' ),
				'title'       => esc_html__( 'Live Publishable Key', 'wp-courseware' ),
				'placeholder' => esc_html__( 'Live Publishable Key', 'wp-courseware' ),
				'desc_tip'    => esc_html__( 'Your Stripe Api Live Publishable Key.', 'wp-courseware' ),
				'default'     => '',
				'class'       => 'stripe-api-creds-live',
			),
			array(
				'type'        => 'password',
				'key'         => $this->get_setting_key( 'live_secret_key' ),
				'title'       => esc_html__( 'Live Secret Key', 'wp-courseware' ),
				'placeholder' => esc_html__( 'Live Secret Key', 'wp-courseware' ),
				'desc_tip'    => esc_html__( 'Your Stripe Api Live Secret Key.', 'wp-courseware' ),
				'default'     => '',
				'class'       => 'stripe-api-creds-live',
			),
			array(
				'type'        => 'password',
				'key'         => $this->get_setting_key( 'test_publishable_key' ),
				'title'       => esc_html__( 'Test Publishable Key', 'wp-courseware' ),
				'placeholder' => esc_html__( 'Test Publishable Key', 'wp-courseware' ),
				'desc_tip'    => esc_html__( 'Your Stripe Api Test Publishable Key.', 'wp-courseware' ),
				'default'     => '',
				'class'       => 'stripe-api-creds-test',
			),
			array(
				'type'        => 'password',
				'key'         => $this->get_setting_key( 'test_secret_key' ),
				'title'       => esc_html__( 'Test Secret Key', 'wp-courseware' ),
				'placeholder' => esc_html__( 'Test Secret Key', 'wp-courseware' ),
				'desc_tip'    => esc_html__( 'Your Stripe Api Test Secret Key.', 'wp-courseware' ),
				'default'     => '',
				'class'       => 'stripe-api-creds-test',
			),
			array(
				'type'     => 'content',
				'key'      => $this->get_setting_key( 'webhooks' ),
				'title'    => esc_html__( 'Webhooks', 'wp-courseware' ),
				'desc_tip' => esc_html__( 'In order for Stripe to function completely, you must configure your Stripe webhooks.', 'wp-courseware' ),
				/* translators: %1$s The webhook url, %2$s The stripe account url. */
				'content'  => sprintf(
					__( 'You must add the webhook endpoint <code>%1$s</code> in your <a target="_blank" href="%2$s">Stripe Account Webhook Settings</a> so you can receive notifications on the charge statuses.', 'wp-courseware' ),
					esc_url_raw( $this->get_webhook_url() ),
					esc_url( 'https://dashboard.stripe.com/account/webhooks' )
				),
				'default'  => '',
			),
			array(
				'type'  => 'heading',
				'key'   => $this->get_setting_key( 'checkout_settings_heading' ),
				'title' => esc_html__( 'Stripe Checkout', 'wp-courseware' ),
				'desc'  => __( 'If enabled, this option shows a <strong>"pay"</strong> button and modal credit card form on the checkout, instead of credit card fields directly on the page.', 'wp-courseware' ),
			),
			array(
				'type'     => 'checkbox',
				'key'      => $this->get_setting_key( 'checkout' ),
				'title'    => esc_html__( 'Stripe Checkout', 'wp-courseware' ),
				'label'    => esc_html__( 'Enable Stripe Checkout', 'wp-coureware' ),
				'desc_tip' => esc_html__( 'If enabled, this option shows a "pay" button and modal credit card form on the checkout, instead of credit card fields directly on the page.', 'wp-courseware' ),
				'default'  => 'no',
			),
			array(
				'type'        => 'imageinput',
				'key'         => $this->get_setting_key( 'checkout_image' ),
				'image_key'   => $this->get_setting_key( 'checkout_image_id' ),
				'title'       => esc_html__( 'Checkout Image', 'wp-courseware' ),
				'placeholder' => esc_html__( 'Optional', 'wp-courseware' ),
				'desc_tip'    => esc_html__( 'Optionally enter the URL to a 128x128px image of your brand or product to be displayed on the Stripe Checkout Modal.', 'wp-courseware' ),
				'component'   => true,
				'settings'    => array(
					array(
						'key'     => $this->get_setting_key( 'checkout_image' ),
						'type'    => 'imageinput',
						'default' => '',
					),
					array(
						'key'     => $this->get_setting_key( 'checkout_image_id' ),
						'type'    => 'number',
						'default' => 0,
					),
				),
			),
			array(
				'type'        => 'text',
				'key'         => $this->get_setting_key( 'checkout_desc' ),
				'title'       => esc_html__( 'Checkout Description', 'wp-courseware' ),
				'placeholder' => esc_html__( 'Optional', 'wp-courseware' ),
				'desc_tip'    => esc_html__( 'Shows a description of your brand or product on the Stripe Checkout Modal.', 'wp-courseware' ),
				'default'     => '',
			),
			array(
				'type'  => 'heading',
				'key'   => $this->get_setting_key( 'other_settings_heading' ),
				'title' => esc_html__( 'Other Stripe Settings', 'wp-courseware' ),
				'desc'  => esc_html__( 'Below are other settings related to the interactions with the Stripe payment gateway.', 'wp-courseware' ),
			),
			array(
				'type'     => 'checkbox',
				'key'      => $this->get_setting_key( 'inline_cc_form' ),
				'title'    => esc_html__( 'Inline Credit Card Form', 'wp-courseware' ),
				'label'    => esc_html__( 'Enable Inline Credit Card Form', 'wp-coureware' ),
				'desc_tip' => esc_html__( 'Choose the style you want to show for your credit card form. When unchecked, the credit card form will display separate credit card number field, expiry date field and cvc field.', 'wp-courseware' ),
				'default'  => 'yes',
			),
			array(
				'type'        => 'text',
				'key'         => $this->get_setting_key( 'statement_descriptor' ),
				'title'       => esc_html__( 'Statement Descriptor', 'wp-courseware' ),
				'placeholder' => esc_html__( 'Optional', 'wp-courseware' ),
				'desc_tip'    => esc_html__( 'This may be up to 22 characters. The statement description must contain at least one letter, may not include ><"\' characters, and will appear on your customer\'s statement in capital letters.', 'wp-courseware' ),
				'desc'        => esc_html__( 'This may be up to 22 characters.', 'wp-courseware' ),
				'default'     => '',
			),
			array(
				'type'     => 'checkbox',
				'key'      => $this->get_setting_key( 'logging' ),
				'title'    => esc_html__( 'Logging', 'wp-courseware' ),
				'label'    => esc_html__( 'Enable logging', 'wp-coureware' ),
				'desc_tip' => esc_html__( 'Log Stripe events, such as webhook requests.', 'wp-courseware' ),
				'default'  => 'no',
			),
		);

		return array_merge( $settings, $stripe_settings );
	}

	/**
	 * Get Webhook Url.
	 *
	 * @since 4.3.0
	 *
	 * @return string
	 */
	public function get_webhook_url() {
		/**
		 * Filter: Stripe Webhook Url
		 *
		 * @since 4.3.0
		 *
		 * @param string  $notify_url The gateway notify url.
		 * @param Gateway $gateway The gateway object.
		 *
		 * @return string $notify_url The modified notify url.
		 */
		return esc_url_raw( apply_filters( "wpcw_gateway_{$this->get_id()}_webhook_url", wpcw()->api->get_api_url( 'gateway-stripe', true ), $this ) );
	}

	/**
	 * Get Return Url.
	 *
	 * @since 4.3.0
	 *
	 * @param null|Order The order object.
	 *
	 * @return string The return url.
	 */
	public function get_return_url( $order = null ) {
		return esc_url_raw( apply_filters( 'wpcw_stripe_get_return_url', add_query_arg( array( 'utm_nooverride' => '1' ), parent::get_return_url( $order ) ) ) );
	}

	/**
	 * Setup Sripe Gateway.
	 *
	 * @since 4.3.0
	 */
	public function setup() {
		parent::setup();

		$this->test_mode      = $this->get_setting( 'test_mode', $this->test_mode );
		$this->api_creds      = array(
			'live_publishable_key' => $this->get_setting( 'live_publishable_key' ),
			'live_secret_key'      => $this->get_setting( 'live_secret_key' ),
			'test_publishable_key' => $this->get_setting( 'test_publishable_key' ),
			'test_secret_key'      => $this->get_setting( 'test_secret_key' ),
		);
		$this->checkout       = $this->get_setting( 'checkout', $this->checkout );
		$this->checkout_image = $this->get_setting( 'checkout_image', $this->checkout_image );
		$this->checkout_desc  = $this->get_setting( 'checkout_desc', $this->checkout_desc );
		$this->inline_cc_form = $this->get_setting( 'inline_cc_form', $this->inline_cc_form );
		$this->statement_desc = $this->get_setting( 'statement_descriptor', $this->statement_desc );
		$this->logging        = $this->get_setting( 'logging', $this->logging );

		if ( $this->is_stripe_checkout() ) {
			$this->order_button_text = esc_html__( 'Continue to payment', 'wp-courseware' );
		}
	}

	/**
	 * Load Stripe Gateway.
	 *
	 * @since 4.3.0
	 */
	public function load() {
		add_action( 'wpcw_frontend_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'wpcw_loaded', array( $this, 'webhook_handler' ) );
		add_action( 'admin_notices', array( $this, 'enable_stripe_notice' ) );
		add_action( 'admin_notices', array( $this, 'enable_stripe_ssl_notice' ) );
		add_filter( 'wpcw_subscription_profile_link_stripe', array( $this, 'subscription_profile_link' ), 10, 2 );
		add_filter( 'wpcw_subscription_transaction_link_stripe', array( $this, 'subscription_transaction_link' ), 10, 2 );
		add_filter( 'wpcw_order_transaction_link_stripe', array( $this, 'order_transaction_link' ), 10, 2 );
	}

	/**
	 * Enable Stripe Notice.
	 *
	 * @since 4.3.0
	 */
	public function enable_stripe_notice() {
		if ( ! wpcw_is_admin_settings_page() ) {
			return;
		}

		$this->setup();

		if ( $this->is_enabled() && ! $this->is_available() ) {
			wpcw_admin_notice_info( sprintf( __( 'Stripe is almost ready. To get started, <a href="%s">set your Stripe account keys.</a>', 'wp-courseware' ), $this->get_admin_url() ) );
		}
	}

	/**
	 * Enable Stripe SSL Notice.
	 *
	 * @since 4.3.0
	 */
	public function enable_stripe_ssl_notice() {
		if ( ! wpcw_is_admin_settings_page() ) {
			return;
		}

		$this->setup();

		if ( $this->is_enabled() && ! $this->is_force_ssl_enabled() ) {
			$force_ssl_option_page = esc_url_raw( add_query_arg( array( 'page' => 'wpcw-settings', 'tab' => 'checkout', 'section' => 'process' ), admin_url( 'admin.php' ) ) );

			wpcw_admin_notice_info( sprintf(
				__( 'Stripe is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout may not be secure! Please enable the <a href="%s">force SSL option</a> and ensure your server has a valid <a target="_blank" href="%s">SSL certificate</a>. Stripe will only work in Test Mode.', 'wp-courseware' ),
				$force_ssl_option_page,
				$force_ssl_option_page,
				'https://en.wikipedia.org/wiki/Transport_Layer_Security'
			) );
		}
	}

	/**
	 * Is Test Mode?
	 *
	 * @since 4.3.0
	 *
	 * @return bool True if enabled, False otherwise.
	 */
	public function is_test_mode() {
		return ( 'yes' === $this->test_mode ) ? true : false;
	}

	/**
	 * Get Api Credential.
	 *
	 * @since 4.3.0
	 *
	 * @param string $key The api credential key.
	 *
	 * @return string The api credential value. Default is blank.
	 */
	protected function get_api_cred( $key ) {
		return isset( $this->api_creds[ $key ] ) ? $this->api_creds[ $key ] : '';
	}

	/**
	 * Get Publishable Key.
	 *
	 * @since 4.3.0
	 *
	 * @return string The publishable key.
	 */
	public function get_publishable_key() {
		return $this->is_test_mode() ? $this->get_api_cred( 'test_publishable_key' ) : $this->get_api_cred( 'live_publishable_key' );
	}

	/**
	 * Get Secret Key.
	 *
	 * @since 4.3.0
	 *
	 * @return string The secret key.
	 */
	protected function get_secret_key() {
		return $this->is_test_mode() ? $this->get_api_cred( 'test_secret_key' ) : $this->get_api_cred( 'live_secret_key' );
	}

	/**
	 * Are Stripe Keys Set?
	 *
	 * @since 4.3.0
	 *
	 * @return bool
	 */
	public function are_keys_set() {
		if ( ! $this->get_secret_key() || ! $this->get_publishable_key() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get Stripe Api.
	 *
	 * @since 4.3.0
	 *
	 * @return bool|Stripe_Api The stripe Api object.
	 */
	public function get_api() {
		if ( empty( $this->api ) ) {
			if ( empty( $this->api_creds ) ) {
				throw new Stripe_Exception( 'wpcw-gateway-stripe-api-keys-not-set', esc_html__( 'The Stripe API keys entered in settings are incorrect.', 'wp-courseware' ) );
			}

			$this->api = new Stripe_Api( $this->get_secret_key(), $this->is_test_mode(), $this->is_logging_enabled() );
		}

		return $this->api;
	}

	/**
	 * Is Stripe Checkout Enabled?
	 *
	 * @since 4.3.0
	 *
	 * @return bool True if enabled, False otherwise.
	 */
	public function is_stripe_checkout() {
		return 'yes' === $this->checkout ? true : false;
	}

	/**
	 * Get Stripe Checkout Image.
	 *
	 * @since 4.3.0
	 *
	 * @return string The checkout image.
	 */
	public function get_stripe_checkout_image() {
		return ! empty( $this->checkout_image ) ? esc_url_raw( $this->checkout_image ) : '';
	}

	/**
	 * Get Stripe Check Description.
	 *
	 * @since 4.3.0
	 *
	 * @return string The checkout description.
	 */
	public function get_stripe_checkout_desc() {
		return wp_kses_post( $this->checkout_desc );
	}

	/**
	 * Is Inline Credit Card Form Enabled?
	 *
	 * @since 4.3.0
	 *
	 * @return bool True if enabled, False otherwise.
	 */
	public function is_inline_cc_form_enabled() {
		return 'yes' === $this->inline_cc_form ? true : false;
	}

	/**
	 * Get Statement Description.
	 *
	 * Stripe requires max of 22 characters and no
	 * special characters with ><"'.
	 *
	 * @since 4.3.0
	 *
	 * @return string The statement description.
	 */
	public function get_statement_desc() {
		return $this->format_statement_desc( $this->statement_desc );
	}

	/**
	 * Formate Statement Description.
	 *
	 * @since 4.3.0
	 *
	 * @param string $desc The statement description.
	 *
	 * @return string $statement_desc The formatted statement description.
	 */
	public function format_statement_desc( $desc ) {
		$statement_desc = wp_kses_post( $desc );
		$statement_desc = str_replace( array( '<', '>', '"', "'" ), '', $statement_desc );
		$statement_desc = substr( trim( $statement_desc ), 0, 22 );

		return $statement_desc;
	}

	/**
	 * Is Logging Enabled?
	 *
	 * @since 4.3.0
	 *
	 * @return bool True if enabled, False otherwise.
	 */
	public function is_logging_enabled() {
		return 'yes' === $this->logging ? true : false;
	}

	/**
	 * Log Stripe Message.
	 *
	 * @since 4.3.0
	 *
	 * @param string $message The log message.
	 */
	public function log( $message = '' ) {
		if ( empty( $message ) || ! $this->is_logging_enabled() ) {
			return;
		}

		$log_entry = "\n" . '====Start Stripe Gateway Log====' . "\n" . $message . "\n" . '====End Stripe Gateway Log====' . "\n";

		wpcw_log( $log_entry );
		wpcw_file_log( array( 'message' => $log_entry ) );
	}

	/**
	 * Is Stripe Gateway Available?
	 *
	 * @since 4.3.0
	 *
	 * @return bool True if is enabled and keys are available.
	 */
	public function is_available() {
		if ( ! parent::is_available() ) {
			return false;
		}

		if ( ! $this->is_test_mode() && ! $this->is_force_ssl_enabled() ) {
			$this->test_mode = 'yes';
		}

		if ( ! $this->are_keys_set() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get Payment Icons.
	 *
	 * @since 4.3.0
	 *
	 * @return array The array of payment icons.
	 */
	public function get_payment_icons() {
		return apply_filters( 'wpcw_stripe_payment_icons', array(
			'cc'         => '<i class="wpcw-fas wpcw-fa-credit-card"></i>',
			'visa'       => '<i class="wpcw-fab wpcw-fa-cc-visa"></i>',
			'amex'       => '<i class="wpcw-fab wpcw-fa-cc-amex"></i>',
			'mastercard' => '<i class="wpcw-fab wpcw-fa-cc-mastercard"></i>',
			'discover'   => '<i class="wpcw-fab wpcw-fa-cc-discover"></i>',
			'diners'     => '<i class="wpcw-fab wpcw-fa-cc-diners-club"></i>',
			'jcb'        => '<i class="wpcw-fab wpcw-fa-cc-jcb"></i>',
		) );
	}

	/**
	 * Get Icon.
	 *
	 * @since 4.3.0
	 *
	 * @return string|void
	 */
	public function get_icon() {
		$icons = $this->get_payment_icons();

		if ( 'USD' !== wpcw_get_currency() ) {
			unset( $icons['discover'] );
			unset( $icons['diners'] );
			unset( $icons['jcb'] );
		}

		$icon_html = '<span class="wpcw-payment-method-icon">';

		foreach ( $icons as $id => $icon ) {
			$icon_html .= $icon;

			if ( $icon !== end( $icons ) ) {
				$icon_html .= '&nbsp;';
			}
		}

		$icon_html .= '</span>';

		return apply_filters( 'wpcw_stripe_gateway_icon', $icon_html );
	}

	/**
	 * List of currencies supported by Stripe that has no decimals.
	 *
	 * @since 4.3.0
	 *
	 * @return array $currencies The currencies that have no decimal.
	 */
	protected function get_no_decimal_currencies() {
		return array(
			'bif', // Burundian Franc
			'djf', // Djiboutian Franc
			'jpy', // Japanese Yen
			'krw', // South Korean Won
			'pyg', // Paraguayan Guaraní
			'vnd', // Vietnamese Đồng
			'xaf', // Central African Cfa Franc
			'xpf', // Cfp Franc
			'clp', // Chilean Peso
			'gnf', // Guinean Franc
			'kmf', // Comorian Franc
			'mga', // Malagasy Ariary
			'rwf', // Rwandan Franc
			'vuv', // Vanuatu Vatu
			'xof', // West African Cfa Franc
		);
	}

	/**
	 * Get Stripe Total.
	 *
	 * @since 4.3.0
	 *
	 * @param string $total The total amount.
	 * @param string $currency Optional. The currency.
	 *
	 * @return string $total The total amount formatted for Stripe.
	 */
	public function get_total( $total, $currency = '' ) {
		if ( ! $currency ) {
			$currency = wpcw_get_currency();
		}

		if ( in_array( strtolower( $currency ), $this->get_no_decimal_currencies() ) ) {
			return absint( $total );
		} else {
			return absint( wpcw_format_decimal( ( (float) $total * 100 ), wpcw_get_currency_decimals() ) );
		}
	}

	/**
	 * Gets the locale with normalization that only Stripe accepts.
	 *
	 * @since 4.3.0
	 *
	 * @return string $locale
	 */
	protected function get_locale() {
		$locale = get_locale();

		/*
		 * Stripe expects Norwegian to only be passed NO.
		 * But WP has different dialects.
		 */
		if ( 'NO' === substr( $locale, 3, 2 ) ) {
			$locale = 'no';
		} else {
			$locale = substr( get_locale(), 0, 2 );
		}

		return $locale;
	}

	/**
	 * Get Stripe Localized Messages.
	 *
	 * @since 4.3.0
	 *
	 * @return mixed|void
	 */
	protected function get_localized_messages() {
		return apply_filters( 'wpcw_stripe_localized_messages', array(
			'invalid_number'           => __( 'The card number is not a valid credit card number.', 'wp-courseware' ),
			'invalid_expiry_month'     => __( 'The card\'s expiration month is invalid.', 'wp-courseware' ),
			'invalid_expiry_year'      => __( 'The card\'s expiration year is invalid.', 'wp-courseware' ),
			'invalid_cvc'              => __( 'The card\'s security code is invalid.', 'wp-courseware' ),
			'incorrect_number'         => __( 'The card number is incorrect.', 'wp-courseware' ),
			'incomplete_number'        => __( 'The card number is incomplete.', 'wp-courseware' ),
			'incomplete_cvc'           => __( 'The card\'s security code is incomplete.', 'wp-courseware' ),
			'incomplete_expiry'        => __( 'The card\'s expiration date is incomplete.', 'wp-courseware' ),
			'expired_card'             => __( 'The card has expired.', 'wp-courseware' ),
			'incorrect_cvc'            => __( 'The card\'s security code is incorrect.', 'wp-courseware' ),
			'incorrect_zip'            => __( 'The card\'s zip code failed validation.', 'wp-courseware' ),
			'invalid_expiry_year_past' => __( 'The card\'s expiration year is in the past', 'wp-courseware' ),
			'card_declined'            => __( 'The card was declined.', 'wp-courseware' ),
			'missing'                  => __( 'There is no card on a customer that is being charged.', 'wp-courseware' ),
			'processing_error'         => __( 'An error occurred while processing the card.', 'wp-courseware' ),
			'invalid_request_error'    => __( 'Unable to process this payment, please try again or use alternative method.', 'wp-courseware' ),
			'invalid_sofort_country'   => __( 'The billing country is not accepted by SOFORT. Please try another country.', 'wp-courseware' ),
		) );
	}

	/**
	 * Stripe Payment Fields.
	 *
	 * @since 4.3.0
	 */
	public function payment_fields() {
		// Description.
		if ( $description = $this->get_description() ) {
			if ( $this->is_test_mode() ) {
				/* translators: %s - link to stripe testing documentation */
				$test_mode_description = apply_filters( 'wpcw_gateway_stripe_test_mode_description', sprintf(
					__( 'TEST MODE ENABLED. In test mode, you can use the card number 4242424242424242 with any CVC and a valid expiration date or check the <a href="%s" target="_blank">Testing Stripe documentation</a> for more card numbers.', 'wp-courseware' ),
					esc_url( 'https://stripe.com/docs/testing' )
				) );

				$description .= ' ' . trim( $test_mode_description );
			}

			printf( '<div class="wpcw-payment-method-desc">%s</div>', wpautop( wptexturize( $description ) ) );
		}

		// User Email
		$user_email = '';
		if ( ( $user = wp_get_current_user() ) && is_a( $user, 'WP_User' ) ) {
			$user_email = $user->user_email;
		}

		// Total.
		$total = wpcw()->cart->get_total();

		// Payment Fields.
		ob_start();
		echo '<div
			id="wpcw-stripe-payment-data"
			data-description="' . esc_attr( strip_tags( $this->get_stripe_checkout_desc() ) ) . '"
			data-email="' . esc_attr( $user_email ) . '"
			data-verify-zip="' . esc_attr( apply_filters( 'wpcw_stripe_checkout_verify_zip', false ) ? 'true' : 'false' ) . '"
			data-billing-address="' . esc_attr( apply_filters( 'wpcw_stripe_checkout_require_billing_address', false ) ? 'true' : 'false' ) . '" 
			data-amount="' . esc_attr( $this->get_total( $total ) ) . '"
			data-name="' . esc_attr( $this->get_statement_desc() ) . '"
			data-currency="' . esc_attr( strtolower( wpcw_get_currency() ) ) . '"
			data-image="' . esc_attr( $this->get_stripe_checkout_image() ) . '"
			data-locale="' . esc_attr( apply_filters( 'wpcw_stripe_checkout_locale', $this->get_locale() ) ) . '"
			data-allow-remember-me="' . esc_attr( apply_filters( 'wpcw_stripe_allow_remember_me', true ) ? 'true' : 'false' ) . '">';

		if ( ! $this->is_stripe_checkout() ) {
			$this->elements_form();
		}

		echo '</div>';
		ob_end_flush();
	}

	/**
	 * Stripe Elements Form.
	 *
	 * @since 4.3.0
	 */
	public function elements_form() {
		?>
		<fieldset id="wpcw-<?php echo esc_attr( $this->get_id() ); ?>-cc-form" class="wpcw-cc-form wpcw-form" style="background:transparent;">
			<?php do_action( 'wpcw_gateway_cc_form_start', $this->get_id() ); ?>
			<?php do_action( 'wpcw_stripe_cc_form_start', $this->get_id() ); ?>

			<?php if ( $this->is_inline_cc_form_enabled() ) { ?>
				<label for="card-element">
					<?php esc_html_e( 'Credit or Debit Card', 'wp-courseware' ); ?>
				</label>

				<div id="wpcw-stripe-card-element" style="background:#fff;padding:0 1em;border:1px solid #ddd;margin:5px 0;padding:10px 5px;">
					<!-- a Stripe Element will be inserted here. -->
				</div>
			<?php } else { ?>
				<div class="wpcw-form-row wpcw-form-row-wide">
					<label><?php esc_html_e( 'Card Number', 'wp-courseware' ); ?> <span class="required">*</span></label>
					<div class="wpcw-stripe-card-group">
						<div id="wpcw-stripe-card-element" style="background:#fff;padding:0 1em;border:1px solid #ddd;margin:5px 0;padding:10px 5px;">
							<!-- a Stripe Element will be inserted here. -->
						</div>

						<i class="wpcw-stripe-cc-brand wpcw-stripe-cc-unknown-brand" alt="Credit Card"></i>
					</div>
				</div>

				<div class="wpcw-form-row wpcw-form-row-first">
					<label><?php esc_html_e( 'Expiry Date', 'wp-courseware' ); ?> <span class="required">*</span></label>
					<div id="wpcw-stripe-exp-element" style="background:#fff;padding:0 1em;border:1px solid #ddd;margin:5px 0;padding:10px 5px;">
						<!-- a Stripe Element will be inserted here. -->
					</div>
				</div>

				<div class="wpcw-form-row wpcw-form-row-last">
					<label><?php esc_html_e( 'Card Code (CVC)', 'wp-courseware' ); ?> <span class="required">*</span></label>
					<div id="wpcw-stripe-cvc-element" style="background:#fff;padding:0 1em;border:1px solid #ddd;margin:5px 0;padding:10px 5px;">
						<!-- a Stripe Element will be inserted here. -->
					</div>
				</div>

				<div class="clear"></div>
			<?php } ?>

			<!-- Used to display form errors -->
			<div id="wpcw-strip-card-errors" class="wpcw-stripe-card-errors" role="alert"></div>

			<?php do_action( 'wpcw_gateway_cc_form_start', $this->get_id() ); ?>
			<?php do_action( 'wpcw_stripe_cc_form_start', $this->get_id() ); ?>

			<div class="clear"></div>
		</fieldset>
		<?php
	}

	/**
	 * Stripe Frontend Scripts.
	 *
	 * @since 4.3.0
	 */
	public function frontend_scripts() {
		// Setup Payment Gateway.
		$this->setup();

		// Check if enabled and on checkout page.
		if ( ! wpcw_is_checkout() || ! $this->is_available() ) {
			return;
		}

		// Check if keys are set.
		if ( ! $this->are_keys_set() ) {
			$this->log( esc_html__( 'The Stripe Payment Gateway keys are not set correctly. Please check your settings.', 'wp-courseware' ) );

			return;
		}

		// Check for SSL.
		if ( ! $this->is_test_mode() && ! is_ssl() ) {
			$this->log( esc_html__( 'Stripe live mode requires and SSL certificate to be set and used on the checkout page.', 'wp-courseware' ) );

			return;
		}

		// Styles.
		wp_register_style( 'wpcw-stripe-css', wpcw_css_file( 'stripe.css' ), array(), WPCW_VERSION );
		wp_enqueue_style( 'wpcw-stripe-css' );

		// Stripe Scripts.
		wp_register_script( 'wpcw-stripe-checkout-js', 'https://checkout.stripe.com/checkout.js', '', WPCW_VERSION, true );
		wp_register_script( 'wpcw-stripe-js', 'https://js.stripe.com/v3/', '', '3.0', true );

		// jQuery Payment.
		// wp_register_script( 'wpcw-jquery-payment', wpcw_js_file( 'payment.js' ), array( 'jquery' ), WPCW_VERSION, true );

		// Local Stripe Script.
		wp_register_script( 'wpcw-stripe', wpcw_js_file( 'stripe.js' ), array( 'wpcw-stripe-js' ), WPCW_VERSION, true );
		wp_localize_script( 'wpcw-stripe', 'wpcw_stripe_params', apply_filters( 'wpcw_stripe_js_params', array_merge( array(
			'key'                                     => $this->get_publishable_key(),
			'i18n_terms'                              => esc_html__( 'Please accept the terms and conditions first', 'wp-courseware' ),
			'i18n_required_fields'                    => esc_html__( 'Please fill in required checkout fields first', 'wp-courseware' ),
			'no_prepaid_card_msg'                     => esc_html__( 'Sorry, we\'re not accepting prepaid cards at this time. Your credit card has not been charged. Please try an alternative payment method.', 'wp-courseware' ),
			'allow_prepaid_card'                      => apply_filters( 'wpcw_stripe_allow_prepaid_card', true ) ? 'yes' : 'no',
			'inline_cc_form'                          => $this->is_inline_cc_form_enabled() ? 'yes' : 'no',
			'stripe_checkout_require_billing_address' => apply_filters( 'wpcw_stripe_checkout_require_billing_address', false ) ? 'yes' : 'no',
			'is_checkout'                             => ( wpcw_is_checkout() ) ? 'yes' : 'no',
			'return_url'                              => $this->get_return_url(),
			'ajax_api_url'                            => wpcw()->ajax->get_url( '%%endpoint%%' ),
			'ajax_api_nonce'                          => wpcw()->ajax->get_nonce(),
			'stripe_nonce'                            => wp_create_nonce( '_wpcw_stripe_nonce' ),
			'statement_desc'                          => $this->get_statement_desc(),
			'elements_options'                        => apply_filters( 'wpcw_stripe_elements_options', array() ),
			'is_stripe_checkout'                      => $this->is_stripe_checkout() ? 'yes' : 'no',
			'elements_styling'                        => apply_filters( 'wpcw_stripe_elements_styling', false ),
			'elements_classes'                        => apply_filters( 'wpcw_stripe_elements_classes', false ),
		), $this->get_localized_messages() ) ) );

		// Add Stripe Checkout if enabled.
		if ( $this->is_stripe_checkout() ) {
			wp_enqueue_script( 'wpcw-stripe-checkout-js' );
		}

		wp_enqueue_script( 'wpcw-stripe' );
	}

	/**
	 * Stripe Webhooks.
	 *
	 * @since 4.3.0
	 */
	public function webhook_handler() {
		$webhooks = new Stripe_Webhooks( $this );
		$webhooks->load();
	}

	/**
	 * Process Payment.
	 *
	 * @since 4.3.0
	 *
	 * @param int   $order_id The order id.
	 * @param bool  $retry Should it be retried on failure.
	 * @param bool  $force_save_source Force save the payment source.
	 * @param mixed $previous_error Any error from a previous request.
	 *
	 * @return array|void
	 */
	public function process_payment( $order_id, $retry = true, $force_save_source = false, $previous_error = false ) {
		try {
			// Get The Order.
			$order = wpcw_get_order( $order_id );

			// Check api is setup.
			$this->get_api();

			// Prepared Source
			$source        = $this->get_source( get_current_user_id() );
			$source_object = $source->source_object;

			// Check if we don't allow prepaid credit cards.
			if ( ! apply_filters( 'wpcw_stripe_allow_prepaid_card', true ) && $this->is_prepaid_card( $source_object ) ) {
				$localized_message = esc_html__( 'Sorry, we\'re not accepting prepaid cards at this time. Your credit card has not been charged. Please try with alternative payment method.', 'wp-courseware' );
				throw new Stripe_Exception( wpcw_print_r( $source_object, true ), $localized_message );
			}

			// Check for exception.
			if ( empty( $source->source_id ) || empty( $source->customer_id ) || empty( $source->customer_object ) ) {
				throw new Stripe_Exception( wpcw_print_r( $source, true ), esc_html__( 'Payment processing failed. Please retry again.', 'wp-courseware' ) );
			}

			/** @var \Stripe\Customer $customer_object */
			$customer_object = $source->customer_object;

			// Save Source and Customer Id to order.
			$order->update_meta( '_stripe_customer_id', $source->customer_id );
			$order->update_meta( '_stripe_source_id', $source->source_id );

			// Process Payment.
			if ( $order->get_total() > 0 ) {
				// Check minimum order amount.
				$this->validate_minimum_order_amount( $order );

				// Begin Processing.
				$this->log( sprintf( __( 'Stripe Gateway: Begin processing Order #%1$s for the amount of %2$s', 'wp-courseware' ), $order_id, html_entity_decode( $order->get_total( true ), ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );

				// Process Recurring Items if they exist.
				if ( $order->has_recurring_items() ) {
					$recurring_orders = $this->process_recurring_items( $order, $customer_object, $source_object, $retry );
					$one_time_order   = $this->process_one_time_items( $order, $customer_object, $source_object, $retry );

					if ( ! empty( $one_time_order ) ) {
						$processed_orders = array_merge( $recurring_orders, array( $one_time_order ) );
					} else {
						$processed_orders = $recurring_orders;
					}

					$completed = true;

					if ( ! empty( $processed_orders ) && is_array( $processed_orders ) ) {
						/** @var Order $processed_order */
						foreach ( $processed_orders as $processed_order ) {
							$order->add_meta( '_order_transaction_id', $processed_order->get_transaction_id(), false );
							if ( ! $processed_order->has_order_status( 'completed' ) ) {
								$completed = false;
							}
						}

						if ( $completed ) {
							$order->payment_complete( 'multiple', sprintf( __( 'Order #%s is completed with multiple transactions attached.', 'wp-courseware' ), $order->get_order_number() ) );
						}
					}
				} else {
					$one_time_order = $this->process_one_time_items( $order, $customer_object, $source_object, $retry );
					if ( $one_time_order->has_order_status( 'completed' ) ) {
						$order->payment_complete( $one_time_order->get_transaction_id(), sprintf( __( 'Order #%s completed. Transaction ID: %s', 'wp-courseware' ), $order->get_order_number(), $one_time_order->get_transaction_id() ) );
					}
				}

				// Complete Payment.
				$order->save();

				/**
				 * Action: Stripe Payment Processed.
				 *
				 * @since 4.3.0
				 *
				 * @param Order            $order The Order Object.
				 * @param \Stripe\Customer $customer_object The stripe customer object.
				 * @param \Stripe\Source   $source_object The stripe source object.
				 */
				do_action( 'wpcw_gateway_stripe_payment_processed', $order, $customer_object, $source_object );
			} elseif ( ( $order->get_total() <= 0 ) && $order->has_recurring_items() && $order->is_amount_zero_because_of_discount() ) {
				// Begin Processing.
				$this->log( sprintf( __( 'Stripe Gateway: Begin processing Order #%1$s containing subscriptions for the amount of %2$s', 'wp-courseware' ), $order_id, html_entity_decode( $order->get_total( true ), ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );

				$recurring_orders = $this->process_recurring_items( $order, $customer_object, $source_object, $retry );

				$completed = true;

				if ( ! empty( $recurring_orders ) && is_array( $recurring_orders ) ) {
					/** @var Order $recurring_order */
					foreach ( $recurring_orders as $recurring_order ) {
						$order->add_meta( '_order_transaction_id', $recurring_order->get_transaction_id(), false );
						if ( ! $recurring_order->has_order_status( 'completed' ) ) {
							$completed = false;
						}
					}

					if ( $completed ) {
						$order->payment_complete( 'multiple', sprintf( __( 'Order #%s is completed some recurring transactions attached.', 'wp-courseware' ), $order->get_order_number() ) );
					}
				}
			} else {
				$order->payment_complete();
			}

			// Empty Cart.
			wpcw()->cart->empty_cart();

			// Return response.
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		} catch ( Stripe_Exception $exception ) {
			wpcw_add_notice( $exception->getLocalizedMessage(), 'error' );

			/* translators: %1$s: Error Message, %2$s: Localized Error Message */
			$this->log( sprintf( __( '%1$s ( %2$s )', 'wp-courseware' ), $exception->getMessage(), $exception->getLocalizedMessage() ) );

			/**
			 * Action: Stripe Gatewy Process Payment Error.
			 *
			 * @since 4.3.0
			 *
			 * @param Stripe_Exception $e The stripe exception object.
			 * @param Order            $order The order object.
			 */
			do_action( 'wpcw_gateway_stripe_process_payment_error', $exception, $order );

			// Update Status
			$order->update_status( 'failed', $exception->getLocalizedMessage() );

			// Send Failed Order Email.
			if ( $order->has_status( array( 'pending', 'failed' ) ) ) {
				$this->send_failed_order_email( $order_id );
			}

			return array(
				'result'   => 'fail',
				'redirect' => '',
			);
		}
	}

	/**
	 * Process Recurring Items.
	 *
	 * @since 4.3.0
	 *
	 * @param Order            $order The order object.
	 * @param \Stripe\Customer $customer_object The stripe customer object.
	 * @param \Stripe\Source   $source_object The stripe source object.
	 * @param bool             $retry Retry if there is an error.
	 *
	 * @return array $orders An array of Order objects for further processing.
	 */
	protected function process_recurring_items( &$order, $customer_object, $source_object, $retry = true ) {
		if ( ! $recurring_items = $order->get_recurring_items() ) {
			return;
		}

		$orders = array();

		/** @var Order_Item $order_item */
		foreach ( $recurring_items as $order_item ) {
			try {
				$subscription = new Stripe_Subscription( $customer_object, $this );
				$subscription->create_subscription( $order, $order_item, $source_object );
				if ( $payment = $subscription->get_payment() ) {
					$orders[] = $payment;
				}
			} catch ( Stripe_Exception $exception ) {
				$this->log( $exception->getMessage() );
				continue;
			}
		}

		return $orders;
	}

	/**
	 * Process One Time Items.
	 *
	 * @since 4.3.0
	 *
	 * @param Order            $order The order object.
	 * @param \Stripe\Customer $customer The stripe customer object.
	 * @param \Stripe\Source   $source The stripe source object.
	 * @param bool             $retry Retry if there is an error.
	 *
	 * @return bool|Order $payment_order The payment order object or false on failure.
	 */
	protected function process_one_time_items( &$order, $customer, $source, $retry = true ) {
		$one_time_items = $order->get_one_time_items();

		// Check to make sure its not empty.
		if ( empty( $one_time_items ) ) {
			return false;
		}

		// Recurring Items Flag.
		$order_has_recurring_items = $order->has_recurring_items();

		/** @var Order $payment_order Create payment order. */
		$payment_order = $order_has_recurring_items ? $this->create_one_time_payment_order( $order ) : $order;

		// Payment Args.
		$payment_args = array(
			'currency'    => strtolower( $payment_order->get_currency() ),
			'description' => sprintf( __( 'Order #%1$s - %2$s', 'wp-courseware' ), $payment_order->get_order_number(), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ),
		);

		// Payment Metadata
		$payment_metadata = array(
			'order_id'      => $payment_order->get_order_number(),
			'student_name'  => sanitize_text_field( $payment_order->get_student_full_name() ),
			'student_email' => sanitize_email( $payment_order->get_student_email() ),
			'order_id'      => $payment_order->get_order_number(),
		);

		// Parent Order Metadata.
		if ( $parent_order_id = $payment_order->get_order_parent_id() ) {
			$payment_metadata['parent_order_id'] = $parent_order_id;
		}

		// Payment Amount.
		if ( ( $payment_order_total = $payment_order->get_total() ) > 0 ) {
			$payment_args['amount'] = $this->get_total( $payment_order_total, $payment_order->get_currency() );
		}

		// Statement Descriptor.
		if ( ( $statement_desc = $this->get_statement_desc() ) ) {
			$payment_args['statement_descriptor'] = $statement_desc;
		}

		// Send Stripe Receipt?
		if ( apply_filters( 'wpcw_stripe_send_stripe_receipt', false ) ) {
			$payment_args['receipt_email'] = $payment_order->get_student_email();
		}

		// Include Balance Transaction.
		$payment_args['expand[]'] = 'balance_transaction';

		// Set Customer
		if ( isset( $customer->id ) ) {
			$payment_args['customer'] = $customer->id;
		}

		// Set Source
		if ( isset( $source->id ) ) {
			$payment_args['source'] = $source->id;
		}

		/**
		 * Filter: Stripe Payment Metadata.
		 *
		 * @since 4.3.0
		 *
		 * @param array $payment_metadata The payment metadata.
		 * @param Order $payment_order The payment order object.
		 * @param \Stripe\Customer The customer object.
		 * @param \Stripe\Source The source object.
		 *
		 * @return array $payment_metadata The modified payment metadata.
		 */
		$payment_args['metadata'] = apply_filters( 'wpcw_stripe_one_time_payment_metadata', $payment_metadata, $payment_order, $customer, $source );

		// Save Source and Customer Id to order.
		$payment_order->update_meta( '_stripe_customer_id', $customer->id );
		$payment_order->update_meta( '_stripe_source_id', $source->id );

		/**
		 * Filter: Stripe Payment Args.
		 *
		 * @since 4.3.0
		 *
		 * @param array $payment_args The stripe payment/charge args.
		 * @param Order $payment_order The payment order object.
		 * @param \Stripe\Customer The stripe customer object.
		 * @param \Stripe\Source The stripe source object.
		 *
		 * @return array $payment_args The modified stripe payment/charge args.
		 */
		$payment_args = apply_filters( 'wpcw_stripe_one_time_payment_args', $payment_args, $payment_order, $customer, $source );

		// Log.
		$this->log( sprintf( __( 'Stripe Gateway: About to process one time payment. Payment Arguments: %s', 'wp-courseware' ), wpcw_print_r( $payment_args, true ) ) );

		// Payment.
		$payment = $this->get_api()->create_charge( $payment_args );

		// Check for Errors.
		if ( ! empty( $payment->error ) ) {
			// Customer param wrong? The user may have been deleted on stripe's end. Remove customer_id. Can be retried without.
			if ( $this->is_error_no_such_customer( $payment->error ) ) {
				delete_user_meta( $payment_order->get_student_id(), '_stripe_customer_id' );
				$payment_order->delete_meta( '_stripe_customer_id' );
			}

			// Retry?
			if ( $this->is_error_retryable( $payment->error ) ) {
				if ( $retry && ( $this->retry_interval <= 5 ) ) {
					sleep( $this->retry_interval );

					$this->retry_interval ++;

					return $this->process_one_time_items( $order, $customer, $source, $retry );
				} else {
					$localized_message = esc_html__( 'Sorry, we are unable to process your payment at this time. Please retry later.', 'wp-courseware' );

					// Add order note.
					$payment_order->add_order_note( $localized_message );

					throw new Stripe_Exception( $payment->error->message, $localized_message );
				}
			}

			$localized_messages = $this->get_localized_messages();

			if ( 'card_error' === $payment->error->type ) {
				$localized_message = isset( $localized_messages[ $payment->error->code ] ) ? $localized_messages[ $payment->error->code ] : $payment->error->localized;
			} else {
				$localized_message = isset( $localized_messages[ $payment->error->type ] ) ? $localized_messages[ $payment->error->type ] : $payment->error->localized;
			}

			// Add Order Note.
			$payment_order->add_order_note( $localized_message );

			throw new Stripe_Exception( $payment->error->message, $localized_message );
		}

		/**
		 * Action: Gateway Stripe Process Payment.
		 *
		 * @since 4.3.0
		 *
		 * @param \Stripe\Charge $payment The payment charge object.
		 * @param Order          $payment_order The payment order object.
		 */
		do_action( 'wpcw_stripe_process_one_time_payment', $payment, $payment_order );

		// Log it.
		$this->log( sprintf( 'Processing One Time Payment... Payment Data: %s', wpcw_print_r( $payment->getLastResponse()->json, true ) ) );

		// Stripe Charge Captured.
		$captured = ( isset( $payment->captured ) && $payment->captured ) ? 'yes' : 'no';
		$payment_order->update_meta( '_stripe_charge_captured', $captured );

		// Store Fees.
		if ( isset( $payment->balance_transaction ) && isset( $payment->balance_transaction->fee ) ) {
			$net = ! empty( $payment->balance_transaction->amount ) ? $this->format_balance_fee( $payment->balance_transaction, 'amount' ) : 0.00;
			$payment_order->update_meta( '_stripe_amount', $net );

			$fee = ! empty( $payment->balance_transaction->fee ) ? $this->format_balance_fee( $payment->balance_transaction, 'fee' ) : 0.00;
			$payment_order->update_meta( '_stripe_fee', $fee );

			$net = ! empty( $payment->balance_transaction->net ) ? $this->format_balance_fee( $payment->balance_transaction, 'net' ) : 0.00;
			$payment_order->update_meta( '_stripe_net', $net );

			$currency = ! empty( $payment->balance_transaction->currency ) ? strtoupper( $payment->balance_transaction->currency ) : null;
			$payment_order->update_meta( '_stripe_currency', $currency );
		}

		if ( 'yes' === $captured ) {
			if ( 'pending' === $payment->status ) {
				$payment_order->set_prop( 'transaction_id', $payment->id );

				/* translators: %1$s: Order Number, %2$s: Stripe Transaction Id */
				$payment_order->update_status( 'on-hold', sprintf( __( 'Order #%1$s awaiting payment. Stripe Txn Id: %2$s', 'wp-courseware' ), $payment_order->get_order_number(), $payment->id ) );
			}

			if ( 'succeeded' === $payment->status ) {
				/* translators: %1$s: Order Number, %2$s: Stripe Transaction Id */
				$payment_order->payment_complete( $payment->id, sprintf( __( 'Order #%1$s complete. Stripe Txn Id: %2$s', 'wp-courseware' ), $payment_order->get_order_number(), $payment->id ) );
			}

			if ( 'failed' === $payment->status ) {
				/* translators: %1$s: Order Number, %2$s: Stripe Transaction Id */
				$localized_message = sprintf( __( 'Order #%1$s payment processing failed. Stripe Txn Id: %2$s', 'wp-courseware' ), $payment_order->get_order_number(), $payment->id );

				$payment_order->add_order_note( $localized_message );

				throw new Stripe_Exception( wpcw_print_r( $response, true ), $localized_message );
			}
		} else {
			/* translators: transaction id */
			$payment_order->update_status( 'on-hold', sprintf( __( 'Stripe payment authorized (Payment/Charge ID: %s). Take further actions in Stripe to complete payment.', 'wp-courseware' ), $payment->id ) );
		}

		// Save Order.
		$payment_order->save();

		/**
		 * Action: Stripe One Time Items Processed.
		 *
		 * @since 4.3.0
		 *
		 * @param Order $payment_order The payment order.
		 * @param \Stripe\Charge The stripe payment/charge object.
		 * @param \Stripe\Customer The stripe customer object.
		 * @param \Stripe\Source The stripe source object.
		 */
		do_action( 'wpcw_stripe_one_time_items_processed', $payment_order, $payment, $customer, $source );

		return $payment_order;
	}

	/**
	 * Create Payment Order.
	 *
	 * @since 4.3.0
	 *
	 * @param Order $parent_order The parent order object.
	 *
	 * @return Order $payment_order The payment order object.
	 */
	protected function create_one_time_payment_order( &$parent_order ) {
		// Create a new payment Order.
		$payment_order = new Order();
		$payment_order->create();

		// Set Type as Payment.
		$payment_order->set_prop( 'order_type', 'payment' );

		// Parent Data.
		$parent_order_id    = $parent_order->get_order_id();
		$parent_order_data  = $parent_order->get_data( true );
		$parent_order_items = $parent_order->get_one_time_items();

		// Unset Certain Parent Data.
		$parent_data_unset = array(
			'order_id',
			'order_type',
			'order_key',
			'order_status',
			'transaction_id',
			'student_ip_address',
			'student_user_agent',
			'created_via',
			'date_created',
			'date_completed',
			'date_paid',
			'cart_hash',
			'subtotal',
			'tax',
			'total',
		);
		foreach ( $parent_data_unset as $item_to_unset ) {
			unset( $parent_order_data[ $item_to_unset ] );
		}

		// Log Information.
		$this->log( sprintf( __( 'Creating Payment Order #%s', 'wp-courseware' ), $payment_order->get_order_id() ) );
		$this->log( sprintf( __( 'Parent Order #%s', 'wp-courseware' ), $parent_order_id ) );

		// Set Data and Parent Order Id.
		$payment_order->set_props( $parent_order_data );
		$payment_order->set_prop( 'order_parent_id', $parent_order_id );

		// Set Initial Status.
		$payment_order->set_prop( 'order_status', 'pending' );

		// Set Order Items.
		$payment_order->insert_order_items( $parent_order_items );

		// Update Totals
		$payment_order->update_totals();

		// Save Order.
		if ( $payment_order->save() ) {
			/* translators: %1$s - Order Id, %2$s - Order Data. */
			$this->log( sprintf( __( 'Payment Order #%1$s saved successfully! Order Data: %2$s', 'wp-courseware' ), $payment_order->get_order_id(), wpcw_print_r( $payment_order->get_data( true ), true ) ) );
		} else {
			/* translators: %1$s - Order Id, %2$s - Order Data. */
			$this->log( sprintf( __( 'Payment Order #%1$s failed to save. Order Data: %2$s', 'wp-courseware' ), $payment_order->get_order_id(), wpcw_print_r( $payment_order->get_data( true ), true ) ) );
		}

		/**
		 * Action: Stripe One Time Payment Order.
		 *
		 * @since 4.3.0
		 *
		 * @param Order          $payment_order The payment order object.
		 * @param \Stripe\Charge $payment The stripe payment/charge object.
		 * @param Order          $parent_order The parent order object.
		 */
		do_action( 'wpcw_stripe_one_time_payment_order', $payment_order, $parent_order );

		return $payment_order;
	}

	/**
	 * Get Source.
	 *
	 * This can be a new token / source or existing.
	 *
	 * If user is logged in and / or has an account, create an account on Stripe.
	 * This way we can attribute the payment to the user to better fight fraud.
	 *
	 * @since 4.3.0
	 *
	 * @param int $student_id The student id.
	 *
	 * @return object The source object.
	 */
	protected function get_source( $student_id ) {
		$source_id       = '';
		$source_object   = '';
		$customer_id     = '';
		$customer_object = '';
		$token_id        = false;
		$is_token        = false;

		// Instantiate
		$customer = new Stripe_Customer( absint( $student_id ), $this->get_api() );

		// New CC info was entered and we have a new source to process.
		if ( ! empty( $_POST['stripe_source'] ) ) {
			$source_object = $this->get_source_object( wpcw_clean( $_POST['stripe_source'] ) );
			$source_id     = $source_object->id;

			if ( 'reusable' === $source_object->usage ) {
				$customer_source = $customer->add_source( $source_id );

				if ( ! empty( $customer_source->error ) ) {
					throw new Stripe_Exception( $customer_source->error->message, $customer_source->error->localized );
				}
			}
		} elseif ( isset( $_POST['stripe_token'] ) && 'new' !== $_POST['stripe_token'] ) {
			$stripe_token = wpcw_clean( $_POST['stripe_token'] );
			$source_id    = $stripe_token;
			$is_token     = true;
		}

		$customer_id     = ( $customer->get_id() ) ? $customer->get_id() : false;
		$customer_object = ( $customer->get_id() ) ? $customer->get_object() : false;

		return (object) array(
			'token_id'        => $token_id,
			'customer_id'     => $customer_id,
			'customer_object' => $customer_object,
			'source_id'       => $source_id,
			'source_object'   => $source_object,
		);
	}

	/**
	 * Get Source Object.
	 *
	 * @since 4.3.0
	 *
	 * @param string $source_id The stripe source id.
	 *
	 * @return \Stripe\Source The stripe source object.
	 */
	protected function get_source_object( $source_id = '' ) {
		if ( empty( $source_id ) ) {
			return '';
		}

		$source_object = $this->get_api()->get_source( $source_id );

		if ( ! empty( $source_object->error ) ) {
			throw new Stripe_Exception( $source_object->error->message, $source_object->error->localized );
		}

		return $source_object;
	}

	/**
	 * Is Prepaid Card?
	 *
	 * @since 4.3.0
	 *
	 * @param object $source_object
	 *
	 * @return bool
	 */
	protected function is_prepaid_card( $source_object ) {
		return ( $source_object && 'token' === $source_object->object && 'prepaid' === $source_object->card->funding );
	}

	/**
	 * Validate Minimum Amount.
	 *
	 * @since 4.3.0
	 *
	 * @param Order The order object.
	 */
	protected function validate_minimum_order_amount( $order ) {
		if ( ( $order->get_total() * 100 ) < $this->get_minimum_amount() ) {
			/* translators: %1$s: minimum dollar amount. */
			throw new Stripe_Exception( 'stripe-minimum-not-met', sprintf( __( 'Sorry, the minimum amount to process this order is %1$s. Update your order or choose a different payment method.', 'wp-courseware' ), wpcw_price( $this->get_minimum_amount() / 100 ) ) );
		}
	}

	/**
	 * Get Minimum Amount.
	 *
	 * @since 4.3.0
	 *
	 * @return int The minimum amount.
	 */
	protected function get_minimum_amount() {
		switch ( wpcw_get_currency() ) {
			case 'USD':
			case 'CAD':
			case 'EUR':
			case 'CHF':
			case 'AUD':
			case 'SGD':
				$minimum_amount = 50;
				break;
			case 'GBP':
				$minimum_amount = 30;
				break;
			case 'DKK':
				$minimum_amount = 250;
				break;
			case 'NOK':
			case 'SEK':
				$minimum_amount = 300;
				break;
			case 'JPY':
				$minimum_amount = 5000;
				break;
			case 'MXN':
				$minimum_amount = 1000;
				break;
			case 'HKD':
				$minimum_amount = 400;
				break;
			default:
				$minimum_amount = 50;
				break;
		}

		return absint( $minimum_amount );
	}

	/**
	 * Format Balance Fee.
	 *
	 * Stripe uses smallest denomination in currencies such as cents.
	 * We need to format the returned currency from Stripe into human readable form.
	 * The amount is not used in any calculations so returning string is sufficient.
	 *
	 * @since 4.3.0
	 *
	 * @param object $balance_transaction
	 * @param string $type
	 *
	 * @return string|void
	 */
	public function format_balance_fee( $balance_transaction, $type = 'fee' ) {
		if ( ! is_object( $balance_transaction ) ) {
			return;
		}

		if ( in_array( strtolower( $balance_transaction->currency ), $this->get_no_decimal_currencies() ) ) {
			if ( 'fee' === $type ) {
				return $balance_transaction->fee;
			}

			if ( 'amount' === $type ) {
				return $balance_transaction->amount;
			}

			return $balance_transaction->net;
		}

		if ( 'fee' === $type ) {
			return number_format( $balance_transaction->fee / 100, 2, '.', '' );
		}

		if ( 'amount' === $type ) {
			return number_format( $balance_transaction->amount / 100, 2, '.', '' );
		}

		return number_format( $balance_transaction->net / 100, 2, '.', '' );
	}

	/**
	 * Format Stripe Amount.
	 *
	 * @since 4.3.0
	 *
	 * @param sring $amount The stripe amount.
	 *
	 * @return string The formatted amount.
	 */
	public function format_stripe_amount( $amount ) {
		if ( in_array( strtolower( wpcw_get_currency() ), $this->get_no_decimal_currencies() ) ) {
			return $amount;
		}

		return number_format( $amount / 100, 2, '.', '' );
	}

	/**
	 * Send Failed Order Email.
	 *
	 * @since 4.3.0
	 *
	 * @param int $order_id The order id.
	 */
	protected function send_failed_order_email( $order_id ) {
		$emails = wpcw()->emails->get_emails();
		// if ( ! empty( $emails ) && ! empty( $order_id ) ) {
		// 	$emails['WC_Email_Failed_Order']->trigger( $order_id );
		// }
	}

	/**
	 * Error: No such customer.
	 *
	 * @since 4.3.0
	 *
	 * @param object $error The error object.
	 */
	protected function is_error_no_such_customer( $error ) {
		return ( $error && 'invalid_request_error' === $error->type && preg_match( '/No such customer/i', $error->message ) );
	}

	/**
	 * Is Error Retryable?
	 *
	 * @since 4.3.0
	 *
	 * @param object $error The error object.
	 */
	protected function is_error_retryable( $error ) {
		$retryable_errors = array( 'invalid_request_error', 'idempotency_error', 'rate_limit_error', 'api_connection_error', 'api_error' );

		return ( $error && in_array( $error->type, $retryable_errors ) );
	}

	/**
	 * Is Error Already Refunded?
	 *
	 * @since 4.3.0
	 *
	 * @param object $error The error object.
	 */
	protected function is_error_already_refunded( $error ) {
		return ( $error && 'invalid_request_error' === $error->type && preg_match( '/has already been refunded/i', $error->message ) );
	}

	/**
	 * Get Transaction Url.
	 *
	 * @since 4.3.0
	 *
	 * @param Order The order object.
	 *
	 * @return string The transaction url.
	 */
	public function get_transaction_url( $order ) {
		if ( $this->is_test_mode() ) {
			$this->transaction_url = 'https://dashboard.stripe.com/test/payments/%s';
		} else {
			$this->transaction_url = 'https://dashboard.stripe.com/payments/%s';
		}

		return parent::get_transaction_url( $order );
	}

	/**
	 * Process Refund.
	 *
	 * @since 4.3.0
	 *
	 * @param Order  $order The order object.
	 * @param null   $amount The amount to refund.
	 * @param string $reason The reason to refund it.
	 *
	 * @return bool|void
	 */
	public function process_refund( $order, $amount = null, $reason = '' ) {
		try {
			if ( ! $order instanceof Order ) {
				throw new Stripe_Exception( 'stripe-order-not-setup', __( 'The order is not setup to process a refund.', 'wp-courseware' ) );
			}

			$this->setup();

			$transaction_id = $order->get_transaction_id();

			if ( empty( $transaction_id ) ) {
				throw new Stripe_Exception( 'stripe-transaction-id-not-set', __( 'The transaction id is not setup to process the refund.', 'wp-courseware' ) );
			}

			$refund = $this->get_api()->create_refund( array(
				'charge'   => $transaction_id,
				'reason'   => 'requested_by_customer',
				'metadata' => array(
					'reason'   => $reason,
					'order_id' => $order->get_order_id(),
				),
			) );

			if ( ! empty( $refund->error ) ) {
				if ( $this->is_error_already_refunded( $refund->error ) ) {
					$order->update_status( 'refunded', $refund->error->message );

					return false;
				}

				throw new Stripe_Exception( $refund->error->type, $refund->error->message );
			}

			$refund_id     = $refund->id;
			$refund_amount = $this->format_stripe_amount( $refund->amount );

			if ( 'failed' === $refund->status ) {
				return false;
			}

			$order->add_meta( '_stripe_refund_id', $refund_id );
			$order->add_meta( '_stripe_refund_amount', $refund_amount );
			$order->update_status( 'refunded', sprintf( __( 'Refunded %1$s - Refund ID: %2$s', 'wp-courseware' ), wpcw_price( $refund_amount ), $refund_id ) );
		} catch ( Stripe_Exception $exception ) {
			$this->log( sprintf( __( 'An error occurred while trying to refund an order. Error Message: %s', 'wp-courseware' ), $exception->getLocalizedMessage() ) );

			return false;
		}

		return true;
	}

	/**
	 * Process Subscription Cancellation.
	 *
	 * @since 4.3.0
	 *
	 * @param Subscription $subscription The subscription object.
	 */
	public function process_subscription_cancellation( $subscription ) {
		try {
			if ( ! $subscription instanceof Subscription ) {
				throw new Stripe_Exception( 'stripe-subscription-not-setup', __( 'The subscription is not setup. Aborting cancellation...', 'wp-courseware' ) );
			}

			$this->setup();

			$profile_id = $subscription->get_profile_id();

			if ( empty( $profile_id ) ) {
				throw new Stripe_Exception( 'stripe-subscription-profile-id-not-set', __( 'The subscription profile id is not setup. Aborting cancellation...', 'wp-courseware' ) );
			}

			if ( ! $subscription->has_status( 'refunded' ) ) {
				$stripe_subscription = $this->get_api()->update_subscription( $profile_id, array( 'cancel_at_period_end' => true ) );
			} else {
				$stripe_subscription = $this->get_api()->get_subscription( $profile_id );
			}

			if ( ! empty( $stripe_subscription->error ) ) {
				throw new Stripe_Exception( $stripe_subscription->error->type, $stripe_subscription->error->message );
			}

			if ( true === $stripe_subscription->cancel_at_period_end ) {
				$subscription->cancel_at_period_end();
			} else {
				$stripe_subscription->cancel();
				$subscription->cancel();
			}
		} catch ( Stripe_Exception $exception ) {
			$this->log( sprintf( __( 'An error occurred while trying to cancel the subscription. Error Message: %s', 'wp-courseware' ), $exception->getLocalizedMessage() ) );

			return false;
		}

		return true;
	}

	/**
	 * Process Subscription Completion.
	 *
	 * @since 4.6.0
	 *
	 * @param Subscription $subscription The subscription object.
	 */
	public function process_subscription_completion( $subscription ) {
		try {
			if ( ! $subscription instanceof Subscription ) {
				throw new Stripe_Exception( 'stripe-subscription-not-setup', __( 'The subscription is not setup. Aborting competion...', 'wp-courseware' ) );
			}

			$this->setup();

			$profile_id = $subscription->get_profile_id();

			if ( empty( $profile_id ) ) {
				throw new Stripe_Exception( 'stripe-subscription-profile-id-not-set', __( 'The subscription profile id is not setup. Aborting completion...', 'wp-courseware' ) );
			}

			$stripe_subscription = $this->get_api()->get_subscription( $profile_id );

			if ( ! empty( $stripe_subscription->error ) ) {
				throw new Stripe_Exception( $stripe_subscription->error->type, $stripe_subscription->error->message );
			}

			$stripe_subscription->cancel();
			$subscription->complete();
		} catch ( Stripe_Exception $exception ) {
			$this->log( sprintf( __( 'An error occurred while trying to complete the subscription. Error Message: %s', 'wp-courseware' ), $exception->getLocalizedMessage() ) );

			return false;
		}

		return true;
	}

	/**
	 * Get Transaction Link.
	 *
	 * @since 4.3.0
	 *
	 * @param string $transaction_id The transaction id.
	 *
	 * @return string $transaction_link The transaction link.
	 */
	public function get_transaction_link( $transaction_id ) {
		$transaction_link = $transaction_id;

		if ( ! empty( $transaction_id ) ) {
			$link = $this->is_test_mode() ? 'https://dashboard.stripe.com/test' : 'https://dashboard.stripe.com';
			$link = sprintf( '%s/payments/%s', $link, $transaction_id );

			$transaction_link = sprintf( '<a target="_blank" href="%s">%s</a>', $link, $transaction_id );
		}

		return $transaction_link;
	}

	/**
	 * Get Profile Link.
	 *
	 * @since 4.3.0
	 *
	 * @param string $profile_id The profile id.
	 *
	 * @return string $profile_link The profile link link.
	 */
	public function get_profile_link( $profile_id ) {
		$profile_link = $profile_id;

		if ( ! empty( $profile_id ) ) {
			$link = $this->is_test_mode() ? 'https://dashboard.stripe.com/test' : 'https://dashboard.stripe.com';
			$link = sprintf( '%s/subscriptions/%s', $link, $profile_id );

			$profile_link = sprintf( '<a target="_blank" href="%s">%s</a>', $link, $profile_id );
		}

		return $profile_link;
	}

	/**
	 * Subscription Profile Link.
	 *
	 * @since 4.3.0
	 *
	 * @param string       $profile_id The profile id.
	 * @param Subscription $subscription The subscription object.
	 *
	 * @return string $profile_link The PayPal profile link.
	 */
	public function subscription_profile_link( $profile_id, $subscription ) {
		$this->setup();

		return $this->get_profile_link( $profile_id );
	}

	/**
	 * Subscription Transaction Link.
	 *
	 * @since 4.3.0
	 *
	 * @param string       $transaction_id The transaction id.
	 * @param Subscription $subscription The subscription object.
	 *
	 * @return string $profile_link The PayPal profile link.
	 */
	public function subscription_transaction_link( $transaction_id, $subscription ) {
		$this->setup();

		return $this->get_transaction_link( $transaction_id );
	}

	/**
	 * Order Transaction Link.
	 *
	 * @since 4.3.0
	 *
	 * @param string $transaction_id The transaction id.
	 * @param Order  $order The order object.
	 *
	 * @return string $profile_link The PayPal profile link.
	 */
	public function order_transaction_link( $transaction_id, $order ) {
		$this->setup();

		return $this->get_transaction_link( $transaction_id );
	}
}
