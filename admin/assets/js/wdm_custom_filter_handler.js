jQuery(document).ready(function(){

    jQuery('#just-my-hotel').select2({
        ajax: {
            url: wdm_metabox_object.ajax_url,
            dataType: 'json',
            type: 'post',
            delay: 250,
            data: function(params) {
                return {
                    action: wdm_metabox_object.my_action1,
                    search_text: params.term, // search term
                    page: params.page || 1,
                    security: wdm_metabox_object.security,
                };
            },
            processResults: function(data,params) {
                params.page = params.page || 1;
                return {
                    results: jQuery.map(data.items, function(item) {
                        return {
                            text: item.text,
                            id: item.id
                        };
                    }),
                    pagination: {
                        more: ( params.page * 10 ) < data.total
                    }
                };
            },
            cache: true
        },
        placeholder: 'Select hotel', // PLEASE LOCALIZE PLACEHOLDER AND TRANSLATE.
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
        width: '150px',
        allowClear: true
    });

    jQuery('#just-my-hotel').on('select2:clear', function (e) {
        jQuery('#just-my-hotel').val(0).trigger('change');
    });
});