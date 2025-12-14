<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

class LRP_IN_Automatic_Language_Detection {

	protected $settings;
	protected $loader;
	/* @var LRP_Url_Converter */
	protected $url_converter;
	/* @var LRP_IN_ALD_Settings */
	protected $lrp_ald_settings;
	/* @var LRP_Languages */
	protected $lrp_languages;

	/**
	 * LRP_Automatic_Language_Detection constructor.
	 *
	 * Defines constants, adds hooks and deals with license page.
	 */
	public function __construct() {

		define( 'LRP_IN_ALD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'LRP_IN_ALD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		require_once( LRP_IN_ALD_PLUGIN_DIR . 'includes/class-ald-settings.php' );
		require_once( LRP_IN_ALD_PLUGIN_DIR . 'includes/class-determine-language.php' );

		$this->lrp_ald_settings = new LRP_IN_ALD_Settings();
		$lrp = LRP_Lingua_Press::get_lrp_instance();

		$this->loader = $lrp->get_component( 'loader' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_cookie_adding' );

		$this->loader->add_action( 'lrp_output_advanced_settings_options', $this->lrp_ald_settings, 'addon_settings_ui', 4, 1 );
		$this->loader->add_action( 'admin_init', $this->lrp_ald_settings, 'register_setting' );

		$this->loader->add_action('wp_footer', $this, 'activate_popup');
        $this->loader->add_action('wp_footer', $this, 'activate_hello_bar');


        $this->loader->add_filter('lrp_advanced_tab_add_element', $this, 'add_automatic_user_language_detection_to_tab');
        $this->loader->add_filter( 'lrp_register_advanced_settings', $this, 'eliminate_no_border_from_setting_array', 10000, 1);
    }

    public function add_automatic_user_language_detection_to_tab($advanced_settings_array){
        $settings_array[] = array(
            'name'          => 'automatic_user_language_detection',
            'type'          => 'separator',
            'label'         => esc_html__( 'Automatic User Language Detection', 'linguapress' ),
            'no-border'     => true,
            'id'            =>'ald_settings',
        );

        array_unshift($advanced_settings_array, $settings_array[0]);

        return $advanced_settings_array;
    }

    public function eliminate_no_border_from_setting_array($settings_array){
        foreach ($settings_array as $item => $value){
            unset($settings_array[$item]['no-border']);
        }

        return $settings_array;
    }

	/**
	 * Enqueue script on all front-end pages
	 */
	public function enqueue_cookie_adding() {
		$lrp_language_cookie_data = $this->get_language_cookie_data();
        if ( apply_filters( 'lrp_ald_enqueue_redirecting_script', true ) ) {
            wp_enqueue_script( 'lrp-language-cookie', LRP_IN_ALD_PLUGIN_URL . 'assets/js/lrp-language-cookie.js', array( 'jquery' ), LRP_IN_ALD_PLUGIN_VERSION );
            wp_localize_script( 'lrp-language-cookie', 'lrp_language_cookie_data', $lrp_language_cookie_data );

            $ald_settings = $this->lrp_ald_settings->get_ald_settings();
            if($ald_settings['popup_option'] == 'popup') {
                wp_register_style( 'lrp-popup-style', LRP_IN_ALD_PLUGIN_URL . 'assets/css/lrp-popup.css' );
                wp_enqueue_style( 'lrp-popup-style' );
            }

        }
	}


	/**
	 * Returns site data useful for determining language from url
	 *
	 * @return array
	 */
	public function get_language_cookie_data() {
		$lrp = LRP_Lingua_Press::get_lrp_instance();
		if ( ! $this->url_converter ) {
			$this->url_converter = $lrp->get_component( 'url_converter' );
		}
		if ( ! $this->settings ) {
			$lrp_settings   = $lrp->get_component( 'settings' );
			$this->settings = $lrp_settings->get_settings();
		}
		if ( ! $this->lrp_languages ) {
			$this->lrp_languages = $lrp->get_component( 'languages' );
		}
		$ald_settings = $this->lrp_ald_settings->get_ald_settings();

        $language_urls = array();
        foreach( $this->settings['publish-languages'] as $language ){
            $language_urls[ $language ] = esc_url( $this->url_converter->get_url_for_language( $language, null, '' ) );

        }

		$data = array(
			'abs_home'          => $this->url_converter->get_abs_home(),
			'url_slugs'         => $this->settings['url-slugs'],
			'cookie_name'       => 'lrp_language',
			'cookie_age'        => '30',
			'cookie_path'       => COOKIEPATH,
			'default_language'  => $this->settings['default-language'],
			'publish_languages' => $this->settings['publish-languages'],
			'lrp_ald_ajax_url'  => apply_filters( 'lrp_ald_ajax_url', LRP_IN_ALD_PLUGIN_URL . 'includes/lrp-ald-ajax.php' ),
			'detection_method'  => $ald_settings['detection-method'],
			'popup_option'      => $ald_settings['popup_option'],
            'popup_type'        => $ald_settings['popup_type'],
            'popup_textarea'    => $ald_settings['popup_textarea'],
            'popup_textarea_change_button' => $ald_settings['popup_textarea_button'],
            'popup_textarea_close_button' =>$ald_settings['popup_textarea_close_button'],
			'iso_codes'         => $this->lrp_languages->get_iso_codes( $this->settings['publish-languages'] ),
            'language_urls'     => $language_urls,
            'english_name'      => $this->lrp_languages->get_language_names($this->settings['publish-languages']),
            'is_iphone_user_check' =>apply_filters('lrp_hide_popup_for_iphone_users', false)

		);

		return apply_filters( 'lrp_language_cookie_data', $data );
	}

	public function activate_popup(){
	    require_once('partials/popup.php');
    }

    public function activate_hello_bar(){
        require_once('partials/no-text-popup.php');
    }
}
