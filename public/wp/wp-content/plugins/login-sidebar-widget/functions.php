<?php

if(!function_exists( 'start_session_if_not_started' )){
	function start_session_if_not_started(){
		if(!session_id()){
			@session_start();
		}
	}
}

if(!function_exists('get_login_error_message_text')){
	function get_login_error_message_text( $errors ){	
		$code = $errors->get_error_code();
		$lap_invalid_username = get_option('lap_invalid_username');
		$lap_invalid_email = get_option('lap_invalid_email');
		$lap_invalid_password = get_option('lap_invalid_password');
		
		if($code == 'invalid_username'){
			if($lap_invalid_username){
				$error = $lap_invalid_username;
			} else {
				$error = $errors->get_error_message();
			}
		} else if($code == 'invalid_email'){
			if($lap_invalid_email){
				$error = $lap_invalid_email;
			} else {
				$error = $errors->get_error_message();
			}
		} else if($code == 'incorrect_password'){
			if($lap_invalid_password){
				$error = $lap_invalid_password;
			} else {
				$error = $errors->get_error_message();
			}
		} else {
			$error = apply_filters( 'lsw_login_errors', $errors );
		}
		return $error;
	}
}

if(!function_exists('lsw_login_error_message')){
	function lsw_login_error_message( $error ){	
		if( is_wp_error( $error ) ) {
			$error = $error->get_error_message();
		}
		return $error;
	}
}

if(!function_exists('login_widget_ap_text_domain')){
	function login_widget_ap_text_domain(){
		load_plugin_textdomain('login-sidebar-widget', FALSE, basename( dirname( __FILE__ ) ) .'/languages');
	}
}

if(!function_exists('lwws_user_captcha_field_no_auto')){
	function lwws_user_captcha_field_no_auto(){
		return 'autocomplete="off"';
	}
}
