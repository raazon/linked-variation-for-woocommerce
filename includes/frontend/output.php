<?php
/**
 * Frontend output for linked variations.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' ); // Cannot access pages directly.
?>

<div class="lvfw-attributes">
	<?php foreach ( $filtered_variations as $attribute_key => $options ) : ?>
		<?php
		// Get a readable label.
		$label = wc_attribute_label( $attribute_key );
		// Check if images should be shown.
		$show_images = lvfw_should_show_images( $attribute_key, $linked_variations );

		// get attribute value by key.
		$current_attribute_names = array();
		foreach ( $product_attributes_keys as $key ) {
			$current_attribute_names[$key] = $product->get_attribute( $key );
		}
		?>
		<div class="lvfw-attribute" role="group" aria-labelledby="<?php echo esc_attr( $label ); ?>-label">
			<span>
				<span class="lvfw-current-label">
					<?php echo esc_html( $label ); ?>:
				</span>
				<?php if ( ! empty( $current_attribute_names[$attribute_key] ) ) : ?>
					<span class="lvfw-current-name">
						<?php echo esc_html( $current_attribute_names[$attribute_key] ); ?>
					</span>
				<?php endif; ?>
			</span>
			<ul class="lvfw-attribute-options">
				<?php foreach ( $options as $value => $product_id ) : ?>
					<?php
					// Get is selected.
					$current         = $value === $current_attributes[ $attribute_key ];
					$attribute_class = 'lvfw-product';
					if ( $current ) {
						$attribute_class .= ' active';
					}

					if ( $show_images ) {
						$attribute_class .= ' lvfw-show-images';
					}
					?>
					<li class="<?php echo esc_attr( $attribute_class ); ?>" data-title="<?php echo esc_attr( $value ); ?>">
						<?php if ( $current ) : ?>
							<span class="lvfw-selected">
								<?php lvfw_display_variation( $show_images, $product_id, $value ); ?>
							</span>
						<?php else : ?>
							<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
								<?php lvfw_display_variation( $show_images, $product_id, $value ); ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>
