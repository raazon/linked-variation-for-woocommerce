<?php
/**
 * WooLinkedVariation functions.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

class WooLinkedVariation {

	// Hold the class instance.
	private static $instance = null;

	/**
	 * The object is created from within the class itself
	 * only if the class has no instance.
	 *
	 * @return void
	 */
	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new WooLinkedVariation();
		}

		return self::$instance;
	}

	// To prevent initiation with outer code.
	public function __construct() {
		if ( is_admin() && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'wp_ajax_linked_by_attributes_ordering', array( $this, 'linked_by_attributes_ordering' ) );
		}

		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'render_linked_variation_frontend' ), 10, 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ), 10, 1 );
	}

	// Update linked_by_attributes_ordering ajax function.
	public function linked_by_attributes_ordering() {
		$ordering = isset( $_POST['ordering'] ) ? $_POST['ordering'] : '';
		$post_id  = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

		// Check the user's permissions.
		if ( 'page' == get_post_type( $post_id ) ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
		}

		if ( isset( $ordering ) ) {
			update_post_meta( $post_id, '_linked_by_attributes_ordering', array_filter( $_POST['ordering'], 'intval' ) );
		}

		die();
	}

	// Get variation name.
	public function get_variation_data( $product_id = '', $taxonomy = '', $field = 'name' ) {

		$terms = wc_get_product_terms( $product_id, $taxonomy );

		if ( $terms ) {
			$termsArray = (array) $terms[0];
			return $termsArray[ $field ];
		}

		return false;
	}

	// Shorting variations - 1
	public function shorting_variations( $linked_variation_id = '' ) {

		// get linked by (attributes) value by vaiation id
		$_linked_by_attributes = get_post_meta( $linked_variation_id, '_linked_by_attributes', true );
		$show_images           = get_post_meta( $linked_variation_id, 'show_images', true );
		$is_primary            = get_post_meta( $linked_variation_id, 'is_primary', true );

		// process variations
		$attributes = array();
		if ( $_linked_by_attributes ) {
			foreach ( $_linked_by_attributes as $key => $_linked_by_attribute ) {
				$attribute          = wc_get_attribute( $_linked_by_attribute );
				$primary            = in_array( $_linked_by_attribute, $is_primary ) ? true : false;
				$attributes[ $key ] = array(
					'id'         => $attribute->id,
					'name'       => $attribute->name,
					'slug'       => $attribute->slug,
					'show_image' => in_array( $_linked_by_attribute, $show_images ) ? true : false,
					'is_primary' => $primary,
				);
			}
		}

		return $attributes;
	}

	// Filter by normal tax query - 2.
	public function build_tax_query( $attributes = array() ) {

		$tax_query       = array();
		$tax_query_count = 0;
		foreach ( $attributes as $attribute ) {
			if ( $attribute['is_primary'] ) {
				$tax_query[ $tax_query_count ]['taxonomy'] = $attribute['slug'];
				$tax_query[ $tax_query_count ]['field']    = 'slug';
				$tax_query[ $tax_query_count ]['terms']    = array();
				$tax_query[ $tax_query_count ]['operator'] = 'EXISTS';
				++$tax_query_count;
			} else {
				$current_variation_name                    = $this->get_variation_data( get_the_ID(), $attribute['slug'], 'slug' );
				$tax_query[ $tax_query_count ]['taxonomy'] = $attribute['slug'];
				$tax_query[ $tax_query_count ]['field']    = 'slug';
				$tax_query[ $tax_query_count ]['terms']    = array( $current_variation_name );
				$tax_query[ $tax_query_count ]['operator'] = 'IN';
				++$tax_query_count;
			}
			++$tax_query_count;
		}

		return $tax_query;
	}

	// Get products by variations - 3.
	public function get_products_by_variations( $attributes = array(), $attribute = array(), $linked_variation_products = array() ) {

		$tax_query       = array();
		$tax_query_count = 0;
		if ( $attribute['is_primary'] ) {
			$tax_query = $this->build_tax_query( $attributes );
		} else {
			foreach ( $attributes as $attribute ) {
				$current_variation_name                    = $this->get_variation_data( get_the_ID(), $attribute['slug'], 'slug' );
				$tax_query[ $tax_query_count ]['taxonomy'] = $attribute['slug'];
				$tax_query[ $tax_query_count ]['field']    = 'slug';
				$tax_query[ $tax_query_count ]['terms']    = $attribute['is_primary'] ? array( $current_variation_name ) : array();
				$tax_query[ $tax_query_count ]['operator'] = $attribute['is_primary'] ? 'IN' : 'EXISTS';
				++$tax_query_count;
			}
		}

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'post__in',
			'post__in'       => $linked_variation_products,
		);

		if ( $tax_query ) {
			$args['tax_query'] = array(
				'relation' => 'AND',
				$tax_query,
			);
		}

		$getProducts = get_posts( $args );

		return $getProducts ? wp_list_pluck( $getProducts, 'ID' ) : array();
	}

	// Get linked variations - 4.
	public function get_linked_variations() {

		// get linked variation
		$linked_variation_id = get_post_meta( get_the_ID(), 'linked_variation_id', true );
		if ( ! $linked_variation_id || 'publish' !== get_post_status( $linked_variation_id ) ) {
			return false;
		}

		// get products
		$linked_variation_products = get_post_meta( $linked_variation_id, 'linked_variation_products', true );

		// get variations
		$attributes = $this->shorting_variations( $linked_variation_id );

		// process variations
		if ( $attributes ) {
			foreach ( $attributes as $key => $attribute ) {
				$attributes[ $key ]['products'] = $this->get_products_by_variations( $attributes, $attribute, $linked_variation_products );
			}
		}

		return $attributes;
	}

	// Get linked products - 5.
	public function get_linked_products() {

		// Get linked variation
		$linked_variation_id = get_post_meta( get_the_ID(), 'linked_variation_id', true );
		if ( ! $linked_variation_id || 'publish' !== get_post_status( $linked_variation_id ) ) {
			return false;
		}

		// Get products.
		$linked_variation_products = get_post_meta( $linked_variation_id, 'linked_variation_products', true );

		return $linked_variation_products;
	}

	// Rnder linked variation.
	public function render_linked_variation_frontend() {
		// get linked variations
		$variations = $this->get_linked_variations();

		// get linked products
		$products = $this->get_linked_products();

		if ( $variations ) :

			if ( file_exists( LVFW_INCLUDE_PATH . 'templates/variations.php' ) ) {
				include_once LVFW_INCLUDE_PATH . 'templates/variations.php';
			} else {
				esc_html_e( 'Variations template file not found.', 'linked-variation-for-woocommerce' );
			}

		elseif ( $products ) :

			if ( file_exists( LVFW_INCLUDE_PATH . 'templates/products.php' ) ) {
				include_once LVFW_INCLUDE_PATH . 'templates/products.php';
			} else {
				esc_html_e( 'Products template file not found.', 'linked-variation-for-woocommerce' );
			}

		endif;
	}

	// Enqueue scripts.
	public function frontend_enqueue_scripts( $hook ) {
		if ( is_product() ) {
			wp_enqueue_script(
				'woo-linked-variation-frontend',
				plugins_url( 'assets/js/woo-linked-variation-frontend.js', LVFW_FILE ),
				array( 'jquery' ),
				LVFW_VERSION
			);
			wp_enqueue_style(
				'woo-linked-variation-frontend',
				plugins_url( 'assets/css/woo-linked-variation-frontend.css', LVFW_FILE ),
				array(),
				LVFW_VERSION
			);
		}
	}
}

WooLinkedVariation::get_instance();
