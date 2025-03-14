<?php

/**
 * Upgrade functions.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined('ABSPATH') || die('Cheatin&#8217; uh?');

/**
 * Migrate old data.
 *
 * @since 2.0.0
 */
function lvfw_migrate_old_data($upgrader_object, $options)
{
	$our_plugin = 'linked-variation-for-woocommerce/linked-variation-for-woocommerce.php';
	if (!empty($options['plugins']) && $options['action'] == 'update' && $options['type'] == 'plugin') {
		foreach ($options['plugins'] as $plugin) {
			if ($plugin == $our_plugin) {
				$args = array(
					'post_type'      => 'woolinkedvariation',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				);

				$woolinkedvariations = get_posts($args);
				if ($woolinkedvariations) {
					foreach ($woolinkedvariations as $woolv) {
						$old_products = get_post_meta($woolv->ID, 'linked_variation_products', true);
						if (!empty($old_products)) {
							$old_attrs = get_post_meta($woolv->ID, '_linked_by_attributes', true);
							$old_show_images = get_post_meta($woolv->ID, 'show_images', true);
							$is_primary = get_post_meta($woolv->ID, 'is_primary', true);

							$attributes = array();
							if (!empty($old_attrs)) {
								foreach ($old_attrs as $key => $attr) {
									$attributes[$attr] = array(
										'name' => wc_get_attribute($attr)->slug,
									);

									if (in_array($attr, $old_show_images)) {
										$attributes[$attr]['show_image'] = 1;
									}
								}
							}

							$new_data[0] = array(
								'source' => 'products',
								'products' => $old_products,
								'attributes' => $attributes,
							);

							update_post_meta($woolv->ID, 'linked_variations', $new_data);
						}
					}
				}

				set_transient('lvfw_migrated', LVFW_VERSION);
			}
		}
	}
}

add_action('upgrader_process_complete', 'lvfw_migrate_old_data', 10, 2);
