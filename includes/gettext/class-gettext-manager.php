<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class LRP_Gettext_Manager
 *
 * Handles Gettext strings
 */
class LRP_Gettext_Manager {
	protected $settings;
	/** @var LRP_Query */
	protected $lrp_query;
	/** @var LRP_Process_Gettext */
	protected $process_gettext;
	/** @var LRP_Plural_Forms */
	protected $plural_forms;
	protected $machine_translator;
	protected $url_converter;
	protected $is_admin_request = null;


	/**
	 * LRP_Gettext_Manager constructor.
	 *
	 * @param array $settings Settings option.
	 */
	public function __construct( $settings ) {
		$this->settings        = $settings;
		$this->plural_forms    = new LRP_Plural_Forms( $this->settings );
		$this->process_gettext = new LRP_Process_Gettext( $this->settings, $this->plural_forms );
	}

	public function get_gettext_component( $component ) {
		return $this->$component;
	}


	/**
	 * Create a global with the gettext strings that exist in the database
	 */
	public function create_gettext_translated_global() {

		global $lrp_translated_gettext_texts, $lrp_translated_gettext_texts_language;
		if ( $this->processing_gettext_is_needed() ) {
			$language = get_locale();

			if ( in_array( $language, $this->settings['translation-languages'] ) ) {
				$lrp_translated_gettext_texts_language = $language;
                global $wpdb, $lrp_wpdb_prefix;
                $lrp_wpdb_prefix = $wpdb->get_blog_prefix();
                $lrp             = LRP_Lingua_Press::get_lrp_instance();
				if ( ! $this->lrp_query ) {
					$this->lrp_query = $lrp->get_component( 'query' );
				}

				$strings = $this->lrp_query->get_all_gettext_strings( $language );
				if ( ! empty( $strings ) ) {
					$lrp_translated_gettext_texts = $strings;
					$lrp_strings                  = array();
					foreach ( $lrp_translated_gettext_texts as $key => $value ) {
						$context     = ( $value['context'] ) ? $value['context'] : 'lrp_context';
						$plural_form = ( $value['plural_form'] ) ? $value['plural_form'] : 0;
						$domain      = ( $value['domain'] ) ? $value['domain'] : $value['tt_domain'];
						$original    = ( $value['original'] ) ? $value['original'] : $value['tt_original'];

						// lrp_context::0::domain::original
						$lrp_strings[ $context . '::' . $plural_form . '::' . $domain . '::' . $original ] = $value;
					}
					$lrp_translated_gettext_texts = $lrp_strings;
				}
			}
		}
	}

	/**
	 * function that applies the gettext filter on frontend on different hooks depending on what we need
	 */
	public function initialize_gettext_processing() {
		$is_ajax_on_frontend = $this::is_ajax_on_frontend();

		/* on ajax hooks from frontend that have the init hook ( we found WooCommerce has it ) apply it earlier */
		if ( $is_ajax_on_frontend || apply_filters( 'lrp_apply_gettext_early', false ) ) {
			add_action( 'wp_loaded', array( $this, 'apply_gettext_filter' ) );
		} else {//otherwise start from the wp_head hook
			add_action( 'wp_head', array( $this, 'apply_gettext_filter' ), 100 );
		}

		//if we have woocommerce installed and it is not an ajax request add a gettext hook starting from wp_loaded and remove it on wp_head
		if ( class_exists( 'WooCommerce' ) && ! $is_ajax_on_frontend && ! apply_filters( 'lrp_apply_gettext_early', false ) ) {
			// WooCommerce launches some ajax calls before wp_head, so we need to apply_gettext_filter earlier to catch them
			add_action( 'wp_loaded', array( $this, 'apply_woocommerce_gettext_filter' ), 19 );
		}
	}

	/* apply the gettext filter here */
	public function apply_gettext_filter() {

		//if we have wocommerce installed remove te hook that was added on wp_loaded
		if ( class_exists( 'WooCommerce' ) ) {
			// WooCommerce launches some ajax calls before wp_head, so we need to apply_gettext_filter earlier to catch them
			remove_action( 'wp_loaded', array( $this, 'apply_woocommerce_gettext_filter' ), 19 );
		}

		$this->call_gettext_filters();

	}

	public function apply_woocommerce_gettext_filter() {
		$this->call_gettext_filters( 'woocommerce_' );
	}

	public function processing_gettext_is_needed() {
		global $pagenow;

		if ( ! $this->url_converter ) {
			$lrp                 = LRP_Lingua_Press::get_lrp_instance();
			$this->url_converter = $lrp->get_component( 'url_converter' );
		}
		if ( $this->is_admin_request === null ) {
			$this->is_admin_request = $this->url_converter->is_admin_request();
		}

		// Do not process gettext strings on wp-login pages. Do not process strings in admin area except for when when is_ajax_on_frontend. Do not process gettext strings when is rest api from admin url referer. Do not process gettext on xmlrpc.pho
		return ( ( $pagenow != 'wp-login.php' ) && ( ! is_admin() || $this::is_ajax_on_frontend() ) && ! $this->is_admin_request && $pagenow != 'xmlrpc.php' );
	}

	public function call_gettext_filters( $prefix = '' ) {
		if ( $this->processing_gettext_is_needed() ) {
			add_filter( 'gettext', array(
				$this->process_gettext,
				$prefix . 'process_gettext_strings_no_context'
			), 100, 3 );
			add_filter( 'gettext_with_context', array(
				$this->process_gettext,
				$prefix . 'process_gettext_strings_with_context'
			), 100, 4 );
			add_filter( 'ngettext', array( $this->process_gettext, $prefix . 'process_ngettext_strings' ), 100, 5 );
			add_filter( 'ngettext_with_context', array(
				$this->process_gettext,
				$prefix . 'process_ngettext_strings_with_context'
			), 100, 6 );

			do_action( 'lrp_call_gettext_filters' );
		}
	}

	public function is_domain_loaded_in_locale( $domain, $locale ) {
		$localemo = $locale . '.mo';
		$length   = strlen( $localemo );

		global $l10n;
		if ( isset( $l10n[ $domain ] ) && is_object( $l10n[ $domain ] ) && method_exists( $l10n[ $domain ], 'get_filename' ) ) {
			$mo_filename = $l10n[ $domain ]->get_filename();

			if ( is_string($mo_filename) ) {

				// $mo_filename does not end with string $locale
				if ( substr( strtolower( $mo_filename ), -$length ) == strtolower( $localemo ) ) {
					return true;
				} else {
					return false;
				}
			}
			return true;
		}

		// if something is not as expected, return true so that we do not interfere
		return true;
	}

	public function verify_locale_of_loaded_textdomain() {
		global $l10n;
		if ( ! empty( $l10n ) && is_array( $l10n ) ) {

			$reload_domains = array();
			$locale         = get_locale();


			foreach ( $l10n as $domain => $item ) {
				if ( ! $this->is_domain_loaded_in_locale( $domain, $locale ) ) {
					$reload_domains[] = $domain;
				}
			}

			foreach ( $reload_domains as $domain ) {
				if ( isset( $l10n[ $domain ] ) && is_object( $l10n[ $domain ] ) ) {
					$path     = $l10n[ $domain ]->get_filename();
					$new_path = preg_replace( '/' . $domain . '-(.*).mo$/i', $domain . '-' . $locale . '.mo', $path );
					if ( $new_path !== $path ) {
						unset( $l10n[ $domain ] );
						load_textdomain( $domain, $new_path );
					}
				}
			}
		}

		// do this function only once per execution. The init hook can be called more than once
		remove_action( 'lrp_call_gettext_filters', array( $this, 'verify_locale_of_loaded_textdomain' ) );
	}

	/**
	 * Function that determines if an ajax request came from the frontend
	 * @return bool
	 */
	static function is_ajax_on_frontend() {

		/* for our own actions return false */
		if ( isset( $_REQUEST['action'] ) && strpos( sanitize_text_field( $_REQUEST['action'] ), 'lrp_' ) === 0 ) {
			return false;
		}

		$lrp           = LRP_Lingua_Press::get_lrp_instance();
		$url_converter = $lrp->get_component( "url_converter" );

		//check here for wp ajax or woocommerce ajax
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX ) ) {
			$referer = '';
			if (!empty( $_REQUEST['_wp_http_referer'])){
				// USUALLY this one is actually REQUEST_URI from the previous page. It's set by the wp_nonce_field() and wp_referer_field()
				// wp_get_referer() returns $_SERVER['REQUEST_URI'] from the prev page (not a full URL)
                // HOWEVER, the _wp_http_referer can be manually set by a plugin, so it can be a FULL URL in some cases
				$referer = wp_unslash( esc_url_raw( $_REQUEST['_wp_http_referer'] ) );
			} elseif (!empty($_SERVER['HTTP_REFERER'])) {
				// this one is an actual URL that the browser sets.
				$referer = wp_unslash( esc_url_raw( $_SERVER['HTTP_REFERER'] ) );
			}

			//if the request did not come from the admin set proper variables for the request (being processed in ajax they got lost) and return true
            // Remove the absolute home prefix from the referer and admin URL
            $referer_uri    = lrp_remove_prefix($url_converter->get_abs_home(), $referer);
            $admin_uri      = lrp_remove_prefix($url_converter->get_abs_home(), admin_url());
            if(!(strpos(trim($referer_uri, '/\\'), trim($admin_uri, '/\\')) === 0)) {
                LRP_Gettext_Manager::set_vars_in_frontend_ajax_request( $referer );
				return true;
			}
		}

		return false;
	}

	/**
	 * Function that sets the needed vars in the ajax request. Beeing ajax the globals got reset and also the REQUEST globals
	 *
	 * @param $referer
	 */
	static function set_vars_in_frontend_ajax_request( $referer ) {

		/* for our own actions don't do nothing */
		if ( isset( $_REQUEST['action'] ) && strpos( sanitize_text_field( $_REQUEST['action'] ), 'lrp_' ) === 0 ) {
			return;
		}

		/* if the request came from preview mode make sure to keep it */
		if ( strpos( $referer, 'lrp-edit-translation=preview' ) !== false && ! isset( $_REQUEST['lrp-edit-translation'] ) ) {
			$_REQUEST['lrp-edit-translation'] = 'preview';
		}

		if ( strpos( $referer, 'lrp-edit-translation=preview' ) !== false && strpos( $referer, 'lrp-view-as=' ) !== false && strpos( $referer, 'lrp-view-as-nonce=' ) !== false ) {
			$parts = parse_url( $referer );
			parse_str( $parts['query'], $query );
			$_REQUEST['lrp-view-as']       = $query['lrp-view-as'];
			$_REQUEST['lrp-view-as-nonce'] = $query['lrp-view-as-nonce'];
		}

		global $LRP_LANGUAGE;
		$lrp           = LRP_Lingua_Press::get_lrp_instance();
		$url_converter = $lrp->get_component( 'url_converter' );
		$LRP_LANGUAGE  = $url_converter->get_lang_from_url_string( $referer );
		if ( empty( $LRP_LANGUAGE ) ) {
			$settings_obj = new LRP_Settings();
			$settings     = $settings_obj->get_settings();
			$LRP_LANGUAGE = $settings["default-language"];
		}
	}


	/**
	 * function that machine translates gettext strings
	 */
	public function machine_translate_gettext() {
		/* @todo  set the original language to detect and also decide if we automatically translate for the default language */
		global $LRP_LANGUAGE, $lrp_gettext_strings_for_machine_translation;
		if ( ! empty( $lrp_gettext_strings_for_machine_translation ) ) {
			if ( ! $this->machine_translator ) {
				$lrp                      = LRP_Lingua_Press::get_lrp_instance();
				$this->machine_translator = $lrp->get_component( 'machine_translator' );
			}

			// Gettext strings are considered by default to be in the English language
			$source_language = apply_filters( 'lrp_gettext_source_language', 'en_US', $LRP_LANGUAGE, array(), $lrp_gettext_strings_for_machine_translation );
			// machine translate new strings
			if ( $this->machine_translator->is_available( array( $source_language, $LRP_LANGUAGE ) ) ) {

				/* Transform associative array into ordered numeric array. We need to keep keys numeric and ordered because $new_strings and $machine_strings depend on it.
				 * Array was constructed as associative with db ids as keys to avoid duplication.
				 */
				$lrp_gettext_strings_for_machine_translation = array_values( $lrp_gettext_strings_for_machine_translation );

				$new_strings = array();
				foreach ( $lrp_gettext_strings_for_machine_translation as $lrp_gettext_string_for_machine_translation ) {
					$new_strings[] = ( $lrp_gettext_string_for_machine_translation['original_plural'] && (int)$lrp_gettext_string_for_machine_translation['plural_form'] > 0 ) ? $lrp_gettext_string_for_machine_translation['original_plural'] : $lrp_gettext_string_for_machine_translation['original'];
				}

				if ( apply_filters( 'lrp_gettext_allow_machine_translation', true, $source_language, $LRP_LANGUAGE, $new_strings, $lrp_gettext_strings_for_machine_translation ) ) {
					$machine_strings = $this->machine_translator->translate( $new_strings, $LRP_LANGUAGE, $source_language );
				} else {
					$machine_strings = apply_filters( 'lrp_gettext_machine_translate_strings', array(), $new_strings, $LRP_LANGUAGE, $lrp_gettext_strings_for_machine_translation );
				}

				if ( ! empty( $machine_strings ) ) {
					foreach ( $new_strings as $key => $new_string ) {
						if ( isset( $machine_strings[ $new_string ] ) ) {
							$lrp_gettext_strings_for_machine_translation[ $key ]['translated'] = $machine_strings[ $new_string ];
						}
					}

					if ( ! $this->lrp_query ) {
						$lrp             = LRP_Lingua_Press::get_lrp_instance();
						$this->lrp_query = $lrp->get_component( 'query' );
					}
					$gettext_insert_update = $this->lrp_query->get_query_component( 'gettext_insert_update' );
					$gettext_insert_update->update_gettext_strings( $lrp_gettext_strings_for_machine_translation, $LRP_LANGUAGE );
				}
			}
		}
	}


	/**
	 * make sure we remove the lrp-gettext wrap from the format the date_i18n receives
	 * ideally if in the gettext filter we would know 100% that a string is a valid date format then we would not wrap it but it seems that it is not easy to determine that ( explore further in the future $d = DateTime::createFromFormat('Y', date('y a') method); )
	 */
	public function handle_date_i18n_function_for_gettext( $j, $dateformatstring, $unixtimestamp, $gmt ) {

		/* remove lrp-gettext wrap */
		$dateformatstring = preg_replace( '/#!lrpst#lrp-gettext (.*?)#!lrpen#/i', '', $dateformatstring );
		$dateformatstring = preg_replace( '/#!lrpst#(.?)\/lrp-gettext#!lrpen#/i', '', $dateformatstring );


		global $wp_locale;
		$i = $unixtimestamp;

		if ( false === $i ) {
			$i = current_time( 'timestamp', $gmt );
		}

		if ( ( ! empty( $wp_locale->month ) ) && ( ! empty( $wp_locale->weekday ) ) ) {
			$datemonth            = $wp_locale->get_month( date( 'm', $i ) );
			$datemonth_abbrev     = $wp_locale->get_month_abbrev( $datemonth );
			$dateweekday          = $wp_locale->get_weekday( date( 'w', $i ) );
			$dateweekday_abbrev   = $wp_locale->get_weekday_abbrev( $dateweekday );
			$datemeridiem         = $wp_locale->get_meridiem( date( 'a', $i ) );
			$datemeridiem_capital = $wp_locale->get_meridiem( date( 'A', $i ) );
			$dateformatstring     = ' ' . $dateformatstring;
			$dateformatstring     = preg_replace( "/([^\\\])D/", "\\1" . backslashit( $dateweekday_abbrev ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])F/", "\\1" . backslashit( $datemonth ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])l/", "\\1" . backslashit( $dateweekday ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])M/", "\\1" . backslashit( $datemonth_abbrev ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])a/", "\\1" . backslashit( $datemeridiem ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])A/", "\\1" . backslashit( $datemeridiem_capital ), $dateformatstring );

			$dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) - 1 );
		}
		$timezone_formats    = array( 'P', 'I', 'O', 'T', 'Z', 'e' );
		$timezone_formats_re = implode( '|', $timezone_formats );
		if ( preg_match( "/$timezone_formats_re/", $dateformatstring ) ) {
			$timezone_string = get_option( 'timezone_string' );
			if ( $timezone_string ) {
				$timezone_object = timezone_open( $timezone_string );
                //date_create( null, $timezone_object );
                //date_create() passing null to parameter #1 ($datetime) of type string is deprecated, from what I found online the null should be replaced with ''
				$date_object     = date_create( '', $timezone_object );
				foreach ( $timezone_formats as $timezone_format ) {
					if ( false !== strpos( $dateformatstring, $timezone_format ) ) {
						$formatted        = date_format( $date_object, $timezone_format );
						$dateformatstring = ' ' . $dateformatstring;
						$dateformatstring = preg_replace( "/([^\\\])$timezone_format/", "\\1" . backslashit( $formatted ), $dateformatstring );
						$dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) - 1 );
					}
				}
			}
		}
		$j = @date( $dateformatstring, $i );

		return $j;

	}

	/**
	 * Strip gettext tags from urls that were parsed by esc_url
	 *
	 * Esc_url() replaces spaces with %20. This is why it is not automatically stripped like the rest of the urls.
	 *
	 * @param $good_protocol_url
	 * @param $original_url
	 * @param $_context
	 *
	 * @return mixed
	 * @since 1.3.8
	 *
	 */
	public function lrp_strip_gettext_tags_from_esc_url( $good_protocol_url, $original_url, $_context ) {
		if ( strpos( $good_protocol_url, '%20data-lrpgettextoriginal=' ) !== false ) {
			// first replace %20 with space  so that gettext tags can be stripped.
			$good_protocol_url = str_replace( '%20data-lrpgettextoriginal=', ' data-lrpgettextoriginal=', $good_protocol_url );
			$good_protocol_url = LRP_Gettext_Manager::strip_gettext_tags( $good_protocol_url );
		}

		return $good_protocol_url;
	}

	/**
	 * Filter sanitize_title() to use our own remove_accents() function so it's based on the default language, not current locale.
	 *
	 * Also removes lrp gettext tags before running the filter because it strip # and ! and / making it impossible to strip the #lrpst later
	 *
	 * @param string $title
	 * @param string $raw_title
	 * @param string $context
	 *
	 * @return string
	 * @since 1.3.1
	 *
	 */
	public function lrp_sanitize_title( $title, $raw_title, $context ) {
		// remove lrp_tags before sanitization, because otherwise some characters (#,!,/, spaces ) are stripped later, and it becomes impossible to strip lrp-gettext later
		$raw_title = LRP_Gettext_Manager::strip_gettext_tags( $raw_title );

		if ( 'save' == $context ) {
			$title = lrp_remove_accents( $raw_title );
		}

		remove_filter( 'sanitize_title', array( $this, 'lrp_sanitize_title' ), 1 );
		$title = apply_filters( 'sanitize_title', $title, $raw_title, $context );
		add_filter( 'sanitize_title', array( $this, 'lrp_sanitize_title' ), 1, 3 );

		return $title;
	}


	/**
	 * function that strips the gettext tags from a string
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	static function strip_gettext_tags( $string ) {
		if ( is_string( $string ) && strpos( $string, 'data-lrpgettextoriginal=' ) !== false ) {
			// final 'i' is for case insensitive. same for the 'i' in  str_ireplace
			$string = preg_replace( '/ data-lrpgettextoriginal=\d+#!lrpen#/i', '', $string );
			$string = preg_replace( '/data-lrpgettextoriginal=\d+#!lrpen#/i', '', $string );//sometimes it can be without space
			$string = str_ireplace( '#!lrpst#lrp-gettext', '', $string );
			$string = str_ireplace( '#!lrpst#/lrp-gettext', '', $string );
			$string = str_ireplace( '#!lrpst#\/lrp-gettext', '', $string );
			$string = str_ireplace( '#!lrpen#', '', $string );
		}


		return $string;
	}


	/**
	 * Function that inserts in db translation from language files for specified original string ids for a specific language
	 * This requests changes locale from the very beginning so all the active plugins/theme load their textdomain translations
	 *
	 * Also creates plural entries for all plural forms so we have an id
	 *
	 * @param $dictionary
	 * @param $language
	 *
	 * @return void
	 */
	public function add_missing_language_file_translations( $dictionary, $language ) {

		$lrp_plural_forms    = $this->get_gettext_component( 'plural_forms' );
		if ( ! $this->lrp_query ) {
			$lrp             = LRP_Lingua_Press::get_lrp_instance();
			$this->lrp_query = $lrp->get_component( 'query' );
		}
		$insert_gettext_strings = array();
		$update_gettext_strings = array();

		$number_of_plural_forms = $lrp_plural_forms->get_number_of_plural_forms( $language );
		if ( ! empty( $dictionary ) ) {
			foreach ( $dictionary as $current_key => $current_string ) {

				$translations = get_translations_for_domain( $current_string['domain'] );
				$context      = ( $current_string['context'] === 'lrp_context' ) ? null : $current_string['context'];
				$translated = '';
				if ( $current_string['original_plural'] ) {

                    /* For some domains in some languages, $translations object is not of type Translations
                     * (but of type WP_Translations) on WP version 6.5+. So it doesn't have this method.
                     * Todo: find an alternative to access plural forms for these cases
                     */
                    if ( !method_exists( $translations, 'translate_entry' ) ) {
                        continue;
                    }

					// Insert translation for all other plural forms than the current one
					for ( $plural_form_i = 0; $plural_form_i < $number_of_plural_forms; $plural_form_i ++ ) {
						if ( $plural_form_i == $current_string['plural_form'] ) {
							continue;
						}
						$translation_exists_for_plural_form = false;
						$plural_form_id_translation_table   = null;
						foreach ( $dictionary as $secondary_key => $secondary_string ) {
							if ( $secondary_key == $current_key ) {
								continue;
							}
							if ( $current_string['ot_id'] === $secondary_string['ot_id'] &&
							     $secondary_string['plural_form'] == $plural_form_i
							) {
								if ( $secondary_string['status'] == 0 ) {
									$plural_form_id_translation_table = $secondary_string['id'];
								} else {
									$translation_exists_for_plural_form = true;
								}
								break;
							}
						}
						if ( ! $translation_exists_for_plural_form ) {
							$translated = $lrp_plural_forms->translate_plural( $current_string['original'], $current_string['original_plural'], $plural_form_i, $context, $translations );

							if ( $translated && $translated != $current_string['original'] && $translated != $current_string['original_plural'] ) {
								$status = 2;
							}else {
								$translated = '';
								$status = 0;
							}
							if ( $plural_form_id_translation_table ) {
								if ( $translated ) {
									$update_gettext_strings[] = array(
										'id'         => $plural_form_id_translation_table,
										'translated' => $translated
									);
								}
							} else {
								$insert_gettext_strings[] = array(
									'original_id'     => $current_string['ot_id'],
									'original'        => $current_string['original'],
									'translated'      => $translated,
									'domain'          => $current_string['domain'],
									'plural_form'     => $plural_form_i,
									'status'          => $status,
									'context'         => $current_string['context'],
									'original_plural' => $current_string['original_plural']
								);
							}
						}

					}

					// Insert translation for this current string
					if ( $current_string['status'] == 0 ) {
						$translated = $lrp_plural_forms->translate_plural( $current_string['original'], $current_string['original_plural'], (int) $current_string['plural_form'], $context, $translations );
					}
				} else {
					if ( $current_string['status'] == 0 && empty( $current_string['translated'] ) ) {
						$translated = $translations->translate( $current_string['original'] );
					}
				}
				if ( $current_string['status'] == 0 && empty( $current_string['translated'] ) ) {
					if ( $translated && $translated != $current_string['original'] && $translated != $current_string['original_plural'] ) {
						$status = 2;
					} else {
						$translated = '';
						$status     = 0;
					}

					if ( $current_string['id'] ) {
						if ( $translated ) {
							$update_gettext_strings[] = array(
								'id'         => $current_string['id'],
								'translated' => $translated,
								'status'     => 2
							);
						}
					} else {
						$insert_gettext_strings[] = array(
							'original_id'     => $current_string['ot_id'],
							'original'        => $current_string['original'],
							'translated'      => $translated,
							'domain'          => $current_string['domain'],
							'plural_form'     => (int) $current_string['plural_form'],
							'status'          => $status,
							'context'         => $current_string['context'],
							'original_plural' => $current_string['original_plural']
						);
					}
				}

			}
			$gettext_insert_update = $this->lrp_query->get_query_component( 'gettext_insert_update' );
			$gettext_insert_update->insert_gettext_strings($insert_gettext_strings, $language);
			$gettext_insert_update->update_gettext_strings($update_gettext_strings, $language, array('translated', 'id', 'status'));
		}
	}
}
