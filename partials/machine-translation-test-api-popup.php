<div class="lrp-test-api-key-popup-overlay">
    <div class="lrp-loading-spinner"></div>
    <div class="lrp-test-api-key-popup">
        <div class="lrp-test-api-key__header">
            <h3 class="lrp-settings-primary-heading"><?php esc_html_e( 'Test API Credentials', 'linguapress' );?></h3>
            <div class="lrp-test-api-key-close-btn">
                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.5 2L1.5 15M1.5 2L14.5 15" stroke="#1D2327" stroke-width="2.16667" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="lrp-test-api-key-referrer__wrapper">
            <div class="lrp-test-api-key-referrer">
                <span class="lrp-settings-secondary-heading"><?php esc_html_e( 'HTTP Referrer: ', 'linguapress' ); ?></span>
                <span class="lrp-settings-secondary-heading lrp-referrer-name"></span>
            </div>
            <span class="lrp-primary-text"><?php esc_html_e( 'Use this HTTP Referrer if the API lets you restrict key usage from its Dashboard.', 'linguapress' );?></span>
        </div>

        <div class="lrp-test-api-key-response lrp-test-api-key-response__wrapper">
            <span class="lrp-settings-secondary-heading"><?php esc_html_e( 'Response', 'linguapress' );?></span>
            <div class="lrp-settings-container"></div>
        </div>

        <div class="lrp-test-api-key-response-body lrp-test-api-key-response__wrapper">
            <span class="lrp-settings-secondary-heading"><?php esc_html_e( 'Response Body', 'linguapress' );?></span>
            <div class="lrp-settings-container"></div>
        </div>

        <div class="lrp-test-api-key-response-full lrp-test-api-key-response__wrapper">
            <span class="lrp-settings-secondary-heading"><?php esc_html_e( 'Entire Response From wp_remote_get():', 'linguapress' );?></span>
            <div class="lrp-settings-container"></div>
        </div>

    </div>
</div>