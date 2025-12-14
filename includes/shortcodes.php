<?php

if ( !defined('ABSPATH' ) )
    exit();

// add conditional language shortcode
add_shortcode( 'lrp_language', 'lrp_language_content');

/* ---------------------------------------------------------------------------
 * Shortcode [lrp_language language="en_EN"] [/lrp_language]
 * --------------------------------------------------------------------------- */


function lrp_language_content( $attr, $content = null ){

    global $LRP_LANGUAGE_SHORTCODE;
    if (!isset($LRP_LANGUAGE_SHORTCODE)){
        $LRP_LANGUAGE_SHORTCODE = array();
    }

    $LRP_LANGUAGE_SHORTCODE[] = $content;

    extract(shortcode_atts(array(
        'language' => '',
    ), $attr));

    $current_language = get_locale();

    if( $current_language == $language ){
        $output = do_shortcode($content);
    }else{
        $output = "";
    }

    return $output;
}

add_filter('lrp_exclude_words_from_automatic_translation', 'lrp_add_shortcode_content_to_excluded_words_from_auto_translation');

function lrp_add_shortcode_content_to_excluded_words_from_auto_translation($excluded_words){

    global $LRP_LANGUAGE_SHORTCODE;
    if (!isset($LRP_LANGUAGE_SHORTCODE)){
        $LRP_LANGUAGE_SHORTCODE = array();
    }

    $excluded_words = array_merge($excluded_words, $LRP_LANGUAGE_SHORTCODE);

    return $excluded_words;

}