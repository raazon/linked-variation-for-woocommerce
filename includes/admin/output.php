<?php
/**
 * Admin output.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

// Add an nonce field so we can check for it later.
wp_nonce_field( 'lvfw_products_nonce_action', 'lvfw_products_nonce' );

$linked_variations = get_post_meta( $post->ID, 'linked_variations', true );

// Check if the retrieved value is valid, otherwise use the default.
if ( empty( $linked_variations ) ) {
	$linked_variations = array(
		array(
			'source'     => 'products',
			'products'   => array(),
			'categories' => array(),
			'tags'       => array(),
			'attributes' => array(),
		),
	);
}

// Return if linked variations is empty.
if ( empty( $linked_variations ) ) {
	return;
}

$product_attributes = wc_get_attribute_taxonomies();
?>
<div class="linked-variations">
	<?php
	foreach ( $linked_variations as $key => $variation ) :
		$source     = isset( $variation['source'] ) ? $variation['source'] : 'products';
		$products   = ( 'products' === $source && isset( $variation['products'] ) ) ? $variation['products'] : array();
		$categories = ( 'categories' === $source && isset( $variation['categories'] ) ) ? $variation['categories'] : array();
		$tags       = ( 'tags' === $source && isset( $variation['tags'] ) ) ? $variation['tags'] : array();
		$attributes = isset( $variation['attributes'] ) ? $variation['attributes'] : array();
		?>
		<div class="linked-variation-item">
			<div class="linked-variation">
				<div class="linked-variation-source">
					<div class="field-label">
						<select class="source-picker" name="source[<?php echo esc_attr( $key ); ?>]">
							<option value="products" <?php selected( $source, 'products' ); ?>>
								<?php echo esc_html__( 'Products', 'linked-variation-for-woocommerce' ); ?>
							</option>
							<option value="categories" <?php selected( $source, 'categories' ); ?>>
								<?php echo esc_html__( 'Categories', 'linked-variation-for-woocommerce' ); ?>
							</option>
							<option value="tags" <?php selected( $source, 'tags' ); ?>>
								<?php echo esc_html__( 'Tags', 'linked-variation-for-woocommerce' ); ?>
							</option>
						</select>
					</div>
					<div class="field-input">
						<!-- products picker -->
						<select class="products-picker hidden" name="products[<?php echo esc_attr( $key ); ?>][]" multiple>
							<?php if ( $products ) : ?>
								<?php foreach ( $products as $product_id ) : ?>
									<option value="<?php echo esc_attr( $product_id ); ?>" selected>
										<?php echo esc_html( get_the_title( $product_id ) ); ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>

						<!-- categories picker -->
						<select class="categories-picker hidden" name="categories[<?php echo esc_attr( $key ); ?>][]" multiple>
							<?php if ( $categories ) : ?>
								<?php foreach ( $categories as $term_id ) : ?>
									<option value="<?php echo esc_attr( $term_id ); ?>" selected>
										<?php echo esc_html( get_term( $term_id )->name ); ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>

						<!-- tags picker -->
						<select class="tags-picker hidden" name="tags[<?php echo esc_attr( $key ); ?>][]" multiple>
							<?php if ( $tags ) : ?>
								<?php foreach ( $tags as $term_id ) : ?>
									<option value="<?php echo esc_attr( $term_id ); ?>" selected>
										<?php echo esc_html( get_term( $term_id )->name ); ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				</div>
			</div>

			<div class="linked-variation">
				<div class="linked-variation-source">
					<div class="field-label">
						<?php echo esc_html__( 'Linked by attributes', 'linked-variation-for-woocommerce' ); ?>
					</div>
					<div class="field-input">
						<div class="attributes">
							<?php
							if ( ! empty( $product_attributes ) ) :
								foreach ( $product_attributes as $attribute_key => $attribute ) :
									?>
									<div class="attribute-item">
										<span class="dashicons dashicons-move"></span>
										<label>
											<?php
											$attribute_id   = (int) $attribute->attribute_id;
											$attribute_name = wc_attribute_taxonomy_name_by_id( $attribute_id );
											$checked_name   = isset( $attributes[ $attribute_id ]['name'] );
											printf(
												'<input name="attributes[%1$s][%2$s][name]" type="checkbox" value="%3$s" %4$s> %5$s',
												esc_attr( $key ),
												esc_attr( $attribute->attribute_id ),
												esc_attr( $attribute_name ),
												$checked_name ? checked( $attributes[ $attribute->attribute_id ]['name'], $attribute_name, false ) : '',
												esc_attr( $attribute->attribute_label ),
											);
											?>
										</label>
										<label>
											<?php
											$checked_images = isset( $attributes[ $attribute_id ]['show_images'] );
											printf(
												'<input name="attributes[%1$s][%2$s][show_images]" type="checkbox" value="1" %3$s> %4$s',
												esc_attr( $key ),
												esc_attr( $attribute->attribute_id ),
												$checked_images ? checked( $attributes[ $attribute->attribute_id ]['show_images'], 1, false ) : '',
												esc_html__( 'Show Images', 'linked-variation-for-woocommerce' )
											);
											?>
										</label>
									</div>
									<?php
								endforeach;
							endif;
							?>
						</div>
					</div>
				</div>
			</div>

			<?php if ( 0 !== $key ) : ?>
				<div class="remove-variation"><?php echo esc_html__( 'Remove', 'linked-variation-for-woocommerce' ); ?></div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

<div class="linked-variation-repeater">
	<div class="linked-variation-source">
		<div class="field-label">
			<button
			class="button button-primary add-variation"
			type="button"
			data-variations="<?php echo is_array( $linked_variations ) ? count( $linked_variations ) : 0; ?>"
			>
				<?php echo esc_html__( 'Add Variation +', 'linked-variation-for-woocommerce' ); ?>
			</button>
		</div>
	</div>
</div>