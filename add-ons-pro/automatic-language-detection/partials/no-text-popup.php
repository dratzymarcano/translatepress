<?php
if ( !defined('ABSPATH' ) )
    exit();
?>
<template id="lrp_ald_no_text_popup_template">
    <div id="lrp_no_text_popup_wrap">
        <div id="lrp_no_text_popup" class="lrp_ald_no_text_popup" data-no-dynamic-translation data-no-translation>
            <?php
            $lrp                   = LRP_Lingua_Press::get_lrp_instance();
            $lrp_settings          = $lrp->get_component( 'settings' );
            $settings              = $lrp_settings->get_settings();
            $this->lrp_languages   = $lrp->get_component( 'languages' );
            $languages_to_display  = $this->settings['publish-languages'];
            $published_languages   = $this->lrp_languages->get_language_names( $languages_to_display );
            $lrp_language_switcher = $lrp->get_component( 'language_switcher' );
            $ls_option             = $lrp_settings->get_language_switcher_options();
            $shortcode_settings    = $ls_option[ $settings['shortcode-options'] ];
            $language_cookie_data  = $this->get_language_cookie_data();
            ?>

            <div id="lrp_ald_not_text_popup_ls_and_button">
                <div id="lrp_ald_no_text_popup_div">
                    <span id="lrp_ald_no_text_popup_text"></span>
                </div>
                <div class="lrp_ald_ls_container">
                    <div class="lrp-language-switcher lrp-language-switcher-container" id="lrp_ald_no_text_select"
                         data-no-translation <?php echo ( isset( $_GET['lrp-edit-translation'] ) && $_GET['lrp-edit-translation'] == 'preview' ) ? 'data-lrp-unpreviewable="lrp-unpreviewable"' : '' ?>>
                        <?php
                        $current_language_preference = $lrp_language_switcher->add_shortcode_preferences( $shortcode_settings, $settings['default-language'], $published_languages[ $settings['default-language'] ] );
                        ?>

                        <div class="lrp-ls-shortcode-current-language" id="<?php echo esc_attr( $settings["default-language"] ); ?>"
                             special-selector="lrp_ald_popup_current_language" data-lrp-ald-selected-language="<?php echo esc_attr( $settings["default-language"] ); ?>">
                            <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
                        </div>
                        <div class="lrp-ls-shortcode-language" id="lrp_ald_no_text_popup_select_container">
                            <div class="lrp-ald-popup-select" id="<?php echo esc_attr( $settings['default-language'] ) ?>"
                                 data-lrp-ald-selected-language= <?php echo esc_attr( $settings['default-language'] ) ?>>
                                <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
                            </div>
                            <?php foreach ( $published_languages as $code => $name ) {
                                if ($code != $settings['default-language']){
                            $language_preference = $lrp_language_switcher->add_shortcode_preferences( $shortcode_settings, $code, $name );
                                    ?>
                                    <div class="lrp-ald-popup-select" id="<?php echo esc_attr( $code ); ?>"
                                         data-lrp-ald-selected-language="<?php echo esc_attr( $code ); ?>">
                                        <?php
                                        echo $language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>

                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="lrp_ald_change_language_div">
                    <a href="<?php echo esc_url( $language_cookie_data['abs_home'] ); ?>" id="lrp_ald_no_text_popup_change_language"></a>
                </div>
                <div id="lrp_ald_no_text_popup_x_button_and_textarea"> <a id="lrp_ald_no_text_popup_x_button"></a><span id="lrp_ald_no_text_popup_x_button_textarea"></span></div>
            </div>
            <div id="lrp_ald_no_text_popup_x">
                <button id="lrp_close"></button>
            </div>
        </div>
    </div>
</template>