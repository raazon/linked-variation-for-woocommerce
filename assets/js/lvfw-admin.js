jQuery(document).ready(function ($) {
	// Function to toggle visibility and initialize Select2
	function toggleFields($container) {
		const selectedSource = $container.find('.source-picker').val();

		// Show/hide fields based on the selected source
		const $products = $container.find('.products-picker');
		const $categories = $container.find('.categories-picker');
		const $tags = $container.find('.tags-picker');

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

		// Destructuring ajax objects
		const { ajaxurl, product_placeholder, category_placeholder, tag_placeholder } = lvfw_ajax_object;

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
					url: ajaxurl + '?action=lvfw_get_source_products', // Your REST API endpoint
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
					url: ajaxurl + '?action=lvfw_get_source_taxonomy&taxonomy=product_cat', // Your REST API endpoint
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
					url: ajaxurl + '?action=lvfw_get_source_taxonomy&taxonomy=product_tag', // Your REST API endpoint
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
});
