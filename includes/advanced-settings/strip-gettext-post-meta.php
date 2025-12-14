<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_register_strip_gettext_post_meta', 70 );
function lrp_register_strip_gettext_post_meta( $settings_array ){
	$settings_array[] = array(
		'name'          => 'strip_gettext_post_meta',
		'type'          => 'checkbox',
		'label'         => esc_html__( 'Filter Gettext wrapping from post meta', 'linguapress' ),
		'description'   => wp_kses( __( 'Filters gettext wrapping such as #!lrpst#lrp-gettext from all updated post meta. Does not affect previous post meta. <br/><strong>Database backup is recommended before switching on.</strong>', 'linguapress' ), array( 'br' => array(), 'strong' => array()) ),
        'id'            => 'troubleshooting',
        'container'     => 'troubleshooting'
    );
	return $settings_array;
}

/**
 * Stripped gettext wrapping from wp_update_post_meta
 */
add_action( 'added_post_meta', 'lrp_filter_lrpgettext_from_updated_post_meta', 10, 4);
add_action( 'updated_postmeta', 'lrp_filter_lrpgettext_from_updated_post_meta', 10, 4);
function lrp_filter_lrpgettext_from_updated_post_meta($meta_id, $object_id, $meta_key, $meta_value){
	$option = get_option( 'lrp_advanced_settings', true );
	if ( isset( $option['strip_gettext_post_meta'] ) && $option['strip_gettext_post_meta'] === 'yes' && class_exists( 'LRP_Translation_Manager' ) ){
		if ( is_serialized($meta_value) ){
			$unserialized_meta_value = unserialize($meta_value);
			$stripped_meta_value = lrp_strip_gettext_array( $unserialized_meta_value );
			$stripped_meta_value = serialize( $stripped_meta_value );
		}else{
			$stripped_meta_value = lrp_strip_gettext_array( $meta_value );
		}

		if ( $stripped_meta_value != $meta_value){
			remove_action('updated_postmeta','lrp_filter_lrpgettext_from_updated_post_meta' );
			update_post_meta( $object_id, $meta_key, $stripped_meta_value );
			add_action( 'updated_postmeta', 'lrp_filter_lrpgettext_from_updated_post_meta', 10, 4);
		}
	}
}

function lrp_strip_gettext_array( $value ){
	if ( is_array( $value ) ){
		foreach( $value as $key => $item ){
			$value[$key] = lrp_strip_gettext_array( $item );
		}
		return $value;
	}else{
		return LRP_Translation_Manager::strip_gettext_tags( $value );
	}
}
