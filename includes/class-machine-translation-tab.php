<?php


if ( !defined('ABSPATH' ) )
    exit();

class LRP_Machine_Translation_Tab {

    private $settings;

    public function __construct( $settings ) {

        $this->settings = $settings;

        add_action( 'plugins_loaded', array( $this, 'add_upsell_filter' ) );
        add_filter( 'lrp_machine_translate_slug', array( $this, 'add_enable_auto_translate_slug_filter' ) );
        add_action( 'wp_ajax_test_api_key', array( $this, 'test_api_key' ) );
    }

    /*
    * Add new tab to TP settings
    *
    * Hooked to lrp_settings_tabs
    */
    public function add_tab_to_navigation( $tabs ){
        $tab = array(
            'name'  => __( 'Automatic Translation', 'linguapress' ),
            'url'   => admin_url( 'admin.php?page=lrp_machine_translation' ),
            'page'  => 'lrp_machine_translation'
        );

        array_splice( $tabs, 2, 0, array( $tab ) );

        return $tabs;
    }

    /*
    * Add submenu for advanced page tab
    *
    * Hooked to admin_menu
    */
    public function add_submenu_page() {
        add_submenu_page( 'LRPHidden', 'LinguaPress Automatic Translation', 'LRPHidden', apply_filters( 'lrp_settings_capability', 'manage_options' ), 'lrp_machine_translation', array( $this, 'machine_translation_page_content' ) );
        add_submenu_page( 'LRPHidden', 'LinguaPress Test Automatic Translation API', 'LRPHidden', apply_filters( 'lrp_settings_capability', 'manage_options' ), 'lrp_test_machine_api', array( $this, 'test_api_page_content' ) );
    }

    /**
    * Register setting
    *
    * Hooked to admin_init
    */
    public function register_setting(){
        register_setting( 'lrp_machine_translation_settings', 'lrp_machine_translation_settings', array( $this, 'sanitize_settings' ) );
    }

    /**
    * Output admin notices after saving settings.
    */
    public function admin_notices(){
        if( isset( $_GET['page'] ) && $_GET['page'] == 'lrp_machine_translation' )
            settings_errors();
    }

    /*
    * Sanitize settings
    */
    public function sanitize_settings($mt_settings ){

        $free_version = !class_exists( 'LRP_Handle_Included_Addons' );
        $seo_pack_active = class_exists( 'LRP_IN_Seo_Pack');

        $settings = array();
        $machine_translation_keys = array( 'machine-translation', 'translation-engine', 'google-translate-key', 'deepl-api-type', 'deepl-api-key', 'openrouter-api-key', 'chatgpt-api-key', 'chatgpt-system-prompt', 'chatgpt-glossary', 'openrouter-system-prompt', 'openrouter-glossary', 'block-crawlers', 'automatically-translate-slug', 'machine_translation_limit', 'machine_translation_log', 'machine_translation_limit_enabled' );
        foreach( $machine_translation_keys as $key ){
            if( isset( $mt_settings[$key] ) ){
                $settings[$key] = $mt_settings[$key];
            }
        }

        if( !empty( $settings['machine-translation'] ) ) {
            $settings['machine-translation'] = sanitize_text_field( $settings['machine-translation'] );
        }else
            $settings['machine-translation'] = 'no';

        if( !empty( $settings['chatgpt-system-prompt'] ) ) {
            $settings['chatgpt-system-prompt'] = sanitize_textarea_field( $settings['chatgpt-system-prompt'] );
        }

        if( !empty( $settings['chatgpt-glossary'] ) ) {
            $settings['chatgpt-glossary'] = sanitize_textarea_field( $settings['chatgpt-glossary'] );
        }

        if( !empty( $settings['openrouter-system-prompt'] ) ) {
            $settings['openrouter-system-prompt'] = sanitize_textarea_field( $settings['openrouter-system-prompt'] );
        }

        if( !empty( $settings['openrouter-glossary'] ) ) {
            $settings['openrouter-glossary'] = sanitize_textarea_field( $settings['openrouter-glossary'] );
        }

        if( !empty( $settings['translation-engine'] ) )
            $settings['translation-engine'] = sanitize_text_field( $settings['translation-engine']  );
        else
            $settings['translation-engine'] = 'google_translate_v2';

        if($settings['translation-engine'] == 'deepl_upsell' && !class_exists( 'LRP_DeepL' ) && !class_exists( 'LRP_IN_DeepL' )){
            $settings['translation-engine'] = 'google_translate_v2';
        }

        if( !empty( $settings['block-crawlers'] ) )
            $settings['block-crawlers'] = sanitize_text_field( $settings['block-crawlers']  );
        else
            $settings['block-crawlers'] = 'no';

        if( !empty( $settings['machine_translation_limit_enabled'] ) )
            $settings['machine_translation_limit_enabled'] = sanitize_text_field( $settings['machine_translation_limit_enabled'] );
        else
            $settings['machine_translation_limit_enabled'] = 'no';

        if( $free_version || !$seo_pack_active ){
            $mt_settings_option = get_option( 'lrp_machine_translation_settings' );
            if( isset( $mt_settings_option['automatically-translate-slug'] ) ){
                $settings['automatically-translate-slug'] = $mt_settings_option['automatically-translate-slug'];
            }
        }
        else{
            if( !empty( $settings['automatically-translate-slug'] ) )
                $settings['automatically-translate-slug'] = sanitize_text_field( $settings['automatically-translate-slug'] );
            else
                $settings['automatically-translate-slug'] = 'no';
        }

        if (  isset ( $_POST['option_page'] ) &&
            $_POST['option_page'] === 'lrp_machine_translation_settings' &&
            current_user_can( apply_filters( 'lrp_translating_capability', 'manage_options' ) ) )
        {
            $db_stored_data = get_option( 'lrp_db_stored_data', array() );
            unset( $db_stored_data['lrp_mt_supported_languages'][ $settings['translation-engine'] ]['last-checked'] );
            update_option( 'lrp_db_stored_data', $db_stored_data );
        }

        return apply_filters( 'lrp_machine_translation_sanitize_settings', $settings, $mt_settings );
    }

    /*
    * Automatic Translation
    */
    public function machine_translation_page_content(){
        $lrp                       = LRP_Lingua_Press::get_lrp_instance();

        $machine_translator_logger = $lrp->get_component( 'machine_translator_logger' );
        $machine_translator_logger->maybe_reset_counter_date();

        $machine_translator        = $lrp->get_component( 'machine_translator' );

        require_once LRP_PLUGIN_DIR . 'partials/machine-translation-settings-page.php';
    }

    /**
    * Test selected API functionality
    */
    public function test_api_page_content(){
        require_once LRP_PLUGIN_DIR . 'partials/test-api-settings-page.php';
    }

    public function load_engines(){
	    include_once LRP_PLUGIN_DIR . 'includes/mtapi/functions.php';
	    include_once LRP_PLUGIN_DIR . 'includes/mtapi/class-mtapi-machine-translator.php';

        include_once LRP_PLUGIN_DIR . 'includes/google-translate/functions.php';
        include_once LRP_PLUGIN_DIR . 'includes/google-translate/class-google-translate-v2-machine-translator.php';

        include_once LRP_PLUGIN_DIR . 'includes/openrouter/functions.php';
        include_once LRP_PLUGIN_DIR . 'includes/openrouter/class-openrouter-machine-translator.php';

        include_once LRP_PLUGIN_DIR . 'includes/chatgpt/functions.php';
        include_once LRP_PLUGIN_DIR . 'includes/chatgpt/class-chatgpt-machine-translator.php';
    }

    public function get_active_engine( ){
        // This $default is just a fail safe. Should never be used. The real default is set in LRP_Settings->set_options function
        $default = 'LRP_MTAPI_Machine_Translator';

        if( empty( $this->settings['lrp_machine_translation_settings']['translation-engine'] ) )
            $value = $default;
        else {
            $deepl_class_name = class_exists('LRP_IN_Deepl_Machine_Translator' ) ? 'LRP_IN_Deepl_Machine_Translator' : 'LRP_Deepl_Machine_Translator';
            $existing_engines = apply_filters('lrp_automatic_translation_engines_classes', array(
                'mtapi' => 'LRP_MTAPI_Machine_Translator',
                'google_translate_v2' => 'LRP_Google_Translate_V2_Machine_Translator',
                'deepl'               => $deepl_class_name,
                'openrouter'          => 'LRP_OpenRouter_Machine_Translator',
                'chatgpt'             => 'LRP_ChatGPT_Machine_Translator'
            ));

            $value = ( isset( $existing_engines[$this->settings['lrp_machine_translation_settings']['translation-engine']] ) ) ? $existing_engines[$this->settings['lrp_machine_translation_settings']['translation-engine']] : '';

            if( !class_exists( $value ) ) {
                $value = $default; //something is wrong if it reaches this
            }
        }

        return new $value( $this->settings );
    }

    public function add_upsell_filter(){
        if( !class_exists( 'LRP_DeepL' ) && !class_exists( 'LRP_IN_DeepL' ) )
            add_filter( 'lrp_machine_translation_engines', [ $this, 'translation_engines_upsell' ], 20 );
    }

    public function translation_engines_upsell( $engines ){
        $engines[] = array( 'value' => 'deepl_upsell', 'label' => __( 'DeepL', 'linguapress' ) );

        return $engines;
    }


    public function add_enable_auto_translate_slug_filter( $allow ){
        if( !empty( $this->settings['lrp_machine_translation_settings']['machine-translation'] ) &&
            $this->settings['lrp_machine_translation_settings']['machine-translation'] == 'yes' &&
            isset( $this->settings['lrp_machine_translation_settings']['automatically-translate-slug'] ) &&
            $this->settings['lrp_machine_translation_settings']['automatically-translate-slug'] == 'yes'
        ){
            $allow = true;
        }
        return $allow;
    }

    public function display_unsupported_languages(){
        $lrp = LRP_Lingua_Press::get_lrp_instance();
        $machine_translator = $lrp->get_component( 'machine_translator' );
        $lrp_languages = $lrp->get_component( 'languages' );

        $correct_key = $machine_translator->is_correct_api_key();

        if ( 'yes' === $this->settings['lrp_machine_translation_settings']['machine-translation'] &&
            !empty( $machine_translator->get_api_key() ) &&
            !$machine_translator->check_languages_availability($this->settings['translation-languages']) &&
            $correct_key
        ){
            $language_names = $lrp_languages->get_language_names( $this->settings['translation-languages'], 'english_name' );

            ?>
            <div class="lrp-settings-container" id="lrp_unsupported_languages">
                <h3 class="lrp-settings-primary-heading"><?php esc_html_e( 'Unsupported languages', 'linguapress' ); ?></h3>
                <div class="lrp-settings-separator"></div>

                <ul class="lrp-unsupported-languages">
                    <?php
                    foreach ( $this->settings['translation-languages'] as $language_code ) {
                        if ( !$machine_translator->check_languages_availability( array( $language_code ) ) ) {
                            echo '<li class="lrp-primary-text-bold">' . esc_html( $language_names[$language_code] ) . '</li>';
                            echo '<div class="lrp-settings-separator" style="width: 65%;"></div>';
                        }
                    }
                    ?>
                </ul>
                <p class="lrp-primary-text">
                    <?php echo wp_kses( __( 'The selected automatic translation engine does not provide support for these languages.<br>You can still manually translate pages in these languages using the Translation Editor.', 'linguapress' ), array( 'br' => array() ) ); ?>
                </p>

            </div>

            <?php
        }
    }

    public function test_api_key(){
        check_ajax_referer( 'lrp_test_api_nonce', 'security' );

        $lrp                = LRP_Lingua_Press::get_lrp_instance();
        $machine_translator = $lrp->get_component( 'machine_translator' );

        $response           = $machine_translator->test_request();

        if ( is_wp_error( $response ) ) {
            wp_send_json_error([
                'message' => esc_html__('API key validation failed.', 'linguapress'),
                'error'   => $response->get_error_message()
            ]);
        }

        ob_start();
        print_r( $response );
        $full_response = ob_get_clean();

        wp_send_json_success([
            'message'      => esc_html__('API key verification was successful.', 'linguapress'),
            'response'     => $response,
            'raw_response' => $full_response
        ]);
    }

}
