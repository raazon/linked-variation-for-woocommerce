<?php

/**
 * Frontend output for linked variations.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined('ABSPATH') || die('Cheatin&#8217; uh?'); // Cannot access pages directly.
?>

<div class="lvfw-attributes">
	<?php foreach ($filtered_variations as $attribute_key => $options) : ?>
		<?php
		// Get a readable label
		$label = wc_attribute_label($attribute_key);
		// Check if images should be shown
		$show_images = lvfw_should_show_images($attribute_key, $linked_variations);
		?>
		<div class="lvfw-attribute">
			<strong><?php echo esc_html($label); ?></strong>
			<ul class="lvfw-attribute-options">
				<?php foreach ($options as $value => $variation_id) : ?>
					<li data-title="<?php echo esc_attr($value); ?>">
						<?php if ($value === $current_attributes[$attribute_key]) : ?>
							<span class="lvfw-selected"><?php echo esc_html($value); ?></span>
						<?php else : ?>
							<a href="<?php echo esc_url(get_permalink($variation_id)); ?>">
								<?php
								if ($show_images) {
									$variation_product = wc_get_product($variation_id);
									$image_id = $variation_product->get_image_id();
									$image = wp_get_attachment_image($image_id, 'thumbnail');
									echo wp_kses_post($image);
								} else {
									echo esc_html($value);
								}
								?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>