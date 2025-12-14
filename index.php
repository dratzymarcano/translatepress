<?php
/*
Plugin Name: LinguaPress - AI Translation
Plugin URI: https://github.com/dratzymarcano/linguapress
Description: AI-powered multilingual translation for WordPress with OpenRouter and ChatGPT support. Visual front-end editor with WooCommerce compatibility.
Version: 1.0.0
Author: LinguaPress Team
Author URI: https://github.com/dratzymarcano
Text Domain: linguapress
Domain Path: /languages
License: GPL2
WC requires at least: 2.5.0
WC tested up to: 10.0.2

== Copyright ==
Copyright 2025 LinguaPress

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();


function lrp_enable_linguapress(){
	$enable_linguapress = true;
	$current_php_version = apply_filters( 'lrp_php_version', phpversion() );

	// 5.6.20 is the minimum version supported by WordPress
	if ( $current_php_version !== false && version_compare( $current_php_version, '5.6.20', '<' ) ){
		$enable_linguapress = false;
		add_action( 'admin_menu', 'lrp_linguapress_disabled_notice' );
	}

	return apply_filters( 'lrp_enable_linguapress', $enable_linguapress );
}

if ( lrp_enable_linguapress() ) {
	require_once plugin_dir_path( __FILE__ ) . 'class-lingua-press.php';

    // Load Add-ons Handler
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-handle-included-addons.php';
    new LRP_Handle_Included_Addons();

	/* make sure we execute our plugin before other plugins so the changes we make apply across the board */
	add_action( 'plugins_loaded', 'lrp_run_linguapress_hooks', 1 );
}
function lrp_run_linguapress_hooks(){
	$lrp = LRP_Lingua_Press::get_lrp_instance();
	$lrp->run();
}

function lrp_linguapress_disabled_notice(){
	echo '<div class="notice notice-error"><p>' . wp_kses( sprintf( __( '<strong>LinguaPress</strong> requires at least PHP version 5.6.20+ to run. It is the <a href="%s">minimum requirement of the latest WordPress version</a>. Please contact your server administrator to update your PHP version.','linguapress' ), 'https://wordpress.org/about/requirements/' ), array( 'a' => array( 'href' => array() ), 'strong' => array() ) ) . '</p></div>';
}

/**
 * Redirect users to the settings page on plugin activation
 */
add_action( 'activated_plugin', 'lrp_plugin_activation_redirect' );
function lrp_plugin_activation_redirect( $plugin ){

	if( !wp_doing_ajax() && $plugin == plugin_basename( __FILE__ ) ) {
		wp_safe_redirect( admin_url( 'options-general.php?page=lingua-press' ) );
		exit();
	}

}

// LinguaPress - AI Translation Plugin
