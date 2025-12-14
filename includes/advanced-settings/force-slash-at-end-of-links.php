<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter('lrp_register_advanced_settings', 'lrp_register_force_slash_in_home_url', 1071);
function lrp_register_force_slash_in_home_url($settings_array)
{
    $settings_array[] = array(
        'name' => 'force_slash_at_end_of_links',
        'type' => 'checkbox',
        'label' => esc_html__('Force slash at end of home url:', 'linguapress'),
        'description' => wp_kses(__('Ads a slash at the end of the home_url() function', 'linguapress'), array('br' => array())),
        'id'            => 'miscellaneous_options',
        'container'     => 'miscellaneous_options'
    );
    return $settings_array;
}
