<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_open_language_switcher_shortcode_on_click', 1350 );
function lrp_open_language_switcher_shortcode_on_click( $settings_array ){
    $settings_array[] = array(
        'name'          => 'open_language_switcher_shortcode_on_click',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Open language switcher only on click', 'linguapress' ),
        'description'   => wp_kses( __( 'Open the language switcher shortcode by clicking on it instead of hovering.<br> Close it by clicking on it, anywhere else on the screen or by pressing the escape key. This will affect only the shortcode language switcher.', 'linguapress' ), array( 'br' => array()) ),
        'id'            => 'miscellaneous_options',
        'container'     => 'language_switcher'
    );
    return $settings_array;
}

function lrp_lsclick_enqueue_scriptandstyle() {
    wp_enqueue_script('lrp-clickable-ls-js', LRP_PLUGIN_URL . 'assets/js/lrp-clickable-ls.js', array('jquery'), LRP_PLUGIN_VERSION, true );

    wp_add_inline_style('lrp-language-switcher-style', '.lrp_language_switcher_shortcode .lrp-language-switcher .lrp-ls-shortcode-current-language.lrp-ls-clicked{
    visibility: hidden;
}

.lrp_language_switcher_shortcode .lrp-language-switcher:hover div.lrp-ls-shortcode-current-language{
    visibility: visible;
}

.lrp_language_switcher_shortcode .lrp-language-switcher:hover div.lrp-ls-shortcode-language{
    visibility: hidden;
    height: 1px;
}
.lrp_language_switcher_shortcode .lrp-language-switcher .lrp-ls-shortcode-language.lrp-ls-clicked,
.lrp_language_switcher_shortcode .lrp-language-switcher:hover .lrp-ls-shortcode-language.lrp-ls-clicked{
    visibility:visible;
    height:auto;
    position: absolute;
    left: 0;
    top: 0;
    display: inline-block !important;
}');
}

function lrp_open_language_switcher_on_click(){
    $option = get_option( 'lrp_advanced_settings', true );

    if(isset($option['open_language_switcher_shortcode_on_click']) && $option['open_language_switcher_shortcode_on_click'] !== 'no'){
        add_action( 'wp_enqueue_scripts', 'lrp_lsclick_enqueue_scriptandstyle', 99 );
    }
}

lrp_open_language_switcher_on_click();