<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="lrp-settings-page" class="wrap">
    <?php require_once LRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <div id="lrp-settings__wrap">
        <div class="lrp-settings-container">
            <h1 class="lrp-settings-primary-heading"> <?php esc_html_e( 'LinguaPress Database Updater', 'linguapress' );?></h1>
            <div class="lrp-settings-separator"></div>

            <div class="grid feat-header">
                <div class="grid-cell">
                    <h2><?php esc_html_e('Updating LinguaPress tables. Please leave this window open.', 'linguapress' );?> </h2>
                    <div id="lrp-update-database-progress">
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>