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
