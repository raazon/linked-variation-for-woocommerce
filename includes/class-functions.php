<?php defined('ABSPATH') || die('Cheatin&#8217; uh?'); // Cannot access pages directly.
/**
 * The WooLinkedVariation main Class.
 */
class WooLinkedVariation
{
    // Hold the class instance.
    private static $instance = null;

    // The object is created from within the class itself
    // only if the class has no instance.
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new WooLinkedVariation();
        }

        return self::$instance;
    }

    // to prevent initiation with outer code.
    public function __construct()
    {
        if (is_admin() && is_plugin_active('woocommerce/woocommerce.php')) {
            add_action('init', array(__CLASS__, 'create_woolinkedvariation_cpt'), 10, 1);
            add_action('add_meta_boxes', array(__CLASS__, 'add_meta_box'), 10, 1);
            add_action('save_post', array(__CLASS__, 'save'), 10, 1);

            add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'), 10, 1);
            add_action('wp_ajax_linked_by_attributes_ordering', array(__CLASS__, 'linked_by_attributes_ordering'));
        }

        add_action('woocommerce_before_add_to_cart_form', array($this, 'render_linked_variation_2'), 10, 0);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'frontend_enqueue_scripts'), 10, 1);

        if (is_admin() && !is_plugin_active('woocommerce/woocommerce.php')) {
            add_action('admin_notices', array(__CLASS__, 'admin_notice_warning'));
        }

        add_filter('plugin_action_links_' . LVFW_BASENAME, array(__CLASS__, 'add_plugin_action_links'));
    }

    // Register Custom Post Type Woo Linked Variation
    public static function create_woolinkedvariation_cpt()
    {
        $labels = array(
            'name' => _x('Woo Linked Variations', 'Post Type General Name', 'linked-variation-for-woocommerce'),
            'singular_name' => _x('Woo Linked Variation', 'Post Type Singular Name', 'linked-variation-for-woocommerce'),
            'menu_name' => _x('Woo Linked Variations', 'Admin Menu text', 'linked-variation-for-woocommerce'),
            'name_admin_bar' => _x('Woo Linked Variation', 'Add New on Toolbar', 'linked-variation-for-woocommerce'),
            'archives' => __('Woo Linked Variation Archives', 'linked-variation-for-woocommerce'),
            'attributes' => __('Woo Linked Variation Attributes', 'linked-variation-for-woocommerce'),
            'parent_item_colon' => __('Parent Woo Linked Variation:', 'linked-variation-for-woocommerce'),
            'all_items' => __('Woo Linked Variations', 'linked-variation-for-woocommerce'),
            'add_new_item' => __('Add New Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'add_new' => __('Add New Linked Variation', 'linked-variation-for-woocommerce'),
            'new_item' => __('New Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'edit_item' => __('Edit Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'update_item' => __('Update Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'view_item' => __('View Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'view_items' => __('View Woo Linked Variations', 'linked-variation-for-woocommerce'),
            'search_items' => __('Search Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'not_found' => __('No linked variations found', 'linked-variation-for-woocommerce'),
            'not_found_in_trash' => __('Not found in Trash', 'linked-variation-for-woocommerce'),
            'featured_image' => __('Featured Image', 'linked-variation-for-woocommerce'),
            'set_featured_image' => __('Set featured image', 'linked-variation-for-woocommerce'),
            'remove_featured_image' => __('Remove featured image', 'linked-variation-for-woocommerce'),
            'use_featured_image' => __('Use as featured image', 'linked-variation-for-woocommerce'),
            'insert_into_item' => __('Insert into Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'uploaded_to_this_item' => __('Uploaded to this Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'items_list' => __('Woo Linked Variations list', 'linked-variation-for-woocommerce'),
            'items_list_navigation' => __('Woo Linked Variations list navigation', 'linked-variation-for-woocommerce'),
            'filter_items_list' => __('Filter Woo Linked Variations list', 'linked-variation-for-woocommerce'),
        );
        $args = array(
            'label' => __('Woo Linked Variation', 'linked-variation-for-woocommerce'),
            'description' => __('WooCommerce Linked Variations', 'linked-variation-for-woocommerce'),
            'labels' => $labels,
            'menu_icon' => 'dashicons-admin-links',
            'supports' => array('title'),
            'taxonomies' => array(),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=product',
            'menu_position' => 5,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'exclude_from_search' => true,
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );
        register_post_type('woolinkedvariation', $args);
    }

    // Adds the meta box container.
    public static function add_meta_box($post_type)
    {
        $post_types = ['woolinkedvariation'];
        if (in_array($post_type, $post_types)) {
            add_meta_box(
                'general-settings',
                __('General Settings', 'linked-variation-for-woocommerce'),
                array(__CLASS__, 'render_meta_box_content'),
                $post_type,
                'advanced',
                'high'
            );
        }
    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public static function render_meta_box_content($post)
    {
        // Add an nonce field so we can check for it later.
        wp_nonce_field('woo_linked_variation_products_nonce_action', 'woo_linked_variation_products_nonce');
        $linked_variation_products = get_post_meta($post->ID, 'linked_variation_products', true) ? get_post_meta($post->ID, 'linked_variation_products', true) : [];

        $attributes = wp_list_pluck(wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_id');
        $_linked_by_attributes = get_post_meta($post->ID, '_linked_by_attributes', true) ? get_post_meta($post->ID, '_linked_by_attributes', true) : [];
        $show_images = get_post_meta($post->ID, 'show_images', true) ? get_post_meta($post->ID, 'show_images', true) : [];
        $is_primary = get_post_meta($post->ID, 'is_primary', true) ? get_post_meta($post->ID, 'is_primary', true) : [];
        $_linked_by_attributes_ordering = get_post_meta($post->ID, '_linked_by_attributes_ordering', true);
        if ($attributes && $_linked_by_attributes_ordering) {
            uksort($attributes, function ($key1, $key2) use ($_linked_by_attributes_ordering) {
                return (array_search($key1, $_linked_by_attributes_ordering) > array_search($key2, $_linked_by_attributes_ordering));
            });
        }

        // get all products
        $get_products = get_posts(
            [
                'post_type' => 'product',
                'posts_per_page' => -1,
            ]
        );
        $products = wp_list_pluck($get_products, 'post_title', 'ID'); ?>

        <div class="woocommerce_options_panel">
            <?php if ($products) : ?>
                <div class="meta-box-item">
                    <label class="widefat" for="_linked_variation_products"><?php esc_attr_e('Select Products', 'linked-variation-for-woocommerce'); ?></label>
                    <select id="_linked_variation_products" class="linked_variation_products" name="linked_variation_products[]" multiple="multiple">
                        <?php foreach ($products as $key => $product) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected(in_array($key, $linked_variation_products)) ?>><?php echo esc_html($product); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($attributes) : ?>
                    <div class="meta-box-item">
                        <label class="widefat" for="_linked_by_attributes"><?php esc_html_e('Linked by (attributes)', 'linked-variation-for-woocommerce'); ?></label>
                        <ul id="sortable" data-id="<?php echo esc_attr($post->ID); ?>">
                            <?php foreach ($attributes as $key => $attribute) : ?>
                                <li id="<?php echo esc_attr($key); ?>" class="ui-state-default">
                                    <div class="inputs">
                                        <label for="attribute-<?php echo esc_attr($key); ?>">
                                            <input type="checkbox" name="_linked_by_attributes[]" id="attribute-<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $_linked_by_attributes)) ?>>
                                            <?php echo esc_attr($attribute); ?>
                                        </label>
                                        <label for="show-image-<?php echo esc_attr($key); ?>">
                                            <input type="checkbox" name="show_images[]" id="show-image-<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $show_images)) ?>>
                                            <?php esc_html_e('Show images', 'linked-variation-for-woocommerce'); ?>
                                        </label>
                                        <label for="is-primary-<?php echo esc_attr($key); ?>">
                                            <input type="checkbox" name="is_primary[]" id="is-primary-<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $is_primary)) ?>>
                                            <?php esc_html_e('Primary', 'linked-variation-for-woocommerce'); ?>
                                        </label>
                                    </div>
                                    <span class="dashicons dashicons-move"></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else : ?>
                    <p><?php esc_attr_e('No attribute found.', 'linked-variation-for-woocommerce'); ?></p>
                <?php endif; ?>

            <?php else : ?>
                <p><?php esc_attr_e('No product found.', 'linked-variation-for-woocommerce'); ?></p>
            <?php endif; ?>

        </div>


        <?php
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public static function save($post_id)
    {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST['woo_linked_variation_products_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['woo_linked_variation_products_nonce'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'woo_linked_variation_products_nonce_action')) {
            return $post_id;
        }

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // unlink previous values
        $linked_variation_products = get_post_meta($post_id, 'linked_variation_products', true);
        if ($linked_variation_products) {
            foreach ($linked_variation_products as $linked_variation_product) {
                update_post_meta($linked_variation_product, 'linked_variation_id', '');
            }
        }

        // Update the linked_variation_products meta field.
        if (isset($_POST['linked_variation_products'])) {
            update_post_meta($post_id, 'linked_variation_products', array_filter($_POST['linked_variation_products'], 'intval'));
            foreach ($_POST['linked_variation_products'] as $linked_variation_product) {
                if (intval($linked_variation_product) && intval($post_id)) {
                    update_post_meta(intval($linked_variation_product), 'linked_variation_id', intval($post_id));
                }
            }
        } else {
            update_post_meta($post_id, 'linked_variation_products', []);
        }

        // save attribute meta
        if (isset($_POST['_linked_by_attributes'])) {
            update_post_meta($post_id, '_linked_by_attributes', array_filter($_POST['_linked_by_attributes'], 'intval'));
        } else {
            update_post_meta($post_id, '_linked_by_attributes', []);
        }

        // save show image meta
        if (isset($_POST['show_images'])) {
            update_post_meta($post_id, 'show_images', array_filter($_POST['show_images'], 'intval'));
        } else {
            update_post_meta($post_id, 'show_images', []);
        }

        // save is primary meta
        if (isset($_POST['is_primary'])) {
            update_post_meta($post_id, 'is_primary', array_filter($_POST['is_primary'], 'intval'));
        } else {
            update_post_meta($post_id, 'is_primary', []);
        }
    }

    // admin enqueue scripts
    public static function admin_enqueue_scripts($hook)
    {
        // get current admin screen, or null
        $screen = get_current_screen();
        // verify admin screen object
        if (is_object($screen)) {
            // enqueue only for specific post types
            if (in_array($screen->post_type, ['woolinkedvariation'])) {
                wp_enqueue_style('select2', plugins_url('assets/css/select2.min.css', LVFW_FILE), []);
                wp_enqueue_script('select2', plugins_url('assets/js/select2.min.js', LVFW_FILE), ['jquery']);
                wp_enqueue_script('woo-linked-variation', plugins_url('assets/js/woo-linked-variation.js', LVFW_FILE), ['jquery']);
                wp_localize_script(
                    'woo-linked-variation',
                    'linked_variation_ajax_object',
                    array(
                        'ajax_url' => admin_url('admin-ajax.php')
                    )
                );
                wp_enqueue_style('jquery-ui', plugins_url('assets/css/jquery-ui.min.css', LVFW_FILE), []);
                wp_enqueue_style('woo-linked-variation', plugins_url('assets/css/woo-linked-variation.css', LVFW_FILE), []);
            }
        }
    }

    // update linked_by_attributes_ordering ajax function
    public static function linked_by_attributes_ordering()
    {
        $ordering = isset($_POST['ordering']) ? $_POST['ordering'] : '';
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : '';

        // Check the user's permissions.
        if ('page' == get_post_type($post_id)) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        if (isset($ordering)) {
            update_post_meta($post_id, '_linked_by_attributes_ordering', array_filter($_POST['ordering'], 'intval'));
        }

        die();
    }

    // render linked variation
    public static function render_linked_variation()
    {
        // get linked variation
        $product = wc_get_product(get_the_ID());
        $linked_variation_id = get_post_meta($product->get_id(), 'linked_variation_id', true);
        if (!$linked_variation_id || 'publish' !== get_post_status($linked_variation_id)) {
            return;
        }

        // get linked by (attributes) value
        $_linked_by_attributes = get_post_meta($linked_variation_id, '_linked_by_attributes', true);
        $show_images = get_post_meta($linked_variation_id, 'show_images', true);

        // get grouped products
        $linked_variation_products = get_post_meta($linked_variation_id, 'linked_variation_products', true);
        $exclude_current_product = array_diff($linked_variation_products, array($product->get_id()));

        // $attributeX = wc_get_attribute($_linked_by_attribute);
        $filter_assigned_attributes = array_filter($product->get_attributes(), 'wc_attributes_array_filter_visible');
        $get_assigned_attributes = array_keys($filter_assigned_attributes);
        $product_attributes = [];
        foreach ($get_assigned_attributes as $get_assigned_attribute) {
            $product_attributes[$get_assigned_attribute] = wc_get_product_terms($product->get_id(), $get_assigned_attribute, array('fields' => 'slugs'));
        }

        // render variations
        if ($_linked_by_attributes && !empty($product_attributes)) : ?>
            <div class="woo-linked-variation-wrap">
                <?php
                var_dump($_linked_by_attributes);
                $all_taxonomies = [];
                foreach ($_linked_by_attributes as $key => $_linked_by_attribute) :
                    $attribute = wc_get_attribute($_linked_by_attribute);
                    $terms = get_terms($attribute->slug, array(
                        'hide_empty' => true,
                    ));
                    $current_term = $product->get_attribute($attribute->slug);
                    array_push($all_taxonomies, $attribute->slug);
                ?>
                    <div class="woo-linked-variation">
                        <div class="linked-variation-label">
                            <strong class="variation-label"><?php echo esc_html($attribute->name); ?>: </strong>
                            <span class="variation-selection" data-variant="<?php echo esc_attr($current_term); ?>"><?php echo esc_html($current_term); ?></span>
                        </div>
                        <?php if ($terms) : ?>
                            <div class="linked-variations">
                                <ul class="linked-variations-buttons">
                                    <?php foreach ($terms as $term_key => $term) : ?>
                                        <?php if ($key === 0) : ?>
                                            <li class="linked-variations-item" data-variant="<?php echo esc_attr($term->name); ?>">
                                                <?php if ($current_term === $term->name) :
                                                    $tax_query_count = 0;
                                                    $tax_query = [];
                                                    foreach ($product_attributes as $product_attribute_key => $product_attribute) {
                                                        $tax_query[$tax_query_count]['taxonomy']    = $product_attribute_key;
                                                        $tax_query[$tax_query_count]['field']       = 'slug';
                                                        $tax_query[$tax_query_count]['terms']       = $product_attribute;
                                                        $tax_query[$tax_query_count]['operator']    = 'IN';
                                                        $tax_query_count++;
                                                    }

                                                    $filter_product_id = self::get_filter_product_id(
                                                        [
                                                            'relation' => 'AND',
                                                            $tax_query
                                                        ],
                                                        [$product->get_id()]
                                                    );
                                                    if ($filter_product_id) : ?>
                                                        <span class="variation-item active-variation" title="<?php echo esc_attr(get_the_title($filter_product_id)); ?>">
                                                            <?php if (in_array($_linked_by_attribute, $show_images)) : ?>
                                                                <img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id($filter_product_id), 'thumbnail') ?>" alt="<?php echo esc_attr($term->name); ?>" />
                                                            <?php else : ?>
                                                                <?php echo esc_html($term->name);  ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php endif;
                                                else :
                                                    $tax_query_count = 0;
                                                    $tax_query = [];
                                                    foreach ($product_attributes as $product_attribute_key => $product_attribute) {
                                                        if ($term->taxonomy != $product_attribute_key) {
                                                            $tax_query[$tax_query_count]['taxonomy']    = $product_attribute_key;
                                                            $tax_query[$tax_query_count]['field']       = 'slug';
                                                            $tax_query[$tax_query_count]['terms']       = $product_attribute;
                                                            $tax_query[$tax_query_count]['operator']    = 'IN';
                                                            $tax_query_count++;
                                                        }
                                                    }

                                                    $filter_product_id = self::get_filter_product_id(
                                                        [
                                                            'relation' => 'AND',
                                                            [
                                                                'taxonomy'        => $term->taxonomy,
                                                                'field'           => 'slug',
                                                                'terms'           => [$term->slug],
                                                                'operator'        => 'IN',
                                                            ],
                                                            $tax_query
                                                        ],
                                                        $exclude_current_product
                                                    );
                                                    if ($filter_product_id) : ?>
                                                        <a href="<?php echo get_the_permalink($filter_product_id); ?>" title="<?php echo esc_attr(get_the_title($filter_product_id)); ?>" class="variation-item">
                                                            <?php if (in_array($_linked_by_attribute, $show_images)) : ?>
                                                                <img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id($filter_product_id), 'thumbnail') ?>" alt="<?php echo esc_attr($term->name); ?>" />
                                                            <?php else : ?>
                                                                <?php echo esc_html($term->name); ?>
                                                            <?php endif; ?>
                                                        </a>
                                                <?php endif;
                                                endif; ?>
                                            </li>
                                        <?php else : ?>
                                            <li class="linked-variations-item" data-variant="<?php echo esc_attr($term->name); ?>">
                                                <?php if ($current_term === $term->name) :
                                                    $tax_query_count = 0;
                                                    $tax_query = [];
                                                    foreach ($product_attributes as $product_attribute_key => $product_attribute) {
                                                        $tax_query[$tax_query_count]['taxonomy']    = $product_attribute_key;
                                                        $tax_query[$tax_query_count]['field']       = 'slug';
                                                        $tax_query[$tax_query_count]['terms']       = $product_attribute;
                                                        $tax_query[$tax_query_count]['operator']    = 'IN';
                                                        $tax_query_count++;
                                                    }

                                                    $filter_product_id = self::get_filter_product_id(
                                                        [
                                                            'relation' => 'AND',
                                                            $tax_query
                                                        ],
                                                        [$product->get_id()]
                                                    );
                                                    if ($filter_product_id) : ?>
                                                        <span class="variation-item active-variation" title="<?php echo esc_attr(get_the_title($filter_product_id)); ?>">
                                                            <?php if (in_array($_linked_by_attribute, $show_images)) : ?>
                                                                <img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id($filter_product_id), 'thumbnail') ?>" alt="<?php echo esc_attr($term->name); ?>" />
                                                            <?php else : ?>
                                                                <?php echo esc_html($term->name);  ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php endif;
                                                else :
                                                    $tax_query_count = 0;
                                                    $tax_query = [];
                                                    foreach ($product_attributes as $product_attribute_key => $product_attribute) {
                                                        if ($term->taxonomy != $product_attribute_key) {
                                                            $tax_query[$tax_query_count]['taxonomy']    = $product_attribute_key;
                                                            $tax_query[$tax_query_count]['field']       = 'slug';
                                                            $tax_query[$tax_query_count]['terms']       = $product_attribute;
                                                            $tax_query[$tax_query_count]['operator']    = 'IN';
                                                            $tax_query_count++;
                                                        }
                                                    }

                                                    $filter_product_id = self::get_filter_product_id(
                                                        [
                                                            'relation' => 'AND',
                                                            [
                                                                'taxonomy'        => $term->taxonomy,
                                                                'field'           => 'slug',
                                                                'terms'           => [$term->slug],
                                                                'operator'        => 'IN',
                                                            ],
                                                            $tax_query
                                                        ],
                                                        $exclude_current_product
                                                    );
                                                    if ($filter_product_id) : ?>
                                                        <a href="<?php echo get_the_permalink($filter_product_id); ?>" title="<?php echo esc_attr(get_the_title($filter_product_id)); ?>" class="variation-item">
                                                            <?php if (in_array($_linked_by_attribute, $show_images)) : ?>
                                                                <img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id($filter_product_id), 'thumbnail') ?>" alt="<?php echo esc_attr($term->name); ?>" />
                                                            <?php else : ?>
                                                                <?php echo esc_html($term->name); ?>
                                                            <?php endif; ?>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
<?php endif;
    }






    // Get linked variations
    public function get_linked_variations($linked_variation_id = '')
    {

        // get products
        $linked_variation_products = get_post_meta($linked_variation_id, 'linked_variation_products', true);

        // get linked by (attributes) value by vaiation id
        $_linked_by_attributes = get_post_meta($linked_variation_id, '_linked_by_attributes', true);
        $show_images = get_post_meta($linked_variation_id, 'show_images', true);

        // process variations
        $attributes = [];
        if ($_linked_by_attributes) {
            foreach ($_linked_by_attributes as $key => $_linked_by_attribute) {
                $attribute = wc_get_attribute($_linked_by_attribute);
                array_push($attributes, $attribute->slug);
                $attributes[$key] = [
                    'id'            => $attribute->id,
                    'name'          => $attribute->name,
                    'slug'          => $attribute->slug,
                    'show_image'    => in_array($_linked_by_attribute, $show_images) ? true : false,
                    'products'      => $this->get_products_by_variations($attribute->slug, $linked_variation_products),
                ];
            }
        }

        return $attributes;
    }

    // Get products by variations
    public function get_products_by_variations($taxonomy = '', $linked_variation_products = [])
    {

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'post__in'       => $linked_variation_products,
            'tax_query'      => [
                [
                    'taxonomy'  => $taxonomy,
                    'field'     => 'slug',
                    'terms'     =>  [],
                    'operator'  => 'EXISTS',
                ]
            ]
        ];

        $getProducts = get_posts($args);

        return $getProducts ? wp_list_pluck($getProducts, 'ID') : [];
    }

    // Get variation name
    public function get_variation_data($product_id = '', $taxonomy = '', $field = 'name')
    {

        $terms = wc_get_product_terms($product_id, $taxonomy);

        if ($terms) {
            $termsArray = (array) $terms[0];
            return $termsArray[$field];
        }

        return false;
    }

    public function render_linked_variation_2()
    {
        // get linked variation
        $linked_variation_id = get_post_meta(get_the_ID(), 'linked_variation_id', true);
        if (!$linked_variation_id || 'publish' !== get_post_status($linked_variation_id)) {
            return;
        }

        $variations = $this->get_linked_variations($linked_variation_id);

        echo '<pre>';
        var_dump($variations);
        echo '</pre>';

        if ($variations) :

            if (file_exists(LVFW_INCLUDE_PATH . 'layouts/layout-1.php')) {
                include_once LVFW_INCLUDE_PATH . 'layouts/layout-1.php';
            } else {
                esc_html_e('Layout file not found.', 'linked-variation-for-woocommerce');
            }

        endif;
    }

    // return post id
    public static function get_filter_product_id($tax_query, $exclude_current_product = [], $order = 'ASC')
    {
        $args =  [
            'post_type'         => 'product',
            'posts_per_page'    => -1,
            'order'             => $order,
        ];

        if ($tax_query) {
            $args['tax_query'] = $tax_query;
        }

        if ($exclude_current_product) {
            $args['post__in'] = $exclude_current_product;
        }

        $filter_product = get_posts($args);

        if ($filter_product) {
            return $filter_product[0]->ID;
        } else {
            return false;
        }
    }

    // Enqueue scripts
    public static function frontend_enqueue_scripts($hook)
    {
        if (is_product()) {
            wp_enqueue_script('woo-linked-variation-frontend', plugins_url('assets/js/woo-linked-variation-frontend.js', LVFW_FILE), ['jquery']);
            wp_enqueue_style('woo-linked-variation-frontend', plugins_url('assets/css/woo-linked-variation-frontend.css', LVFW_FILE), []);
        }
    }

    // Show a messsage if WooCommerce plugin is deactive
    public static function admin_notice_warning()
    {
        $plugin_data = get_plugin_data(LVFW_FILE);
        printf(
            '<div class="notice notice-warning is-dismissible"><p>%1$s %2$s</p></div>',
            $plugin_data['Name'],
            __('not operational, This plugin only work when <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce plugin</a> is active.', 'linked-variation-for-woocommerce')
        );
    }

    // Applied to the list of links to display on the plugins page (beside the activate/deactivate links).
    public static function add_plugin_action_links($links)
    {
        $links = array_merge(array(
            '<a href="' . esc_url(admin_url('/edit.php?post_type=woolinkedvariation')) . '">' . __('Variations', 'linked-variation-for-woocommerce') . '</a>'
        ), $links);

        return $links;
    }
}

WooLinkedVariation::getInstance();
