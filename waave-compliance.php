<?php
/**
 * Plugin Name:          WAAVE Compliance WP
 * Description:          WAAVE Compliance WP
 * Version:              1.1.14
 * Requires at least:    4.6
 * Require PHP:          7.4
 * Author:               WAAVE
 * Author URI:           https://getwaave.com
 * License:              GPLv3
 * License URI:          https://www.gnu.org/licenses/gpl-3.0.html
 * Requires Plugins:     woocommerce
 * WC requires at least: 6.5
 * WC tested up to:      8.2
 *
 * @package WAAVE_Compliance
 */

defined( 'ABSPATH' ) || exit;

define( 'WAAVE_COMPLIANCE_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'WAAVE_COMPLIANCE_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WAAVE_COMPLIANCE_MAIN_FILE', __FILE__ );

// Make sure WooCommerce is active.
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
	return;
}

/**
 * Declare plugin compatibility with WooCommerce HPOS.
 *
 * @since 1.1.14
 */
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * WAAVE Compliance init
 */
function waave_compliance_init() {
	require_once plugin_basename( 'vendor/autoload.php' );
	require_once plugin_basename( 'includes/class-waave-compliance.php' );
	require_once plugin_basename( 'includes/class-waave-compliance-rest-api.php' );
	require_once plugin_basename( 'includes/class-waave-compliance-age-gate.php' );
}
add_action( 'plugins_loaded', 'waave_compliance_init' );

add_action( 'init', array( 'WAAVE_Compliance', 'init' ) );
add_action( 'init', array( 'WAAVE_Compliance_Age_Gate', 'init' ) );
add_action( 'rest_api_init', array( 'WAAVE_Compliance_REST_API', 'init' ) );

/**
 * Adds plugin page links
 *
 * @since 1.0.0
 * @param array $links all plugin links.
 * @return array $links all plugin links + our custom links
 */
function waave_compliance_plugin_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=waave' ) . '">Configure</a>',
	);

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'waave_compliance_plugin_links' );
