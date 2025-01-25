(function ($) {
	"use strict";
	jQuery(document).ready(function () {
		// function
		$(function () {
			// product selection - select2
			var data = [
				{
					id: 0,
					text: 'enhancement'
				},
				{
					id: 1,
					text: 'bug'
				},
				{
					id: 2,
					text: 'duplicate'
				},
				{
					id: 3,
					text: 'invalid'
				},
				{
					id: 4,
					text: 'wontfix'
				}
			];

			var {ajaxurl, product_placeholder} = lvfw_ajax_object;

			$("select[name=product]").select2({
				width: "100%",
				placeholder: product_placeholder,
				multiple: true,
				allowClear: true,
				tags: false,
				// data: data,
				minimumInputLength: 3,
				ajax: {
					url: ajaxurl + '?action=lvfw_get_source_data', // Your REST API endpoint
					dataType: 'json',
					delay: 250, // Delay in milliseconds to avoid excessive API calls
					data: function (params) {
						return {
							search: params.term // Send the search term as a query parameter
						};
					},
					processResults: function (data) {
						return {
							results: data // Map the data directly
						};
					}
				}
			});

			// disable auto sorting select2
			$("select").on("select2:select", function (evt) {
				var element = evt.params.data.element;
				var $element = $(element);

				$element.detach();
				$(this).append($element);
				$(this).trigger("change");
			});

			// variation shorting - jquery UI sortable
			$("#sortable").sortable({
				axis: "y",
				cursor: "move",
				placeholder: "ui-state-highlight",
				update: function (event, ui) {
					var order = $(this).sortable("toArray");
					var postId = $(this).data("id");
					$.ajax({
						type: "POST",
						url: linked_variation_ajax_object.ajax_url,
						data: {
							action: "linked_by_attributes_ordering",
							ordering: order,
							post_id: postId,
						},
						beforeSend: function () { },
						success: function (response) {
							console.log(response);
						},
						error: function (errorThrown, status, error) {
							console.log(status);
						},
					});
				},
			});

			$("#sortable").disableSelection();
		});
	});
})(jQuery);
