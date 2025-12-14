<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function lrp_render_blocks( $block_content, $block ) {
    $block_attrs = $block['attrs']['LrpContentRestriction'] ?? null;

    // Abort if the block does not have the content restriction settings attribute
    if ( !isset( $block_attrs ) || empty( $block_attrs['selected_languages'] ) )
        return $block_content;

    global $LRP_LANGUAGE;

    $lrp             = LRP_Lingua_Press::get_lrp_instance();
    $languagesObject = $lrp->get_component( 'languages' );
    $settings        = ( $lrp->get_component( 'settings' ) )->get_settings();

    $published_languages = $languagesObject->get_language_names( $settings['publish-languages'] );

    $current_language_name = $published_languages[$LRP_LANGUAGE];

    $should_exclude_block = $block_attrs['restriction_type'] === 'include' && !in_array( $current_language_name, $block_attrs['selected_languages'] )
                            || $block_attrs['restriction_type'] === 'exclude' && in_array( $current_language_name, $block_attrs['selected_languages'] );

    if ( $should_exclude_block ) return '';

    return $block_content;
}
add_filter( 'render_block', 'lrp_render_blocks', 10, 2 );


/**
 * Adds the `lrpContentRestriction` attribute to all blocks
 */
add_action( 'wp_loaded', 'lrp_add_custom_attributes_to_blocks', 199 );
function lrp_add_custom_attributes_to_blocks() {
	$registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

	foreach( $registered_blocks as $name => $block ) {
		$block->attributes['LrpContentRestriction'] = [
			'type'    => 'object',
            'properties' => [
                'restriction_type' => [
                    'type' => 'string',
                ],
                'selected_languages' => [
                    'type' => 'array',
                ],
                'panel_open' => [
                    'type' => 'boolean',
                ],
            ],
			'default' => [
                'restriction_type'   => 'exclude',
                'selected_languages' => [],
                'panel_open'         => true,
            ],
		];
	}

}

