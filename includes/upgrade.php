<?php
/**
 * Upgrade functions.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

/**
 * Migrates old data for the "Linked Variation for WooCommerce" plugin during plugin updates.
 *
 * This function is hooked to the `upgrader_process_complete` action and is triggered
 * when the plugin is updated. It retrieves all posts of the custom post type
 * `woolinkedvariation`, processes their metadata, and updates them to the new format.
 *
 * @param WP_Upgrader $upgrader_object The upgrader object.
 * @param array       $options         An array of options for the upgrade process.
 *                                     - 'plugins': List of plugins being updated.
 *                                     - 'action': The type of action being performed (e.g., 'update').
 *                                     - 'type': The type of update (e.g., 'plugin').
 *
 * @return void
 */
function lvfw_migrate_old_data( $upgrader_object, $options ) {
	$our_plugin = 'linked-variation-for-woocommerce/linked-variation-for-woocommerce.php';
	if ( ! empty( $options['plugins'] ) && $options['action'] == 'update' && $options['type'] == 'plugin' ) { // phpcs:ignore
		foreach ( $options['plugins'] as $plugin ) {
			if ( $plugin == $our_plugin ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
				$args = array(
					'post_type'      => 'woolinkedvariation',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				);

				$woolinkedvariations = get_posts( $args );
				if ( $woolinkedvariations ) {
					foreach ( $woolinkedvariations as $woolv ) {
						$old_products = get_post_meta( $woolv->ID, 'linked_variation_products', true );
						if ( ! empty( $old_products ) ) {
							$old_attrs       = get_post_meta( $woolv->ID, '_linked_by_attributes', true );
							$old_show_images = get_post_meta( $woolv->ID, 'show_images', true );
							$is_primary      = get_post_meta( $woolv->ID, 'is_primary', true );

							$attributes = array();
							if ( ! empty( $old_attrs ) ) {
								foreach ( $old_attrs as $key => $attr ) {
									$attributes[ $attr ] = array(
										'name' => wc_get_attribute( $attr )->slug,
									);

									if ( in_array( $attr, $old_show_images ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
										$attributes[ $attr ]['show_images'] = '1';
									}
								}
							}

							$new_data[0] = array(
								'source'     => 'products',
								'products'   => $old_products,
								'attributes' => $attributes,
							);

							update_post_meta( $woolv->ID, 'linked_variations', $new_data );
						}
					}
				}

				set_transient( 'lvfw_migrated', LVFW_VERSION );
			}
		}
	}
}

add_action( 'upgrader_process_complete', 'lvfw_migrate_old_data', 10, 2 );
