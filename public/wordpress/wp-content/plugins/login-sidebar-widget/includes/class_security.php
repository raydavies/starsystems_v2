<?php
if(!class_exists('login_widget_admin_security')){
	class login_widget_admin_security {
		
		public function __construct(){
			$captcha_on_admin_login = (get_option('captcha_on_admin_login') == 'Yes'?true:false);
			if($captcha_on_admin_login and in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) )){
				add_action( 'login_form', array( $this, 'security_add' ) );
			}
			
			$login_ap_forgot_pass_link = get_option('login_ap_forgot_pass_link');
			if($login_ap_forgot_pass_link and !in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ){	
				add_filter( 'lostpassword_url', array( $this, 'ap_lost_password_url_filter'), 10, 2 );
			}
			
			add_action ( 'ap_login_log_front', array( $this, 'ap_login_log_front_action'), 1, 1 );
			add_filter( 'authenticate', array( $this, 'myplugin_auth_signon'), 30, 3 );
		
			$captcha_on_user_login = (get_option('captcha_on_user_login') == 'Yes'?true:false);
			if($captcha_on_user_login){
				add_action( 'login_ap_form', array( $this, 'security_add_user' ) );
			}
	
			if( in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ){
				add_action('wp_login', array ( $this, 'check_ap_login_success' ) );
				add_filter('login_errors', array( $this, 'check_ap_login_failed' ) );
			}
		}
		
		public function ap_lost_password_url_filter( $lostpassword_url, $redirect ) {
			$login_ap_forgot_pass_link = get_option('login_ap_forgot_pass_link');
			return esc_url( get_permalink($login_ap_forgot_pass_link) );
		}
	
		public function check_ap_login_success(){
			$lla = new login_log_adds;
			$lla->log_add($_SERVER['REMOTE_ADDR'], 'Login success', date("Y-m-d H:i:s"), 'success');
		}
		
		public function check_ap_login_failed( $error ){	
			global $errors;
			$lla = new login_log_adds;
			
			if(is_wp_error($errors)) {
				$err_codes = $errors->get_error_codes();
			} else {
				return $error;
			}
			
			if ( in_array( 'invalid_username', $err_codes ) or in_array( 'invalid_email', $err_codes ) or in_array( 'incorrect_password', $err_codes ) ) {
				$lla->log_add($_SERVER['REMOTE_ADDR'], 'Error in login', date("Y-m-d H:i:s"), 'failed');
			}
			
			return $error;
		}
		
		public function ap_login_log_front_action( $error ){
			$lla = new login_log_adds;
			$err_codes = $error->get_error_codes();
			if ( in_array( 'invalid_username', $err_codes ) or in_array( 'invalid_email', $err_codes ) or in_array( 'incorrect_password', $err_codes ) ) {
				$lla->log_add($_SERVER['REMOTE_ADDR'], 'Error in login', date("Y-m-d H:i:s"), 'failed');
			}
			
		}
		
		public function security_add(){
			include( LSW_DIR_PATH . '/view/admin/captcha.php');
		}
	
		public function myplugin_auth_signon( $user, $username, $password ) {
			start_session_if_not_started();
			$lla = new login_log_adds;
			
			$captcha_on_admin_login = (get_option('captcha_on_admin_login') == 'Yes'?true:false);
			if($captcha_on_admin_login){
				if( isset($_POST['admin_captcha']) and sanitize_text_field($_POST['admin_captcha']) != $_SESSION['lsw_captcha_code'] ){
					$lla->log_add($_SERVER['REMOTE_ADDR'], 'Security code do not match', date("Y-m-d H:i:s"), 'failed');
					return new WP_Error( 'error_security_code', __( "Security code do not match.", "login-sidebar-widget" ) );
				}
			}
			
			$captcha_on_user_login = (get_option('captcha_on_user_login') == 'Yes'?true:false);
			if( $captcha_on_user_login and (isset($_POST['user_captcha']) and sanitize_text_field($_POST['user_captcha']) != $_SESSION['lsw_captcha_code']) ){
				$lla->log_add($_SERVER['REMOTE_ADDR'], 'Security code do not match', date("Y-m-d H:i:s"), 'failed');
				return new WP_Error( 'error_security_code', __( "Security code do not match.", "login-sidebar-widget" ) );
			} 
			
			// All In One WP Security //
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( 'all-in-one-wp-security-and-firewall/wp-security.php' ) ) {
				global $aio_wp_security;
				if ( $aio_wp_security->configs->get_value('aiowps_enable_login_captcha') == '1' ){
					$captcha_error = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Your answer was incorrect - please try again.', 'all-in-one-wp-security-and-firewall'));
					$captcha_answer = filter_input(INPUT_POST, 'aiowps-captcha-answer', FILTER_VALIDATE_INT);
		
					$captcha_temp_string = filter_input(INPUT_POST, 'aiowps-captcha-temp-string', FILTER_SANITIZE_STRING);
					if ( is_null($captcha_temp_string) ){
						$lla->log_add($_SERVER['REMOTE_ADDR'], 'Security answer is incorrect', date("Y-m-d H:i:s"), 'failed');
						return $captcha_error;
					}
					$captcha_secret_string = $aio_wp_security->configs->get_value('aiowps_captcha_secret_key');
					$submitted_encoded_string = base64_encode($captcha_temp_string.$captcha_secret_string.$captcha_answer);
					$trans_handle = sanitize_text_field(filter_input(INPUT_POST, 'aiowps-captcha-string-info', FILTER_SANITIZE_STRING));
					$captcha_string_info_trans = (AIOWPSecurity_Utility::is_multisite_install() ? get_site_transient('aiowps_captcha_string_info_'.$trans_handle) : get_transient('aiowps_captcha_string_info_'.$trans_handle));
					if ( $submitted_encoded_string !== $captcha_string_info_trans ){
						$lla->log_add($_SERVER['REMOTE_ADDR'], 'Security answer is incorrect', date("Y-m-d H:i:s"), 'failed');
						return $captcha_error;
					}
				}
			}
			// All In One WP Security //
			
			return $user;
		}
		
		public function security_add_user(){
			include( LSW_DIR_PATH . '/view/frontend/captcha.php');
		}
	}
}

if(!function_exists('security_init')){
	function security_init(){
		new login_widget_admin_security;
	}
}

