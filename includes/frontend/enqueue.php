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
	// Get current admin screen, or null.
	// $screen = get_current_screen();

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
