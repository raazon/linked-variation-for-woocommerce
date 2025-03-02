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
	<?php foreach ($filtered_variations as $attribute_key => $options) :
		$label = wc_attribute_label($attribute_key); // Get a readable label
	?>
		<div class="lvfw-attribute">
			<strong><?php echo esc_html($label); ?></strong>
			<ul class="lvfw-attribute-options">
				<?php foreach ($options as $value => $variation_id) : ?>
					<li>
						<?php if ($value === $current_attributes[$attribute_key]) : ?>
							<span class="lvfw-selected"><?php echo esc_html($value); ?></span>
						<?php else : ?>
							<a href="<?php echo esc_url(get_permalink($variation_id)); ?>">
								<?php echo esc_html($value); ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>