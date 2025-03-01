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
	echo 'Hello...';
}

add_action( 'woocommerce_before_add_to_cart_form', 'lvfw_display_linked_variation', 10, 0 );