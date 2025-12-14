/*
 * Script used in Settings Page
 */

jQuery( function() {

    /**
     * Change the language selector and slugs
     */
    function LRP_Settings_Language_Selector() {
        var _this = this;
        var duplicate_url_error_message;
        var iso_codes;
        var error_handler;

        /**
         * Initialize select to become select2
         */
        this.initialize_select2 = function () {
            jQuery('.lrp-select2').each(function () {
                var select_element = jQuery(this);
                select_element.select2(/*arguments*/);
            });
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

        this.error_check = function( new_language ){
            error_handler.show_hide_warning( new_language, true );

            if ( error_handler.has_error === true ){
                return true;
            }

            error_handler.languages.push( new_language );

            return false;
        };

        this.add_language = function(){
            var selected_language = jQuery( '#lrp-select-language' );
            var new_language = selected_language.val();

            if ( new_language == "" ){
                return;
            }

            if ( jQuery( "#lrp-languages-table .lrp-language" ).length >= 2 && jQuery( '.lrp-language-selector-limited' ).length ){
                jQuery(".lrp-upsell-multiple-languages").show('fast');
                return;
            }

            if ( _this.error_check( new_language ) === true ){
                return;
            }

            selected_language.val( '' ).trigger( 'change' );

            var new_option = jQuery( '.lrp-language' ).first().clone();

            _this.supports_formality( new_language, new_option );

            error_handler.add_language_change_listener( new_option.find('.lrp-translation-language') );

            new_option = jQuery( new_option );

            new_option.find('.lrp-translation-language').on( 'change', _this.change_language );

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

            var remove = new_option.find( '.lrp-remove-language__container' ).toggle();

            new_option = jQuery( '#lrp-sortable-languages' ).append( new_option );
            new_option.find( '.lrp-remove-language__container' ).last().click( _this.remove_language );
        };

        this.change_language = function( event ){
            var new_language_element          = jQuery(event.target).closest( '.lrp-language' );
            var new_language_code             = jQuery(event.target).next().find('.select2-selection__rendered').attr('title');

            _this.supports_formality( new_language_code, new_language_element );
        }

        this.remove_language = function( element ){
            var message = jQuery( '.lrp-remove-language' ).attr( 'data-confirm-message' );
            var confirmed = confirm( message );

            if ( confirmed ) {
                let language_to_remove = jQuery( element.target ).closest( '.lrp-language' );

                let language_to_remove_code = language_to_remove.find('.lrp-language-code').val();

                // remove language from array
                error_handler.languages.splice( error_handler.languages.indexOf(language_to_remove_code), 1 ) ;

                language_to_remove.remove();

                error_handler.show_hide_warning( language_to_remove_code );
            }

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

        this.supports_formality = function( new_language_code, new_language_element ) {
            var languages_that_support_formality = lrp_url_slugs_info[ 'languages_that_support_formality' ];
            var formality_match                  = new_language_code.match( /formal|informal/ ) !== null ? new_language_code.match( /formal|informal/ )[ 0 ] : false; // check if the language is innately formal/informal e.g. de_DE_formal
            var formality_select_field           = new_language_element.find( '.lrp-translation-language-formality' );
            var stripped_formal_language         = error_handler.strip_formal_language( new_language_code );

            if ( formality_select_field.length === 0 ){
                return;
            }

            formality_select_field.removeClass( 'lrp-formality-disabled' ); // when a language is added,  the fields are cloned - which means that the select field could have the .lrp-formality-disabled class even if the language supports formality

            if ( stripped_formal_language && languages_that_support_formality[ stripped_formal_language ] === 'true' ){
                select_change( formality_match );
                return;
            }

            if ( !languages_that_support_formality?.[ new_language_code ] || languages_that_support_formality[ new_language_code ] === 'false' ){
                formality_select_field.addClass( 'lrp-formality-disabled' );
            }

            select_change( 'default' );

            function select_change( option_value ) {
                formality_select_field.find( 'option' ).each( function () {

                    if ( jQuery( this ).attr( 'value' ) === option_value ){
                        jQuery( this ).attr( 'selected', 'selected' );
                        return;
                    }

                    jQuery( this ).removeAttr( 'selected' );

                } );
            }
        }

        this.initialize = function () {
            this.initialize_select2();

            error_handler = new LRP_Error_handler();
            duplicate_url_error_message = lrp_url_slugs_info['error_message_duplicate_slugs'];
            iso_codes = lrp_url_slugs_info['iso_codes'];

            jQuery( '#lrp-sortable-languages' ).sortable({ handle: '.lrp-sortable-handle' });
            jQuery( '#lrp-add-language' ).click( _this.add_language );
            jQuery( '.lrp-col-remove-language .lrp-remove-language__container' ).click( _this.remove_language );
            jQuery( '#lrp-default-language' ).on( 'change', _this.update_default_language );
            jQuery( "form[action='options.php']").on ( 'submit', _this.check_unique_url_slugs );
            jQuery( '#lrp-languages-table' ).on( 'change', '.lrp-translation-language', _this.update_url_slug_and_status );
            jQuery('.lrp-language .lrp-select2').not( '#lrp-default-language' ).on( 'change', _this.change_language );
            jQuery( '.lrp-select2' ).on( 'select2:open', function(){
                document.querySelector( '.select2-search__field' ).focus();
            });
        };

        this.initialize();
    }

    /*
     * Manage adding and removing items from an option of tpe list from Advanced Settings page
     */
    function LRP_Advanced_Settings_List( table ){

        var _this = this

        this.addEventHandlers = function( table ){
            var add_list_entry = table.querySelector( '.lrp-add-list-entry' );

            // add event listener on ADD button
            add_list_entry.querySelector('.lrp-adst-button-add-new-item').addEventListener("click", _this.add_item );

            var removeButtons = table.querySelectorAll( '.lrp-adst-remove-element' );
            for( var i = 0 ; i < removeButtons.length ; i++ ) {
                removeButtons[i].addEventListener("click", _this.remove_item)
            }
        }

        this.remove_item = function( event ){
            if ( confirm( document.querySelector('.lrp-adst-remove-element-text').getAttribute( 'data-confirm-message' ) ) ){
                jQuery( event.target ).closest( '.lrp-list-entry' ).remove()
            }
        }

        this.add_item = function () {
            var add_list_entry = table.querySelector( '.lrp-add-list-entry' );
            var clone = add_list_entry.cloneNode(true)

            // Remove the lrp-add-list-entry class from the second element after it was cloned
            add_list_entry.classList.remove('lrp-add-list-entry');

            // Show Add button, hide Remove button
            add_list_entry.querySelector( '.lrp-adst-button-add-new-item' ).style.display = 'none'
            add_list_entry.querySelector( '.lrp-adst-remove-element' ).parentNode.style.display = 'block'

            // Design change to add the cloned element at the bottom of list
            // Done becasue the select box element cannot be cloned with its selected state
            var itemInserted =  add_list_entry.parentNode.insertBefore(clone, add_list_entry.nextSibling);

            // Set name attributes
            var dataNames = add_list_entry.querySelectorAll( '[data-name]' )
            for( var i = 0 ; i < dataNames.length ; i++ ) {
                dataNames[i].setAttribute( 'name', dataNames[i].getAttribute('data-name') );
            }

            var removeButtons = table.querySelectorAll( '.lrp-adst-remove-element' );
            for( var i = 0 ; i < removeButtons.length ; i++ ) {
                removeButtons[i].addEventListener("click", _this.remove_item)
            }

            // Reset values of textareas with new items
            var dataValues = clone.querySelectorAll( '[data-name]' )
            for( var i = 0 ; i < dataValues.length ; i++ ) {
                dataValues[i].value = ''
            }

            //Restore checkbox(es) values after cloning and clearing; alternative than excluding from reset
            var restoreCheckboxes = clone.querySelectorAll ( 'input[type=checkbox]' )
            for( var i = 0 ; i < restoreCheckboxes.length ; i++ ) {
                restoreCheckboxes[i].value = 'yes'
            }

            // Add click listener on new row's Add button
            var addButton = itemInserted.querySelector('.lrp-adst-button-add-new-item');
            addButton.addEventListener("click", _this.add_item );
        }

        _this.addEventHandlers( table )
    }
    var lrpSettingsLanguages = new LRP_Settings_Language_Selector();

    jQuery('#lrp-default-language').on("select2:selecting", function(e) {
        jQuery(".lrp-settings-warning").show('fast');
    });
    /*
     * Automatic Translation Page
     */

    // Hide API Fields Based on Selected Translation Engine
    jQuery('#lrp-translation-engines').on('change', function (){
        jQuery('.lrp-engine').hide();

        // backwards compatibility for when Paid version not updated. Deepl missing .lrp-engine and #deepl selectors in html
        jQuery("#lrp-deepl-api-type-pro").closest('tr').hide();
        jQuery("#lrp-deepl-key").closest('tr').hide();

        jQuery( '#lrp-test-api-key' ).show(); // initiate the default so we can hide for deepl_upsell
        if (jQuery(this).val() == 'deepl_upsell'){
            jQuery( '#lrp-test-api-key' ).hide()
        }

        if ( jQuery("#" + this.value).length > 0 ){
            jQuery("#" + this.value).show();
            LRP_check_visible_engine_for_upsale_mtapi( this.value );

        }else{
            // backwards compatibility for when Paid version not updated. Deepl missing .lrp-engine and #deepl selectors in html
            jQuery("#lrp-deepl-api-type-pro").closest('tr').show();
            jQuery("#lrp-deepl-key").closest('tr').show();
        }
    })



    // Used for the main machine translation toggle to show/hide all options below it
    function LRP_show_hide_machine_translation_options(){
        if( jQuery( '#lrp-machine-translation-enabled' ).val() != 'yes' )
            jQuery( '.lrp-machine-translation-options tbody tr:not(:first-child)').hide()
        else
            jQuery( '.lrp-machine-translation-options tbody tr:not(:first-child)').show()

        if( jQuery( '#lrp-machine-translation-enabled' ).val() == 'yes' )
            jQuery('.lrp-translation-engine:checked').trigger('change')
    }

    LRP_show_hide_machine_translation_options()
    jQuery('#lrp-machine-translation-enabled').on( 'change', function(){
        LRP_show_hide_machine_translation_options()
    })

    function LRP_test_API_key(){
        const testPopupOverlay = document.querySelector( '.lrp-test-api-key-popup-overlay');
        const testPopup        = document.querySelector( '.lrp-test-api-key-popup' );

        if ( !testPopup )
            return;

        const testApiBtn = document.querySelector( '#lrp-test-api-key button' );

        const closePopup = document.querySelector( '.lrp-test-api-key-close-btn' );

        closePopup.addEventListener( "click", function () {
            testPopupOverlay.style.visibility = "hidden";
            testPopup.style.visibility = "hidden";
        });

        testPopupOverlay.addEventListener( "click", function () {
            if ( testPopup.style.visibility === 'hidden' )
                return;

            testPopupOverlay.style.visibility = "hidden";
            testPopup.style.visibility = "hidden";
        });

        testPopup.addEventListener( "click", function (event) {
            event.stopPropagation();
        });

        if ( typeof testApiBtn !== 'undefined' && testApiBtn ) {

            testApiBtn.addEventListener("click", function () {
                testPopupOverlay.style.visibility = 'visible';

                jQuery('.lrp-loading-spinner').show();

                jQuery.ajax({
                    url: lrp_url_slugs_info['admin-ajax'],
                    type: 'POST',
                    data: {
                        action: 'test_api_key',
                        security: document.querySelector('#lrp_test_api_nonce_field').value
                    },
                    success: function (response) {
                        if (response.success) {
                            testPopup.style.visibility = 'visible';

                            populatePopup(response.data);
                        } else {
                            console.error("Error:", response.data.message);
                        }
                    },
                    error: function () {
                        console.error("AJAX request failed");
                    },
                    complete: function () {
                        jQuery('.lrp-loading-spinner').hide();
                    }
                });
            });
        }
        function populatePopup( response ){
            const popupResponse = testPopup.querySelector('.lrp-test-api-key-response .lrp-settings-container');
            const popupResponseBody = testPopup.querySelector('.lrp-test-api-key-response-body .lrp-settings-container');
            const popupResponseFull = testPopup.querySelector('.lrp-test-api-key-response-full .lrp-settings-container');

            popupResponse.textContent     = JSON.stringify( response.response.response );
            popupResponseBody.textContent = response.response.body;
            popupResponseFull.textContent = response.raw_response;
        }
    }
    LRP_test_API_key();

    function LRP_check_visible_engine_for_upsale_mtapi( selected_engine ) {

        jQuery('#tpai-upsale').hide();
        jQuery( '.lrp-upsale-fill' ).hide();

        if (selected_engine == 'mtapi' && jQuery('.lrp-upsale-fill').attr('id') === 'LinguaPress') {
            jQuery('#tpai-upsale').show();
            jQuery( '.lrp-upsale-fill' ).show();
        } else {
            jQuery('#tpai-upsale').hide();
            jQuery( '.lrp-upsale-fill' ).hide();

        }
    }


    // check quota for site on TP AI
    function LRP_TP_AI_Recheck(){
        jQuery( '#lrp-refresh-tpai-text-recheck').hide()
        jQuery( '#lrp-refresh-tpai-text-rechecking').show()
        jQuery.ajax({
                        url: lrp_url_slugs_info['admin-ajax'],
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'lrp_ai_recheck_quota',
                            nonce: lrp_url_slugs_info['lrp-tpai-recheck-nonce']
                        },
                        success: function (response) {
                            if( response.hasOwnProperty('quota') ){
                                jQuery( '#lrp-ai-quota-number').text( Number(response.quota).toLocaleString('en-US') )
                                if ( !response.quota ){
                                    jQuery( '#lrp-refresh-tpai-text-recheck').finish().delay(1700).fadeIn("fast")
                                }else{
                                    jQuery( '#lrp-refresh-tpai').finish().delay(1100).fadeOut("slow")
                                }
                            }
                            jQuery( '#lrp-refresh-tpai-text-done').finish().fadeIn("fast").delay(1000).fadeOut("slow")
                            jQuery( '#lrp-refresh-tpai-text-recheck').hide()
                            jQuery( '#lrp-refresh-tpai-text-rechecking').hide()

                            lrp_ai_recheck_in_progress = false
                        },
                        error: function (response) {
                            jQuery( '#lrp-refresh-tpai-text-done').finish().fadeIn("fast").delay(1000).fadeOut("slow");
                            jQuery( '#lrp-refresh-tpai-text-rechecking').hide()
                            jQuery( '#lrp-refresh-tpai-text-recheck').finish().delay(1700).fadeIn("fast")
                            lrp_ai_recheck_in_progress = false
                        }
                    })
    }

    // Enable target field only if checkbox is checked
    function lrp_enable_disable_conditional_field( targetField, checkboxField ){
        const checkbox = document.querySelector( checkboxField );
        const target = document.querySelector( targetField );

        if ( !checkbox )
            return;

        function toggleInput() {
            if (target.matches("input, select, textarea, button")) {
                target.disabled = !checkbox.checked;
            } else {
                const inputs = target.querySelectorAll("input, select, textarea, button");
                inputs.forEach(input => {
                    input.disabled = !checkbox.checked;
                })
            }
        }

        checkbox.addEventListener("change", toggleInput);
        toggleInput();
    }
    lrp_enable_disable_conditional_field( '#lrp-machine-translation-limit','#lrp-machine-translation-limit-toggle' );
    lrp_enable_disable_conditional_field( 'select[name="lrp_advanced_settings[enable_hreflang_xdefault]"]','#enable_hreflang_xdefault-checkbox' );
    lrp_enable_disable_conditional_field( 'div.lrp-input-array-rows__wrapper','#language_date_format' );


    lrp_ai_recheck_in_progress = false
    jQuery("#lrp-refresh-tpai").on('click', function(){
        if ( !lrp_ai_recheck_in_progress ){
            lrp_ai_recheck_in_progress = true
            LRP_TP_AI_Recheck()
        }
    })

    /*
    * END Automatic Translation Page
    */

    // Options of type List adding, from Advanced Settings page
    var lrpListOptions = document.querySelectorAll( '.lrp-adst-list-option' );
    for ( var i = 0 ; i < lrpListOptions.length ; i++ ){
        new LRP_Advanced_Settings_List( lrpListOptions[i] );
    }

});

//Advanced Settings Tabs
function LRP_Advanced_Settings_Tabs() {
    function init() {
        if (!window.location.search.includes('lrp_advanced_page')) return;

        jQuery('.lrp-settings-container').hide();

        let navItems = document.querySelectorAll(".lrp_advanced_tab_content_table_item");
        let containers = document.querySelectorAll(".lrp-settings-container");
        let settingsReferer = document.querySelector("#lrp_advanced_settings_referer"); // Hidden input field

        function getURLParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        function updateURLParameter(param, value) {
            let url = new URL(window.location.href);
            url.searchParams.set(param, value);
            window.history.replaceState(null, "", url.toString());
        }

        function showTargetContainer(targetClass) {
            containers.forEach(container => container.style.display = "none");

            let targetContainers = document.querySelectorAll(`.lrp-settings-container-${targetClass}`);
            if (targetContainers.length > 0) {
                targetContainers.forEach(container => container.style.display = "block");
            }

            document.querySelectorAll(".lrp_advanced_tab_content_table_item").forEach(el => {
                el.classList.remove("lrp-nav-active");
            });

            let activeNavItem = document.querySelector(`.lrp_advanced_tab_content_table_item a.${targetClass}`);
            if (activeNavItem) {
                activeNavItem.closest(".lrp_advanced_tab_content_table_item").classList.add("lrp-nav-active");
            }
        }

        navItems.forEach(item => {
            item.addEventListener("click", function (event) {
                event.preventDefault();

                let targetClass = this.querySelector("a").classList[0];

                updateURLParameter("tab", targetClass);
                if (settingsReferer) settingsReferer.value = targetClass; // Store tab in hidden input
                showTargetContainer(targetClass);
            });
        });

        // On page load, check for &tab= in URL or in the hidden input
        let activeTab = getURLParameter("tab") || (settingsReferer ? settingsReferer.value : null);

        if (activeTab) {
            showTargetContainer(activeTab);
        } else {
            let firstNavItem = navItems[0]?.querySelector("a");
            if (firstNavItem) {
                let firstTargetClass = firstNavItem.classList[0];
                updateURLParameter("tab", firstTargetClass);
                if (settingsReferer) settingsReferer.value = firstTargetClass; // Default tab stored in hidden input
                showTargetContainer(firstTargetClass);
            }
        }
    }

    return {
        init: init
    }
}

function LRP_Error_handler() {

    this.has_error = false;
    this.languages = [];
    let _this = this;
    let $error_container;
    let error_type;

    this.init = function () {
        $error_container = jQuery('.lrp-add-language-error-container');
        this.set_language_list();
        this.init_event_listeners();
    }

    this.set_language_list = function () {
        let language_nodes = document.querySelectorAll('.lrp-language .lrp-language-code');

        for (let i = 0; i < language_nodes.length; i++) {
            this.languages[i] = language_nodes[i].value;
        }

    }

    // If the language is formal / informal, returns it but stripped of the _informal or _formal parts
    // Returns false otherwise
    this.strip_formal_language = function (new_language_code) {
        let formality_map = {
            _informal: '',
            _formal: ''
        };

        if (new_language_code.includes('formal') || new_language_code.includes('informal')) {
            new_language_code = new_language_code.replace(/_formal|_informal/, function (matched) {
                return formality_map[matched];
            });

            return new_language_code;
        }

        return false;
    }

    this.has_formal_variant = function (new_language_code, languages_array) {

        for (let language of languages_array) {
            let stripped_formal_language = this.strip_formal_language(language); // false if is not a formal language

            if (stripped_formal_language && stripped_formal_language === new_language_code) {
                return true;
            }
        }

        return false;
    }

    this.set_error_type = function (new_language_code, is_new_language_added) {
        let languages_array = is_new_language_added ? [].concat(this.languages, new_language_code) : this.languages;

        if (languages_array.length !== new Set(languages_array).size) {
            error_type = "duplicates";
            return true;
        }

        for (let language_code of languages_array) {
            let stripped_formal_language = this.strip_formal_language(language_code);

            if (stripped_formal_language !== false && languages_array.includes(stripped_formal_language) || this.has_formal_variant(language_code, languages_array)) {
                error_type = "formality";
                return true;
            }
        }

        return false;
    }

    this.change_warning_text = function () {
        let error_container_text;

        switch (error_type) {
            case 'formality':
                error_container_text = lrp_url_slugs_info['error_message_formality'];
                break;

            case 'duplicates':
                error_container_text = lrp_url_slugs_info['error_message_duplicate_languages'];
                break;
        }

        $error_container.html(error_container_text);
    }

    // Displays the warning message with the relevant text in case there is an error
    // Or hides the warning message in case it was resolved
    this.show_hide_warning = function (new_language_code, is_new_language_added = false) {
        this.has_error = this.set_error_type(new_language_code, is_new_language_added);

        if (this.has_error !== false) {
            this.change_warning_text();
            $error_container.show('fast');
        }

        if (this.has_error === false && $error_container.is(':visible')) {
            $error_container.hide('fast');
        }

    }

    this.init_event_listeners = function () {
        let language_nodes = document.querySelectorAll('.lrp-language .lrp-select2');
        this.add_language_change_listener(language_nodes);
    }

    this.add_language_change_listener = function (nodes) {
        let $nodes = jQuery(nodes);

        $nodes.on('change', language_change);

        function language_change(event) {
            // .lrp-language-code is changed after the language changes so there is a small window in which we can get the old value
            let old_language_code = jQuery(event.target).closest('.lrp-language').find('.lrp-language-code').val();
            let new_language_code = jQuery(event.target).next().find('.select2-selection__rendered').attr('title');

            _this.languages[_this.languages.indexOf(old_language_code)] = new_language_code;

            _this.show_hide_warning(new_language_code);
        }
    }

    this.init();
}

// LRP Email Course
jQuery(document).ready(function (e) {
    // init advanced settings tabs
    var initialize = new LRP_Advanced_Settings_Tabs();
    initialize.init();

    jQuery('.lrp-email-course input[type="submit"]').on('click', function (e) {

        e.preventDefault()

        jQuery( '.lrp-email-course .lrp-email-course__error' ).removeClass( 'visible' )

        jQuery('.lrp-email-course input[type="submit"]').addClass( 'disabled' )

        var email = jQuery( '.lrp-email-course input[name="lrp_email_course_email"]').val()

        if ( !lrp_validateEmail( email ) ){
            jQuery( '.lrp-email-course .lrp-email-course__error' ).addClass( 'visible' )
            jQuery( '.lrp-email-course input[name="lrp_email_course_email"]' ).focus()

            return
        }

        if( email != '' ){

            jQuery( '.lrp-email-course input[type="submit"]' ).val( 'Working...' )

            var data = new FormData()
            data.append( 'email', email )

            var version = jQuery('.lrp-email-course input[name="lrp_installed_plugin_version"]').val()
            if ( version != '' )
                data.append( 'version', version )

            jQuery.ajax({
                            url: 'https://linguapress.com/wp-json/lrp-api/emailCourseSubscribe',
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            data: data,
                            success: function (response) {

                                if( response.message ){

                                    jQuery( '.lrp-email-course .lrp-email-course__message').text( response.message ).addClass( 'visible' ).addClass( 'success' )
                                    jQuery( '.lrp-email-course .lrp-email-course__form' ).hide()
                                    jQuery( '.lrp-email-course__footer' ).css( 'visibility', 'hidden' )

                                    lrp_dimiss_email_course()

                                }

                            },
                            error: function (response) {

                                jQuery('.lrp-email-course input[type="submit"]').val('Sign me up!')

                            }
                        })

        }
    })
})

function lrp_validateEmail(email) {

    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());

}