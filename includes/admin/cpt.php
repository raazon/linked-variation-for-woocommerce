<?php
/**
 * Register Custom Post Type.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

/**
 * Register Custom Post Type.
 *
 * @since 2.0.0
 * @package Lvfw
 */
function lvfw_create_woolinkedvariation_cpt() {
	$labels = array(
		'name'                  => _x( 'Linked Variations', 'Post Type General Name', 'linked-variation-for-woocommerce' ),
		'singular_name'         => _x( 'Linked Variation', 'Post Type Singular Name', 'linked-variation-for-woocommerce' ),
		'menu_name'             => _x( 'Linked Variations', 'Admin Menu text', 'linked-variation-for-woocommerce' ),
		'name_admin_bar'        => _x( 'Linked Variation', 'Add New on Toolbar', 'linked-variation-for-woocommerce' ),
		'archives'              => esc_html__( 'Linked Variation Archives', 'linked-variation-for-woocommerce' ),
		'attributes'            => esc_html__( 'Linked Variation Attributes', 'linked-variation-for-woocommerce' ),
		'parent_item_colon'     => esc_html__( 'Parent Linked Variation:', 'linked-variation-for-woocommerce' ),
		'all_items'             => esc_html__( 'Linked Variations', 'linked-variation-for-woocommerce' ),
		'add_new_item'          => esc_html__( 'Add New Linked Variation', 'linked-variation-for-woocommerce' ),
		'add_new'               => esc_html__( 'Add New Linked Variation', 'linked-variation-for-woocommerce' ),
		'new_item'              => esc_html__( 'New Linked Variation', 'linked-variation-for-woocommerce' ),
		'edit_item'             => esc_html__( 'Edit Linked Variation', 'linked-variation-for-woocommerce' ),
		'update_item'           => esc_html__( 'Update Linked Variation', 'linked-variation-for-woocommerce' ),
		'view_item'             => esc_html__( 'View Linked Variation', 'linked-variation-for-woocommerce' ),
		'view_items'            => esc_html__( 'View Linked Variations', 'linked-variation-for-woocommerce' ),
		'search_items'          => esc_html__( 'Search Linked Variation', 'linked-variation-for-woocommerce' ),
		'not_found'             => esc_html__( 'No linked variations found', 'linked-variation-for-woocommerce' ),
		'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'linked-variation-for-woocommerce' ),
		'featured_image'        => esc_html__( 'Featured Image', 'linked-variation-for-woocommerce' ),
		'set_featured_image'    => esc_html__( 'Set featured image', 'linked-variation-for-woocommerce' ),
		'remove_featured_image' => esc_html__( 'Remove featured image', 'linked-variation-for-woocommerce' ),
		'use_featured_image'    => esc_html__( 'Use as featured image', 'linked-variation-for-woocommerce' ),
		'insert_into_item'      => esc_html__( 'Insert into Linked Variation', 'linked-variation-for-woocommerce' ),
		'uploaded_to_this_item' => esc_html__( 'Uploaded to this Linked Variation', 'linked-variation-for-woocommerce' ),
		'items_list'            => esc_html__( 'Linked Variations list', 'linked-variation-for-woocommerce' ),
		'items_list_navigation' => esc_html__( 'Linked Variations list navigation', 'linked-variation-for-woocommerce' ),
		'filter_items_list'     => esc_html__( 'Filter Linked Variations list', 'linked-variation-for-woocommerce' ),
	);

	$args = array(
		'label'               => esc_html__( 'Linked Variation', 'linked-variation-for-woocommerce' ),
		'description'         => esc_html__( 'WooCommerce Linked Variations', 'linked-variation-for-woocommerce' ),
		'labels'              => $labels,
		'menu_icon'           => 'dashicons-admin-links',
		'supports'            => array( 'title', 'revisions' ),
		'taxonomies'          => array(),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=product',
		'menu_position'       => 5,
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'exclude_from_search' => true,
		'show_in_rest'        => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
	);

	register_post_type( 'woolinkedvariation', $args );
}
add_action( 'init', 'lvfw_create_woolinkedvariation_cpt', 10, 1 );
