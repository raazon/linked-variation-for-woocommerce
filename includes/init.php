<?php defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' ); // Cannot access pages directly.

final class Init {

	private static $instance = null;

	private function __construct() {
		// Prevent direct instantiation.
	}

	public static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function run() {
		// Fired when activate.
		register_activation_hook( LVFW_FILE, array( __CLASS__, 'activate' ) );

		// Fired when deactivate.
		register_deactivation_hook( LVFW_FILE, array( __CLASS__, 'deactivate' ) );

		// Load plugin textdomain.
		add_action( 'init', array( __CLASS__, 'load_textdomain' ) );

		// Admin notice if Woocommerce is not installed.
		add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );

		// Add plugin action links.
		add_filter( 'plugin_action_links_' . LVFW_BASENAME, array( $this, 'add_plugin_action_links' ) );

		// Include required files.
		$this->includes();
	}

	public static function activate() {}

	public static function deactivate() {}

	public static function load_textdomain() {
		load_plugin_textdomain(
			'linked-variation-for-woocommerce',
			false,
			LVFW_PATH . '/languages'
		);
	}

	public static function admin_notice() {
		if ( ! current_user_can( 'activate_plugins' ) || ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$plugin_path       = 'woocommerce/woocommerce.php'; // Path to the WooCommerce plugin file.
		$installed_plugins = get_plugins();
		$active_plugins    = get_option( 'active_plugins' );
		$notice_html       = '';

		// Check if WooCommerce is installed.
		if ( ! isset( $installed_plugins[ $plugin_path ] ) ) {
			$install_url = wp_nonce_url(
				self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ),
				'install-plugin_woocommerce'
			);

			$notice_html = sprintf(
				/* translators: 1: Plugin name 2: WooCommerce */
				esc_html__( 'The %1$s plugin requires %2$s to be installed and activated.', 'linked-variation-for-woocommerce' ),
				'<strong>' . esc_html__( 'Linked Variation for WooCommerce', 'linked-variation-for-woocommerce' ) . '</strong>',
				'<strong>' . esc_html__( 'WooCommerce', 'linked-variation-for-woocommerce' ) . '</strong>'
			);

			$notice_html .= sprintf(
				'<br><a href="%s" class="button-primary" style="margin-top: 5px;">%s</a>',
				esc_url( $install_url ),
				esc_html__( 'Install WooCommerce', 'linked-variation-for-woocommerce' )
			);
		}

		// Check if WooCommerce is installed but not activated.
		if ( isset( $installed_plugins[ $plugin_path ] ) && ! in_array( $plugin_path, $active_plugins ) ) {
			$activate_url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'activate',
						'plugin' => $plugin_path,
					),
					admin_url( 'plugins.php' )
				),
				'activate-plugin_' . $plugin_path
			);

			$notice_html = sprintf(
				/* translators: 1: Plugin name 2: WooCommerce */
				esc_html__( 'The %1$s plugin requires %2$s to be activated.', 'linked-variation-for-woocommerce' ),
				'<strong>' . esc_html__( 'Linked Variation for WooCommerce', 'linked-variation-for-woocommerce' ) . '</strong>',
				'<strong>' . esc_html__( 'WooCommerce', 'linked-variation-for-woocommerce' ) . '</strong>'
			);

			$notice_html .= sprintf(
				'<br><a href="%s" class="button-primary" style="margin-top: 5px;">%s</a>',
				esc_url( $activate_url ),
				esc_html__( 'Activate WooCommerce', 'linked-variation-for-woocommerce' )
			);
		}

		// Display the notice if needed.
		if ( ! empty( $notice_html ) ) {
			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				$notice_html
			);
		}
	}

	public function add_plugin_action_links( $links ) {
		$links = array_merge(
			array(
				'<a href="' . esc_url( admin_url( '/edit.php?post_type=woolinkedvariation' ) ) . '">' . __( 'Variations', 'linked-variation-for-woocommerce' ) . '</a>',
			),
			$links
		);

		return $links;
	}

	private function includes() {
		// Include required files.
		require_once LVFW_INCLUDE_PATH . 'admin/loader.php';
	}
}

// Get the singleton instance and run the plugin.
Init::getInstance()->run();
