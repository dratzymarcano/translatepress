<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div class="lrp-settings-container lrp-settings-container-ald_settings">
    <h2 class="lrp-settings-primary-heading"><?php esc_html_e( 'User Language Detection Method', 'linguapress' ); ?></h2>
    <div class="lrp-settings-separator"></div>

    <div class='lrp-settings-options__wrapper'>
        <div class="lrp-radio__wrapper lrp-settings-options-item">
            <?php foreach ( $detection_methods as $value => $label ) : ?>
                <label class="lrp-primary-text">
                    <input type="radio" name="lrp_ald_settings[detection-method]" value="<?php echo esc_attr( $value ); ?>"
                        <?php checked( $ald_settings['detection-method'], $value ); ?>>
                    <?php echo esc_html( $label ); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <span class="lrp-description-text">
            <?php echo wp_kses_post( __( "Select how the language should be detected for first time visitors.<br>The visitor's last displayed language will be remembered through cookies." , 'linguapress' ) ); ?>
        </span>
        <?php if ( !empty( $ip_warning_message ) ) : ?>
            <div class="lrp-settings-warning"><?php echo $ip_warning_message;//phpcs:ignore  ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="lrp-settings-container lrp-settings-container-ald_settings">
    <h2 class="lrp-settings-primary-heading"><?php esc_html_e( 'User Notification Popup', 'linguapress' ); ?></h2>
    <div class="lrp-settings-separator"></div>

    <div class='lrp-settings-options__wrapper'>
        <span class="lrp-description-text">
            <?php echo esc_html__( "A popup appears asking the user if they want to be redirected." , 'linguapress' ); ?>
        </span>

        <div class="lrp-radio__wrapper lrp-settings-options-item">
            <span class="lrp-primary-text-bold"><?php esc_html_e( 'Popup Type', 'linguapress' ); ?></span>
            <?php foreach ( $popup_type as $value => $label ) : ?>
                <label class="lrp-primary-text">
                    <input type="radio" name="lrp_ald_settings[popup_type]" value="<?php echo esc_attr( $value ); ?>"
                        <?php checked( $ald_settings['popup_type'], $value ); ?>>
                    <?php echo esc_html( $label ); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="lrp-settings-options-item lrp-settings-options-item__column lrp-option__wrapper">
            <span class="lrp-primary-text-bold"><?php esc_html_e( 'Popup Text', 'linguapress' ); ?></span>
            <textarea class="lrp-textarea-small" name="lrp_ald_settings[popup_textarea]"><?php echo $setting_option['popup_textarea'] // phpcs:ignore?></textarea>

            <span class="lrp-description-text">
                <?php echo wp_kses_post( __( "The same text is displayed in all languages. <br>A selecting language switcher will be appended to the pop-up. The detected language is pre-selected." , 'linguapress' ) ); ?>
            </span>
        </div>

        <div class="lrp-settings-options-item lrp-settings-options-item__column lrp-option__wrapper">
            <span class="lrp-primary-text-bold"><?php esc_html_e( 'Button Text', 'linguapress' ); ?></span>
            <input type="text" id="lrp-popup-textarea_button" name="lrp_ald_settings[popup_textarea_button]" value="<?php echo $setting_option['popup_textarea_button'] // phpcs:ignore?>">

            <span class="lrp-description-text">
                <?php echo esc_html__( "Write the text you wish to appear on the button.." , 'linguapress' ); ?>
            </span>
        </div>

        <div class="lrp-settings-options-item lrp-settings-options-item__column lrp-option__wrapper">
            <span class="lrp-primary-text-bold"><?php esc_html_e( 'Close Button Text', 'linguapress' ); ?></span>
            <input type="text" id="lrp-popup-textarea_close_button" name="lrp_ald_settings[popup_textarea_close_button]" value="<?php echo $setting_option['popup_textarea_close_button'] // phpcs:ignore?>">

            <span class="lrp-description-text">
                <?php echo esc_html__( "Write the text you wish to appear on the close button. Leave empty for just the close button." , 'linguapress' ); ?>
            </span>
        </div>
    </div>
</div>