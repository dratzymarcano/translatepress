jQuery( function() {
    function trigger_update_by_ajax( data ) {
        jQuery.ajax({
            url: lrp_updb_localized['admin_ajax_url'],
            type: 'post',
            dataType: 'json',
            data: data,
            success: function (response) {
                jQuery('#lrp-update-database-progress').append(response['progress_message'])
                if ( response['lrp_update_completed'] == 'no' ) {
                    trigger_update_by_ajax(response);
                }
            },
            error: function (errorThrown) {
                jQuery('#lrp-update-database-progress').append(errorThrown['responseText'])
                console.log('LinguaPress AJAX Request Error while triggering database update');
            }
        });
    };
    trigger_update_by_ajax( {
        action: 'lrp_update_database',
        lrp_updb_nonce: lrp_updb_localized['nonce'],
        initiate_update: true,
    } );
});