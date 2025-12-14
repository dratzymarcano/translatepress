<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'lrp_machine_translation_engines', 'lrp_mtapi_add_engine', 10 );
function lrp_mtapi_add_engine( $engines ){
	$engines[] = array( 'value' => 'mtapi', 'label' => __( 'LinguaPress AI', 'linguapress' ) );
	return $engines;
}

add_action( 'lrp_machine_translation_extra_settings_middle', 'lrp_mtapi_add_settings' );
function lrp_mtapi_add_settings( $mt_settings ){
    require_once("class-mtapi-customer.php");
    //$lrp = LRP_Lingua_Press::get_lrp_instance();

	$license = get_option('lrp_license_key');
	$status = get_option('lrp_license_status');
	$details = get_option('lrp_license_details');

    if (!isset($details['valid'][0])) $status = false;

    $linguapress_version_name = (defined('LINGUA_PRESS')) ? LINGUA_PRESS : 'LinguaPress';

    //dd($status);
	//dd(array($license, $status, $details));
    if ($status === false) : ?>

    <div class="lrp-get-free-license__container"
        <?php if ($linguapress_version_name !== 'LinguaPress') echo 'style="background: #F6F7F7"'; ?>>
        <div class="lrp-engine lrp-automatic-translation-engine__container" id="mtapi">
            <span class="lrp-primary-text-bold">
                <img src="<?php echo esc_url(LRP_PLUGIN_URL.'assets/images/'); ?>ai-icon.svg" width="24" height="24"/>
                LinguaPress AI <?php //this is not localized by choice ?>
            </span>

            <div class="lrp-automatic-translation-license-notice__wrapper">
                <svg class="lrp-no-license-automatic-translation__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18 10C18 5.58 14.42 2 10 2C5.58 2 2 5.58 2 10C2 14.42 5.58 18 10 18C14.42 18 18 14.42 18 10ZM12 10L15 13L13 15L10 12L7 15L5 13L8 10L5 7L7 5L10 8L13 5L15 7L12 10Z" fill="#9CA1A8"/>
                </svg>

                <span id="lrp-mtapi-key" class="lrp-primary-text lrp-settings-error-text">
                    <?php esc_html_e('No Active License Detected for this website.', 'linguapress'); ?>
                </span>
            </div>
<?php if ($linguapress_version_name == 'LinguaPress') :
            ?>
            <span class="lrp-secondary-text lrp-get-free-license-text">
                <?php esc_html_e('In order to enable Automatic Translation using LinguaPress AI, you need a license key by creating a free account.', 'linguapress'); ?>
            </span>
<?php endif;?>
            <div class="lrp-automatic-translation-get-license-buttons">
<?php if ( $linguapress_version_name == 'LinguaPress' ) : ?>
                <a href="<?php echo esc_url( 'https://linguapress.com/ai-free/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettingsAT&utm_campaign=tpaifree' ) ?>" class="lrp-get-free-license-link lrp-get-free-license-button button-primary" target="_blank" id="lrp-enter-license-button">
                    <?php esc_html_e( 'Create your Free Account', 'linguapress' ); ?>
                </a>

                <span class="lrp-secondary-text lrp-text-auto"><?php esc_html_e(' or ', 'linguapress'); ?></span>
<?php endif;?>
                <a href="<?php echo esc_url( admin_url('admin.php?page=lrp_license_key') ) ?>" class="lrp-enter-license-link lrp-get-free-license-button lrp-button-secondary" id="lrp-enter-license-button">
                        <?php esc_html_e( 'Enter your license key', 'linguapress' ); ?>
                </a>

            </div>
        </div>

        <?php if ( $linguapress_version_name == 'LinguaPress' ) : ?>
        <div class="lrp-automatic-translation-engine__upsale" id="tpai-upsale">
            <span class="lrp-primary-text-bold">
                <?php esc_html_e('Your free account includes: ', 'linguapress'); ?>
            </span>

            <span class="lrp-secondary-text lrp-check-text">
                <img src="<?php echo esc_url(LRP_PLUGIN_URL.'assets/images/'); ?>green-circle-check.png" width="20px" height="20px"/>
                <?php esc_html_e('Access to LinguaPress AI for instant automatic translations', 'linguapress'); ?>
            </span>

            <span class="lrp-secondary-text lrp-check-text">
                <img src="<?php echo esc_url(LRP_PLUGIN_URL.'assets/images/'); ?>green-circle-check.png" width="20px" height="20px"/>
                <?php esc_html_e('2000 AI words to translate automatically', 'linguapress'); ?>
            </span>

            <div class="lrp-upsale-fill" id="<?php echo esc_html( $linguapress_version_name )  ?>" style="display: none;">
                <span class="lrp-primary-text lrp-upsale-text-red">
                   <?php esc_html_e("Get more AI Tokens and unlock all AI features with LinguaPress Pro.", "linguapress"); ?>
                        <a href="https://linguapress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettingsAT&utm_campaign=tpaifree" id="lrp-upgrade-link" target="_blank">
                            <span class="lrp-upsale-text-link">
                                <span><?php esc_html_e("Upgrade now", "linguapress"); ?></span>
                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 7.3252L7 17.3252M17 7.3252H8M17 7.3252V16.3252" stroke="#354052" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </span>
                        </a>
                </span>

            </div>
        </div>
                <?php endif; ?>
    </div>
    <?php endif;

	if ($status === 'valid') :

        $product_name = '<strong>' . str_replace('+', ' ', $details['valid'][0]->item_name) . '</strong>';

        // MTAPI_URL needs to be defined in wp-config.php for local host development
        $mtapi_url = (defined('MTAPI_URL')  ? MTAPI_URL : 'https://mtapi.linguapress.com' );

        $mtapi_server = new LRP_MTAPI_Customer($mtapi_url);
		$site_status = $mtapi_server->lookup_site($license, home_url());

        $site_status['quota'] = isset ( $site_status['quota'] ) ? $site_status['quota'] : 0;

        set_transient("lrp_mtapi_cached_quota", $site_status['quota'], 5*60);

        $quota       = ($site_status['quota'] < 500) ? 0 : ceil($site_status['quota'] / 5 );

        // this $total_quota is not correct due to quota_used should account for ALL websites added to this license.
        // however, in case the site does have a user defined limit, the quota_used is correct.
        // without further changes to mtapi we don't have a proper way of knowing what's the quota_used.
        // will hide total_quota and let progress bar in place as the approximation is good enough
    if ( !isset( $site_status['quota_used'])){
        $site_status['quota_used'] = 0;
    }
        $total_quota = ceil( ( $site_status['quota'] + $site_status['quota_used'] ) / 5 );

        $formatted_quota = number_format( $quota );
        $formatted_total_quota  = number_format( $total_quota );

        $usage_percentage = ($total_quota > 0) ? ($quota / $total_quota) * 100 : 0;
    ?>
    <div class="lrp-engine lrp-automatic-translation-engine__container" id="mtapi">
        <span class="lrp-primary-text-bold">
            <img src="<?php echo esc_url(LRP_PLUGIN_URL.'assets/images/'); ?>ai-icon.svg" width="24" height="24"/>
            LinguaPress AI <?php //this is not localized by choice ?>
        </span>

        <div class="lrp-automatic-translation-license-notice__wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 3.33989C18.5083 4.21075 19.7629 5.46042 20.6398 6.96519C21.5167 8.46997 21.9854 10.1777 21.9994 11.9192C22.0135 13.6608 21.5725 15.3758 20.72 16.8946C19.8676 18.4133 18.6332 19.6831 17.1392 20.5782C15.6452 21.4733 13.9434 21.9627 12.2021 21.998C10.4608 22.0332 8.74055 21.6131 7.21155 20.7791C5.68256 19.9452 4.39787 18.7264 3.48467 17.2434C2.57146 15.7604 2.06141 14.0646 2.005 12.3239L2 11.9999L2.005 11.6759C2.061 9.94888 2.56355 8.26585 3.46364 6.79089C4.36373 5.31592 5.63065 4.09934 7.14089 3.25977C8.65113 2.42021 10.3531 1.98629 12.081 2.00033C13.8089 2.01437 15.5036 2.47589 17 3.33989ZM15.707 9.29289C15.5348 9.12072 15.3057 9.01729 15.0627 9.002C14.8197 8.98672 14.5794 9.06064 14.387 9.20989L14.293 9.29289L11 12.5849L9.707 11.2929L9.613 11.2099C9.42058 11.0607 9.18037 10.9869 8.9374 11.0022C8.69444 11.0176 8.46541 11.121 8.29326 11.2932C8.12112 11.4653 8.01768 11.6943 8.00235 11.9373C7.98702 12.1803 8.06086 12.4205 8.21 12.6129L8.293 12.7069L10.293 14.7069L10.387 14.7899C10.5624 14.926 10.778 14.9998 11 14.9998C11.222 14.9998 11.4376 14.926 11.613 14.7899L11.707 14.7069L15.707 10.7069L15.79 10.6129C15.9393 10.4205 16.0132 10.1802 15.9979 9.93721C15.9826 9.69419 15.8792 9.46509 15.707 9.29289Z" fill="#4AB067"/>
            </svg>

            <span id="lrp-mtapi-key" class="lrp-primary-text"><?php
                printf(wp_kses(__('You have a valid %s <strong>license</strong>.', 'linguapress'), array( 'strong' => array() ) ),
                    wp_kses( $product_name, array( 'strong' => array() ) )
                ); ?>
            </span>
        </div>

        <span class="lrp-secondary-text">
            <?php echo "<strong>" . esc_html( $formatted_quota ) . "</strong>" .  esc_html__( ' words remaining. ', 'linguapress' ); ?>

            <?php if ( isset( $site_status['exception'][0]['message'] ) && $site_status['exception'][0]['message'] == "Site not found." ) : ?>
                <span id="lrp-refresh-tpai">
                    <span id="lrp-refresh-tpai-dashicon" class="dashicons dashicons-controls-repeat"></span>
                    <span id="lrp-refresh-tpai-text-recheck" class="lrp-primary-text">
                        <?php esc_html_e( 'Recheck', 'linguapress' ); ?>
                    </span>
                </span>
                <span id="lrp-refresh-tpai-text-rechecking "  class="lrp-primary-text" style="display:none">
                    <?php esc_html_e( 'Rechecking...', 'linguapress' ); ?>
                </span>
                <span id="lrp-refresh-tpai-text-done" class="lrp-primary-text" style="display:none">
                    <?php esc_html_e( 'Done.', 'linguapress' ); ?>
                </span>
            <?php endif; ?>
        </span>

        <div class="lrp-quota-bar">
            <div class="lrp-quota-progress" style="width: <?php echo esc_attr( $usage_percentage ); ?>%;"></div>
        </div>

        <span class="lrp-secondary-text">
            <?php
                printf(
                    esc_html__( 'Manage your license & quota on the %s', 'linguapress' ),
                    '<a href="' . esc_url( 'https://linguapress.com/account/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettingsAT&utm_campaign=tp-ai') . '" target="_blank" class="lrp-settings-link"> '. esc_html__('LinguaPress.com Account Page', 'linguapress') . '</a>'
                );
            ?>
        </span>
    </div>

        <div class="lrp-upsale-fill lrp-upsale-fill-active-license" id="<?php echo esc_html( $linguapress_version_name )?>" style=" display: none " >
                <span class="lrp-primary-text lrp-upsale-text-red">
                   <?php esc_html_e("Get more AI Tokens and unlock all AI features with LinguaPress Pro.", "linguapress"); ?>
                        <a href="https://linguapress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettingsAT&utm_campaign=tpaifree" id="lrp-upgrade-link" target="_blank">
                            <span class="lrp-upsale-text-link">
                                <span><?php esc_html_e("Upgrade now", "linguapress"); ?></span>
                            <svg width="20" height="20" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 7.3252L7 17.3252M17 7.3252H8M17 7.3252V16.3252" stroke="#354052" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </span>
                        </a>
                </span>

        </div>
	<?php
	endif;
}

/**
 * Store url
 *
 * In order of priority MTAPI_STORE_URL, tpcom.ddev.site, linguapress.com
 *
 * @return string
 */
function lrp_mtapi_get_store_url() {
    $store_url = ( !isset( $store_url ) ) ? ( ( defined( 'MTAPI_STORE_URL' ) ) ? MTAPI_STORE_URL : null ) : $store_url;
    $store_url = ( !isset( $store_url ) ) ? ( ( defined( 'MTAPI_URL' ) && MTAPI_URL == 'https://mtapi.ddev.site' ) ? 'https://tpcom.ddev.site' : null ) : $store_url;
    return ( !isset( $store_url ) ) ? "https://linguapress.com" : $store_url;
}

/**
 * Make sure linguapress.com syncs with MTAPI for this license and this site
 *
 * Performed when saving Automatic Translation tab settings
 */
add_filter( 'lrp_machine_translation_sanitize_settings', 'lrp_mtapi_sync_license', 10, 2 );
function lrp_mtapi_sync_license( $settings, $mt_settings ) {
    if ( isset ( $_POST['option_page'] ) &&
        $_POST['option_page'] === 'lrp_machine_translation_settings' &&
        current_user_can( apply_filters( 'lrp_translating_capability', 'manage_options' ) ) &&
        $settings['translation-engine'] === 'mtapi' )
    {
        $license = get_option( 'lrp_license_key' );
        $status  = get_option( 'lrp_license_status' );

        if ( $status === 'valid' ) {
            lrp_mtapi_sync_license_call( $license );
        }
    }

    return $settings;
}

/**
 * Make linguapress.com sync with MTAPI for this license and this site
 */
function lrp_mtapi_sync_license_call( $license_key ) {
    $lrp = LRP_Lingua_Press::get_lrp_instance();

    if ( !empty( $lrp->tp_product_name ) ) {

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'sync_mtapi_license',
            'license'    => $license_key,
            'url'        => home_url(),
            'version'    => LRP_PLUGIN_VERSION
        );
        $store_url  = lrp_mtapi_get_store_url();
        // Call the custom API.
        $response = wp_remote_post( $store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
        return $response;
    }
    return false;
}

add_action('wp_ajax_lrp_ai_recheck_quota','lrp_ai_recheck_quota');
function lrp_ai_recheck_quota(){
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'lrp_translating_capability', 'manage_options' ) ) ) {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'lrp_ai_recheck_quota' ) {
            $nonce_okay = check_ajax_referer( 'lrp-tpai-recheck', 'nonce' );
            if ( $nonce_okay ){
                $license = get_option( 'lrp_license_key' );
                $status  = get_option( 'lrp_license_status' );

                if ( $status === 'valid' ) {
                    $response = lrp_mtapi_sync_license_call( $license );
                    if ( is_array( $response ) && ! is_wp_error( $response ) && isset( $response['response'] ) &&
                        isset( $response['response']['code']) && $response['response']['code'] == 200 ) {

                        $mtapi_url = (defined('MTAPI_URL')  ? MTAPI_URL : 'https://mtapi.linguapress.com' );

                        require_once("class-mtapi-customer.php");
                        $mtapi_server = new LRP_MTAPI_Customer($mtapi_url);
                        $site_status = $mtapi_server->lookup_site($license, home_url());

                        $site_status['quota'] = isset ( $site_status['quota'] ) ? $site_status['quota'] : 0;
                        $quota = intval(ceil($site_status['quota'] / 5));
                        echo lrp_safe_json_encode( ['quota' => $quota ] ); //phpcs:ignore
                    }
                }
            }

        }
    }
    wp_die();
}