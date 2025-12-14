<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_register_disable_dynamic_translation', 30 );
function lrp_register_disable_dynamic_translation( $settings_array ){
	$settings_array[] = array(
		'name'          => 'disable_dynamic_translation',
		'type'          => 'checkbox',
		'label'         => esc_html__( 'Disable dynamic translation', 'linguapress' ),
		'description'   => wp_kses( __( 'It disables detection of strings displayed dynamically using JavaScript. <br/>Strings loaded via a server side AJAX call will still be translated.', 'linguapress' ), array( 'br' => array() ) ),
        'id'            => 'troubleshooting',
        'container'     => 'troubleshooting'

    );
	return $settings_array;
}

add_filter( 'lrp_enable_dynamic_translation', 'lrp_adst_disable_dynamic' );
function lrp_adst_disable_dynamic( $enable ){
	$option = get_option( 'lrp_advanced_settings', true );
	if ( isset( $option['disable_dynamic_translation'] ) && $option['disable_dynamic_translation'] === 'yes' ){
		return false;
	}
	return $enable;
}

add_filter( 'lrp_editor_missing_scripts_and_styles', 'lrp_adst_disable_dynamic2' );
function lrp_adst_disable_dynamic2( $scripts ){
	$option = get_option( 'lrp_advanced_settings', true );
	if ( isset( $option['disable_dynamic_translation'] ) && $option['disable_dynamic_translation'] === 'yes' ){
		unset($scripts['lrp-translate-dom-changes.js']);
	}
	return $scripts;
}