<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_register_remove_duplicate_entries_from_db', 530 );
function lrp_register_remove_duplicate_entries_from_db( $settings_array ){
    $settings_array[] = array(
        'name'          => 'remove_duplicate_entries_from_db',
        'type'          => 'text',
        'label'         => esc_html__( 'Optimize LinguaPress database tables', 'linguapress' ),
        'description'   => wp_kses_post( sprintf( __( '<a href="%s">Click here</a> to access the database optimization tool.', 'linguapress' ), admin_url('admin.php?page=lrp_remove_duplicate_rows') ) ) . '<br>' . esc_html__('It helps remove possible duplicate translations, clear unnecessary data and repair possible metadata issues.','linguapress') . '<br>' . wp_kses_post(sprintf( __( '<a href="%s" target="_blank">Here</a> you can observe the last 5 SQL errors relevant to LinguaPress if they exist.', 'linguapress' ), admin_url('admin.php?page=lrp_error_manager') ) ),
        'id'            => 'debug',
        'container'     => 'debug'
    );
    return $settings_array;
}
