<?php

/**
 * Plugin Name:       Linked Variation for WooCommerce
 * Plugin URI:        https://raazon.com/
 * Description:       This is a helper plugin of WooCommerce built to link separate products together by attributes.
 * Version:           2.0.0
 * Author:            Razon
 * Author URI:        https://raazon.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       linked-variation-for-woocommerce
 * Domain Path:       /languages
 * 
 * WC requires at least: 3.8
 * WC tested up to: 5.3.0
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
	exit;
}

// Define plugin constants.
define('LVFW_VERSION', '2.0.0');
define('LVFW_FILE', trailingslashit(__FILE__));
define('LVFW_BASENAME', plugin_basename(__FILE__));
define('LVFW_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('LVFW_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('LVFW_INCLUDE_PATH', LVFW_PATH . 'includes/');
define('LVFW_API_URL', 'https://api.devsace.com/linked-variation-for-woocommerce');

// Loading init file
require_once LVFW_PATH . '/includes/init.php';
