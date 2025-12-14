<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_register_exclude_words_from_auto_translate', 100 );
function lrp_register_exclude_words_from_auto_translate( $settings_array ){
    $settings_array[] = array(
        'name'          => 'exclude_words_from_auto_translate',
        'type'          => 'list_input',
        'columns'       => array(
            'words' => __('String', 'linguapress' ),
        ),
        'label'         => esc_html__( 'Exclude strings from automatic translation', 'linguapress' ),
        'description'   => wp_kses( __( 'Do not automatically translate these strings (ex. names, technical words...)<br>Paragraphs containing these strings will still be translated except for the specified part.', 'linguapress' ), array( 'br' => array() ) ),
        'id'            => 'exclude_strings',
        'container'     => 'exclude_at_strings'
    );
    return $settings_array;
}


add_filter( 'lrp_exclude_words_from_automatic_translation', 'lrp_exclude_words_from_auto_translate' );
function lrp_exclude_words_from_auto_translate( $exclude_words ){
    $option = get_option( 'lrp_advanced_settings', true );
    $add_skip_selectors = array( );
    if ( isset( $option['exclude_words_from_auto_translate'] ) && is_array( $option['exclude_words_from_auto_translate']['words'] ) ) {
        $exclude_words = array_merge( $exclude_words, $option['exclude_words_from_auto_translate']['words'] );
    }

    return $exclude_words;
}

