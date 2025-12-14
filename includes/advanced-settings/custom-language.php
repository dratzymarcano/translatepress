<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter('lrp_register_advanced_settings', 'lrp_register_custom_language', 2285);
/*
 * To use the 'mixed' type for advanced settings, there needs to be specified the type of the control
 * There are 4 options to choose from:
 * text: simple textbox
 * textarea: classic textarea used in TP advanced options
 * select: a dropdown select box with the possible options set in a sub-array
 *  like 'option_name'   => array ('label'=> esc_html__( 'Option label', 'linguapress' ), 'type' => 'select', 'values' => array ( __('Volvo','linguapress') , __('Saab', 'linguapress'), __('Scania', 'linguapress') ) ),
 *
 *
 * checkbox: a classic checkbox with the checked value always set to 'yes' and the unchecked value to empty.
 * For the elements that don't require pre-determined values, leave the 'values' array empty
 *
 */
function lrp_register_custom_language($settings_array){
    $first_description = wp_kses(  __( 'To edit an existing LinguaPress language, input the language code and fill in only the columns you want to overwrite (e.g. Language name, Flag).<br>You can also add new custom languages. They will be available under General settings, All Languages list, where the URL slug can be edited.' , 'linguapress' ), [ 'br' => [] ] );
    $second_description = wp_kses(  __( 'For custom flag, first upload the image in media library then paste the URL.<br>Changing or deleting a custom language will impact translations and site URL\'s.<br>The Language code and the ISO Code should contain only alphabetical values, numerical values, "-" and "_".<br>The ISO Codes can be found on <a href = "https://cloud.google.com/translate/docs/languages" target = "_blank">Google ISO Codes</a> and <a href = "https://www.deepl.com/docs-api/translating-text/" target = "_blank">DeepL Target Codes</a>.' , 'linguapress' ), array( 'br' => array(), 'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ) ));

    $settings_array[] = array(
		'name'          => 'custom_language',
		'columns'       => array (
                            'cuslangcode' => array ('label' => esc_html__( 'Language code', 'linguapress' ), 'type' => 'text', 'values' => '', 'placeholder' => 'e.g. en_US', 'required' => true ),
                            'cuslangname' => array ('label' => esc_html__( 'Language name', 'linguapress' ), 'type' => 'text', 'values' => '', 'placeholder' => '', 'required' => false ),
                            'cuslangnative' => array ('label' => esc_html__( 'Native name', 'linguapress' ), 'type' => 'text', 'values' => '', 'placeholder' => '', 'required' => false ),
                            'cuslangiso' => array ('label' => esc_html__( 'ISO Code', 'linguapress' ), 'type' => 'text', 'values' => '', 'placeholder' => 'e.g. en', 'required' => false ),
                            'cuslangflag' => array ('label' => esc_html__( 'Flag URL', 'linguapress' ), 'type' => 'text', 'values' => '', 'placeholder' => '', 'required' => false ),
							'cuslangisrtl' => array ('label' => esc_html__( 'Text RTL', 'linguapress' ), 'type' => 'checkbox', 'values' => '', 'placeholder' => '', 'required' => false ),
		),
		'type'          => 'mixed',
		'label'         => esc_html__( 'Custom language', 'linguapress' ),
        /* phpcs:ignore */
        'first_description'  => $first_description,
        /* phpcs:ignore */
        'second_description' => $second_description,
        'id'            => 'custom_language',
        'container'     => 'custom_language',
    );

    return $settings_array;
}
