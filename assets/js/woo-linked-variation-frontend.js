(function ($) {
    "use strict";
    jQuery(document).ready(function () {

        // linked variation label change on hover 
        $(function () {
            var wooLinkedVariation = $('.woo-linked-variation');
            var currentVariation = wooLinkedVariation.find('.linked-variation-label .variation-selection');
            var singleVariation = wooLinkedVariation.find('.linked-variations ul li');
            singleVariation.hover(function () {
                var thisDataVariant = $(this).data('variant');
                $(this).parents('.woo-linked-variation').find('.linked-variation-label .variation-selection').html(thisDataVariant);
            }, function () {
                $(this).parents('.woo-linked-variation').find('.linked-variation-label .variation-selection').html(currentVariation.data('variant'));
            });
        });

    });
})(jQuery);