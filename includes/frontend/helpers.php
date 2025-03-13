<?php

/**
 * Load helpers files.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined('ABSPATH') || die('Cheatin&#8217; uh?'); // Cannot access pages directly.

function lvfw_should_show_images($attribute_key, $linked_variations)
{
	if (!isset($linked_variations['attributes'])) {
		return false;
	}

	foreach ($linked_variations['attributes'] as $attribute) {
		if (
			$attribute['name'] === $attribute_key &&
			isset($attribute['show_images']) &&
			$attribute['show_images'] === '1'
		) {
			return true;
		}
	}

	return false;
}

function lvfw_display_variation($show_images, $product_id, $value)
{
	if ($show_images) {
		echo sprintf(
			'<img src="%s" alt="%s">',
			esc_url(get_the_post_thumbnail_url($product_id), 'post-thumbnail'),
			esc_attr($value)
		);
	} else {
		echo esc_html($value);
	}
}
