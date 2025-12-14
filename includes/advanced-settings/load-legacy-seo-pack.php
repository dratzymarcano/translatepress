<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_register_load_legacy_seo_pack', 90 );
function lrp_register_load_legacy_seo_pack( $settings_array ){
    // only add this if seo pack is active
    $add_ons_settings = get_option( 'lrp_add_ons_settings', array() );
    if( isset( $add_ons_settings['tp-add-on-seo-pack/tp-seo-pack.php'] ) && $add_ons_settings['tp-add-on-seo-pack/tp-seo-pack.php'] ){
        $settings_array[] = array(
            'name'          => 'load_legacy_seo_pack',
            'type'          => 'checkbox',
            'label'         => esc_html__( 'Load legacy SEO Pack Add-On', 'linguapress' ),
            'description'   => wp_kses( __( 'In case the recent migration to the new slug rewrite is causing trouble, set this to Yes to use the old method <br> Please <a href="https://linguapress.com/support/open-ticket/" target="_blank">open a support ticket</a> letting us know of the issues you are having.', 'linguapress' ), array( 'br' => array(), 'a' => array( 'href' => array(), 'target' => array() ) ) ),
            'id'            => 'troubleshooting',
            'container'     => 'troubleshooting'
        );
    }

    return $settings_array;
}
