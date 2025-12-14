jQuery( function(){

    jQuery( 'input[value="lrp_nbol_all_languages"]' ).click( function(){
        if( jQuery( this ).is(':checked') ) {
            jQuery( '.lrp-nbol-lang-input', jQuery( this ).parent().parent() ).prop('readonly', 'readonly');
        }
        else{
            jQuery( '.lrp-nbol-lang-input', jQuery( this ).parent().parent() ).removeAttr('readonly');
        }
    });

    jQuery( '.lrp-nbol-lang-input' ).click( function(){
        if( jQuery(this).prop('readonly') ) {
            allLangInput = jQuery('input[value="lrp_nbol_all_languages"]', jQuery(this).parent().parent());
            if (allLangInput.is(':checked')) {
                allLangInput.trigger('click');
            }
        }
    });

});