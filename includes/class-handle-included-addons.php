<?php

if ( !class_exists('LRP_Handle_Included_Addons') ){
    class LRP_Handle_Included_Addons{

        const TP_MINIMUM_VERSION = '2.8.7'; // Minimum version of LinguaPress
        function __construct(){

            // Stop TP from redirecting to default language when subdirectory is on but minimum version not met
            add_filter( 'lrp_allow_language_redirect', array( $this, 'stop_redirecting_if_tp_minimum_version_not_met'), 99999, 1 );

            // Both lrp_run_linguapress_hooks and lrp_main_plugin_minimum_version_check run on priority 1, we need to make sure that the latter runs first so no errors occur
            remove_action(  'plugins_loaded', 'lrp_run_linguapress_hooks', 1 );

            add_action( 'plugins_loaded', [ $this, 'lrp_main_plugin_minimum_version_check' ], 1 );

            if ( function_exists( 'lrp_run_linguapress_hooks' ) )
                add_action('plugins_loaded', 'lrp_run_linguapress_hooks',1 );

            //disable old addons and create database entries for add-ons status
            add_action( 'plugins_loaded', array( $this, 'disable_old_add_ons' ), 12 );

            //activate an add-on when you press the button from the add-ons page
            add_action( 'lrp_add_ons_activate', array( $this, 'lrp_activate_add_ons' ) );
            //deactivate an add-on when you press the button from the add-ons page
            add_action( 'lrp_add_ons_deactivate', array( $this, 'lrp_deactivate_add_ons' ) );
            //show the button in the add-ons page with the correct action
            add_filter( 'lrp_add_on_is_active', array( $this, 'lrp_check_add_ons_activation' ) , 10, 2 );

            add_action( 'admin_notices', array( $this, 'lrp_main_plugin_notice' ) );

            //include add-on files that contain activation hooks even when add-ons are deactivated
            $this->include_mandatory_addon_files();

            //include the addons from the main plugin if they are activated
            $this->include_addons();
        }

        /**
         * Stop TP from redirecting to default language when subdirectory is on but minimum version not met
         */
        public function stop_redirecting_if_tp_minimum_version_not_met( $redirect ) {
            if ( !defined('LRP_PLUGIN_VERSION' ) || version_compare( LRP_PLUGIN_VERSION, self::TP_MINIMUM_VERSION, '>=' ) )
                return $redirect; // do nothing

            return false;
        }

        /**
         * Add a notice if the LinguaPress main plugin is active and right version
         */
        function lrp_main_plugin_notice(){
            // Removed installation checks as this is now part of the main plugin
        }

        /**
         * Check if the main plugin is updated to the minimum version required by LinguaPress Pro
         *
         * Block LinguaPress from loading in case it's not
         *
         * @return void
         */
        function lrp_main_plugin_minimum_version_check(){
            if ( defined('LINGUA_PRESS') && LINGUA_PRESS === 'LinguaPress - Dev' ) return;

            if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            if ( !defined('LRP_PLUGIN_VERSION' ) || version_compare( LRP_PLUGIN_VERSION, self::TP_MINIMUM_VERSION, '>=' ) )
                return; // do nothing

            add_action( 'admin_notices', [ $this, 'lrp_show_minimum_version_warning' ], 10 );
            add_filter( 'lrp_allow_tp_to_run', '__return_false' ); // Stop TP from loading anything
        }

        function lrp_show_minimum_version_warning() {
            $update_url = admin_url('plugins.php?s=linguapress&plugin_status=all');
            $current_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : '';

            // Check if the current URL contains 's=linguapress&plugin_status=all'
            $hide_button = strpos($current_url, 's=linguapress&plugin_status=all') !== false;

            echo '<div class="notice notice-error" style="display: flex; align-items: center; padding: 10px;">
              <p style="margin: 0; padding-right: 10px;">' . wp_kses( sprintf(
                    __( 'Please update LinguaPress to version %2$s or newer. Your currently installed version of LinguaPress is no longer compatible with the current version of %1$s.',
                        'linguapress' ),
                    'LinguaPress Pro',
                    self::TP_MINIMUM_VERSION
                ), [] ) . ' ' .
                esc_html__( 'All LinguaPress functionalities are disabled until then.', 'linguapress' ) . '</p>' .
                ( !$hide_button ? '<a href="' . esc_url( $update_url ) . '" class="button-primary" style="margin-left: 10px;">' . esc_html__( 'Update Now', 'linguapress' ) . '</a>' : '' ) . /* phpcs:ignore */ /* everything is escaped or pure html */
                '</div>';
        }

        /**
         * Function that determines if an add-on is active or not
         * @param $bool
         * @param $slug
         * @return mixed
         */
        function lrp_check_add_ons_activation( $bool, $slug ){
            $lrp_add_ons_settings = get_option( 'lrp_add_ons_settings', array() );
            if( !empty( $lrp_add_ons_settings[$slug] ) )
                $bool = $lrp_add_ons_settings[$slug];

            return $bool;
        }

        /**
         * Function that activates a PB add-on
         */
        function lrp_activate_add_ons( $slug ){
            $this->lrp_activate_or_deactivate_add_on( $slug, true );
        }

        /**
         * Function that deactivates a PB add-on
         */

        function lrp_deactivate_add_ons( $slug ){
            $this->lrp_activate_or_deactivate_add_on( $slug, false );
        }


        /**
         * Function used to activate or deactivate a PB add-on
         */
        function lrp_activate_or_deactivate_add_on( $slug, $action ){
            $lrp_add_ons_settings = get_option( 'lrp_add_ons_settings', array() );
            $lrp_add_ons_settings[$slug] = $action;
            update_option( 'lrp_add_ons_settings', $lrp_add_ons_settings );
        }


        /**
         * Check if an addon was active as a slug before it was programmatically deactivated by us
         * On the plugin updates, where we transitioned add-ons we save the status in an option 'lrp_old_add_ons_status'
         * @param $slug
         * @return false
         */
        function was_addon_active_as_plugin( $slug ){
            $old_add_ons_status = get_option( 'lrp_old_add_ons_status' );
            if( isset( $old_add_ons_status[$slug] ) )
                return $old_add_ons_status[$slug];
            else
                return false;
        }

        /**
         * Function that returns the slugs of old addons that were plugins
         * @return string[]
         */
        function get_old_addons_slug_list(){
            $old_addon_list = array(
                'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php',
                'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php',
                'tp-add-on-deepl/index.php',
                'tp-add-on-extra-languages/tp-extra-languages.php',
                'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php',
                'tp-add-on-seo-pack/tp-seo-pack.php',
                'tp-add-on-translator-accounts/index.php',
            );

            return $old_addon_list;
        }


        /**
         * Deactivate the old addons as plugins
         */
        function disable_old_add_ons(){

            //if it's triggered in the frontend we need this include
            if( !function_exists('is_plugin_active') )
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            $old_addons_list = $this->get_old_addons_slug_list();
            $deactivated_addons = 0;

            $old_add_ons_status = get_option( 'lrp_old_add_ons_status', array() );

            foreach( $old_addons_list as $addon_slug ){
                if( is_plugin_active($addon_slug) ){

                    if( !isset( $old_add_ons_status[$addon_slug] ) )//construct here the old add-ons status just once
                        $old_add_ons_status[$addon_slug] = true;

                    if( is_multisite() ){
                        if( is_plugin_active_for_network($addon_slug) )
                            deactivate_plugins($addon_slug, true);
                        else
                            deactivate_plugins($addon_slug, true, false);
                    }
                    else {
                        deactivate_plugins($addon_slug, true);
                    }
                    $deactivated_addons++;
                }
                else{
                    if( !isset( $old_add_ons_status[$addon_slug] ) )
                        $old_add_ons_status[$addon_slug] = false;
                }
            }
            if ( isset( $_GET['activate'] ) && $deactivated_addons === 1 ){
                add_action( 'load-plugins.php',
                    function(){
                        add_action( 'in_admin_header',
                            function(){
                                add_filter( 'gettext', array( $this, 'disable_old_add_ons_notice' ), 99, 3 );
                            }
                        );
                    }
                );
            } elseif ( isset( $_GET['activate-multi'] ) && $deactivated_addons !== 0 ){
                add_action( 'admin_notices', array( $this, 'disable_old_add_ons_notice_multi' ) );
            }


            if( !empty( $old_add_ons_status ) ){
                $old_add_ons_option = get_option( 'lrp_old_add_ons_status', array() );
                if( empty( $old_add_ons_option ) )
                    update_option( 'lrp_old_add_ons_status', $old_add_ons_status );//this should not change

                $add_ons_settings = get_option( 'lrp_add_ons_settings', array() );
                if( empty( $add_ons_settings ) ) {
                    //activate by default a couple of add-ons
                    $old_add_ons_status['tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php'] = true;
                    $old_add_ons_status['tp-add-on-deepl/index.php'] = true;
                    $old_add_ons_status['tp-add-on-extra-languages/tp-extra-languages.php'] = true;

                    update_option('lrp_add_ons_settings', $old_add_ons_status);//this should be set just once
                }
            }
        }

        /**
         * Modify the output of the notification when trying to activate an old addon
         * @param $translated_text
         * @param $untranslated_text
         * @param $domain
         * @return mixed|string
         */
        function disable_old_add_ons_notice( $translated_text, $untranslated_text, $domain )
        {
            $old = array(
                "Plugin activated."
            );

            $new = "This LinguaPress add-on has been migrated to the main plugin and is no longer used. You can delete it.";

            if ( in_array( $untranslated_text, $old, true ) )
            {
                $translated_text = $new;
                remove_filter( current_filter(), __FUNCTION__, 99 );
            }
            return $translated_text;
        }

        /**
         * Modify the output of the notification when trying to activate an old addon
         */
        function disable_old_add_ons_notice_multi() {
            ?>
            <div id="message" class="updated notice is-dismissible">
                <p><?php _e( 'This LinguaPress add-on has been migrated to the main plugin and is no longer used. You can delete it.', 'linguapress' ); ?></p>
            </div>
            <?php
        }


        /**
         * Function that includes the add-ons from the main plugin
         */
        function include_addons(){

            $add_ons_settings = get_option( 'lrp_add_ons_settings', array() );

            if( !empty( $add_ons_settings ) ){
                foreach( $add_ons_settings as $add_on_slug => $add_on_enabled ){
                    if( $add_on_enabled ){

                        //include here the advanced addons
                        $seo_addon_dir = "seo-pack";
                        $option = get_option( 'lrp_advanced_settings', true );
                        if ( isset( $option['load_legacy_seo_pack'] ) && $option['load_legacy_seo_pack'] === 'yes' ){
                            $seo_addon_dir = "seo-pack-legacy";
                        }

                        if( $add_on_slug === 'tp-add-on-seo-pack/tp-seo-pack.php' && file_exists(LRP_PLUGIN_DIR . 'add-ons-advanced/'. $seo_addon_dir .'/tp-seo-pack.php') )
                            require_once LRP_PLUGIN_DIR . 'add-ons-advanced/'. $seo_addon_dir .'/tp-seo-pack.php';
                        if( $add_on_slug === 'tp-add-on-extra-languages/tp-extra-languages.php' && file_exists(LRP_PLUGIN_DIR . 'add-ons-advanced/extra-languages/tp-extra-languages.php') )
                            require_once LRP_PLUGIN_DIR . 'add-ons-advanced/extra-languages/tp-extra-languages.php';

                        //include here the PRO addons
                        if( $add_on_slug === 'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php' && file_exists(LRP_PLUGIN_DIR . 'add-ons-pro/automatic-language-detection/tp-automatic-language-detection.php') )
                            require_once LRP_PLUGIN_DIR . 'add-ons-pro/automatic-language-detection/tp-automatic-language-detection.php';
                        if( $add_on_slug === 'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php' && file_exists(LRP_PLUGIN_DIR . 'add-ons-pro/browse-as-other-roles/tp-browse-as-other-role.php') )
                            require_once LRP_PLUGIN_DIR . 'add-ons-pro/browse-as-other-roles/tp-browse-as-other-role.php';
                        if( $add_on_slug === 'tp-add-on-deepl/index.php' && file_exists(LRP_PLUGIN_DIR . 'add-ons-pro/deepl/index.php') )
                            require_once LRP_PLUGIN_DIR . 'add-ons-pro/deepl/index.php';
                        if( $add_on_slug === 'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php' && file_exists(LRP_PLUGIN_DIR . 'add-ons-pro/navigation-based-on-language/tp-navigation-based-on-language.php') )
                            require_once LRP_PLUGIN_DIR . 'add-ons-pro/navigation-based-on-language/tp-navigation-based-on-language.php';
                        if( $add_on_slug === 'tp-add-on-translator-accounts/index.php' && file_exists(LRP_PLUGIN_DIR . 'add-ons-pro/translator-accounts/index.php') )
                            require_once LRP_PLUGIN_DIR . 'add-ons-pro/translator-accounts/index.php';
                    }
                }
            }

        }

        /**
         * Include add-on files that contain activation hooks even when add-ons are deactivated
         *
         * Necessary in order to perform actions during the operation of activation or deactivation of that add-on
         */
        function include_mandatory_addon_files(){
            if( file_exists(LRP_PLUGIN_DIR . 'add-ons-pro/translator-accounts/includes/class-translator-accounts-activator.php') )
                require_once LRP_PLUGIN_DIR . 'add-ons-pro/translator-accounts/includes/class-translator-accounts-activator.php';

            if ( file_exists( LRP_PLUGIN_DIR . 'add-ons-advanced/seo-pack/tp-seo-pack-activator.php' ) ) {
                require_once LRP_PLUGIN_DIR . 'add-ons-advanced/seo-pack/tp-seo-pack-activator.php';
            }
        }
    }
}
