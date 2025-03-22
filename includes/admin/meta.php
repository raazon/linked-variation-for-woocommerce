<?php defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' ); // Cannot access pages directly.

// Adds the meta box woolinkedvariation post type.
function lvfw_cpt_meta_box( $post_type ) {
	$post_types = array( 'woolinkedvariation' );
	if ( in_array( $post_type, $post_types ) ) {
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


function lvfw_cpt_meta_box_linked_variations( $post ) {
	require_once LVFW_INCLUDE_PATH . 'admin/output.php';
}

function lvfw_cpt_meta_box_content_thank_you( $post ) {
	printf(
		'<p> %1$s <a href="https://wordpress.org/support/plugin/linked-variation-for-woocommerce/reviews/?filter=5" target="_blank">%2$s</a></p> <p>%3$s</p>',
		__( 'Thank you for using our plugin. If you like our plugin please' ),
		__( 'Rate Us.' ),
		__( 'Your rating is our inspiration!' )
	);
}

/**
 * Save the meta when the post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 */
function lvfw_save_post_hook( $post_id, $post, $update ) {
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

	// Get the data from $_POST.
	$sources    = isset( $_POST['source'] ) ? (array) $_POST['source'] : array();
	$products   = isset( $_POST['products'] ) ? (array) $_POST['products'] : array();
	$categories = isset( $_POST['categories'] ) ? (array) $_POST['categories'] : array();
	$tags       = isset( $_POST['tags'] ) ? (array) $_POST['tags'] : array();
	$attributes = isset( $_POST['attributes'] ) ? (array) $_POST['attributes'] : array();

	// Check if the data is available in $_POST.
	if ( ! empty( $sources ) ) {
		// Initialize an empty array to store the variations.
		$linked_variations = array();

		// Loop through each source and build the desired structure.
		foreach ( $sources as $index => $source ) {
			$linked_variations[ $index ] = array(
				'source'     => $source,
				'products'   => ( $source === 'products' && isset( $products[ $index ] ) ) ? $products[ $index ] : array(),
				'categories' => ( $source === 'categories' && isset( $categories[ $index ] ) ) ? $categories[ $index ] : array(),
				'tags'       => ( $source === 'tags' && isset( $tags[ $index ] ) ) ? $tags[ $index ] : array(),
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

add_action( 'save_post', 'lvfw_save_post_hook', 10, 3 );
