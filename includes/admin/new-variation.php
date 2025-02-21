<?php defined('ABSPATH') || die('Cheatin&#8217; uh?'); // Cannot access pages directly.

// $key = isset($key) ? $key : '';
// $product_attributes = wc_get_attribute_taxonomies();
?>
<div class="linked-variation-item">
	<div class="linked-variation">
		<div class="linked-variation-source">
			<div class="field-label">
				<select class="source-picker" name="source[<?php echo esc_attr($key); ?>]">
					<option value="products" <?php selected($source, 'products'); ?>>
						<?php echo esc_html__('Products', 'linked-variation-for-woocommerce'); ?>
					</option>
					<option value="categories" <?php selected($source, 'categories'); ?>>
						<?php echo esc_html__('Categories', 'linked-variation-for-woocommerce'); ?>
					</option>
					<option value="tags" <?php selected($source, 'tags'); ?>>
						<?php echo esc_html__('Tags', 'linked-variation-for-woocommerce'); ?>
					</option>
				</select>
			</div>
			<div class="field-input">
				<!-- products picker -->
				<select class="products-picker hidden" name="products[<?php echo esc_attr($key); ?>][]" multiple>

				</select>

				<!-- categories picker -->
				<select class="categories-picker hidden" name="categories[<?php echo esc_attr($key); ?>][]" multiple>

				</select>

				<!-- tags picker -->
				<select class="tags-picker hidden" name="tags[<?php echo esc_attr($key); ?>][]" multiple>

				</select>
			</div>
		</div>
	</div>

	<div class="linked-variation">
		<div class="linked-variation-source">
			<div class="field-label">
				<?php echo esc_html__('Linked by attributes', 'linked-variation-for-woocommerce'); ?>
			</div>
			<div class="field-input">
				<div class="attributes">
					<?php if (!empty($product_attributes)) :
						foreach ($product_attributes as $attribute_key => $attribute) : ?>
							<div class="attribute-item">
								<span class="dashicons dashicons-move"></span>
								<label>
									<?php
									$attribute_id = (int) $attribute->attribute_id;
									$attribute_name = wc_attribute_taxonomy_name_by_id($attribute_id);
									$checked_name = isset($attributes[$attribute_id]['name']);
									printf(
										'<input name="attributes[%1$s][%2$s][name]" type="checkbox" value="%3$s" %4$s> %5$s',
										esc_attr($key),
										esc_attr($attribute->attribute_id),
										esc_attr($attribute_name),
										$checked_name ? checked($attributes[$attribute->attribute_id]['name'], $attribute_name, false) : '',
										$attribute->attribute_label,
									);
									?>
								</label>
								<label>
									<?php
									$checked_images = isset($attributes[$attribute_id]['show_images']);
									printf(
										'<input name="attributes[%1$s][%2$s][show_images]" type="checkbox" value="1" %3$s> %4$s',
										esc_attr($key),
										esc_attr($attribute->attribute_id),
										$checked_images ?  checked($attributes[$attribute->attribute_id]['show_images'], 1, false) : '',
										esc_html__('Show Images', 'linked-variation-for-woocommerce')
									);
									?>
								</label>
							</div>
					<?php endforeach;
					endif; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="remove-variation">Remove</div>
</div>