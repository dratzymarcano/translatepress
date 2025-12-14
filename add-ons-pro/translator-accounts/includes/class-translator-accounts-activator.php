<?php

if ( !defined('ABSPATH' ) )
    exit();

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    LinguaPress - Translator Accounts Add-on
 * @subpackage LinguaPress - Translator Accounts Add-on/includes
 * @author     Cristian Antohe
 */
if( !class_exists('LRP_IN_Translator_Accounts_Activator') ) {
    class LRP_IN_Translator_Accounts_Activator {
        /**
         * Create the translator user role.
         *
         * The translator user role is similar to a subscriber, with the extra capabilities of translate_strings and upload_files.
         *
         * @since    1.0.0
         */
        public static function activate() {
            $role = get_role( 'translator' );

            if ( $role ) {
                $role->add_cap( 'translate_strings' );
                $role->add_cap( 'upload_files' );
            } else {
                add_role(
                    'translator',
                    __( 'Translator', 'linguapress' ),
                    array(
                        'read'              => true,
                        'translate_strings' => true,
                        'upload_files'      => true,
                    )
                );
            }
        }

    }
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
if ( !function_exists('lrp_in_activate_translator_accounts') ) {
    function lrp_in_activate_translator_accounts($addon) {
        if ( $addon === 'tp-add-on-translator-accounts/index.php' ) {
            LRP_IN_Translator_Accounts_Activator::activate();
        }
    }

    add_action( 'lrp_add_ons_activate', 'lrp_in_activate_translator_accounts', 10, 1 );
}