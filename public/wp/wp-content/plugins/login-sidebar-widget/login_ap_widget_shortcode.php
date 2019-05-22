<?php
function login_widget_ap_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'title' => '',
     ), $atts ) );
     
	ob_start();
	$aplf = new ap_login_form;
	if($title){
		echo '<h2>'. esc_html( $title ) .'</h2>';
	}
	$aplf->login_form();
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}

function forgot_password_ap_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'title' => '',
     ), $atts ) );
     
	ob_start();
	$fpc = new ap_forgot_pass_class;
	if($title){
		echo '<h2>'. esc_html( $title ) .'</h2>';
	}
	$fpc->forgot_pass_form();
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}
