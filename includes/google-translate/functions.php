<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_machine_translation_engines', 'lrp_gt_add_engine', 10 );
function lrp_gt_add_engine( $engines ){
    $engines[] = array( 'value' => 'google_translate_v2', 'label' => __( 'Google Translate v2', 'linguapress' ) );

    return $engines;
}
add_action( 'lrp_machine_translation_extra_settings_middle', 'lrp_gt_add_settings' );

function lrp_gt_add_settings( $mt_settings ){
    $lrp                = LRP_Lingua_Press::get_lrp_instance();
    $machine_translator = $lrp->get_component( 'machine_translator' );

    $translation_engine = isset( $mt_settings['translation-engine'] ) ? $mt_settings['translation-engine'] : '';
    $api_key = isset( $mt_settings['google-translate-key'] ) ? $mt_settings['google-translate-key'] : '';

    // Check for API errors only if $translation_engine is Google.
    if ( 'google_translate_v2' === $translation_engine ) {
        $api_check = $machine_translator->check_api_key_validity();

    }

    // Check for errors.
    $error_message = '';
    $show_errors   = false;
    if ( isset( $api_check ) && true === $api_check['error'] ) {
        $error_message = $api_check['message'];
        $show_errors    = true;
    }

    $text_input_classes = array(
        'lrp-text-input',
    );
    if ( $show_errors && 'google_translate_v2' === $translation_engine ) {
        $text_input_classes[] = 'lrp-text-input-error';
    }
    ?>

    <div class="lrp-engine lrp-automatic-translation-engine__container" id="google_translate_v2">
        <span class="lrp-primary-text-bold"><?php esc_html_e( 'Google Translate API Key', 'linguapress' ); ?> </span>

        <div class="lrp-automatic-translation-api-key-container">
            <input type="text" id="lrp-g-translate-key" placeholder="<?php esc_html_e( 'Add your API Key here...', 'linguapress' ); ?>" class="<?php echo esc_html( implode( ' ', $text_input_classes ) ); ?>" name="lrp_machine_translation_settings[google-translate-key]" value="<?php if( !empty( $mt_settings['google-translate-key'] ) ) echo esc_attr( $mt_settings['google-translate-key']);?>"/>
            <?php
            // Only show errors if Google Translate is active.
            if ( 'google_translate_v2' === $translation_engine && function_exists( 'lrp_output_svg' ) ) {
                $machine_translator->automatic_translation_svg_output( $show_errors );
            }
            ?>
        </div>

        <?php
        if ( $show_errors && 'google_translate_v2' === $translation_engine ) {
            ?>
            <span class="lrp-error-inline lrp-settings-error-text">
                <?php echo wp_kses_post( $error_message ); ?>
            </span>
            <?php
        }
        ?>

        <span class="lrp-description-text">
            <?php echo wp_kses( __( 'Visit <a href="https://cloud.google.com/docs/authentication/api-keys" target="_blank">this link</a> to see how you can set up an API key, <strong>control API costs</strong> and set HTTP referrer restrictions.', 'linguapress' ), [ 'a' => [ 'href' => [], 'title' => [], 'target' => [] ], 'strong' => [] ] ); ?>
            <br><?php echo esc_html( sprintf( __( 'Your HTTP referrer is: %s', 'linguapress' ), $machine_translator->get_referer() ) ); ?>
        </span>
    </div>

    <?php
}

add_filter( 'lrp_machine_translation_sanitize_settings', 'lrp_gt_sanitize_settings' );
function lrp_gt_sanitize_settings( $mt_settings ){
    if( !empty( $mt_settings['google-translate-key'] ) )
        $mt_settings['google-translate-key'] = sanitize_text_field( $mt_settings['google-translate-key']  );

    return $mt_settings;
}

/**
 * Returns an appropriate error/success message for the Google Translate access.
 *
 * @param int $code The code returned by Google Translate access.
 *
 * @return array [ (string) $message, (bool) $error ].
 */
function lrp_gt_response_codes( $code ) {
    $is_error       = false;
    $code           = intval( $code );
    $return_message = '';

    /**
     * Determine if we have a 4xx or 5xx error.
     *
     * @see https://cloud.google.com/apis/design/errors
     */
    if ( preg_match( '/4\d\d/', $code ) ) {
        $is_error = true;
        $return_message = esc_html__( 'There was an error with your Google Translate key.', 'linguapress' );
    } elseif ( preg_match( '/5\d\d/', $code ) ) {
        $is_error = true;
        $return_message = esc_html__( 'There was an error on the server processing your Google Translate key.', 'linguapress' );
    }
    
    return array(
        'message' => $return_message,
        'error'   => $is_error,
    );
}
