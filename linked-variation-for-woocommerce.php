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

defined('ABSPATH') || die('Cheatin&#8217; uh?');

/**
 * Main class file.
 *
 * @since 2.0.0
 */
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

		// Register the autoloader.
		spl_autoload_register(array($this, 'autoloader'));

		// Fired when activate.
		register_activation_hook(LVFW_FILE, array($this, 'activate'));

		// Fired when deactivate.
		register_deactivation_hook(LVFW_FILE, array($this, 'deactivate'));

		// Load plugin textdomain.
		add_action('init', array($this, 'load_textdomain'));

		// Admin notice if Woocommerce is not installed.
		add_action('admin_notices', array($this, 'admin_notice'));

		// Add plugin action links.
		add_filter(
			'plugin_action_links_' . LVFW_BASENAME,
			array($this, 'add_plugin_action_links')
		);

		// Include required files.
		$this->includes();
	}

	/**
	 * Prevent cloning of the instance.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing of the instance.
	 *
	 * @throws \RuntimeException If unserialization is attempted.
	 */
	public function __wakeup()
	{
		throw new \RuntimeException('Unserializing a singleton is not allowed.');
	}

	/**
	 * Retrieves the single instance of the class.
	 *
	 * @return WooLinkedVariation The instance.
	 */
	public static function get_instance(): self
	{
		if (null === self::$instance) {
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

	/**
	 * Activate Hook.
	 *
	 * @since 2.0.0
	 */
	public function activate(): void {}

	/**
	 * Deactivate Hook.
	 *
	 * @since 2.0.0
	 */
	public function deactivate(): void {}

	/**
	 * Load plugin textdomain
	 *
	 * @since 2.0.0
	 */
	public function load_textdomain(): void
	{
		load_plugin_textdomain(
			'linked-variation-for-woocommerce',
			false,
			LVFW_PATH . 'languages'
		);
	}

	/**
	 * Admin Notice.
	 *
	 * @since 2.0.0
	 */
	public function admin_notice(): void
	{
		if (! current_user_can('activate_plugins') || class_exists('WooCommerce')) {
			return;
		}

		$plugin_path       = 'woocommerce/woocommerce.php'; // Path to the WooCommerce plugin file.
		$installed_plugins = get_plugins();
		$active_plugins    = get_option('active_plugins');
		$notice_html       = '';

		// Check if WooCommerce is installed.
		if (! isset($installed_plugins[$plugin_path])) {
			$install_url = wp_nonce_url(
				self_admin_url('update.php?action=install-plugin&plugin=woocommerce'),
				'install-plugin_woocommerce'
			);

			$notice_html = sprintf(
				/* translators: 1: Plugin name 2: WooCommerce */
				esc_html__('The %1$s plugin requires %2$s to be installed and activated.', 'linked-variation-for-woocommerce'),
				'<strong>' . esc_html__('Linked Variation for WooCommerce', 'linked-variation-for-woocommerce') . '</strong>',
				'<strong>' . esc_html__('WooCommerce', 'linked-variation-for-woocommerce') . '</strong>'
			);

			$notice_html .= sprintf(
				'<br><a href="%s" class="button-primary" style="margin-top: 5px;">%s</a>',
				esc_url($install_url),
				esc_html__('Install WooCommerce', 'linked-variation-for-woocommerce')
			);
		}

		// Check if WooCommerce is installed but not activated.
		if (isset($installed_plugins[$plugin_path]) && ! in_array($plugin_path, $active_plugins, true)) {
			$activate_url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'activate',
						'plugin' => $plugin_path,
					),
					admin_url('plugins.php')
				),
				'activate-plugin_' . $plugin_path
			);

			$notice_html = sprintf(
				/* translators: 1: Plugin name 2: WooCommerce */
				esc_html__('The %1$s plugin requires %2$s to be activated.', 'linked-variation-for-woocommerce'),
				'<strong>' . esc_html__('Linked Variation for WooCommerce', 'linked-variation-for-woocommerce') . '</strong>',
				'<strong>' . esc_html__('WooCommerce', 'linked-variation-for-woocommerce') . '</strong>'
			);

			$notice_html .= sprintf(
				'<br><a href="%s" class="button-primary" style="margin-top: 5px;">%s</a>',
				esc_url($activate_url),
				esc_html__('Activate WooCommerce', 'linked-variation-for-woocommerce')
			);
		}

		// Display the notice if needed.
		if (! empty($notice_html)) {
			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				wp_kses_post($notice_html)
			);
		}
	}

	/**
	 * Add plugin action links.
	 *
	 * @since 2.0.0
	 * @param array $links Action links.
	 */
	public function add_plugin_action_links(array $links): array
	{
		$links = array_merge(
			array(
				'<a href="' . esc_url(admin_url('/edit.php?post_type=woolinkedvariation')) . '">' . __('Variations', 'linked-variation-for-woocommerce') . '</a>',
			),
			$links
		);

		return $links;
	}

	/**
	 * Include required files.
	 *
	 * @since 2.0.0
	 */
	private function includes()
	{
		// Check if WooCommerce is active.
		$active_plugins = get_option('active_plugins');
		if (! in_array('woocommerce/woocommerce.php', $active_plugins)) {
			return;
		}

		// Upgrade the plugin.
		if (get_transient('lvfw_migrated')) {
			require_once LVFW_INCLUDE_PATH . 'upgrade.php';
		}

		// Include required admin files.
		require_once LVFW_INCLUDE_PATH . 'admin/loader.php';

		// Include required frontend files.
		require_once LVFW_INCLUDE_PATH . 'frontend/loader.php';
	}
}

// Initialize the plugin.
WooLinkedVariation::get_instance();
