<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter('lrp_register_advanced_settings', 'lrp_register_enable_numerals_translation', 1081);
function lrp_register_enable_numerals_translation($settings_array)
{
    $settings_array[] = array(
        'name' => 'enable_numerals_translation',
        'type' => 'checkbox',
        'label' => esc_html__('Translate numbers and numerals', 'linguapress'),
        'description' => esc_html__('Enable translation of numbers ( e.g. phone numbers)', 'linguapress'),
        'id'            => 'miscellaneous_options',
        'container'     => 'miscellaneous_options'
    );
    return $settings_array;
}
