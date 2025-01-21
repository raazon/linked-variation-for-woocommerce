<?php

/**
 * Enqueue admin scripts.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined('ABSPATH') || die('Cheatin&#8217; uh?');

// Admin enqueue scripts.
function lvfw_admin_enqueue_scripts($hook) {
	// Get current admin screen, or null.
	$screen = get_current_screen();
	// verify admin screen object
	if (is_object($screen)) {
		// Enqueue only for specific post types.
		if (in_array($screen->post_type, array('woolinkedvariation'), true)) {
			wp_enqueue_style(
				'select2',
				plugins_url('assets/css/select2.min.css', LVFW_FILE),
				array(),
				LVFW_VERSION
			);
			wp_enqueue_script(
				'select2',
				plugins_url('assets/js/select2.min.js', LVFW_FILE),
				array('jquery'),
				LVFW_VERSION
			);
			wp_enqueue_script(
				'woo-linked-variation',
				plugins_url('assets/js/woo-linked-variation.js', LVFW_FILE),
				array('jquery'),
				LVFW_VERSION
			);
			wp_localize_script(
				'woo-linked-variation',
				'linked_variation_ajax_object',
				array(
					'ajax_url' => admin_url('admin-ajax.php'),
				)
			);
			wp_enqueue_style(
				'jquery-ui',
				plugins_url(
					'assets/css/jquery-ui.min.css',
					LVFW_FILE
				),
				array(),
				LVFW_VERSION
			);
			wp_enqueue_style(
				'woo-linked-variation',
				plugins_url(
					'assets/css/woo-linked-variation.css',
					LVFW_FILE
				),
				array(),
				LVFW_VERSION
			);
		}
	}
}

add_action('admin_enqueue_scripts', 'lvfw_admin_enqueue_scripts', 10, 1);
