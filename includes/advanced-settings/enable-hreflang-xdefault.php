<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_register_enable_hreflang_xdefault', 1100 );
function lrp_register_enable_hreflang_xdefault( $settings_array ){
    $settings_array[] = array(
        'name'          => 'enable_hreflang_xdefault',
        'type'          => 'custom',
        'default'       => 'disabled',
        'label'         => esc_html__( 'Enable the hreflang x-default tag for language:', 'linguapress' ),
        'description'   => wp_kses( __( 'Enables the hreflang="x-default" for an entire language. See documentation for more details.', 'linguapress' ), array( 'br' => array() ) ),
        'options'       => lrp_get_lang_for_xdefault(),
        'id'            => 'miscellaneous_options',
        'container'     => 'miscellaneous_options',
    );

    return $settings_array;
}

function lrp_get_lang_for_xdefault(){
    $published_lang_labels = lrp_get_languages();
    return array_merge(['disabled' => 'Disabled'], $published_lang_labels);
}

add_filter( 'lrp_advanced_setting_custom_enable_hreflang_xdefault', 'lrp_output_enable_hreflang_xdefault' );
function lrp_output_enable_hreflang_xdefault( $setting ){
    $lrp_settings = ( new LRP_Settings() )->get_settings();
    $adv_option = $lrp_settings['lrp_advanced_settings'];

    $checked = ( isset( $adv_option[ $setting['name'] ] ) && $adv_option[ $setting['name'] ] !== 'disabled' ) || ( isset( $adv_option[ $setting['name'] . '-checkbox' ] ) && $adv_option[ $setting['name'] . '-checkbox' ] === 'yes' )
        ? 'checked' : '';

    $select = "<select class='lrp-select' name='lrp_advanced_settings[" . esc_attr( $setting['name'] ) . "]'>";

    foreach ( $setting['options'] as $option_key => $option_value ) {
        if ( $option_key === 'disabled' )
            continue;

        $selected = $adv_option[ $setting['name'] ] === $option_key ? ' selected' : '';

        $select .= "<option value='". esc_attr( $option_key ) ."' $selected>". esc_html( $option_value )."</option>";
    }

    $select .= "</select>";

    $html = "<div class='lrp-settings-custom-checkbox__wrapper'>
                <div class='lrp-settings-checkbox'>
                    <input type='checkbox' id='" . esc_attr( $setting['name'] ) . "-checkbox' 
                           name='lrp_advanced_settings[" . esc_attr( $setting['name'] ) . "-checkbox]' 
                           value='yes' " . $checked . " />
    
                    <label for='" . esc_attr( $setting['name'] ) . "-checkbox' class='lrp-checkbox-label'>
                        <div class='lrp-checkbox-content'>
                            <span class='lrp-primary-text-bold'>" . esc_html( $setting['label'] ) . "</span>
                            <span class='lrp-description-text'>" . wp_kses_post( $setting['description'] ) . "</span>
                        </div>
                    </label>
                </div>
                $select
            </div>";

    return $html;
}