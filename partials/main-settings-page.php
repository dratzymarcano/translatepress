<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="lrp-settings-page" class="wrap lrp-main-settings">
    <?php require_once LRP_PLUGIN_DIR . 'partials/settings-header.php' ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'lrp_settings' ); ?>
        <?php do_action ( 'lrp_settings_navigation_tabs' ); ?>

        <div id="lrp-settings__wrap">
            <div class="lrp-settings-container lrp-settings-container-languages">
                <h3 class="lrp-settings-primary-heading"><?php esc_html_e( 'Website Languages', 'linguapress' ); ?></h3>
                <div class="lrp-settings-separator"></div>

                <div class="lrp-default-language__container">
                    <p class="lrp-default-language-label lrp-primary-text-bold"><?php esc_html_e( 'Default Language', 'linguapress' ); ?></p>
                    <div class="lrp-default-language-select__wrapper">
                        <select id="lrp-default-language" name="lrp_settings[default-language]" class="lrp-select2">
                            <?php
                            foreach( $languages as $language_code => $language_name ){ ?>
                                <option title="<?php echo esc_attr( $language_code ); ?>" value="<?php echo esc_attr( $language_code ); ?>" <?php echo ( $this->settings['default-language'] == $language_code ? 'selected' : '' ); ?> >
                                    <?php echo esc_html( $language_name ); ?>
                                </option>
                            <?php }?>
                        </select>
                        <span class="lrp-description-text"><?php esc_html_e( 'Select the original language of your content.', 'linguapress' ); ?></span>
                    </div>
                </div>

                <p class="lrp-settings-warning" style="display: none;" >
                    <?php esc_html_e( 'WARNING. Changing the default language will invalidate existing translations.', 'linguapress' ); ?><br/>
                    <?php esc_html_e( 'Even changing from en_US to en_GB, because they are treated as two different languages.', 'linguapress' ); ?><br/>
                    <?php esc_html_e( 'In most cases changing the default flag is all it is needed: ', 'linguapress' ); ?>
                    <a href="https://linguapress.com/docs/developers/replace-default-flags/"><?php esc_html_e( 'replace the default flag', 'linguapress' ); ?></a>
                </p>

                <?php do_action( 'lrp_language_selector', $languages ); ?>

            </div>

            <div class="lrp-settings-container">
                <h3 class="lrp-settings-primary-heading"><?php esc_html_e( 'Language Settings', 'linguapress' ); ?></h3>
                <div class="lrp-settings-separator"></div>

                <div class="lrp-settings-options__wrapper">
                    <div class="lrp-settings-checkbox lrp-settings-options-item">
                        <input type="checkbox" id="lrp-native-language-name" name="lrp_settings[native_or_english_name]"
                               value="native_name" <?php checked( $this->settings['native_or_english_name'], 'native_name' ); ?> />

                        <label for="lrp-native-language-name" class="lrp-checkbox-label">
                            <div class="lrp-checkbox-content">
                                <span class="lrp-primary-text-bold"><?php esc_html_e('Use Native language name', 'linguapress'); ?></span>
                                <span class="lrp-description-text"><?php esc_html_e('Check if you want to display languages in their native names. Otherwise, languages will be displayed in English.', 'linguapress'); ?></span>
                            </div>
                        </label>
                    </div>

                    <div class="lrp-settings-checkbox lrp-settings-options-item">
                        <input type="checkbox" id="lrp-subdirectory-for-default-language" name="lrp_settings[add-subdirectory-to-default-language]"
                               value="yes" <?php checked($this->settings['add-subdirectory-to-default-language'], 'yes'); ?> />

                        <label for="lrp-subdirectory-for-default-language" class="lrp-checkbox-label">
                            <div class="lrp-checkbox-content">
                                <span class="lrp-primary-text-bold"><?php esc_html_e('Use a subdirectory for the default language', 'linguapress'); ?></span>
                                <span class="lrp-description-text">
                                    <?php echo wp_kses( __( 'Check if you want to add the subdirectory in the URL for the default language.</br>By checking this option, the default language seen by website visitors will become the first one in the "All Languages" list.', 'linguapress' ), array( 'br' => array() ) ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="lrp-settings-checkbox lrp-settings-options-item">
                        <input type="checkbox" id="lrp-force-language-in-custom-links" name="lrp_settings[force-language-to-custom-links]"
                               value="yes" <?php checked($this->settings['force-language-to-custom-links'], 'yes'); ?> />

                        <label for="lrp-force-language-in-custom-links" class="lrp-checkbox-label">
                            <div class="lrp-checkbox-content">
                                <span class="lrp-primary-text-bold"><?php esc_html_e('Force language in custom links', 'linguapress'); ?></span>
                                <span class="lrp-description-text">
                                    <?php esc_html_e( 'Select Yes if you want to force custom links without language encoding to keep the currently selected language.', 'linguapress' ); ?>
                                </span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="lrp-settings-container">
                <h3 class="lrp-settings-primary-heading"><?php esc_html_e( 'Language Switcher', 'linguapress' ); ?></h3>
                <div class="lrp-settings-separator"></div>

                <div class="lrp-settings-options__wrapper">
                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                        <input type="checkbox" disabled checked id="lrp-ls-shortcode" >

                        <label>
                            <div class="lrp-checkbox-content">
                                <b class="lrp-primary-text-bold"><?php esc_html_e( 'Shortcode ', 'linguapress' ); ?>[language-switcher] </b>
                                <?php $this->output_language_switcher_select( 'shortcode-options', $this->settings['shortcode-options'] ); ?>

                                <span class="lrp-description-text">
                                    <?php esc_html_e( 'Use shortcode on any page or widget.', 'linguapress' ); ?>
                                    <?php echo wp_kses_post( sprintf( __('You can also add the <a href="%s" title="Language Switcher Block Documentation">Language Switcher Block</a> in the WP Gutenberg Editor.', 'linguapress'), esc_url('https://linguapress.com/docs/settings/#language-switcher-block' ) ) ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                        <input type="checkbox" id="lrp-ls-menu" disabled checked >

                        <label>
                            <div class="lrp-checkbox-content">
                                <b class="lrp-primary-text-bold"><?php esc_html_e( 'Menu item', 'linguapress' ); ?></b>
                                <?php $this->output_language_switcher_select( 'menu-options', $this->settings['menu-options'] ); ?>
                                <span class="lrp-description-text">
                                    <?php
                                    $link_start = '<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) .'">';
                                    $link_end = '</a>';
                                    printf( wp_kses( __( 'Go to  %1$s Appearance -> Menus%2$s to add languages to the Language Switcher in any menu.', 'linguapress' ), [ 'a' => [ 'href' => [] ] ] ), $link_start, $link_end ); //phpcs:ignore ?>
                                    <a href="https://linguapress.com/docs/settings/#language-switcher" target="_blank"><?php esc_html_e( 'Learn more in our documentation.', 'linguapress' ); ?></a>
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                        <input type="checkbox" id="lrp-ls-floater" name="lrp_settings[lrp-ls-floater]" value="yes" <?php if ( isset($this->settings['lrp-ls-floater']) && ( $this->settings['lrp-ls-floater'] == 'yes' ) ){ echo 'checked'; }  ?>>

                        <label>
                            <div class="lrp-checkbox-content">
                                <b class="lrp-primary-text-bold"><?php esc_html_e( 'Floating language selection', 'linguapress' ); ?></b>
                                <?php $this->output_language_switcher_select( 'floater-options', $this->settings['floater-options'] ); ?>
                                <?php $this->output_language_switcher_floater_color( $this->settings['floater-color'] ); ?>
                                <?php $this->output_language_switcher_floater_possition( $this->settings['floater-position'] ); ?>
                                <span class="lrp-description-text">
                                    <?php esc_html_e( 'Add a floating dropdown that follows the user on every page.', 'linguapress' ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                        <input type="checkbox" id="lrp-ls-show-poweredby" name="lrp_settings[lrp-ls-show-poweredby]"  value="yes"  <?php if ( isset($this->settings['lrp-ls-show-poweredby']) && ( $this->settings['lrp-ls-show-poweredby'] == 'yes' ) ){ echo 'checked'; }  ?>>

                        <label>
                            <div class="lrp-checkbox-content">
                                <b class="lrp-primary-text-bold"><?php esc_html_e( 'Show "Powered by LinguaPress"', 'linguapress' ); ?></b>
                                <span class="lrp-description-text">
                                    <?php esc_html_e( 'Show the small "Powered by LinguaPress" label in the floater language switcher.', 'linguapress' ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <?php do_action ( 'lrp_extra_settings', $this->settings ); ?>
                </div>
            </div>

            <?php
            $email_course_dismissed = get_user_meta( get_current_user_id(), 'lrp_email_course_dismissed', true );

            if( ( empty( $email_course_dismissed ) || $email_course_dismissed != '1' ) && false ) : ?>
                <div class="lrp-email-course">
                    <div class="lrp-email-course__content">
                        <h2>
                            <?php esc_html_e( '5 Days to Better Multilingual Websites', 'linguapress' ); ?>
                        </h2>

                        <p>
                            <?php printf( esc_html__( '%sJoin our FREE & EXCLUSIVE onboarding course%s and learn how to grow your multilingual traffic, reach international markets, and save time & money while getting the most out of LinguaPress!', 'linguapress' ), '<strong>', '</strong>' ); ?>
                        </p>

                        <div class="lrp-email-course__message"></div>

                        <div class="lrp-email-course__form">
                            <div class="lrp-email-course__error"><?php esc_html_e( 'Invalid email address', 'linguapress' ) ?></div>

                            <input type="email" name="lrp_email_course_email" placeholder="<?php esc_html_e( 'Your email', 'linguapress' ) ?>" value=""/>
                            <input type="hidden" name="lrp_installed_plugin_version" value="<?php echo esc_attr( LRP_Plugin_Optin::get_current_active_version() ); ?>" />

                            <input type="submit" class="button-primary" value="<?php esc_attr_e( 'Sign me up!', 'linguapress' ); ?>"/>
                        </div>

                        <p class="lrp-email-course__footer">
                            <?php esc_html_e( 'Sign up with your email address and receive a 5-part email guide to help you maximize the power of LinguaPress.', 'linguapress' ); ?>
                        </p>

                        <!-- <a class="lrp-email-course__close" href="#dismiss-email-course" title="<?php esc_html_e( 'Dismiss email course notification', 'linguapress') ?>"></a> -->
                    </div>
                </div>
            <?php endif; ?>
            <button type="submit" class="lrp-submit-btn">
                <?php esc_html_e( 'Save Changes', 'linguapress' ); ?>
            </button>

        </div>
    </form>
</div>
