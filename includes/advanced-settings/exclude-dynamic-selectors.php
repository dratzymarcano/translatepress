<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_register_skip_dynamic_selectors', 110 );
function lrp_register_skip_dynamic_selectors( $settings_array ){
	$settings_array[] = array(
		'name'          => 'skip_dynamic_selectors',
		'type'          => 'list_input',
		'columns'       => array(
			'selector' => __('Selector', 'linguapress' ),
		),
		'label'         => esc_html__( 'Exclude from dynamic translation', 'linguapress' ),
		'description'   => wp_kses( __( 'Do not dynamically translate strings that are found in html nodes matching these selectors.<br>Excludes all the children of HTML nodes matching these selectors from being translated using JavaScript.<br/>These strings will still be translated on the server side if possible.', 'linguapress' ), array( 'br' => array() ) ),
        'id'            => 'exclude_strings',
        'container'     => 'exclude_dynamic_strings'
	);
	return $settings_array;
}


 add_filter( 'lrp_skip_selectors_from_dynamic_translation', 'lrp_skip_dynamic_translation_for_selectors' );
function lrp_skip_dynamic_translation_for_selectors( $skip_selectors ){
	$option = get_option( 'lrp_advanced_settings', true );
	$add_skip_selectors = array( );
	if ( isset( $option['skip_dynamic_selectors'] ) && is_array( $option['skip_dynamic_selectors']['selector'] ) ) {
		$add_skip_selectors = $option['skip_dynamic_selectors']['selector'];
	}
	return array_merge( $skip_selectors, $add_skip_selectors );
}
