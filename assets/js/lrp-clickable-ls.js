jQuery('.lrp_language_switcher_shortcode .lrp-ls-shortcode-current-language').click(function () {
    jQuery( '.lrp_language_switcher_shortcode .lrp-ls-shortcode-current-language' ).addClass('lrp-ls-clicked');
    jQuery( '.lrp_language_switcher_shortcode .lrp-ls-shortcode-language' ).addClass('lrp-ls-clicked');
});

jQuery('.lrp_language_switcher_shortcode .lrp-ls-shortcode-language').click(function () {
    jQuery( '.lrp_language_switcher_shortcode .lrp-ls-shortcode-current-language' ).removeClass('lrp-ls-clicked');
    jQuery( '.lrp_language_switcher_shortcode .lrp-ls-shortcode-language' ).removeClass('lrp-ls-clicked');
});

jQuery(document).keyup(function(e) {
    if (e.key === "Escape") {
        jQuery( '.lrp_language_switcher_shortcode .lrp-ls-shortcode-current-language' ).removeClass('lrp-ls-clicked');
        jQuery( '.lrp_language_switcher_shortcode .lrp-ls-shortcode-language' ).removeClass('lrp-ls-clicked');
    }
});

jQuery(document).on("click", function(event){
    if(!jQuery(event.target).closest(".lrp_language_switcher_shortcode .lrp-ls-shortcode-current-language").length){
        jQuery( '.lrp_language_switcher_shortcode .lrp-ls-shortcode-current-language' ).removeClass('lrp-ls-clicked');
        jQuery( '.lrp_language_switcher_shortcode .lrp-ls-shortcode-language' ).removeClass('lrp-ls-clicked');
    }
});