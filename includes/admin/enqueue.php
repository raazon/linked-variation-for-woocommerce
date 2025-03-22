<?php
/**
 * Enqueue admin scripts.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

/**
 * Admin enqueue scripts.
 *
 * @since 2.0.0
 * @package Lvfw
 */
function lvfw_admin_enqueue_scripts() {
	// Get current admin screen, or null.
	$screen = get_current_screen();

	// verify admin screen object.
	if ( is_object( $screen ) ) {
		// Enqueue only for specific post types.
		if ( in_array( $screen->post_type, array( 'woolinkedvariation' ), true ) ) {
			wp_enqueue_style(
				'select2',
				plugins_url( 'assets/css/select2.min.css', LVFW_FILE ),
				array(),
				LVFW_VERSION
			);
			wp_enqueue_script(
				'select2',
				plugins_url( 'assets/js/select2.min.js', LVFW_FILE ),
				array( 'jquery' ),
				LVFW_VERSION,
				array()
			);
			wp_enqueue_script(
				'lvfw-admin',
				plugins_url( 'assets/js/lvfw-admin.js', LVFW_FILE ),
				array( 'jquery' ),
				LVFW_VERSION,
				array()
			);
			wp_localize_script(
				'lvfw-admin',
				'lvfw_ajax_object',
				array(
					'ajaxurl'              => admin_url( 'admin-ajax.php' ),
					'product_placeholder'  => esc_html( 'Select products...', 'linked-variation-for-woocommerce' ),
					'category_placeholder' => esc_html( 'Select categories...', 'linked-variation-for-woocommerce' ),
					'tag_placeholder'      => esc_html( 'Select tags...', 'linked-variation-for-woocommerce' ),
					'confirm_message'      => esc_html( 'Are you sure you want to delete this variation?', 'linked-variation-for-woocommerce' ),
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
				'lvfw-admin',
				plugins_url(
					'assets/css/lvfw-admin.css',
					LVFW_FILE
				),
				array(),
				LVFW_VERSION
			);
		}
	}
}

add_action( 'admin_enqueue_scripts', 'lvfw_admin_enqueue_scripts', 10, 1 );
