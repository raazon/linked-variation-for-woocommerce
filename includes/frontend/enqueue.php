<?php

/**
 * Enqueue frontend scripts.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined('ABSPATH') || die('Cheatin&#8217; uh?');

/**
 * Frontend enqueue scripts.
 *
 * @since 2.0.0
 * @package Lvfw
 */
function lvfw_frontend_enqueue_scripts() {
	// Check if product single page.
	if ( !is_singular('product') ) {
		return;
	}

	// Check linked variations exist.
	$linked_variations = array();
	if ( function_exists('lvfw_find_linked_variation_post') ) {
		$linked_variations = lvfw_find_linked_variation_post( get_the_ID() );
	}

	if ( empty( $linked_variations ) ) {
		return;
	}

	// verify admin screen object.
	if (is_singular('product')) {
		wp_enqueue_style(
			'lvfw-frontend',
			plugins_url('assets/css/lvfw-frontend.css', LVFW_FILE),
			array(),
			LVFW_VERSION
		);

		wp_enqueue_script(
			'lvfw-frontend',
			plugins_url('assets/js/lvfw-frontend.js', LVFW_FILE),
			array(),
			LVFW_VERSION,
			array(
				'in_footer' => true,
			)
		);
	}
}

add_action('wp_enqueue_scripts', 'lvfw_frontend_enqueue_scripts', 10);
