<?php
if ( !defined('ABSPATH' ) )
    exit();
?>
<div class="lrp_model_container" id="lrp_ald_modal_container" style="display: none" data-no-dynamic-translation data-no-translation>
    <?php
    $lrp                = LRP_Lingua_Press::get_lrp_instance();
    $lrp_settings       = $lrp->get_component( 'settings' );
    $settings           = $lrp_settings->get_settings();
    $this->lrp_languages = $lrp->get_component('languages');
    $languages_to_display = $this->settings['publish-languages'];
    $published_languages = $this->lrp_languages->get_language_names( $languages_to_display );
    $lrp_language_switcher = $lrp->get_component('language_switcher');
    $ls_option = $lrp_settings->get_language_switcher_options();
    $shortcode_settings = $ls_option[$settings['shortcode-options']];
    $language_cookie_data = $this->get_language_cookie_data();
    ?>
    <div class="lrp_ald_modal" id="lrp_ald_modal_popup">
            <div id="lrp_ald_popup_text"></div>

        <div class="lrp_ald_select_and_button">
            <div class="lrp_ald_ls_container">
            <div class="lrp-language-switcher lrp-language-switcher-container"  id="lrp_ald_popup_select_container" data-no-translation <?php echo ( isset( $_GET['lrp-edit-translation'] ) && $_GET['lrp-edit-translation'] == 'preview' ) ? 'data-lrp-unpreviewable="lrp-unpreviewable"' : '' ?>>
                <?php
                $current_language_preference = $lrp_language_switcher->add_shortcode_preferences($shortcode_settings, $settings['default-language'], $published_languages[$settings['default-language']]);
                ?>

                <div class="lrp-ls-shortcode-current-language" id="<?php echo esc_attr($settings["default-language"]); ?>" special-selector="lrp_ald_popup_current_language" data-lrp-ald-selected-language= "<?php echo esc_attr($settings["default-language"]); ?>">
                    <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
                </div>
                <div class="lrp-ls-shortcode-language">
                    <div class="lrp-ald-popup-select" id="<?php echo esc_attr($settings["default-language"]); ?>" data-lrp-ald-selected-language = "<?php echo esc_attr($settings['default-language']);?>">
                        <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
                    </div>
                    <?php foreach ( $published_languages as $code => $name ){
                        if ($code != $settings['default-language']){
                            $language_preference = $lrp_language_switcher->add_shortcode_preferences($shortcode_settings, $code, $name);
                            ?>
                            <div class="lrp-ald-popup-select" id="<?php echo esc_attr($code); ?>" data-lrp-ald-selected-language = "<?php echo esc_attr($code); ?>">
                                <?php echo $language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            </div>


            <div class="lrp_ald_button">
            <a href="<?php echo esc_url( $language_cookie_data['abs_home'] ); ?>" id="lrp_ald_popup_change_language"></a>
            </div>
         </div>
        <a id="lrp_ald_x_button_and_textarea" href="#"> <span id="lrp_ald_x_button"></span><span id="lrp_ald_x_button_textarea"></span></a>
    </div>
</div>
