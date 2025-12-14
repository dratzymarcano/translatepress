<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_machine_translation_engines', 'lrp_openrouter_add_engine', 10 );
function lrp_openrouter_add_engine( $engines ){
    $engines[] = array( 'value' => 'openrouter', 'label' => __( 'OpenRouter', 'linguapress' ) );

    return $engines;
}
add_action( 'lrp_machine_translation_extra_settings_middle', 'lrp_openrouter_add_settings' );

function lrp_openrouter_add_settings( $mt_settings ){
    $lrp                = LRP_Lingua_Press::get_lrp_instance();
    $machine_translator = $lrp->get_component( 'machine_translator' );

    $translation_engine = isset( $mt_settings['translation-engine'] ) ? $mt_settings['translation-engine'] : '';
    $api_key = isset( $mt_settings['openrouter-api-key'] ) ? $mt_settings['openrouter-api-key'] : '';

    // Check for API errors only if $translation_engine is OpenRouter.
    if ( 'openrouter' === $translation_engine ) {
        // $api_check = $machine_translator->check_api_key_validity();
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
    if ( $show_errors && 'openrouter' === $translation_engine ) {
        $text_input_classes[] = 'lrp-text-input-error';
    }
    ?>

    <div class="lrp-engine lrp-automatic-translation-engine__container" id="openrouter">
        <span class="lrp-primary-text-bold"><?php esc_html_e( 'OpenRouter API Key', 'linguapress' ); ?> </span>

        <div class="lrp-automatic-translation-api-key-container">
            <input type="text" id="lrp-openrouter-api-key" placeholder="<?php esc_html_e( 'Add your API Key here...', 'linguapress' ); ?>" class="<?php echo esc_html( implode( ' ', $text_input_classes ) ); ?>" name="lrp_machine_translation_settings[openrouter-api-key]" value="<?php if( !empty( $mt_settings['openrouter-api-key'] ) ) echo esc_attr( $mt_settings['openrouter-api-key']);?>"/>
            <?php
            // Only show errors if OpenRouter is active.
            if ( 'openrouter' === $translation_engine && function_exists( 'lrp_output_svg' ) ) {
                $machine_translator->automatic_translation_svg_output( $show_errors );
            }
            ?>
        </div>
        
        <br>
        <span class="lrp-primary-text-bold"><?php esc_html_e( 'System Prompt (Context & Tone)', 'linguapress' ); ?> </span>
        <p class="lrp-description-text"><?php esc_html_e( 'Instruct the AI on how to translate (e.g., "You are a professional translator. Use a formal tone.").', 'linguapress' ); ?></p>
        <textarea id="lrp-openrouter-system-prompt" class="lrp-textarea" name="lrp_machine_translation_settings[openrouter-system-prompt]" rows="3" style="width: 100%;"><?php if( !empty( $mt_settings['openrouter-system-prompt'] ) ) echo esc_textarea( $mt_settings['openrouter-system-prompt']);?></textarea>

        <br><br>
        <span class="lrp-primary-text-bold"><?php esc_html_e( 'Glossary / Do Not Translate', 'linguapress' ); ?> </span>
        <p class="lrp-description-text"><?php esc_html_e( 'Enter words or phrases that should NOT be translated (comma separated). E.g., "BrandName, ProductX".', 'linguapress' ); ?></p>
        <textarea id="lrp-openrouter-glossary" class="lrp-textarea" name="lrp_machine_translation_settings[openrouter-glossary]" rows="2" style="width: 100%;"><?php if( !empty( $mt_settings['openrouter-glossary'] ) ) echo esc_textarea( $mt_settings['openrouter-glossary']);?></textarea>



        <?php
        if ( $show_errors && 'openrouter' === $translation_engine ) {
            ?>
            <span class="lrp-error-inline lrp-settings-error-text">
                <?php echo wp_kses_post( $error_message ); ?>
            </span>
            <?php
        }
        ?>

        <span class="lrp-description-text">
            <?php echo wp_kses( __( 'Enter your OpenRouter API Key.', 'linguapress' ), [ 'a' => [ 'href' => [], 'title' => [], 'target' => [] ], 'strong' => [] ] ); ?>
        </span>
    </div>

    <?php
}

add_filter( 'lrp_machine_translation_sanitize_settings', 'lrp_openrouter_sanitize_settings' );
function lrp_openrouter_sanitize_settings( $mt_settings ){
    if( !empty( $mt_settings['openrouter-api-key'] ) )
        $mt_settings['openrouter-api-key'] = sanitize_text_field( $mt_settings['openrouter-api-key']  );

    return $mt_settings;
}
