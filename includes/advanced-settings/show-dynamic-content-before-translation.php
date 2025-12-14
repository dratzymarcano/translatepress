<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_register_show_dynamic_content_before_translation', 20 );
function lrp_register_show_dynamic_content_before_translation( $settings_array ){
	$settings_array[] = array(
		'name'          => 'show_dynamic_content_before_translation',
		'type'          => 'checkbox',
		'label'         => esc_html__( 'Fix missing dynamic content', 'linguapress' ),
		'description'   => wp_kses( __( 'May help fix missing content inserted using JavaScript. <br> It shows dynamically inserted content in original language for a moment before the translation request is finished.', 'linguapress' ), array( 'br' => array()) ),
        'id'            => 'troubleshooting',
        'container'     => 'troubleshooting'
    );
	return $settings_array;
}


/**
* Apply "show dynamic content before translation" fix only on front page
*/
add_filter( 'lrp_show_dynamic_content_before_translation', 'lrp_show_dynamic_content_before_translation' );
function lrp_show_dynamic_content_before_translation( $allow ){
	$option = get_option( 'lrp_advanced_settings', true );
	if ( isset( $option['show_dynamic_content_before_translation'] ) && $option['show_dynamic_content_before_translation'] === 'yes' ){
		return true;
	}
	return $allow;
}
