<?php
/**
 * Frontend display.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

/**
 * Display linked variation message.
 *
 * This function outputs a message before the WooCommerce add to cart form.
 *
 * @hooked woocommerce_before_add_to_cart_form - 10
 */
function lvfw_display_linked_variation() {
	// Get the current product ID.
	$product_id        = get_the_ID();
	$product           = wc_get_product( $product_id );
	$linked_variations = lvfw_find_linked_variation_post( $product_id );

	if ( empty( $linked_variations ) ) {
		return;
	}

	$source   = $linked_variations['source'] ?? 'products';
	$products = $linked_variations['products'] ?? array();

	$linked_attributes = $linked_variations['attributes'] ?? array();
	$link_images       = isset( $link_data['images'] ) ? $link_data['images'] : array();

	// Include the current product in filtering.
	$products[] = $product_id;

	// Get product attributes dynamically.
	$product_attributes      = $product->get_attributes();
	$product_attributes_keys = array_keys( $product_attributes );

	if ( empty( $product_attributes_keys ) ) {
		return; // No attributes found.
	}

	// Store the current product's attribute values.
	$current_attributes = array();
	foreach ( $product_attributes_keys as $attribute_key ) {
		$current_attributes[ $attribute_key ] = $product->get_attribute( $attribute_key );
	}

	$variations = array();

	// Loop through all linked products and store variations.
	foreach ( $products as $variation_id ) {
		$variation_product = wc_get_product( $variation_id );
		if ( ! $variation_product ) {
			continue;
		}

		$variation_data = array();
		foreach ( $product_attributes_keys as $attribute_key ) {
			$variation_data[ $attribute_key ] = $variation_product->get_attribute( $attribute_key );
		}

		$variations[ $variation_id ] = $variation_data;
	}

	// Prepare filtered variations dynamically.
	$filtered_variations = array();

	foreach ( $product_attributes_keys as $attribute_key ) {
		$filtered_variations[ $attribute_key ] = array();

		foreach ( $variations as $variation_id => $variation_data ) {
			// Ensure the same values for all other attributes except the current one.
			$is_valid = true;
			foreach ( $product_attributes_keys as $other_key ) {
				if ( $other_key !== $attribute_key && $variation_data[ $other_key ] !== $current_attributes[ $other_key ] ) {
					$is_valid = false;
					break;
				}
			}

			if ( $is_valid ) {
				$filtered_variations[ $attribute_key ][ $variation_data[ $attribute_key ] ] = $variation_id;
			}
		}
	}

	// Display the filtered variations.
	if ( $filtered_variations ) {
		require_once LVFW_INCLUDE_PATH . 'frontend/output.php';
	}
}

add_action( 'woocommerce_before_add_to_cart_form', 'lvfw_display_linked_variation', 10, 0 );

/**
 * Finds a linked variation post for a given product ID.
 *
 * This function queries all 'woolinkedvariation' posts and checks if the given product ID
 * matches any of the linked variations based on products, categories, or tags.
 *
 * @param int $current_product_id The ID of the current product.
 * @return int|false The ID of the matching 'woolinkedvariation' post, or false if no match is found.
 */
function lvfw_find_linked_variation_post( $current_product_id ) {
	// Query all woolinkedvariation posts.
	$args = array(
		'post_type'      => 'woolinkedvariation',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids', // Retrieve only post IDs.
	);

	$linked_variation_posts = get_posts( $args );

	if ( empty( $linked_variation_posts ) ) {
		return false; // No linked variations found.
	}

	// Get product's category and tag IDs.
	$product_categories = wp_get_post_terms( $current_product_id, 'product_cat', array( 'fields' => 'ids' ) );
	$product_tags       = wp_get_post_terms( $current_product_id, 'product_tag', array( 'fields' => 'ids' ) );

	// Loop through each woolinkedvariation post to find a match.
	foreach ( $linked_variation_posts as $post_id ) {
		$linked_variations = get_post_meta( $post_id, 'linked_variations', true );

		if ( ! is_array( $linked_variations ) ) {
			continue; // Skip if invalid data.
		}

		foreach ( $linked_variations as $variation ) {
			$source = $variation['source'] ?? 'products';

			// Check if the product exists in 'products' source.
			if ( 'products' === $source && in_array( $current_product_id, $variation['products'] ?? array(), true ) ) {
				return $variation; // Found matching woolinkedvariation post.
			}

			// Check if the product's category exists in 'categories' source.
			if ( 'categories' === $source && ! empty( $variation['categories'] ) ) {
				if ( array_intersect( $product_categories, $variation['categories'] ) ) {
					return $variation;
				}
			}

			// Check if the product's tag exists in 'tags' source.
			if ( 'tags' === $source && ! empty( $variation['tags'] ) ) {
				if ( array_intersect( $product_tags, $variation['tags'] ) ) {
					return $variation;
				}
			}
		}
	}

	return false; // No linked variation found.
}
