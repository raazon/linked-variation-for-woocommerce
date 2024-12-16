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

		// thank you
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
	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'lvfw_products_nonce_action', 'lvfw_products_nonce' );

	$linked_variation_products = get_post_meta( $post->ID, 'linked_variation_products', true ) ?? array();

	$attributes                     = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_id' );
	$_linked_by_attributes          = get_post_meta( $post->ID, '_linked_by_attributes', true ) ? get_post_meta( $post->ID, '_linked_by_attributes', true ) : array();
	$show_images                    = get_post_meta( $post->ID, 'show_images', true ) ? get_post_meta( $post->ID, 'show_images', true ) : array();
	$is_primary                     = get_post_meta( $post->ID, 'is_primary', true ) ? get_post_meta( $post->ID, 'is_primary', true ) : array();
	$_linked_by_attributes_ordering = get_post_meta( $post->ID, '_linked_by_attributes_ordering', true );
	if ( $attributes && $_linked_by_attributes_ordering ) {
		uksort(
			$attributes,
			function ( $key1, $key2 ) use ( $_linked_by_attributes_ordering ) {
				return ( array_search( $key1, $_linked_by_attributes_ordering ) > array_search( $key2, $_linked_by_attributes_ordering ) );
			}
		);
	}

	// get all products
	$get_products = get_posts(
		array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
		)
	);
	$products     = wp_list_pluck( $get_products, 'ID' );
	if ( $linked_variation_products ) {
		$nonSelected = array_diff( $products, $linked_variation_products );
		$products    = array_merge( $linked_variation_products, $nonSelected );
	}
	?>

	<div class="woocommerce_options_panel">
		<?php if ( $products ) : ?>
			<div class="meta-box-item">
				<label class="widefat" for="_linked_variation_products"><?php esc_attr_e( 'Select Products', 'linked-variation-for-woocommerce' ); ?></label>
				<select id="_linked_variation_products" class="linked_variation_products" name="linked_variation_products[]" multiple="multiple">
					<?php foreach ( $products as $product ) : ?>
						<option value="<?php echo esc_attr( $product ); ?>" <?php selected( in_array( $product, $linked_variation_products ) ); ?>><?php echo get_the_title( $product ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php if ( $attributes ) : ?>
				<div class="meta-box-item">
					<label class="widefat" for="_linked_by_attributes"><?php esc_html_e( 'Linked by (attributes)', 'linked-variation-for-woocommerce' ); ?></label>
					<ul id="sortable" data-id="<?php echo esc_attr( $post->ID ); ?>">
						<?php foreach ( $attributes as $key => $attribute ) : ?>
							<li id="<?php echo esc_attr( $key ); ?>" class="ui-state-default">
								<div class="inputs">
									<label for="attribute-<?php echo esc_attr( $key ); ?>">
										<input type="checkbox" name="_linked_by_attributes[]" id="attribute-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $_linked_by_attributes ) ); ?>> <?php echo esc_attr( $attribute ); ?>
									</label>
									<label for="show-image-<?php echo esc_attr( $key ); ?>">
										<input type="checkbox" name="show_images[]" id="show-image-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $show_images ) ); ?>> <?php esc_html_e( 'Show images', 'linked-variation-for-woocommerce' ); ?>
									</label>
									<label for="is-primary-<?php echo esc_attr( $key ); ?>">
										<input type="checkbox" name="is_primary[]" id="is-primary-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $is_primary ) ); ?>> <?php esc_html_e( 'Primary', 'linked-variation-for-woocommerce' ); ?>
									</label>
								</div>
								<span class="dashicons dashicons-move"></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php else : ?>
				<p><?php esc_attr_e( 'No attribute found.', 'linked-variation-for-woocommerce' ); ?></p>
			<?php endif; ?>

		<?php else : ?>
			<p><?php esc_attr_e( 'No product found.', 'linked-variation-for-woocommerce' ); ?></p>
		<?php endif; ?>

	</div>


	<?php
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
function lvfw_save_post_hook( $post_id ) {
	/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['woo_linked_variation_products_nonce'] ) ) {
		return $post_id;
	}

	$nonce = $_POST['woo_linked_variation_products_nonce'];

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'woo_linked_variation_products_nonce_action' ) ) {
		return $post_id;
	}

	/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
	}

	/* OK, it's safe for us to save the data now. */

	// unlink previous values
	$linked_variation_products = get_post_meta( $post_id, 'linked_variation_products', true );
	if ( $linked_variation_products ) {
		foreach ( $linked_variation_products as $linked_variation_product ) {
			update_post_meta( $linked_variation_product, 'linked_variation_id', '' );
		}
	}

	// Update the linked_variation_products meta field.
	if ( isset( $_POST['linked_variation_products'] ) ) {
		update_post_meta( $post_id, 'linked_variation_products', array_map( 'intval', $_POST['linked_variation_products'] ) );
		foreach ( $_POST['linked_variation_products'] as $linked_variation_product ) {
			if ( intval( $linked_variation_product ) && intval( $post_id ) ) {
				update_post_meta( intval( $linked_variation_product ), 'linked_variation_id', intval( $post_id ) );
			}
		}
	} else {
		update_post_meta( $post_id, 'linked_variation_products', array() );
	}

	// save attribute meta
	if ( isset( $_POST['_linked_by_attributes'] ) ) {
		update_post_meta( $post_id, '_linked_by_attributes', array_filter( $_POST['_linked_by_attributes'], 'intval' ) );
	} else {
		update_post_meta( $post_id, '_linked_by_attributes', array() );
	}

	// save show image meta
	if ( isset( $_POST['show_images'] ) ) {
		update_post_meta( $post_id, 'show_images', array_filter( $_POST['show_images'], 'intval' ) );
	} else {
		update_post_meta( $post_id, 'show_images', array() );
	}

	// save is primary meta
	if ( isset( $_POST['is_primary'] ) ) {
		update_post_meta( $post_id, 'is_primary', array_filter( $_POST['is_primary'], 'intval' ) );
	} else {
		update_post_meta( $post_id, 'is_primary', array() );
	}
}
add_action( 'save_post', 'lvfw_save_post_hook', 10, 1 );
