<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

class LRP_IN_Extra_Languages{

    protected $url_converter;
    protected $lrp_languages;
    protected $settings;
    protected $loader;

    public function __construct() {

        define( 'LRP_IN_EL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'LRP_IN_EL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

        $lrp = LRP_Lingua_Press::get_lrp_instance();
        $this->loader = $lrp->get_component( 'loader' );
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_sortable_language_script' );
        $this->loader->remove_hook( 'lrp_language_selector' );
        $this->loader->add_action( 'lrp_language_selector', $this, 'languages_selector', 10, 1 );
    }

    public function languages_selector( $languages ){
        if ( ! $this->url_converter ){
            $lrp = LRP_Lingua_Press::get_lrp_instance();
            $this->url_converter = $lrp->get_component( 'url_converter' );
        }
        if ( ! $this->settings ){
            $lrp = LRP_Lingua_Press::get_lrp_instance();
            $lrp_settings = $lrp->get_component( 'settings' );
            $this->settings = $lrp_settings->get_settings();
        }
        require_once( LRP_IN_EL_PLUGIN_DIR . 'partials/language-selector-pro.php' );
    }

    public function enqueue_sortable_language_script( ){
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'lingua-press' ){
            wp_enqueue_script( 'lrp-sortable-languages', LRP_IN_EL_PLUGIN_URL . 'assets/js/lrp-sortable-languages.js', array( 'jquery-ui-sortable' ), LRP_PLUGIN_VERSION );
        }
    }

}