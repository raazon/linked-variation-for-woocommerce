<?php
/**
 * Meta Box for Linked Variations.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' ); // Cannot access pages directly.

/**
 * Add meta box for linked variations.
 *
 * @param string $post_type The post type.
 * @return void
 */
function lvfw_cpt_meta_box( $post_type ) {
	$post_types = array( 'woolinkedvariation' );
	if ( in_array( $post_type, $post_types, true ) ) {
		add_meta_box(
			'linkedvariations',
			esc_html__( 'Linked Variations', 'linked-variation-for-woocommerce' ),
			'lvfw_cpt_meta_box_linked_variations',
			$post_type,
			'advanced',
			'high'
		);

		// thank you.
		add_meta_box(
			'thank-you',
			esc_html__( 'Thank You!', 'linked-variation-for-woocommerce' ),
			'lvfw_cpt_meta_box_content_thank_you',
			$post_type,
			'side',
			'default'
		);
	}
}

add_action( 'add_meta_boxes', 'lvfw_cpt_meta_box', 10, 1 );

/**
 * Linked Variations Meta Box.
 *
 * @return void
 */
function lvfw_cpt_meta_box_linked_variations() {
	require_once LVFW_INCLUDE_PATH . 'admin/output.php';
}

/**
 * Thank You Meta Box Content.
 *
 * @return void
 */
function lvfw_cpt_meta_box_content_thank_you() {
	printf(
		'<p> %1$s <a href="https://wordpress.org/support/plugin/linked-variation-for-woocommerce/reviews/?filter=5" target="_blank">%2$s</a></p> <p>%3$s</p>',
		esc_html__( 'Thank you for using our plugin. If you like our plugin please', 'linked-variation-for-woocommerce' ),
		esc_html__( 'Rate Us.', 'linked-variation-for-woocommerce' ),
		esc_html__( 'Your rating is our inspiration!', 'linked-variation-for-woocommerce' )
	);
}

/**
 * Save the meta when the post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return void
 */
function lvfw_save_post_hook( $post_id ) {
	/*
	* If this is an autosave, our form has not been submitted,
	* so we don't want to do anything.
	*/
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	/*
	* If post type is not woolinkedvariation.
	*/
	if ( 'woolinkedvariation' !== get_post_type( $post_id ) ) {
		return;
	}

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	/* OK, it's safe for us to save the data now. */

	// Check nonce for form validation.
	$nonce = isset( $_REQUEST['lvfw_products_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lvfw_products_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'lvfw_products_nonce_action' ) ) {
		wp_die( 'Invalid nonce or nonce verification failed!' );
	}

	// Sanitize and unslash the input variables.

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$sources = isset( $_POST['source'] ) ? (array) wp_unslash( $_POST['source'] ) : array();

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$products = isset( $_POST['products'] ) ? (array) wp_unslash( $_POST['products'] ) : array();

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$categories = isset( $_POST['categories'] ) ? (array) wp_unslash( $_POST['categories'] ) : array();

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tags = isset( $_POST['tags'] ) ? (array) wp_unslash( $_POST['tags'] ) : array();

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$attributes = isset( $_POST['attributes'] ) ? (array) wp_unslash( $_POST['attributes'] ) : array();

	// Check if the data is available in $_POST.
	if ( ! empty( $sources ) ) {
		// Initialize an empty array to store the variations.
		$linked_variations = array();

		// Loop through each source and build the desired structure.
		foreach ( $sources as $index => $source ) {
			$linked_variations[ $index ] = array(
				'source'     => $source,
				'products'   => ( 'products' === $source && isset( $products[ $index ] ) ) ? $products[ $index ] : array(),
				'categories' => ( 'categories' === $source && isset( $categories[ $index ] ) ) ? $categories[ $index ] : array(),
				'tags'       => ( 'tags' === $source && isset( $tags[ $index ] ) ) ? $tags[ $index ] : array(),
				'attributes' => isset( $attributes[ $index ] ) ? $attributes[ $index ] : array(),
			);
		}

		// make all product, categories, tags integer.
		foreach ( $linked_variations as $key => $variation ) {
			$linked_variations[ $key ]['products']   = array_map( 'intval', $variation['products'] );
			$linked_variations[ $key ]['categories'] = array_map( 'intval', $variation['categories'] );
			$linked_variations[ $key ]['tags']       = array_map( 'intval', $variation['tags'] );
		}

		// Update the post meta with the structured data.
		update_post_meta( $post_id, 'linked_variations', $linked_variations );
	}
}

add_action( 'save_post', 'lvfw_save_post_hook', 10 );
