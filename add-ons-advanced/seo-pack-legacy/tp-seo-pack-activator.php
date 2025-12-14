<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

/**
 * The code that runs during plugin activation.
 */


if(!function_exists('lrp_in_sp_activator')){
    function lrp_in_sp_activator( $addon ){
        if ( $addon === 'tp-add-on-seo-pack/tp-seo-pack.php' ) {
            lrp_in_sp_wpseo_clear_sitemap();
        }
    }

    add_action('lrp_add_ons_activate', 'lrp_in_sp_activator', 10, 1);
    add_action('lrp_add_ons_deactivate','lrp_in_sp_activator', 10, 1);
}

if(!function_exists('lrp_in_sp_wpseo_clear_sitemap')) {
    function lrp_in_sp_wpseo_clear_sitemap() {
        global $wpdb;
        // delete all "yst_sm" transients
        $sql = "
            DELETE
            FROM {$wpdb->options}
            WHERE option_name like '\_transient\_yst\_sm%'
            OR option_name like '\_transient\_timeout\_yst\_sm%'
        ";

        $wpdb->query( $sql );
    }
}