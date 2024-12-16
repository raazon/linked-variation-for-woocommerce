<?php defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' ); // Cannot access pages directly.

// admin enqueue scripts
function lvfw_admin_enqueue_scripts( $hook ) {
	// get current admin screen, or null
	$screen = get_current_screen();
	// verify admin screen object
	if ( is_object( $screen ) ) {
		// enqueue only for specific post types
		if ( in_array( $screen->post_type, array( 'woolinkedvariation' ) ) ) {
			wp_enqueue_style( 'select2', plugins_url( 'assets/css/select2.min.css', LVFW_FILE ), array() );
			wp_enqueue_script( 'select2', plugins_url( 'assets/js/select2.min.js', LVFW_FILE ), array( 'jquery' ) );
			wp_enqueue_script( 'woo-linked-variation', plugins_url( 'assets/js/woo-linked-variation.js', LVFW_FILE ), array( 'jquery' ) );
			wp_localize_script(
				'woo-linked-variation',
				'linked_variation_ajax_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
			wp_enqueue_style( 'jquery-ui', plugins_url( 'assets/css/jquery-ui.min.css', LVFW_FILE ), array() );
			wp_enqueue_style( 'woo-linked-variation', plugins_url( 'assets/css/woo-linked-variation.css', LVFW_FILE ), array() );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'lvfw_admin_enqueue_scripts', 10, 1 );
