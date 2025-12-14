<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

if( !class_exists('LRP_String_Translation_API_Taxonomy_Slug') ) {
    class LRP_String_Translation_API_Taxonomy_Slug
    {
        protected $type = 'taxonomy-slug';
        protected $option_name = 'lrp_taxonomy_slug_translation';
        protected $helper;
        protected $settings;
        protected $option_based_slugs;

        public function __construct($settings)
        {
            $this->settings = $settings;
            $this->helper = new LRP_String_Translation_Helper();
            $this->option_based_slugs = new LRP_IN_SP_Option_Based_Strings();
        }

        public function get_strings()
        {
            $this->helper->check_ajax($this->type, 'get');

            $all_slugs = $this->option_based_slugs->get_public_slugs('taxonomies');

            $return = $this->option_based_slugs->get_strings_for_option_based_slug($this->type, $this->option_name, $all_slugs);

            echo lrp_safe_json_encode($return);//phpcs:ignore
            wp_die();
        }

        public function save_strings()
        {

            $this->helper->check_ajax($this->type, 'save');

            $this->option_based_slugs->save_strings_for_option_based_slug($this->type, $this->option_name);
        }


        /**
         * Get the type of the operation used for save_strings_for_option_based_slug()
         * @return string
         */
        public function get_type()
        {
            return $this->type;
        }

        /**
         * Get the option name for taxonomy base slugs where they are translated
         * @return string
         */
        public function get_option_name()
        {
            return $this->option_name;
        }
    }
}