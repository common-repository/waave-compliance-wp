<?php
/**
 * WAAVE Compliance
 *
 * @class       WAAVE_Compliance_REST_API
 * @package WAAVE_Compliance
 */

use Automattic\WooCommerce\Client;

/**
 * WAAVE_Compliance_REST_API
 */
class WAAVE_Compliance_REST_API {
	const API_PROD_URL    = 'https://getwaave.co';
	const API_SANDBOX_URL = 'https://staging.getwaave.co';

	const MAX_PER_PAGE = 100;

	/**
	 * Woocommerce
	 *
	 * @var Client
	 */
	private static $woocommerce;

	/**
	 * Function init.
	 */
	public static function init() {
		if ( ! function_exists( 'register_rest_route' ) ) {
			return false;
		}

		register_rest_route(
			'waave-compliance/v1',
			'/products',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( 'WAAVE_Compliance_REST_API', 'get_products' ),
					'permission_callback' => array( 'WAAVE_Compliance_REST_API', 'permissions_check' ),
				),
			)
		);

		register_rest_route(
			'waave-compliance/v1',
			'/products/categories',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( 'WAAVE_Compliance_REST_API', 'get_products_categories' ),
					'permission_callback' => array( 'WAAVE_Compliance_REST_API', 'permissions_check' ),
				),
			)
		);

		register_rest_route(
			'waave-compliance/v1',
			'/version',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( 'WAAVE_Compliance_REST_API', 'get_plugin_version' ),
					'permission_callback' => array( 'WAAVE_Compliance_REST_API', 'permissions_check' ),
				),
			)
		);

		register_rest_route(
			'waave-compliance/v1',
			'/disclaimer/sync',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( 'WAAVE_Compliance_REST_API', 'sync_disclaimer' ),
					'permission_callback' => array( 'WAAVE_Compliance_REST_API', 'permissions_check' ),
				),
			)
		);

		add_filter(
			'woocommerce_rest_product_object_query',
			function( $args, $request ) {
				$after = $request->get_param( 'after' );
				if ( $after ) {
					$args['date_query'][0]['column'] = 'post_modified';
				}
				return $args;
			},
			10,
			2
		);
	}

	/**
	 * Function get_plugin_version.
	 */
	public static function get_plugin_version() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$data = get_plugin_data( WAAVE_COMPLIANCE_MAIN_FILE );
		if ( is_plugin_active( 'waave-complete/woocommer-waave-complete.php' ) ) {
			$complete = get_plugin_data( WC_WAAVE_MAIN_FILE );
		}
		if ( is_plugin_active( 'waave-plugin/woocommer-gateway-waave.php' ) ) {
			$checkout = get_plugin_data( WC_WAAVE_PLUGIN_PATH );
		}

		$versions = array(
			'compliance' => $data['Version'],
		);
		if ( isset( $complete ) ) {
			$versions['complete'] = $complete['Version'];
		}
		if ( isset( $checkout ) ) {
			$versions['checkout'] = $checkout['Version'];
		}

		return $versions;
	}

	/**
	 * Function get_products.
	 *
	 * @param Request $request request.
	 */
	public static function get_products( $request ) {
		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );
		$order    = $request->get_param( 'order' );
		$orderby  = $request->get_param( 'orderby' );
		$search   = $request->get_param( 'search' );
		$category = $request->get_param( 'category' );
		$slug     = $request->get_param( 'slug' );
		$after    = $request->get_param( 'after' );

		if ( empty( $page ) ) {
			$page = 1;
		}

		if ( empty( $per_page ) ) {
			$per_page = self::MAX_PER_PAGE;
		}

		$options = array(
			'page'     => $page,
			'per_page' => $per_page > self::MAX_PER_PAGE ? self::MAX_PER_PAGE : $per_page,
			'status'   => 'any',
		);

		if ( $order ) {
			$options['order'] = $order;
		}

		if ( $orderby ) {
			$options['orderby'] = $orderby;
		}

		if ( $search ) {
			$options['search'] = $search;
		}

		if ( $category ) {
			$options['category'] = $category;
		}

		if ( $slug ) {
			$options['slug'] = $slug;
		}

		if ( $after ) {
			$options['after'] = $after;
		}

		$woocommerce = self::create_woocommerce_client();
		$products    = $woocommerce->get( 'products', $options );

		return $products;
	}

	/**
	 * Function get_products_categories.
	 *
	 * @param Request $request request.
	 */
	public static function get_products_categories( $request ) {
		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );
		$order    = $request->get_param( 'order' );
		$orderby  = $request->get_param( 'orderby' );
		$search   = $request->get_param( 'search' );
		$slug     = $request->get_param( 'slug' );

		if ( empty( $page ) ) {
			$page = 1;
		}

		if ( empty( $per_page ) ) {
			$per_page = self::MAX_PER_PAGE;
		}

		$options = array(
			'page'     => $page,
			'per_page' => $per_page > self::MAX_PER_PAGE ? self::MAX_PER_PAGE : $per_page,
		);

		if ( $order ) {
			$options['order'] = $order;
		}

		if ( $orderby ) {
			$options['orderby'] = $orderby;
		}

		if ( $search ) {
			$options['search'] = $search;
		}

		if ( $slug ) {
			$options['slug'] = $slug;
		}

		$woocommerce = self::create_woocommerce_client();
		$categories  = $woocommerce->get( 'products/categories', $options );

		return $categories;
	}

	/**
	 * Function sync_disclaimer.
	 */
	public static function sync_disclaimer() {
		$waave_api_url = self::API_PROD_URL;
		if ( 'yes' === get_option( 'waave_compliance_testmode' ) ) {
			$waave_api_url = self::API_SANDBOX_URL;
		}

		$venue_id = get_option( 'waave_compliance_venue_id' );
		if ( ! $venue_id ) {
			return;
		}

		$url = $waave_api_url . '/compliance/categories/age-gate/' . $venue_id;

		$request         = wp_remote_get( $url );
		$verticals_cache = json_decode( wp_remote_retrieve_body( $request ), true );
		update_option( 'waave_disclaimer', $verticals_cache );
		return wp_send_json( $verticals_cache, 200, 0 );
	}

	/**
	 * Function permissions_check.
	 */
	public static function permissions_check() {
		return true;
	}

	/**
	 * Function create_woocommerce_client.
	 */
	private static function create_woocommerce_client() {
		if ( self::$woocommerce ) {
			return self::$woocommerce;
		}

		$url             = get_site_url();
		$consumer_key    = get_option( 'waave_compliance_consumer_key' );
		$consumer_secret = get_option( 'waave_compliance_consumer_secret' );

		$options = array(
			'query_string_auth' => true,
		);

		self::$woocommerce = new Client(
			$url,
			$consumer_key,
			$consumer_secret,
			$options
		);

		return self::$woocommerce;
	}
}
