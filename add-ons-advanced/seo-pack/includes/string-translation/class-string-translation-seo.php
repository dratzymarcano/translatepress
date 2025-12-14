<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();


class LRP_IN_SP_String_Translation_SEO {

    public function add_string_translation_types( $string_types_config, $lrp_string_translation ) {
        $option_based_strings = new LRP_IN_SP_Option_Based_Strings();
        $slugs_string_type    = array(
            'slugs' =>
                array(
                    'name'           => __( 'URL Slugs Translation', 'linguapress' ),
                    'tab_name'       => __( 'Slugs', 'linguapress' ),
                    'category_based' => true,
                    'categories'     => array(
                        'taxonomy'       => array(
                            'name'                   => __( 'Taxonomy Slugs', 'linguapress' ),
                            'search_name'            => __( 'Search Taxonomy Slugs', 'linguapress' ),
                            'class_name_suffix'      => 'Taxonomy_Slug',
                            'plugin_path'            => LRP_IN_SP_PLUGIN_DIR,
                            'nonces'                 => $lrp_string_translation->get_nonces_for_type( 'taxonomy' ),
                            'save_nonce'             => wp_create_nonce( 'string_translation_save_strings_taxonomy' ),
                            'table_columns'          => array(
                                'original'   => __( 'Taxonomy Slug', 'linguapress' ),
                                'translated' => __( 'Translation', 'linguapress' )
                            ),
                            'show_original_language' => false,
                            'filters'                => array()
                        ),
                        'term'           => array(
                            'name'                   => __( 'Term Slugs', 'linguapress' ),
                            'search_name'            => __( 'Search Term Slugs', 'linguapress' ),
                            'class_name_suffix'      => 'Term_Slug',
                            'plugin_path'            => LRP_IN_SP_PLUGIN_DIR,
                            'nonces'                 => $lrp_string_translation->get_nonces_for_type( 'term' ),
                            'table_columns'          => array(
                                'original'   => __( 'Term Slug', 'linguapress' ),
                                'translated' => __( 'Translation', 'linguapress' ),
                                'taxonomy'   => __( 'Taxonomy', 'linguapress' )
                            ),
                            'show_original_language' => false,
                            'filters'                => array(
                                'taxonomy' => array_merge(
                                    array( 'lrp_default' => __( 'Filter by Taxonomy', 'linguapress' ) ),
                                    $option_based_strings->get_public_slugs( 'taxonomies', true, array(), false )
                                )
                            )
                        ),
                        'postslug'            => array(
                            'name'                   => __( 'Post Slugs', 'linguapress' ),
                            'search_name'            => __( 'Search Post Slugs', 'linguapress' ),
                            'class_name_suffix'      => 'Post_Slug',
                            'plugin_path'            => LRP_IN_SP_PLUGIN_DIR,
                            'nonces'                 => $lrp_string_translation->get_nonces_for_type( 'postslug' ),
                            'table_columns'          => array(
                                'id'         => __( 'Post ID', 'linguapress' ),
                                'original'   => __( 'Post Slug', 'linguapress' ),
                                'translated' => __( 'Translation', 'linguapress' ),
                                'post_type'  => __( 'Post Type', 'linguapress' )
                            ),
                            'show_original_language' => false,
                            'filters'                => array(
                                'post-type'   => array_merge(
                                    array( 'lrp_default' => __( 'Filter by Post Type', 'linguapress' ) ),
                                    $option_based_strings->get_public_slugs( 'post_types', true, array(), false )
                                ),
                                'post-status' => array_merge(
                                    array( 'publish' => __( 'Published', 'linguapress' ) ),
                                    array( 'lrp_any' => __( 'Any Post Status', 'linguapress' ) ),
                                    get_post_statuses()
                                )
                            )
                        ),
                        'post-type-base' => array(
                            'name'                   => __( 'Post Type Base Slugs', 'linguapress' ),
                            'table_columns'          => array(
                                'original'   => __( 'Post Type Base Slug', 'linguapress' ),
                                'translated' => __( 'Translation', 'linguapress' )
                            ),
                            'show_original_language' => false,
                            'search_name'            => __( 'Search Post Type Base Slugs', 'linguapress' ),
                            'class_name_suffix'      => 'Post_Type_Base_Slug',
                            'plugin_path'            => LRP_IN_SP_PLUGIN_DIR,
                            'nonces'                 => $lrp_string_translation->get_nonces_for_type( 'post-type-base' ),
                            'filters'                => array()
                        )
                    )
                )
        );

        if ( class_exists( 'WooCommerce' ) ) {
            $slugs_string_type['slugs']['categories']['woocommerce-slug'] = array(
                'name'                   => __( 'WooCommerce Slugs', 'linguapress' ),
                'table_columns'          => array(
                    'original'   => __( 'WooCommerce Slug', 'linguapress' ),
                    'translated' => __( 'Translation', 'linguapress' )
                ),
                'show_original_language' => false,
                'search_name'            => __( 'Search WooCommerce Slugs', 'linguapress' ),
                'class_name_suffix'      => 'WooCommerce_Slug',
                'plugin_path'            => LRP_IN_SP_PLUGIN_DIR,
                'nonces'                 => $lrp_string_translation->get_nonces_for_type( 'woocommerce-slug' ),
                'filters'                => array()
            );

        }

        // Add 'other-slug' as the last category
        $slugs_string_type['slugs']['categories']['other-slug'] = array(
            'name' => __('Other Slugs', 'linguapress'),
            'table_columns' => array(
                'original' => __('Other Slugs', 'linguapress'),
                'translated' => __('Translation', 'linguapress')
            ),
            'show_original_language' => false,
            'search_name' => __('Search Other Slugs', 'linguapress'),
            'class_name_suffix' => 'Other_Slug',
            'plugin_path' => LRP_IN_SP_PLUGIN_DIR,
            'nonces' => $lrp_string_translation->get_nonces_for_type('other-slug'),
            'filters' => array()
        );

        return $slugs_string_type + $string_types_config;
    }

    /**
     * Enable navigation tabs
     * Hooked to lrp_editors_navigation
     *
     * @param $editors_navigation
     * @return array
     */
    public function enable_editors_navigation( $editors_navigation ){
        $editors_navigation['show'] = true;
        return $editors_navigation;
    }
}