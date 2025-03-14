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
			<span><?php echo esc_html($label); ?></span>
			<ul class="lvfw-attribute-options">
				<?php foreach ($options as $value => $product_id) : ?>
					<?php
					// Get is selected
					$current = $value === $current_attributes[$attribute_key];
					$attribute_class = 'lvfw-product';
					if ($current) {
						$attribute_class .= ' active';
					}

					if ($show_images) {
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
		/* attributes wrapper */
		--lvfw-attrs-gap: 20px;
		--lvfw-attr-gap: 10px;

		/* attribute items */
		--lvfw-attr-min-width: 50px;
		--lvfw-attr-border-width: 1px;
		--lvfw-attr-border-style: solid;
		--lvfw-attr-border-color: #dddddd;
		--lvfw-attr-border-hover-color: #017d01;
		--lvfw-attr-border: var(--lvfw-attr-border-width) var(--lvfw-attr-border-style) var(--lvfw-attr-border-color);
		--lvfw-attr-padding: 4px;

		/* attribute items inner */
		--lvfw-attr-inner-bg: #efefef;
		--lvfw-attr-inner-padding: 0 4px;

		/* attribute image */
		--lvfw-attr-image-width: 50px;
		--lvfw-attr-image-height: 50px;
	}

	.lvfw-attributes {
		display: flex;
		flex-direction: column;
		gap: var(--lvfw-attrs-gap);
	}

	.lvfw-attribute>span {
		display: block;
	}

	.lvfw-attribute ul {
		margin: 0;
		padding: 0;
		list-style: none;
		display: flex;
		flex-wrap: wrap;
		gap: var(--lvfw-attr-gap);
	}

	.lvfw-attribute ul li {
		display: inline-block;
		border: var(--lvfw-attr-border);
		padding: var(--lvfw-attr-padding);
		transition: border-color 0.3s;
		min-width: var(--lvfw-attr-min-width);
	}

	.lvfw-attribute ul li.active,
	.lvfw-attribute ul li:hover {
		border-color: var(--lvfw-attr-border-hover-color);
	}

	.lvfw-attribute ul li :is(a, span) {
		display: block;
		background: var(--lvfw-attr-inner-bg);
	}

	.lvfw-attribute ul li:not(.lvfw-show-images) :is(a, span) {
		padding: var(--lvfw-attr-inner-padding);
	}

	/* reset css */
	.lvfw-attribute ul li a:focus {
		outline: none;
	}

	.lvfw-attribute ul li img {
		width: var(--lvfw-attr-image-width);
		height: var(--lvfw-attr-image-height);
	}
</style>