<?php

/**
 * Frontend display.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined('ABSPATH') || die('Cheatin&#8217; uh?');

/**
 * Display linked variation message.
 *
 * This function outputs a message before the WooCommerce add to cart form.
 *
 * @hooked woocommerce_before_add_to_cart_form - 10
 */
function lvfw_display_linked_variation()
{
	// Get the current product ID
	$current_product_id = get_the_ID();
	$linked_variation_post_id = lvfw_find_linked_variation_post($current_product_id);

	echo '<pre>';
	var_dump($linked_variation_post_id);
	echo '</pre>';

	echo 'Hello...';
}

add_action('woocommerce_before_add_to_cart_form', 'lvfw_display_linked_variation', 10, 0);

/**
 * Finds a linked variation post for a given product ID.
 *
 * This function queries all 'woolinkedvariation' posts and checks if the given product ID
 * matches any of the linked variations based on products, categories, or tags.
 *
 * @param int $current_product_id The ID of the current product.
 * @return int|false The ID of the matching 'woolinkedvariation' post, or false if no match is found.
 */
function lvfw_find_linked_variation_post($current_product_id)
{
	// Query all woolinkedvariation posts
	$args = [
		'post_type'      => 'woolinkedvariation',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids', // Retrieve only post IDs
	];

	$linked_variation_posts = get_posts($args);

	if (empty($linked_variation_posts)) {
		return false; // No linked variations found
	}

	// Get product's category and tag IDs
	$product_categories = wp_get_post_terms($current_product_id, 'product_cat', ['fields' => 'ids']);
	$product_tags = wp_get_post_terms($current_product_id, 'product_tag', ['fields' => 'ids']);

	// Loop through each woolinkedvariation post to find a match
	foreach ($linked_variation_posts as $post_id) {
		$linked_variations = get_post_meta($post_id, 'linked_variations', true);

		if (!is_array($linked_variations)) {
			continue; // Skip if invalid data
		}

		foreach ($linked_variations as $variation) {
			$source = $variation['source'] ?? '';

			// Check if the product exists in 'products' source
			if ($source === 'products' && in_array($current_product_id, $variation['products'] ?? [], true)) {
				return $post_id; // Found matching woolinkedvariation post
			}

			// Check if the product's category exists in 'categories' source
			if ($source === 'categories' && !empty($variation['categories'])) {
				if (array_intersect($product_categories, $variation['categories'])) {
					return $post_id;
				}
			}

			// Check if the product's tag exists in 'tags' source
			if ($source === 'tags' && !empty($variation['tags'])) {
				if (array_intersect($product_tags, $variation['tags'])) {
					return $post_id;
				}
			}
		}
	}

	return false; // No linked variation found
}
