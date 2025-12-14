document.addEventListener("DOMContentLoaded", function(event) {
    function lrpClearWooCartFragments(){

        // clear WooCommerce cart fragments when switching language
        var lrp_language_switcher_urls = document.querySelectorAll(".lrp-language-switcher-container a:not(.lrp-ls-disabled-language)");

        for (i = 0; i < lrp_language_switcher_urls.length; i++) {
            lrp_language_switcher_urls[i].addEventListener("click", function(){
                if ( typeof wc_cart_fragments_params !== 'undefined' && typeof wc_cart_fragments_params.fragment_name !== 'undefined' ) {
                    window.sessionStorage.removeItem(wc_cart_fragments_params.fragment_name);
                }
            });
        }
    }

    lrpClearWooCartFragments();
});
