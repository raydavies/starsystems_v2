<?php
class login_settings {

	public $default_style = '.log_forms { width: 98%; padding: 5px; border: 1px solid #CCC; margin: 2px; box-sizing:border-box; } .log-form-group{ margin: 0px; width: 100%; padding:5px; } .log_forms input[type=text], input[type=password] { width: 100%; padding: 7px 0 7px 4px !important; border: 1px solid #E3E3E3; margin:0px !important; } .log_forms input[type=submit] { width: 100%; padding: 7px; border: 1px solid #7ac9b7; } .log_forms input[type=text]:focus, input[type=password]:focus { border-color: #4697e4; } .lw-error{ color:#ff0000; } input.lw-error{ border:1px solid #ff0000 !important; }';
	
	public function __construct() {
		$this->load_settings();
	}
	
	public function login_widget_ap_save_settings(){
		global $lsw_default_options_data;
		
		if(isset($_POST['option']) and $_POST['option'] == "login_widget_ap_save_settings"){
			
			if ( ! isset( $_POST['login_widget_ap_field'] )  || ! wp_verify_nonce( $_POST['login_widget_ap_field'], 'login_widget_ap_action' ) ) {
			   wp_die( __('Sorry, your nonce did not verify.', 'login-sidebar-widget'));
			   exit;
			} 
			$lmc = new login_message_class;
			
			if( is_array($lsw_default_options_data) ){
				foreach( $lsw_default_options_data as $key => $value ){
					if ( !empty( $_REQUEST[$key] ) ) {
						if( $value['sanitization'] == 'sanitize_text_field' ){
							update_option( $key, sanitize_text_field($_REQUEST[$key]) );
						} elseif( $value['sanitization'] == 'esc_html' ){
							update_option( $key, esc_html($_REQUEST[$key]) );
						} elseif( $value['sanitization'] == 'esc_textarea' ){
							update_option( $key, esc_textarea($_REQUEST[$key]) );
						} else {
							update_option( $key, sanitize_text_field($_REQUEST[$key]) );
						}
					} else {
						delete_option( $key );
					}
				}
			}
			
			if(isset( $_POST['load_default_style'] ) and sanitize_text_field($_POST['load_default_style']) == "Yes"){
				update_option( 'custom_style_ap', sanitize_text_field($this->default_style) );
			} else {
				update_option( 'custom_style_ap',  sanitize_text_field($_POST['custom_style_ap']) );
			}
			
			$lmc->add_message('Settings updated successfully.','updated');
		}
	}
	
	public function removeslashes($string){
		$string=implode("",explode("\\",$string));
		return stripslashes(trim($string));
	}

	public function login_widget_ap_options () {
		global $wpdb, $lsw_default_options_data;
		
		$lmc = new login_message_class;
		
		$stripslashes = array('custom_style_ap', 'forgot_password_link_mail_subject', 'forgot_password_link_mail_body', 'new_password_mail_subject', 'new_password_mail_body', 'login_sidebar_widget_from_email', 'lap_invalid_username', 'lap_invalid_email', 'lap_invalid_password' );
		
		if( is_array($lsw_default_options_data) ){
			foreach( $lsw_default_options_data as $key => $value ){
				if( is_array($stripslashes) and in_array($key, $stripslashes) ){
					$$key = $this->removeslashes( get_option($key) );
				} else {
					$$key = get_option($key);
				}
			}
		}
		
		$custom_style_ap = $this->removeslashes(get_option('custom_style_ap'));
		
		echo '<div class="wrap">';
		$lmc->show_message();
	
		self :: fb_login_pro_add();
		self :: social_login_so_setup_add();
		self :: help_support();
		self :: wp_register_profile_add();
	
		form_class::form_open();
		wp_nonce_field('login_widget_ap_action','login_widget_ap_field');
		form_class::form_input('hidden','option','','login_widget_ap_save_settings');
		include( LSW_DIR_PATH . '/view/admin/settings.php');
		form_class::form_close();
		
		self :: donate(); 
		echo '</div>';
	}
		
	public function login_widget_ap_menu () {
		add_menu_page( 'Login Widget', 'Login Widget Settings', 'activate_plugins', 'login_widget_ap', array( $this,'login_widget_ap_options' ));
	}
	
	public function load_settings(){
		add_action( 'admin_menu' , array( $this, 'login_widget_ap_menu' ) );
		add_action( 'admin_init', array( $this, 'login_widget_ap_save_settings' ) );
	}
	
	private static function wp_register_profile_add(){
		include( LSW_DIR_PATH . '/view/admin/register_pro_add.php');
	}
	
	public static function help_support(){
		include( LSW_DIR_PATH . '/view/admin/help.php');
	}
	
	private static function fb_login_pro_add(){
		include( LSW_DIR_PATH . '/view/admin/login_pro_add.php');
	}
	
	private static function social_login_so_setup_add(){
		include( LSW_DIR_PATH . '/view/admin/social_login_add.php');
	}
	
	public static function donate(){
		include( LSW_DIR_PATH . '/view/admin/donate.php');
	}
}