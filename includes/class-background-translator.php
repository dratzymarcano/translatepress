<?php

if ( !defined('ABSPATH' ) )
    exit();

class LRP_Background_Translator {

    protected $settings;
    protected $lrp_languages;

    public function __construct( $settings ) {
        $this->settings = $settings;
        $lrp = LRP_Lingua_Press::get_lrp_instance();
        $this->lrp_languages = $lrp->get_component('languages');

        add_action( 'save_post', array( $this, 'trigger_background_translation' ), 10, 3 );
    }

    /**
     * Trigger background translation on post save
     *
     * @param int $post_id
     * @param WP_Post $post
     * @param bool $update
     */
    public function trigger_background_translation( $post_id, $post, $update ) {
        // Only run on publish or update of public post types
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( $post->post_status !== 'publish' ) {
            return;
        }

        $post_type = get_post_type_object( $post->post_type );
        if ( !$post_type || !$post_type->public ) {
            return;
        }

        // Get all active languages
        $published_languages = $this->lrp_languages->get_languages( 'publish' );
        $default_language = $this->settings['default-language'];

        // Get the permalink
        $permalink = get_permalink( $post_id );

        if ( !$permalink ) {
            return;
        }

        // Loop through languages and trigger translation
        foreach ( $published_languages as $language_code ) {
            if ( $language_code === $default_language ) {
                continue;
            }

            $url = $this->get_translated_url( $permalink, $language_code );
            
            // Send non-blocking request
            $this->send_async_request( $url );
        }
    }

    /**
     * Get translated URL for a specific language
     */
    protected function get_translated_url( $url, $language_code ) {
        $lrp = LRP_Lingua_Press::get_lrp_instance();
        $url_converter = $lrp->get_component( 'url_converter' );
        return $url_converter->get_url_for_language( $language_code, $url, '' );
    }

    /**
     * Send asynchronous request to the URL
     */
    protected function send_async_request( $url ) {
        $args = array(
            'timeout'   => 0.01,
            'blocking'  => false,
            'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
        );

        wp_remote_get( $url, $args );
    }
}
