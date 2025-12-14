<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="lrp-settings-page" class="wrap">
    <?php require_once LRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'lrp_advanced_settings' ); ?>
        <?php do_action ( 'lrp_settings_navigation_tabs' ); ?>

        <div class="advanced_setting_tab_class">
            <div id="lrp-settings__wrap">
                <?php do_action('lrp_before_output_advanced_settings_options' ); ?>
                <?php do_action('lrp_output_advanced_settings_options' ); ?>
                <button type="submit" class="lrp-submit-btn">
                    <?php esc_html_e( 'Save Changes', 'linguapress' ); ?>
                </button>
            </div>
        </div>
    </form>
</div>
