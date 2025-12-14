<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Controls_Manager;

class LRP_Elementor {
    private static $_instance = null;
    public $locations = array(
        array(
            'element' => 'common',
            'action'  => '_section_style',
        ),
        array(
            'element' => 'section',
            'action'  => 'section_advanced',
        ),
        array(
            'element' => 'container',
            'action'  => 'section_layout',
        )
    );
    public $section_name_show    = 'lrp_section_show';
    public $section_name_exclude = 'lrp_section_exclude';

	/**
	 * Register plugin action hooks and filters
	 */
	public function __construct() {

        // Register new section to display restriction controls
        $this->register_sections();

        // Setup controls
        $this->register_controls();

        // Filter widget content
		add_filter( 'elementor/widget/render_content', array( $this, 'widget_render' ), 10, 2 );

		// Filter sections display & add custom messages
		add_action( 'elementor/frontend/section/should_render', array( $this, 'section_render' ), 10, 2 );

        // Filter container display
        add_action( 'elementor/frontend/container/should_render', array( $this, 'section_render' ), 10, 2 );

        // Add data-no-translation to elements that are restricted to a particular language
        add_action( 'elementor/element/after_add_attributes', array( $this, 'add_attributes' ) );

        add_filter( 'lrp_allow_language_redirect', array( $this, 'lrp_elementor_compatibility' ) );

        // Disable Element Cache when Language Restriction rules are setup for an element
		add_filter( 'elementor/element/is_dynamic_content', array( $this, 'are_language_restriction_rules_setup' ), 20, 3 );

	}

    /**
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @return LRP_Elementor An instance of the class.
     */
    public static function instance() {

        if ( is_null( self::$_instance ) )
            self::$_instance = new self();

        return self::$_instance;

    }

    private function register_sections() {

        foreach( $this->locations as $where ){
            add_action( 'elementor/element/'.$where['element'].'/'.$where['action'].'/after_section_end', array( $this, 'add_section_show' ), 10, 2 );
            add_action( 'elementor/element/'.$where['element'].'/'.$where['action'].'/after_section_end', array( $this, 'add_section_exclude' ), 10, 2 );
        }

    }

    // Register controls to sections and widgets
    private function register_controls() {

        foreach( $this->locations as $where ){
            add_action('elementor/element/'.$where['element'].'/'.$this->section_name_show.'/before_section_end', array( $this, 'add_controls_show' ), 10, 2 );
            add_action('elementor/element/'.$where['element'].'/'.$this->section_name_exclude.'/before_section_end', array( $this, 'add_controls_exclude' ), 10, 2 );
        }

    }

    public function add_section_show( $element, $args ) {

        $exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $element->get_unique_name(), $this->section_name_show );

        if( !is_wp_error( $exists ) )
            return false;

        $element->start_controls_section(
            $this->section_name_show, array(
                'tab'   => Controls_Manager::TAB_ADVANCED,
                'label' => __( 'Restrict by Language', 'linguapress' )
            )
        );

        $element->end_controls_section();

    }

    public function add_section_exclude( $element, $args ) {

        $exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $element->get_unique_name(), $this->section_name_exclude );

        if( !is_wp_error( $exists ) )
            return false;

        $element->start_controls_section(
            $this->section_name_exclude, array(
                'tab'   => Controls_Manager::TAB_ADVANCED,
                'label' => __( 'Exclude from Language', 'linguapress' )
            )
        );

        $element->end_controls_section();

    }

    // Define controls
	public function add_controls_show( $element, $args ) {

		$element_type = $element->get_type();

		$element->add_control(
			'lrp_language_restriction', array(
				'label'       => __( 'Restrict element to language', 'linguapress' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Show this element only in one language.', 'linguapress' ),
			)
		);

        $element->add_control(
            'lrp_language_restriction_automatic_translation', array(
                'label'       => __( 'Enable translation', 'linguapress' ),
                'type'        => Controls_Manager::SWITCHER,
                'description' => __( 'Allow translation to the corresponding language only if the content is written in the default language.', 'linguapress' ),
            )
        );

		$element->add_control(
			'lrp_language_restriction_heading', array(
				'label'     => __( 'Select language', 'linguapress' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);


        $lrp                 = LRP_Lingua_Press::get_lrp_instance();
        $lrp_languages       = $lrp->get_component( 'languages' );
        $lrp_settings        = $lrp->get_component( 'settings' );
        $published_languages = $lrp_languages->get_language_names( $lrp_settings->get_settings()['publish-languages'] );

		$element->add_control(
            'lrp_restricted_languages', array(
                'type'        => Controls_Manager::SELECT2,
                'options'     => $published_languages,
				'label_block' => 'true',
				'description' => __( 'Choose in which language to show this element.', 'linguapress' ),
            )
        );

	}

    public function add_controls_exclude( $element, $args ) {

		$element_type = $element->get_type();

		$element->add_control(
			'lrp_exclude_handler', array(
				'label'       => __( 'Exclude element from language', 'linguapress' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Exclude this element from specific languages.', 'linguapress' ),
			)
		);

		$element->add_control(
			'lrp_excluded_heading', array(
				'label'     => __( 'Select languages', 'linguapress' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);


        $lrp                 = LRP_Lingua_Press::get_lrp_instance();
        $lrp_languages       = $lrp->get_component( 'languages' );
        $lrp_settings        = $lrp->get_component( 'settings' );
        $published_languages = $lrp_languages->get_language_names( $lrp_settings->get_settings()['publish-languages'] );

		$element->add_control(
            'lrp_excluded_languages', array(
                'type'                => Controls_Manager::SELECT2,
                'options'             => $published_languages,
				'multiple'            => 'true',
				'label_block'         => 'true',
				'description'         => __( 'Choose from which languages to exclude this element.', 'linguapress' ),
            )
        );

        $message  = '<p>' . __( 'This element will still be visible when you are translating your website through the Translation Editor.', 'linguapress' ) . '</p>';
        $message .= '<p>' . __( 'The content of this element should be written in the default language.', 'linguapress' ) . '</p>';

		$element->add_control(
            'lrp_excluded_message', array(
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => $message,
            )
        );

	}

    // Verifies if element is hidden
	public function is_hidden( $element ) {

		$settings = $element->get_settings();

        if( isset( $settings['lrp_language_restriction'] ) && $settings['lrp_language_restriction'] == 'yes' && !empty( $settings['lrp_restricted_languages'] ) ){

            $current_language = get_locale();

            if( $current_language != $settings['lrp_restricted_languages'] )
                return true;

        }

        if( !isset( $_GET['lrp-edit-translation'] ) && isset( $settings['lrp_exclude_handler'] ) && $settings['lrp_exclude_handler'] == 'yes' && !empty( $settings['lrp_excluded_languages'] ) ){

            $current_language = get_locale();

            if( in_array( $current_language, $settings['lrp_excluded_languages'] ) )
                return true;

        }

		return false;

	}

	// Widget display & custom messages
	public function widget_render( $content, $widget ) {

		if( $this->is_hidden( $widget ) ){

            if( \Elementor\Plugin::$instance->editor->is_edit_mode() )
                return $content;

            return '<style>' . $widget->get_unique_selector() . '{display:none !important}</style>';

        }

		return $content;

	}

	// Section display
	public function section_render( $should_render, $element ) {

		if( $this->is_hidden( $element ) === true )
			return false;

		return $should_render;

	}

    public function add_attributes( $element ){

        $settings = $element->get_settings();

        if( isset( $settings['lrp_language_restriction'] ) && $settings['lrp_language_restriction'] == 'yes' && !empty( $settings['lrp_restricted_languages'] ) && isset( $settings['lrp_language_restriction_automatic_translation'] ) && $settings['lrp_language_restriction_automatic_translation'] != 'yes')
            $element->add_render_attribute( '_wrapper', 'data-no-translation' );

    }

    /**
     * Do not redirect when elementor preview is present
     *
     * @param $allow_redirect
     *
     * @return bool
     */
    public function lrp_elementor_compatibility( $allow_redirect ){

        // compatibility with Elementor preview. Do not redirect to subdir language when elementor preview is present.
        if ( isset( $_GET['elementor-preview'] ) )
            return false;

        return $allow_redirect;

    }

    public function are_language_restriction_rules_setup( $is_dynamic_content, $data, $element ){

		if( empty( $data['settings'] ) )
			return $is_dynamic_content;

		if( isset( $data['settings']['lrp_language_restriction'] ) && $data['settings']['lrp_language_restriction'] == 'yes' )
			return true;

		if( isset( $data['settings']['lrp_exclude_handler'] ) && $data['settings']['lrp_exclude_handler'] == 'yes' )
			return true;

		if( isset( $data['settings']['lrp_restricted_languages'] ) && !empty( $data['settings']['lrp_restricted_languages'] ) )
			return true;

		if( isset( $data['settings']['lrp_excluded_languages'] ) && !empty( $data['settings']['lrp_excluded_languages'] ) )
			return true;

		return $is_dynamic_content;

	}
}

// Instantiate Plugin Class
LRP_Elementor::instance();
