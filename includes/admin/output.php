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
			'products'   => array( 1, 2 ),
			'categories' => array(),
			'tags'       => array(),
		),
		array(
			'source'     => 'categories',
			'products'   => array(),
			'categories' => array( 9, 10 ),
			'tags'       => array(),
		),
	);
}


?>
<div class="linked-variations">
	<?php
	foreach ( $linked_variations as $key => $link ) :
		$source = isset( $link['source'] ) ? $link['source'] : 'products';
		?>
		<!-- variation -->
		<div class="linked-variation">
			<div class="linked-variation-source">
				<label>
					<?php echo esc_html__( 'Variation Source', 'linked-variation-for-woocommerce' ); ?>
					<select name="linked_variations[<?php echo esc_attr( $key ); ?>]source">
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
				</label>
			</div>
		</div>
		<!-- variation end -->
	<?php endforeach; ?>
</div>