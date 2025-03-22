<?php
/**
 * AJAX functions for the admin area.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' ); // Cannot access pages directly.

/**
 * Handles the AJAX request to retrieve product attributes.
 *
 * This function processes the AJAX request, sanitizes the input variables,
 * verifies the nonce, and then outputs the product attributes.
 *
 * @return void Outputs a JSON response with the product attributes.
 */
function lvfw_get_source_products() {
	$search_term = isset( $_REQUEST['search'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search'] ) ) : '';
	$nonce       = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';

	// Verify the nonce.
	if ( ! wp_verify_nonce( $nonce, 'lvfw_products_nonce_action' ) ) {
		wp_die( 'Invalid nonce' );
	}

	// Query WooCommerce products.
	$args = array(
		'limit'   => 10, // Limit the number of results.
		'status'  => 'publish', // Only include published products.
		'orderby' => 'title',
		'order'   => 'ASC',
	);

	// Include a search query if provided.
	if ( ! empty( $search_term ) ) {
		$args['s'] = $search_term; // Search by product title.
	}

	// Use WooCommerce Product Query.
	$product_query = new WC_Product_Query( $args );
	$products      = $product_query->get_products();

	// Format the results for Select2.
	$results = array();
	foreach ( $products as $product ) {
		$results[] = array(
			'id'   => $product->get_id(), // Product ID.
			'text' => $product->get_name(), // Product Name.
		);
	}

	// Return the results as JSON.
	echo wp_json_encode( $results );

	wp_die(); // Required to terminate AJAX calls properly.
}

add_action( 'wp_ajax_lvfw_get_source_products', 'lvfw_get_source_products' );

/**
 * Handles the AJAX request to retrieve product categories or tags.
 *
 * This function processes the AJAX request, sanitizes the input variables,
 * verifies the nonce, and then outputs the product categories or tags.
 *
 * @return void Outputs a JSON response with the product categories or tags.
 */
function lvfw_get_source_taxonomy() {
	$taxonomy    = isset( $_REQUEST['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['taxonomy'] ) ) : '';
	$search_term = isset( $_REQUEST['search'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search'] ) ) : '';
	$nonce       = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';

	// Verify the nonce.
	if ( ! wp_verify_nonce( $nonce, 'lvfw_products_nonce_action' ) ) {
		wp_die( 'Invalid nonce' );
	}

	// Query WooCommerce products categories taxonomy.
	$taxonomies = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'name__like' => $search_term, // Search term.
			'number'     => 10, // Limit results.
		)
	);

	// Format the results for Select2.
	$results = array();
	foreach ( $taxonomies as $taxonomy ) {
		$results[] = array(
			'id'   => $taxonomy->term_id,
			'text' => $taxonomy->name,
		);
	}

	// Return the results as JSON.
	echo wp_json_encode( $results );

	wp_die(); // Required to terminate AJAX calls properly.
}

add_action( 'wp_ajax_lvfw_get_source_taxonomy', 'lvfw_get_source_taxonomy' );

/**
 * Handles the AJAX request to retrieve a new variation form.
 *
 * This function processes the AJAX request, sanitizes the input variables,
 * verifies the nonce, and then outputs a new variation form for the product.
 *
 * @return void Outputs a JSON response with the new variation form and updated key.
 */
function lvfw_get_new_variation() {
	$key   = isset( $_REQUEST['key'] ) ? absint( $_REQUEST['key'] ) : '';
	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';

	// Verify the nonce.
	if ( ! wp_verify_nonce( $nonce, 'lvfw_products_nonce_action' ) ) {
		wp_die( 'Invalid nonce' );
	}

	// Get product attributes.
	$product_attributes = wc_get_attribute_taxonomies();

	// Set the source.
	$source = 'products';

	// Output the new variation form.
	ob_start();
	include LVFW_INCLUDE_PATH . 'admin/new-variation.php';
	$output = ob_get_clean();

	// Return the output.
	echo wp_json_encode(
		array(
			'key'    => $key + 1,
			'output' => $output,
		)
	);

	wp_die(); // Required to terminate AJAX calls properly.
}

add_action( 'wp_ajax_lvfw_get_new_variation', 'lvfw_get_new_variation' );
