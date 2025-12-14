<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_show_opposite_flag_language_switcher_shortcode', 1250 );
function lrp_show_opposite_flag_language_switcher_shortcode( $settings_array ){
    $settings_array[] = array(
        'name'          => 'show_opposite_flag_language_switcher_shortcode',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Show opposite language in the language switcher', 'linguapress' ),
        'description'   => wp_kses( __( 'Transforms the language switcher into a button showing the other available language, not the current one.<br> Only works when there are exactly two languages, the default one and a translation one.<br>This will affect the shortcode language switcher and floating language switcher as well.<br> To achieve this in menu language switcher go to Appearance->Menus->Language Switcher and select Opposite Language.', 'linguapress' ), array( 'br' => array()) ),
        'id'            => 'miscellaneous_options',
        'container'     => 'language_switcher'
    );
    return $settings_array;
}

function lrp_opposite_ls_current_language( $current_language, $published_languages, $LRP_LANGUAGE, $settings ){
    if ( count ( $published_languages ) == 2 ) {
        foreach ($published_languages as $code => $name) {
            if ($code != $LRP_LANGUAGE) {
                $current_language['code'] = $code;
                $current_language['name'] = $name;
                break;
            }
        }
    }
    return $current_language;
}

function lrp_opposite_ls_other_language( $other_language, $published_languages, $LRP_LANGUAGE, $settings ){
    if ( count ( $published_languages ) == 2 ) {
        $other_language = array();
        foreach ($published_languages as $code => $name) {
            if ($code != $LRP_LANGUAGE) {
                $other_language[$code] = $name;
                break;
            }
        }
    }
    return $other_language;
}

function lrp_opposite_ls_hide_disabled_language($return, $current_language, $current_language_preference, $settings){
    if ( count( $settings['publish-languages'] ) == 2 ){
        return false;
    }
    return $return;
}

function lrp_enqueue_language_switcher_shortcode_scripts(){
    $lrp                 = LRP_Lingua_Press::get_lrp_instance();
    $lrp_languages       = $lrp->get_component( 'languages' );
    $lrp_settings        = $lrp->get_component( 'settings' );   
    $published_languages = $lrp_languages->get_language_names( $lrp_settings->get_settings()['publish-languages'] );
    if(count ( $published_languages ) == 2 ) {
        wp_add_inline_style( 'lrp-language-switcher-style', '.lrp-language-switcher > div {
    padding: 3px 5px 3px 5px;
    background-image: none;
    text-align: center;}' );
    }
}

function lrp_opposite_ls_floating_current_language($current_language, $published_languages, $LRP_LANGUAGE, $settings){
    if ( count ( $published_languages ) == 2 ) {
        foreach ($published_languages as $code => $name) {
            if ($code != $LRP_LANGUAGE) {
                $current_language['code'] = $code;
                $current_language['name'] = $name;
                break;
            }
        }
    }
    return $current_language;
}

function lrp_opposite_ls_floating_other_language( $other_language, $published_languages, $LRP_LANGUAGE, $settings ){
    if ( count ( $published_languages ) == 2 ) {
        $other_language = array();
        foreach ($published_languages as $code => $name) {
            if ($code != $LRP_LANGUAGE) {
                $other_language[$code] = $name;
                break;
            }
        }
    }
    return $other_language;
}

function lrp_opposite_ls_floating_hide_disabled_language($return, $current_language, $settings){
    if ( count( $settings['publish-languages'] ) == 2 ){
        return false;
    }
    return $return;
}

function lrp_show_opposite_flag_settings(){
    $option = get_option( 'lrp_advanced_settings', true );

     if(isset($option['show_opposite_flag_language_switcher_shortcode']) && $option['show_opposite_flag_language_switcher_shortcode'] !== 'no'){
         add_filter( 'lrp_ls_shortcode_current_language', 'lrp_opposite_ls_current_language', 10, 4 );
         add_filter( 'lrp_ls_shortcode_other_languages', 'lrp_opposite_ls_other_language', 10, 4 );
         add_filter( 'lrp_ls_shortcode_show_disabled_language', 'lrp_opposite_ls_hide_disabled_language', 10, 4 );
         add_action( 'wp_enqueue_scripts', 'lrp_enqueue_language_switcher_shortcode_scripts', 20 );
         add_action('lrp_ls_floating_current_language', 'lrp_opposite_ls_floating_current_language', 10, 4);
         add_action('lrp_ls_floating_other_languages', 'lrp_opposite_ls_floating_other_language', 10, 4);
         add_action('lrp_ls_floater_show_disabled_language', 'lrp_opposite_ls_floating_hide_disabled_language', 10, 3 );
     }
 }

lrp_show_opposite_flag_settings();