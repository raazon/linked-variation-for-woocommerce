<?php

/**
 *
 * @link              https://devsace.com/
 * @package           WooLinkedVariation
 *
 * @wordpress-plugin
 * Plugin Name:       Linked Variation for WooCommerce
 * Plugin URI:        https://demos.devsace.com/plugins/linked-variation-for-woocommerce/
 * Description:       This is a helper plugin of WooCommerce built to link separate products together by attributes.
 * Version:           1.0.2
 * Author:            DevsAce
 * Author URI:        https://devsace.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       linked-variation-for-woocommerce
 * Domain Path:       /languages
 * 
 * WC requires at least: 3.8
 * WC tested up to: 5.3.0
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// load once plugin.php file
if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// Load plugin textdomain.
add_action('init', 'woo_linked_variation_load_textdomain');
function woo_linked_variation_load_textdomain()
{
    load_plugin_textdomain('linked-variation-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

// Declering Constant
define('LVFW_VERSION', '1.0.2'); // Currently plugin version.
define('LVFW_API_URL', 'https://api.devsace.com/linked-variation-for-woocommerce'); // Currently plugin version.
define('LVFW_FILE', __FILE__);
define('LVFW_BASENAME', plugin_basename(__FILE__));
define('LVFW_PATH', plugin_dir_path(__FILE__));
define('LVFW_INCLUDE_PATH', trailingslashit(LVFW_PATH) . 'includes/');

// The code that runs during plugin activation.
function activate_woo_linked_variation()
{
    require_once LVFW_PATH . '/includes/class-activator.php';
}
register_activation_hook(__FILE__, 'activate_woo_linked_variation');


// The code that runs during plugin deactivation.
function deactivate_woo_linked_variation()
{
    require_once LVFW_PATH . '/includes/class-deactivator.php';
}
register_deactivation_hook(__FILE__, 'deactivate_woo_linked_variation');


// Loading init file
require_once LVFW_PATH . '/includes/init.php';
