<?php
/**
 * WAAVE Compliance
 *
 * @class       WAAVE_Compliance
 * @package WAAVE_Compliance
 */

/**
 * WAAVE_Compliance
 */
class WAAVE_Compliance {
	const API_PROD_URL    = 'https://getwaave.co';
	const API_SANDBOX_URL = 'https://staging.getwaave.co';

	/**
	 * Instance.
	 *
	 * @var this
	 */
	public static $instance;

	/**
	 * Function init.
	 */
	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new WAAVE_Compliance();
		}

		return self::$instance;
	}

	/**
	 * Function construct.
	 */
	public function __construct() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 99 );
		add_action( 'woocommerce_settings_waave', array( $this, 'settings_tab' ) );
		add_action( 'woocommerce_update_options_waave', array( $this, 'update_settings' ) );
	}

	/**
	 * Function add_settings_tab.
	 *
	 * @param array $settings_tabs settings_tabs.
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs['waave'] = 'WAAVE';
		return $settings_tabs;
	}

	/**
	 * Function settings_tabs.
	 */
	public function settings_tab() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Function update_settings.
	 */
	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );
		$this->call_waave_active_api();
		$time_remerber_me = 10 * 365 * 24 * 60 * 60; // 10 years
		setcookie( 'age_verification_mode', get_option( 'waave_compliance_age_gate_mode' ) == 'yes' ? 1 : 0, time() + $time_remerber_me, COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Function get_settings.
	 */
	private function get_settings() {
		return array(
			'section_age_gate_title'       => array(
				'name' => 'Age Gate & Disclaimers',
				'type' => 'title',
				'desc' => '',
				'id'   => 'waave_compliance_age_gate_title',
			),
			'age_gate_mode'                => array(
				'name'    => 'WAAVE Age Gate Mode',
				'type'    => 'checkbox',
				'id'      => 'waave_compliance_age_gate_mode',
				'desc'    => 'Enable/Disable',
				'default' => 'yes',
			),
			'disclaimers_mode'             => array(
				'name'    => 'WAAVE Disclaimers Mode',
				'type'    => 'checkbox',
				'id'      => 'waave_compliance_disclaimers_mode',
				'desc'    => 'Enable/Disable',
				'default' => 'yes',
			),
			'footer_mode'                  => array(
				'name'    => 'WAAVE Footer Mode',
				'type'    => 'checkbox',
				'id'      => 'waave_compliance_footer_mode',
				'desc'    => 'Enable/Disable',
				'default' => 'yes',
			),
			'disclaimers_background_color' => array(
				'name'    => '',
				'type'    => 'color',
				'id'      => 'waave_compliance_disclaimers_background_color',
				'desc'    => 'Background Waave Footer Color',
				'default' => '#fff',
			),
			'disclaimers_color'            => array(
				'name'    => '',
				'type'    => 'color',
				'id'      => 'waave_compliance_disclaimers_color',
				'desc'    => 'Waave Footer Color',
				'default' => '#111',
			),
			'section_age_gate_end'         => array(
				'type' => 'sectionend',
				'id'   => 'waave_compliance_age_gate_section_end',
			),
			'section_title'                => array(
				'name' => 'REST API',
				'type' => 'title',
				'desc' => '',
				'id'   => 'waave_compliance_section_title',
			),
			'consumer_key'                 => array(
				'name' => 'Consumer key',
				'type' => 'text',
				'desc' => '',
				'id'   => 'waave_compliance_consumer_key',
			),
			'consumer_secret'              => array(
				'name' => 'Consumer secret',
				'type' => 'password',
				'desc' => '',
				'id'   => 'waave_compliance_consumer_secret',
			),
			'section_end'                  => array(
				'type' => 'sectionend',
				'id'   => 'waave_compliance_section_end',
			),
			'section_client_title'         => array(
				'name' => 'Client',
				'type' => 'title',
				'desc' => '',
				'id'   => 'waave_compliance_section_client_title',
			),
			'testmode'                     => array(
				'name'    => 'WAAVE Sandbox',
				'type'    => 'checkbox',
				'id'      => 'waave_compliance_testmode',
				'default' => 'yes',
			),
			'venue_id'                     => array(
				'name' => 'Venue ID',
				'type' => 'text',
				'desc' => '',
				'id'   => 'waave_compliance_venue_id',
			),
			'client_password'              => array(
				'name' => 'Password',
				'type' => 'password',
				'desc' => '',
				'id'   => 'waave_compliance_client_password',
			),
			'section_client_end'           => array(
				'type' => 'sectionend',
				'id'   => 'waave_compliance_section_client_end',
			),
		);
	}

	/**
	 * Function call_waave_active_api.
	 */
	private function call_waave_active_api() {
		$plugin_data = get_plugin_data( WAAVE_COMPLIANCE_MAIN_FILE );
		$body        = array(
			'version'             => $plugin_data['Version'],
			'ping_url'            => get_rest_url( null, 'waave-compliance/v1/version' ),
			'category_url'        => get_rest_url( null, 'waave-compliance/v1/products/categories' ),
			'menu_item_url'       => get_rest_url( null, 'waave-compliance/v1/products' ),
			'disclaimer_sync_url' => get_rest_url( null, 'waave-compliance/v1/disclaimer/sync' ),
			'venue_id'            => get_option( 'waave_compliance_venue_id' ),
			'password'            => get_option( 'waave_compliance_client_password' ),
		);

		$url = self::API_PROD_URL;
		if ( 'yes' == get_option( 'waave_compliance_testmode' ) ) {
			$url = self::API_SANDBOX_URL;
		}

		$url .= '/compliance/active';

		$options = array(
			'body' => $body,
		);

		$request  = wp_remote_post( $url, $options );
		$response = json_decode( wp_remote_retrieve_body( $request ), true );

		if ( empty( $response['success'] ) ) {
			WC_Admin_Settings::add_error( $response['message'] );
		}
	}
}
