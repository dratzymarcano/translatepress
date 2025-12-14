<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_show_regular_tab_in_string_translation', 525 );
function lrp_show_regular_tab_in_string_translation( $settings_array ){
	$settings_array[] = array(
		'name'          => 'show_regular_tab_in_string_translation',
		'type'          => 'checkbox',
		'label'         => esc_html__( 'Show regular strings tab in String Translation', 'linguapress' ),
		'description'   => wp_kses( __( 'Adds an additional tab on the String Translation interface that allows editing translations of user-inputted strings.', 'linguapress' ), array( 'br' => array() ) ),
        'id'            => 'debug',
        'container'     => 'debug'
    );
	return $settings_array;
}

add_filter( 'lrp_show_regular_strings_string_translation', 'lrp_show_regular_strings_tab_string_translation' );
function lrp_show_regular_strings_tab_string_translation( $enable ){
	$option = get_option( 'lrp_advanced_settings', true );
	if ( isset( $option['show_regular_tab_in_string_translation'] ) && $option['show_regular_tab_in_string_translation'] === 'yes' ){
		return true;
	}
	return $enable;
}
