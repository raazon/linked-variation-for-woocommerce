jQuery(document).ready(function ($) {
	// Destructuring ajax objects
	const { ajaxurl, product_placeholder, category_placeholder, tag_placeholder } = lvfw_ajax_object;

	// Function to toggle visibility and initialize Select2
	function toggleFields($container) {
		const selectedSource = $container.find('.source-picker').val();

		// Show/hide fields based on the selected source
		const $products = $container.find('.products-picker');
		const $categories = $container.find('.categories-picker');
		const $tags = $container.find('.tags-picker');
		const nonce = $container.parents('form').find('input[name="lvfw_products_nonce"]').val();

		// Safely destroy Select2 before hiding
		if ($products.data('select2')) {
			$products.select2('destroy');
		}

		if ($categories.data('select2')) {
			$categories.select2('destroy');
		}

		if ($tags.data('select2')) {
			$tags.select2('destroy');
		}

		if (selectedSource === 'products') {
			$products.show().select2({
				width: "100%",
				placeholder: product_placeholder,
				multiple: true,
				allowClear: true,
				tags: false,
				// data: data,
				minimumInputLength: 3,
				ajax: {
					url: ajaxurl + `?action=lvfw_get_source_products&nonce=${nonce}`, // Your REST API endpoint
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
			$categories.hide();
			$tags.hide();
		} else if (selectedSource === 'categories') {
			$categories.show().select2({
				width: "100%",
				placeholder: category_placeholder,
				multiple: true,
				allowClear: true,
				tags: false,
				// data: data,
				minimumInputLength: 3,
				ajax: {
					url: ajaxurl + `?action=lvfw_get_source_taxonomy&taxonomy=product_cat&nonce=${nonce}`, // Your REST API endpoint
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
			$products.hide();
			$tags.hide();
		} else if (selectedSource === 'tags') {
			$tags.show().select2({
				width: "100%",
				placeholder: tag_placeholder,
				multiple: true,
				allowClear: true,
				tags: false,
				// data: data,
				minimumInputLength: 3,
				ajax: {
					url: ajaxurl + `?action=lvfw_get_source_taxonomy&taxonomy=product_tag&nonce=${nonce}`, // Your REST API endpoint
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
			$products.hide();
			$categories.hide();
		}
	}

	// Initialize visibility and Select2 for all linked-variation items
	$('.linked-variation-item').each(function () {
		toggleFields($(this));
	});

	// Handle change event on the source dropdown
	$(document).on('change', '.source-picker', function () {
		const $container = $(this).closest('.linked-variation-item');
		toggleFields($container);
	});

	// Initialize variation item shorting
	$(".linked-variations").sortable({
		axis: "y", // Allow sorting only vertically
		cursor: "move", // Change cursor to "move" while dragging
		placeholder: "ui-state-highlight", // Use a placeholder for the drop position
	});

	// Initialize attributes item shorting
	$(".linked-variations .attributes").sortable({
		axis: "y", // Allow sorting only vertically
		cursor: "move", // Change cursor to "move" while dragging
		placeholder: "ui-state-highlight", // Use a placeholder for the drop position
	});

	// Handle click event on the add variation button
	$(document).on('click', '.add-variation', function () {
		var thisElm = $(this);
		var variations_key = thisElm.data('variations');

		// Send ajax request to get the variation form
		$.ajax({
			url: ajaxurl,
			type: 'GET',
			data: {
				action: 'lvfw_get_new_variation',
				key: variations_key,
				nonce: thisElm.parents('form').find('input[name="lvfw_products_nonce"]').val()
			},
			success: function (response) {
				var obj = JSON.parse(response);
				var $newVariation = $(obj.output);

				// Append the new variation
				$('.linked-variations').append($newVariation);

				// Update the data-variations attribute
				thisElm.data('variations', obj.key);

				// Reinitialize the necessary event handlers and functionality
				toggleFields($newVariation);
				$newVariation.find('.source-picker').trigger('change');
				$(".linked-variations").sortable("refresh");
				$newVariation.find('.attributes').sortable({
					axis: "y",
					cursor: "move",
					placeholder: "ui-state-highlight"
				});
			}
		});
	});

	// Handle click event on the remove variation button
	$(document).on('click', '.remove-variation', function () {
		// before delete show confirm box
		if (!confirm(lvfw_ajax_object.confirm_message)) {
			return false;
		}

		// Remove the variation
		$(this).closest('.linked-variation-item').remove();
	});
});
