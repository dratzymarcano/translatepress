<?php

if ( !defined('ABSPATH' ) )
    exit();

$current_language_preference = $this->add_shortcode_preferences($shortcode_settings, $current_language['code'], $current_language['name']);

?>
<div class="lrp_language_switcher_shortcode">
<div class="lrp-language-switcher lrp-language-switcher-container" data-no-translation <?php echo ( isset( $_GET['lrp-edit-translation'] ) && $_GET['lrp-edit-translation'] == 'preview' ) ? 'data-lrp-unpreviewable="lrp-unpreviewable"' : '' ?>>
    <div class="lrp-ls-shortcode-current-language">
        <a href="#" class="lrp-ls-shortcode-disabled-language lrp-ls-disabled-language" title="<?php echo esc_attr( $current_language['name'] ); ?>" onclick="event.preventDefault()">
			<?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
		</a>
    </div>
    <div class="lrp-ls-shortcode-language">
        <?php if ( apply_filters('lrp_ls_shortcode_show_disabled_language', true, $current_language, $current_language_preference, $this->settings ) ){ ?>
        <a href="#" class="lrp-ls-shortcode-disabled-language lrp-ls-disabled-language"  title="<?php echo esc_attr( $current_language['name'] ); ?>" onclick="event.preventDefault()">
			<?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
		</a>
        <?php } ?>
    <?php foreach ( $other_languages as $code => $name ){

        $language_preference = $this->add_shortcode_preferences($shortcode_settings, $code, $name);
        ?>
        <a href="<?php echo (isset($is_editor) && $is_editor) ? '#' : esc_url( $this->url_converter->get_url_for_language($code, false) ); /* phpcs:ignore */ /* $is_editor is not outputted */ ?>" title="<?php echo esc_attr( $name ); ?>">
            <?php echo $language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
        </a>

    <?php } ?>
    </div>
    <script type="application/javascript">
        // need to have the same with set from JS on both divs. Otherwise it can push stuff around in HTML
        var lrp_ls_shortcodes = document.querySelectorAll('.lrp_language_switcher_shortcode .lrp-language-switcher');
        if ( lrp_ls_shortcodes.length > 0) {
            // get the last language switcher added
            var lrp_el = lrp_ls_shortcodes[lrp_ls_shortcodes.length - 1];

            var lrp_shortcode_language_item = lrp_el.querySelector( '.lrp-ls-shortcode-language' )
            // set width
            var lrp_ls_shortcode_width                                               = lrp_shortcode_language_item.offsetWidth + 16;
            lrp_shortcode_language_item.style.width                                  = lrp_ls_shortcode_width + 'px';
            lrp_el.querySelector( '.lrp-ls-shortcode-current-language' ).style.width = lrp_ls_shortcode_width + 'px';

            // We're putting this on display: none after we have its width.
            lrp_shortcode_language_item.style.display = 'none';
        }
    </script>
</div>
</div>