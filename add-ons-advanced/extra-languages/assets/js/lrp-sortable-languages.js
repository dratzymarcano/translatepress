function LRP_IN_Sortable_Languages() {
    var _this = this;
    var duplicate_url_error_message;
    var iso_codes;

    this.remove_language = function( element ){
        var message = jQuery( element.target ).attr( 'data-confirm-message' );
        var confirmed = confirm( message );
        if ( confirmed ) {
            jQuery ( element.target ).parent().parent().remove();
        }
    };

    this.get_default_url_slug = function( new_language ){
        var return_slug = iso_codes[new_language];
        var url_slugs = _this.get_existing_url_slugs();
        url_slugs.push( return_slug );
        if ( has_duplicates ( url_slugs ) ){
            return_slug = new_language;
        }
        return return_slug.toLowerCase();
    };

    this.add_language = function(){
        var selected_language = jQuery( '#lrp-select-language' );
        var new_language = selected_language.val();
        if ( new_language == "" ){
            return;
        }

        selected_language.val( '' ).trigger( 'change' );

        var new_option = jQuery( '.lrp-language' ).first().clone();
        new_option = jQuery( new_option );

        new_option.find( '.lrp-hidden-default-language' ).remove();
        new_option.find( '.select2-container' ).remove();
        var select = new_option.find( 'select.lrp-translation-language' );
        select.removeAttr( 'disabled' );
        select.find( 'option' ).each(function(index, el){
            el.text = el.text.replace('Default: ', '');
        })

        select.val( new_language );
        select.select2();

        var checkbox = new_option.find( 'input.lrp-translation-published' );
        checkbox.removeAttr( 'disabled' );
        checkbox.val( new_language );

        var url_slug = new_option.find( 'input.lrp-language-slug' );
        url_slug.val( _this.get_default_url_slug( new_language ) );
        url_slug.attr('name', 'lrp_settings[url-slugs][' + new_language + ']' );

        var language_code = new_option.find( 'input.lrp-language-code' );
        language_code.val( new_language);

        var remove = new_option.find( '.lrp-remove-language' ).toggle();

        new_option = jQuery( '#lrp-sortable-languages' ).append( new_option );
        new_option.find( '.lrp-remove-language' ).last().click( _this.remove_language );
    };

    this.update_default_language = function(){
        var selected_language = jQuery( '#lrp-default-language').val();
        jQuery( '.lrp-hidden-default-language' ).val( selected_language );
        jQuery( '.lrp-translation-published[disabled]' ).val( selected_language );
        jQuery( '.lrp-translation-language[disabled]').val( selected_language ).trigger( 'change' );
    };

    function has_duplicates(array) {
        var valuesSoFar = Object.create(null);
        for (var i = 0; i < array.length; ++i) {
            var value = array[i];
            if (value in valuesSoFar) {
                return true;
            }
            valuesSoFar[value] = true;
        }
        return false;
    }

    this.get_existing_url_slugs = function(){
        var url_slugs = [];
        jQuery( '.lrp-language-slug' ).each( function (){
            url_slugs.push( jQuery( this ).val().toLowerCase() );
        } );
        return url_slugs;
    };

    this.check_unique_url_slugs = function (event){
        var url_slugs = _this.get_existing_url_slugs();
        if ( has_duplicates(url_slugs)){
            alert( duplicate_url_error_message );
            event.preventDefault();
        }
    };

    this.update_url_slug_and_status = function ( event ) {
        var select = jQuery( event.target );
        var new_language = select.val();
        var row = jQuery( select ).parents( '.lrp-language' ) ;
        row.find( '.lrp-language-slug' ).attr( 'name', 'lrp_settings[url-slugs][' + new_language + ']').val( '' ).val( _this.get_default_url_slug( new_language ) );
        row.find( '.lrp-language-code' ).val( '' ).val( new_language );
        row.find( '.lrp-translation-published' ).val( new_language );
    };

    this.initialize = function () {
        duplicate_url_error_message = lrp_url_slugs_info['error_message_duplicate_slugs'];
        iso_codes = lrp_url_slugs_info['iso_codes'];

        jQuery( '#lrp-sortable-languages' ).sortable({ handle: '.lrp-sortable-handle' });
        jQuery( '#lrp-add-language' ).click( _this.add_language );
        jQuery( '.lrp-remove-language' ).click( _this.remove_language );
        jQuery( '#lrp-default-language' ).on( 'change', _this.update_default_language );
        jQuery( "form[action='options.php']").on ( 'submit', _this.check_unique_url_slugs );
        jQuery( '#lrp-languages-table' ).on( 'change', '.lrp-translation-language', _this.update_url_slug_and_status );
    };

    this.initialize();
}

var lrpSortableLanguages;

// Initialize the Translate Press Settings after jQuery is ready
jQuery( function() {
    lrpSortableLanguages = new LRP_IN_Sortable_Languages();
});
