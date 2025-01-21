<?php

/**
 * Plugin Name:       Linked Variation for WooCommerce
 * Plugin URI:        https://raazon.com/
 * Description:       This is a helper plugin of WooCommerce built to link separate products together by attributes.
 * Version:           2.0.0
 * Author:            Razon
 * Author URI:        https://raazon.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       linked-variation-for-woocommerce
 * Domain Path:       /languages
 *
 * WC requires at least: 3.8
 * WC tested up to: 5.3.0
 *
 * @package Lvfw
 */

defined('ABSPATH') || exit; // Prevent direct access

final class WooLinkedVariation
{
	/**
	 * The single instance of the class.
	 *
	 * @var WooLinkedVariation|null
	 */
	private static ?self $instance = null;

	/**
	 * Constructor.
	 *
	 * Private to enforce singleton pattern.
	 */
	private function __construct()
	{
		// Define plugin constants.
		$this->constants();

		// Register the autoloader
		spl_autoload_register([$this, 'autoloader']);
	}

	/**
	 * Prevent cloning of the instance.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing of the instance.
	 *
	 * @throws RuntimeException If unserialization is attempted.
	 */
	public function __wakeup()
	{
		throw new \RuntimeException("Unserializing a singleton is not allowed.");
	}

	/**
	 * Retrieves the single instance of the class.
	 *
	 * @return WooLinkedVariation The instance.
	 */
	public static function get_instance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Define plugin constants.
	 *
	 * @return void
	 */
	public function constants(): void
	{
		define('LVFW_VERSION', '2.0.0');
		define('LVFW_FILE', trailingslashit(__FILE__));
		define('LVFW_BASENAME', plugin_basename(__FILE__));
		define('LVFW_PATH', trailingslashit(plugin_dir_path(__FILE__)));
		define('LVFW_URL', trailingslashit(plugin_dir_url(__FILE__)));
		define('LVFW_INCLUDE_PATH', LVFW_PATH . 'includes/');
	}

	/**
	 * Autoloader method for loading classes from the `inc` directory.
	 *
	 * @param string $class_name Fully-qualified class name.
	 * @return void
	 * @throws RuntimeException If the class file is not found.
	 */
	private function autoloader(string $class_name): void
	{
		// Check if the class belongs to this plugin's namespace.
		$plugin_namespace = 'WooLinkedVariation\\';
		if (strpos($class_name, $plugin_namespace) !== 0) {
			return; // Ignore classes not in the plugin's namespace.
		}

		// Remove the plugin namespace from the class name.
		$relative_class_name = str_replace($plugin_namespace, '', $class_name);

		// Convert namespace separators to directory separators.
		$relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $relative_class_name);

		// Build the full path to the class file.
		$file_path = LVFW_INCLUDE_PATH . $relative_path . '.php';

		// Include the class file if it exists.
		if (file_exists($file_path)) {
			require_once $file_path;
		} else {
			// Optionally log an error or throw an exception.
			wp_die(
				sprintf(
					/* translators: %s: Class file path */
					esc_html__('Class file not found: %s', 'linked-variation-for-woocommerce'),
					esc_html($file_path)
				)
			);
		}
	}
}

// Initialize the plugin.
WooLinkedVariation::get_instance();
