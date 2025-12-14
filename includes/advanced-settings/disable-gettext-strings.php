<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_register_advanced_settings', 'lrp_translation_for_gettext_strings', 523 );
function lrp_translation_for_gettext_strings( $settings_array ){
    $settings_array[] = array(
        'name'          => 'disable_translation_for_gettext_strings',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Disable translation for gettext strings', 'linguapress' ),
        'description'   => wp_kses( __( 'Gettext Strings are strings outputted by themes and plugins. <br> Translating these types of strings through LinguaPress can be unnecessary if they are already translated using the .po/.mo translation file system.<br>Enabling this option can improve the page load performance of your site in certain cases. The disadvantage is that you can no longer edit gettext translations using LinguaPress, nor benefit from automatic translation on these strings.', 'linguapress' ), array( 'br' => array()) ),
        'id'            => 'debug',
        'container'     => 'debug'
        );
    return $settings_array;
}

add_action( 'lrp_before_running_hooks', 'lrp_remove_hooks_to_disable_gettext_translation', 10, 1);
function lrp_remove_hooks_to_disable_gettext_translation( $lrp_loader ){
    $option = get_option( 'lrp_advanced_settings', true );
    if ( isset( $option['disable_translation_for_gettext_strings'] ) && $option['disable_translation_for_gettext_strings'] === 'yes' ) {
        $lrp             = LRP_Lingua_Press::get_lrp_instance();
        $gettext_manager = $lrp->get_component( 'gettext_manager' );
        $lrp_loader->remove_hook( 'init', 'create_gettext_translated_global', $gettext_manager );
        $lrp_loader->remove_hook( 'shutdown', 'machine_translate_gettext', $gettext_manager );
    }
}

add_filter( 'lrp_skip_gettext_querying', 'lrp_skip_gettext_querying', 10, 4 );
function lrp_skip_gettext_querying( $skip, $translation, $text, $domain ){
    $option = get_option( 'lrp_advanced_settings', true );
    if ( isset( $option['disable_translation_for_gettext_strings'] ) && $option['disable_translation_for_gettext_strings'] === 'yes' ) {
        return true;
    }
    return $skip;
}



add_action( 'lrp_editor_notices', 'display_message_for_disable_gettext_in_editor', 10, 1 );
function display_message_for_disable_gettext_in_editor( $lrp_editor_notices ) {
    $option = get_option( 'lrp_advanced_settings', true );

    // Skip if user dismissed it
    if ( get_user_meta( get_current_user_id(), '_lrp_dismissed_gettext_notice', true ) ) {
        return $lrp_editor_notices;
    }

    if ( isset( $option['disable_translation_for_gettext_strings'] ) && $option['disable_translation_for_gettext_strings'] === 'yes' ) {
        $url = add_query_arg( array(
            'page' => 'lrp_advanced_page#debug_options',
        ), site_url('wp-admin/admin.php') );

        $ajax_url = admin_url( 'admin-ajax.php' );

        $html  = "<div id='lrp-gettext-notice' class='lrp-notice lrp-notice-warning'>";

        $html .= '<p><strong>' . esc_html__( 'Gettext Strings translation is disabled', 'linguapress' ) . '</strong></p>';
        $html .= '<p>' . esc_html__( 'To enable it go to ', 'linguapress' ) .
            '<a class="lrp-link-primary" target="_blank" href="' . esc_url( $url ) . '">' .
            esc_html__( 'LinguaPress->Advanced Settings->Debug->Disable translation for gettext strings', 'linguapress' ) .
            '</a>' . esc_html__(' and uncheck the Checkbox.', 'linguapress') .'</p>';

        // Custom dismiss link
        $html .= '<a href="#" id="lrp-dismiss-gettext-notice" class="lrp-button-primary">'. esc_html__('Dismiss', 'linguapress') .'</a>';

        // Inline JS with ajax URL hardcoded
        $html .= "<script>
            document.addEventListener('DOMContentLoaded', function(){
                var btn = document.getElementById('lrp-dismiss-gettext-notice');
                if (btn) {
                    btn.addEventListener('click', function(e){
                        e.preventDefault();
                        var notice = document.getElementById('lrp-gettext-notice');
                        if (notice) notice.style.display = 'none';
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '" . esc_url( $ajax_url ) . "', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.send('action=lrp_dismiss_gettext_notice');
                    });
                }
            });
        </script>";

        $html .= '</div>';

        $lrp_editor_notices = $html;
    }

    return $lrp_editor_notices;
}

// Handle AJAX dismiss
add_action( 'wp_ajax_lrp_dismiss_gettext_notice', function() {
    if ( current_user_can( 'edit_posts' ) ) {
        update_user_meta( get_current_user_id(), '_lrp_dismissed_gettext_notice', true );
    }
    wp_die();
});
