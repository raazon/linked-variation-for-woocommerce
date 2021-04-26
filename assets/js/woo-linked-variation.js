(function ($) {
    "use strict";
    jQuery(document).ready(function () {

        // function
        $(function () {
            jQuery('.linked_variation_products').select2({
                width: '100%',
                placeholder: "Select a product",
                allowClear: true,
            });

            $('#sortable').sortable({
                axis: 'y',
                cursor: 'move',
                placeholder: 'ui-state-highlight',
                update: function (event, ui) {
                    var order = $(this).sortable('toArray');
                    var postId = $(this).data('id');
                    $.ajax({
                        type: 'POST',
                        url: linked_variation_ajax_object.ajax_url,
                        data: {
                            action: 'linked_by_attributes_ordering',
                            ordering: order,
                            post_id: postId,
                        },
                        beforeSend: function () {
                            console.log('Sending....');
                        },
                        success: function (response) {
                            console.log(response);
                        },
                        error: function (errorThrown, status, error) {
                            console.log(status);
                        }
                    });
                }
            });
            $("#sortable").disableSelection();
        });

        // function
        $(function () {

        });

    });
})(jQuery);