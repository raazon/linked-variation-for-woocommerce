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
				<?php foreach ($options as $value => $product_id) : ?>
					<?php
					// Get is selected
					$current = $value === $current_attributes[$attribute_key];
					$attribute_class = 'lvfw-product';
					if($current) {
						$attribute_class .= ' active';
					}

					if($show_images) {
						$attribute_class .= ' lvfw-show-images';
					}
					?>
					<li class="<?php echo esc_attr($attribute_class); ?>" data-title="<?php echo esc_attr($value); ?>">
						<?php if ($current) : ?>
							<span class="lvfw-selected">
								<?php lvfw_display_variation($show_images, $product_id, $value); ?>
							</span>
						<?php else : ?>
							<a href="<?php echo esc_url(get_permalink($product_id)); ?>">
								<?php lvfw_display_variation($show_images, $product_id, $value); ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>

<style>
	.lvfw-attributes {
		display: flex;
		flex-direction: column;
		gap: 20px;
	}

	.lvfw-attribute strong {
		display: block;
	}

	.lvfw-attribute ul {
		margin: 0;
		padding: 0;
		list-style: none;
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
	}

	.lvfw-attribute ul li {
		display: inline-block;
		border: 1px solid #dddddd;
		padding: 4px;
		transition: border-color 0.3s;
		min-width: 50px;
	}

	.lvfw-attribute ul li.active,
	.lvfw-attribute ul li:hover {
		border-color: #017d01;
	}

	.lvfw-attribute ul li :is(a, span) {
		display: block;
		background: #efefef;
	}

	.lvfw-attribute ul li:not(.lvfw-show-images) :is(a, span) {
		padding: 0 4px;
	}

	.lvfw-attribute ul li img {
		width: 50px;
		height: 50px;
	}
</style>