<?php
/**
 * Load helpers files.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' ); // Cannot access pages directly.

/**
 * Determines whether images should be shown for a specific attribute key
 * within the linked variations.
 *
 * @param string $attribute_key The key of the attribute to check.
 * @param array  $linked_variations The linked variations data, which should
 * include an 'attributes' key containing an array of attribute data.
 *
 * @return bool True if images should be shown for the specified attribute key, false otherwise.
 */
function lvfw_should_show_images( $attribute_key, $linked_variations ) {
	if ( ! isset( $linked_variations['attributes'] ) ) {
		return false;
	}

	foreach ( $linked_variations['attributes'] as $attribute ) {
		if (
			$attribute['name'] === $attribute_key &&
			isset( $attribute['show_images'] ) &&
			$attribute['show_images'] === '1'
		) {
			return true;
		}
	}

	return false;
}

function lvfw_display_variation( $show_images, $product_id, $value ) {
	if ( $show_images ) {
		printf(
			'<img src="%s" alt="%s">',
			esc_url( get_the_post_thumbnail_url( $product_id ), 'post-thumbnail' ),
			esc_attr( $value )
		);
	} else {
		echo esc_html( $value );
	}
}
