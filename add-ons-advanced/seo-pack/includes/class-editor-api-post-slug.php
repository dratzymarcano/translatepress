<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

class LRP_IN_Editor_Api_Post_Slug {

	/* @var LRP_Query */
	protected $lrp_query;
	/* @var LRP_IN_SP_Slug_Manager */
	protected $slug_manager;
	/* @var LRP_Translation_Manager */
	protected $translation_manager;
	/* @var LRP_Url_Converter */
	protected $url_converter;
    protected $settings;

    /* @var LRP_Slug_Query */
    protected $slug_query;

    /* @var LRP_IN_SP_Editor_Actions */
    protected $editor_actions;

	/**
	 * LRP_Translation_Manager constructor.
	 *
	 * @param array $settings Settings option.
	 */
	public function __construct( $settings, $slug_manager ) {
		$this->settings = $settings;
		$this->slug_manager = $slug_manager;
        $this->slug_query = new LRP_Slug_Query();
        $this->editor_actions = new LRP_IN_SP_Editor_Actions( $this->slug_query, $settings );
	}

	/**
	 * Returns translations of slugs
	 *
	 * Hooked to wp_ajax_lrp_get_translations_postslug
	 */
	public function postslug_get_translations() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'postslug_get_translations', 'security' );
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'lrp_get_translations_postslug' && ! empty( $_POST['language'] ) && in_array( $_POST['language'], $this->settings['translation-languages'] ) ) {
				$ids = (empty($_POST['string_ids']) )? array() : json_decode(stripslashes($_POST['string_ids']));/* phpcs:ignore */ /* sanitized downstream */
				if ( is_array( $ids )){
					$lrp = LRP_Lingua_Press::get_lrp_instance();
					if (!$this->translation_manager) {
						$this->translation_manager = $lrp->get_component('translation_manager');
					}
					$localized_text = $this->translation_manager->string_groups();
					$id_array = array();
					$dictionaries = array();

					foreach ( $ids as $id ) {
						if ( isset( $id ) && is_numeric( $id ) ) {
							$id_array[] = (int) $id;
						}
					}

					foreach( $id_array as $post_id ) {
                        $original = get_post_field( 'post_name', $post_id );
                        $original_id_array = $this->slug_query->get_ids_from_original( (array) $original );

                        $original_id = $original_id_array[$original];

                        $translations = $this->slug_query->get_translated_slugs_from_original( (array) $original );

                        $entry = array(
                            'dbID'              => $post_id,
							'original_id'       => $original_id,
							'translationsArray' => array(),
							'type'              => 'postslug',
							'group'             => $localized_text['slugs'],
							'original'          => urldecode( $original )
						);

						foreach ( $this->settings['translation-languages'] as $language ) {
							if ( $language != $this->settings['default-language'] ) {
                                if ( isset( $translations[ $original_id ][ $language ] ) ){
                                    $translated = $translations[ $original_id ][ $language ]['translated'];
                                    $translation_id = $translations[$original_id][$language]['id'];
                                    $status = $translations[$original_id][$language]['status'];
                                }else {
                                    $translated = '';
                                    $translation_id = '';
                                    $status = 0;
                                }
								$entry['translationsArray'][$language] = array(
									'translation_id'    => $translation_id,
                                    'original_id'       => $original_id,
                                    'status'            => $status,
									'translated'        => urldecode( $translated ),
									'editedTranslation' => urldecode( $translated ),
								);
							}
						}
						$dictionaries[] = $entry;
					}
					echo lrp_safe_json_encode( $dictionaries );//phpcs:ignore
				}
			}
		}
		wp_die();
	}

	/**
	 * Save translations of slugs
	 *
	 * Hooked to wp_ajax_lrp_save_translations_postslug
	 */
	public function postslug_save_translations() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && apply_filters( 'lrp_translating_capability', 'manage_options' ) ) {
			check_ajax_referer( 'postslug_save_translations', 'security' );
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'lrp_save_translations_postslug' && !empty( $_POST['strings'] ) ) {
				$slugs = json_decode(stripslashes($_POST['strings'])); /* phpcs:ignore */ /* sanitized downstream */
				$update_slugs = array();

                if ( !empty( $slugs ) ){
                    $update_slugs = $this->editor_actions->save_slugs( $slugs, 'post' );
                }

			}
		}

		echo lrp_safe_json_encode( $update_slugs );//phpcs:ignore
		wp_die();
	}

}