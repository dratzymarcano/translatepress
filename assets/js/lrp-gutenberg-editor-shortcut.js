jQuery( function () {
    function lrp_place_tp_button() {

        // check if gutenberg's editor root element is present.
        var editorEl = document.getElementById( 'editor' )
        if ( !editorEl ){ // do nothing if there's no gutenberg root element on page.
            return
        }

        var unsubscribe = wp.data.subscribe( function () {
            if ( !document.getElementById( "lrp-link-id" ) ){
                // Support the changes in UI in WordPress 6.5
                var toolbarLeftEl = editorEl.querySelector('.editor-document-tools__left')
                if ( !toolbarLeftEl ) { // Fallback to the legacy toolbar class for compatibility with older versions
                    toolbarLeftEl = editorEl.querySelector('.edit-post-header-toolbar__left')
                }

                if ( toolbarLeftEl instanceof HTMLElement ){
                    toolbarLeftEl.insertAdjacentHTML( "afterend", lrp_url_tp_editor[ 0 ] )
                }
            }
        } )
    }

    /**
     * There is no good way to trigger a function when block was loaded for the first time or when adding a new block
     * so this workaround was needed. Inline JS that fixes width is not working in Gutenberg Editor.
     * https://github.com/WordPress/gutenberg/issues/8379
     */
    function lrp_gutenberg_blocks_loaded(){
        if ( !lrp_localized['dont_adjust_width']){
            var blockLoadedInterval = setInterval( function () {
                var lrp_ls_shortcodes = document.querySelectorAll( ".lrp_language_switcher_shortcode .lrp-language-switcher:not(.lrp-set-width)" )
                if ( lrp_ls_shortcodes.length > 0 ){
                    lrp_adjust_shortcode_width( lrp_ls_shortcodes )
                }

            }, 500 );
        }
    }


    function lrp_adjust_shortcode_width(lrp_ls_shortcodes){
        for( var i = 0; i < lrp_ls_shortcodes.length; i++ ) {
            var lrp_el = lrp_ls_shortcodes[i];
            lrp_ls_shortcodes[i].classList.add("lrp-set-width")
            var lrp_shortcode_language_item                                          = lrp_el.querySelector( ".lrp-ls-shortcode-language" )
            // set width
            var lrp_ls_shortcode_width                                               = lrp_shortcode_language_item.offsetWidth + 16;
            lrp_shortcode_language_item.style.width                                  = lrp_ls_shortcode_width + "px";
            lrp_el.querySelector( ".lrp-ls-shortcode-current-language" ).style.width = lrp_ls_shortcode_width + "px";

            // We\'re putting this on display: none after we have its width.
            lrp_shortcode_language_item.style.display = "none";
        }
    }

    lrp_place_tp_button()
    lrp_gutenberg_blocks_loaded()

} )
