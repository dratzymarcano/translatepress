<?php


if ( !defined('ABSPATH' ) )
    exit();

function lrp_register_fix_broken_html( $settings_array ){
    $settings_array[] = array(
        'name'          => 'fix_broken_html',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Fix broken HTML', 'linguapress' ),
        'description'   => wp_kses( __( 'General attempt to fix broken or missing HTML on translated pages.<br/>', 'linguapress' ), array( 'br' => array(), 'strong' => array() ) ),
        'id'            => 'troubleshooting',
        'container'     => 'troubleshooting'
    );
    return $settings_array;
}

add_filter('lrp_try_fixing_invalid_html', 'lrp_fix_broken_html');
function lrp_fix_broken_html($allow) {

    $option = get_option( 'lrp_advanced_settings', true );
    if ( isset( $option['fix_broken_html'] ) && $option['fix_broken_html'] === 'yes' ) {
        return true;
    }
    return $allow;
}