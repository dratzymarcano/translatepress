<?php
if ( !defined('ABSPATH' ) )
    exit();

?>
<div class="wrap lrp-optin-page" id="lrp-settings-page">

    <div class="lrp-optin-page__wrap">
        <div class="lrp-optin-page__content">

            <div class="lrp-optin-page__top">
                <img class="lrp-option-page__logo-wordpress" src="<?php echo esc_attr(LRP_PLUGIN_URL . '/assets/images/plugin_optin_logo_wordpress.png') ?>">

                <span class="lrp-optin-page__extra-icon dashicons dashicons-plus"></span>

                <img class="lrp-option-page__logo-linguapress" src="<?php echo esc_attr(LRP_PLUGIN_URL . '/assets/images/plugin_optin_logo_linguapress.png') ?>">
            </div>

            <p class="lrp-optin-page__message">
                <?php printf( wp_kses_post( __( 'Hey %s,<br>Never miss an important update - opt in to our security and feature updates notifications, and non-sensitive diagnostic tracking.', 'linguapress' ) ), '<strong>'. esc_html($this->get_user_name() ) . '</strong>' );?>
            </p>

            <div class="lrp-optin-page__bottom">
                <a class="button-primary" href="<?php echo esc_attr(wp_nonce_url( add_query_arg( [] ), 'lrp_enable_plugin_optin' )); ?>" onclick="this.classList.add('disabled')" ><?php esc_html_e( 'Allow & Continue', 'linguapress' ); ?></a>

                <a class="button-secondary" href="<?php echo esc_attr(wp_nonce_url( add_query_arg( [] ), 'lrp_disable_plugin_optin' )); ?>"><?php esc_html_e( 'Skip', 'linguapress' ); ?></a>
            </div>

        </div>

        <div class="lrp-optin-page__footer">
            
            <div class="lrp-optin-page__more-wrap">
                <a class="lrp-optin-page__more" href="#" onclick="event.preventDefault(); document.getElementsByClassName('lrp-optin-page__extra')[0].classList.toggle('hidden');"><?php esc_html_e( 'This will allow LinguaPress to:', 'linguapress' ); ?></a>
            </div>

            <div class="lrp-optin-page__extra hidden">
                <div class="lrp-optin-page__extra-line">
                    <span class="lrp-optin-page__extra-icon dashicons dashicons-admin-users"></span>
                    <div class="lrp-optin-page__extra-content">
                        <h4><?php esc_html_e( 'Your profile overview', 'linguapress' ); ?></h4>
                        <p><?php esc_html_e( 'Name and email address', 'linguapress' ); ?></p>
                    </div>
                </div>

                <!-- <div class="lrp-optin-page__extra-line">
                    <span class="lrp-optin-page__extra-icon dashicons dashicons-admin-settings"></span>
                    <div class="lrp-optin-page__extra-content">
                        <h4><?php //esc_html_e( 'Your site overview', 'linguapress' ); ?></h4>
                        <p><?php //esc_html_e( 'Site URL, WP version, PHP info', 'linguapress' ); ?></p>
                    </div>
                </div> -->

                <div class="lrp-optin-page__extra-line">
                    <span class="lrp-optin-page__extra-icon dashicons dashicons-testimonial"></span>
                    <div class="lrp-optin-page__extra-content">
                        <h4><?php esc_html_e( 'Admin Notices', 'linguapress' ); ?></h4>
                        <p><?php esc_html_e( 'Updates, announcements, marketing, no spam', 'linguapress' ); ?></p>
                    </div>
                </div>

                <div class="lrp-optin-page__extra-line">
                    <span class="lrp-optin-page__extra-icon dashicons dashicons-admin-plugins"></span>
                    <div class="lrp-optin-page__extra-content">
                        <h4><?php esc_html_e( 'Plugin status & settings', 'linguapress' ); ?></h4>
                        <p><?php esc_html_e( 'Active, Deactivated, installed version and settings', 'linguapress' ); ?></p>
                    </div>
                </div>

                <!-- <div class="lrp-optin-page__extra-line">
                    <span class="lrp-optin-page__extra-icon dashicons dashicons-menu"></span>
                    <div class="lrp-optin-page__extra-content">
                        <h4><?php //esc_html_e( 'Plugins & Themes', 'linguapress' ); ?></h4>
                        <p><?php //esc_html_e( 'Title, slug, version and is active', 'linguapress' ); ?></p>
                    </div>
                </div> -->
            </div>

            <div class="lrp-optin-page__footer-links">
                <a target="_blank" href="https://linguapress.com/privacy-policy/"><?php esc_html_e( 'Privacy Policy', 'linguapress' ); ?></a>
                -
                <a target="_blank" href="https://linguapress.com/terms-conditions/#section10"><?php esc_html_e( 'Terms of Service', 'linguapress' ); ?></a>
            </div>
        </div>
    </div>

</div>