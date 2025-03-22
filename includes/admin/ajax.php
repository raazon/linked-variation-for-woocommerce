<?php defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' ); // Cannot access pages directly.

// Customize the callback to your liking
function lvfw_get_source_products() {
	$search_term = isset( $_REQUEST['search'] ) ? esc_attr( $_REQUEST['search'] ) : '';

	// Query WooCommerce products
	$args = array(
		'limit'   => 10, // Limit the number of results
		'status'  => 'publish', // Only include published products
		'orderby' => 'title',
		'order'   => 'ASC',
	);

	// Include a search query if provided
	if ( ! empty( $search_term ) ) {
		$args['s'] = $search_term; // Search by product title
	}

	// Use WooCommerce Product Query
	$product_query = new WC_Product_Query( $args );
	$products      = $product_query->get_products();

	// Format the results for Select2
	$results = array();
	foreach ( $products as $product ) {
		$results[] = array(
			'id'   => $product->get_id(), // Product ID
			'text' => $product->get_name(), // Product Name
		);
	}

	// Return the results as JSON
	echo wp_json_encode( $results );

	wp_die(); // Required to terminate AJAX calls properly
}

add_action( 'wp_ajax_lvfw_get_source_products', 'lvfw_get_source_products' );

// Customize the callback to your liking
function lvfw_get_source_taxonomy() {
	$taxonomy    = isset( $_REQUEST['taxonomy'] ) ? esc_attr( $_REQUEST['taxonomy'] ) : '';
	$search_term = isset( $_REQUEST['search'] ) ? esc_attr( $_REQUEST['search'] ) : '';

	// Query WooCommerce products categories taxonomy
	$taxonomies = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'name__like' => $search_term, // Search term
			'number'     => 10, // Limit results
		)
	);

	// Format the results for Select2
	$results = array();
	foreach ( $taxonomies as $taxonomy ) {
		$results[] = array(
			'id'   => $taxonomy->term_id,
			'text' => $taxonomy->name,
		);
	}

	// Return the results as JSON
	echo wp_json_encode( $results );

	wp_die(); // Required to terminate AJAX calls properly
}

add_action( 'wp_ajax_lvfw_get_source_taxonomy', 'lvfw_get_source_taxonomy' );


function lvfw_get_new_variation() {
	$key = isset( $_REQUEST['key'] ) ? esc_attr( $_REQUEST['key'] ) : '';

	// Get product attributes
	$product_attributes = wc_get_attribute_taxonomies();

	// Output the new variation form
	ob_start();
	include LVFW_INCLUDE_PATH . 'admin/new-variation.php';
	$output = ob_get_clean();

	// Return the output
	echo wp_json_encode(
		array(
			'key'    => $key + 1,
			'output' => $output,
		)
	);

	wp_die(); // Required to terminate AJAX calls properly
}

add_action( 'wp_ajax_lvfw_get_new_variation', 'lvfw_get_new_variation' );
