<?php defined('ABSPATH') || die('Cheatin&#8217; uh?'); // Cannot access pages directly.

//Customize the callback to your liking
function lvfw_get_source_data() {
    $search_term = isset($_REQUEST['search']) ? esc_attr($_REQUEST['search']) : '';

    // Query WooCommerce products
    $args = [
        'limit' => 10, // Limit the number of results
        'status' => 'publish', // Only include published products
        'orderby' => 'title',
        'order' => 'ASC',
    ];

	// Include a search query if provided
	if (!empty($search_term)) {
		$args['s'] = $search_term; // Search by product title
	}

	// Use WooCommerce Product Query
	$product_query = new WC_Product_Query($args);
	$products = $product_query->get_products();

    // Format the results for Select2
    $results = [];
    foreach ($products as $product) {
        $results[] = [
            'id' => $product->get_id(), // Product ID
            'text' => $product->get_name(), // Product Name
        ];
    }

    // Return the results as JSON
    echo wp_json_encode($results);

    wp_die(); // Required to terminate AJAX calls properly
}

add_action( 'wp_ajax_lvfw_get_source_data', 'lvfw_get_source_data' );
