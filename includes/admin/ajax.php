<?php defined('ABSPATH') || die('Cheatin&#8217; uh?'); // Cannot access pages directly.

//Customize the callback to your liking
function lvfw_get_source_data() {
    $search_term = isset($_REQUEST['search']) ? esc_attr($_REQUEST['search']) : '';

    // Dummy data
    $dummy_data = [
        [ 'id' => 1, 'text' => 'Hello' ],
        [ 'id' => 2, 'text' => 'World' ],
        [ 'id' => 3, 'text' => 'Test Product' ],
        [ 'id' => 4, 'text' => 'Another Product' ],
    ];

	// Filter the dummy data based on the search term
	if (!empty($search_term)) {
		$dummy_data = array_filter($dummy_data, function ($item) use ($search_term) {
			return stripos($item['text'], $search_term) !== false;
		});
	}

	echo wp_json_encode(array_values($dummy_data));

	wp_die();
}

add_action( 'wp_ajax_lvfw_get_source_data', 'lvfw_get_source_data' );
