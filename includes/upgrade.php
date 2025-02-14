<?php
/**
 * Upgrade functions.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

/**
 * Migrate old data.
 *
 * @since 2.0.0
 */
function lvfw_migrate_old_data() {
	// Add plugin version to db.
	if ( get_option( 'lvfw_db_version' ) !== LVFW_VERSION ) {
		update_option( 'lvfw_db_version', LVFW_VERSION );

		// Migrate old data.
		$args = array(
			'post_type'      => 'woolinkedvariation',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$woolinkedvariations = get_posts( $args );
		if ( $woolinkedvariations ) {
			foreach ( $woolinkedvariations as $woolinkedvariation ) {
				$is_primary = get_post_meta( $woolinkedvariation->ID, 'is_primary', true );
				if ( ! $is_primary ) {
					$_linked_by_attributes = get_post_meta( $woolinkedvariation->ID, '_linked_by_attributes', true );
					update_post_meta( $woolinkedvariation->ID, 'is_primary', array( $_linked_by_attributes[0] ) );
				}
			}
		}
	}
}
add_action( 'plugins_loaded', 'lvfw_migrate_old_data' );
