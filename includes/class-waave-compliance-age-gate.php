<?php
/**
 * WAAVE Compliance
 *
 * @class       WC_WAAVE_Age_Gate
 * @version     1.0.1
 * @package WAAVE_Compliance_Age_Gate
 */

/**
 * WAAVE_Compliance_Age_Gate.
 */
class WAAVE_Compliance_Age_Gate {
	const API_PROD_URL    = 'https://getwaave.co';
	const API_SANDBOX_URL = 'https://staging.getwaave.co';

	/**
	 * Instance
	 *
	 * @var this
	 */
	public static $instance;

	/**
	 * Init function
	 */
	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new WAAVE_Compliance_Age_Gate();
		}
		return self::$instance;
	}

	/**
	 * Construct.
	 */
	public function __construct() {
		// Configuration fields.
		$this->venue_id               = get_option( 'waave_compliance_venue_id' );
		$this->age_gate_mode          = get_option( 'waave_compliance_age_gate_mode' );
		$this->disclaimers_mode       = get_option( 'waave_compliance_disclaimers_mode' );
		$this->footer_mode            = get_option( 'waave_compliance_footer_mode', 'yes' );
		$this->disclaimers_product    = get_option( 'waave_compliance_disclaimers_product' );
		$this->disclaimers_cart       = get_option( 'waave_compliance_disclaimers_cart' );
		$this->disclaimers_checkout   = get_option( 'waave_compliance_disclaimers_checkout' );
		$this->test_mode              = get_option( 'waave_compliance_testmode' );
		$this->waave_background_color = get_option( 'waave_compliance_disclaimers_background_color' );
		$this->waave_color            = get_option( 'waave_compliance_disclaimers_color' );

		// Api.
		$this->api_url = self::API_PROD_URL;
		if ( 'yes' === $this->test_mode ) {
			$this->api_url = self::API_SANDBOX_URL;
		}

		if ( ! is_admin() ) {
			if ( ! isset( $_COOKIE['age_verification_mode'] ) ) {
				$time_remember_me = 10 * 365 * 24 * 60 * 60; // 10 years
				setcookie( 'age_verification_mode', get_option( 'waave_compliance_age_gate_mode' ) === 'yes' ? 1 : 0, time() + $time_remember_me, COOKIEPATH, COOKIE_DOMAIN );
			}
			add_action( 'wp_footer', array( $this, 'age_verification_popup' ) );
			add_action( 'wp_footer', array( $this, 'show_disclaimers_footer' ) );
			add_action( 'wp_footer', array( $this, 'waave_compliance_add_footer' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'age_verification_assets' ) );
		}
		add_action( 'wp_ajax_set_age_verification_cookie', array( $this, 'set_age_verification_cookie' ) );
		add_action( 'wp_ajax_nopriv_set_age_verification_cookie', array( $this, 'set_age_verification_cookie' ) );
	}

	/**
	 * Function waave_compliance_add_footer
	 */
	public function waave_compliance_add_footer() {
		if ( 'yes' !== $this->footer_mode ) {
			return;
		}

		echo '<div style="text-align: center; padding-top: 10px; background-color: ' . esc_html( $this->waave_background_color ) . '; color: ' . esc_html( $this->waave_color ) . ';">
			<a style="margin: 0 auto" href="https://www.getwaave.com/note-to-authorities" target="_blank">
				<img style="display: inline; height: 60px" src="' . esc_url( WAAVE_COMPLIANCE_PLUGIN_URL . '/assets/images/WaaveCompliace_Trust_Seal.png' ) . '" alt="WAAVE Compliance" />
			</a>
		</div>';
	}

	/**
	 * Function age_verification_assets
	 */
	public function age_verification_assets() {
		$plugin_data = get_plugin_data( WAAVE_COMPLIANCE_MAIN_FILE );
		$version     = $plugin_data['Version'];
		wp_enqueue_script( 'age-verification', plugins_url( '../assets/js/age-verification.js', __FILE__ ), array( 'jquery' ), $version, true );
		wp_enqueue_style( 'age-verification', plugins_url( '../assets/css/age-verification.css', __FILE__ ), array(), $version );

		wp_localize_script(
			'age-verification',
			'my_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'age-verification' ),
			)
		);
	}

	/**
	 * Function set_age_verification_cookie.
	 */
	public function set_age_verification_cookie() {
		check_ajax_referer( 'age-verification' );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( isset( $_COOKIE['remember_me'] ) ) {
				$time_remember_me = 10 * 365 * 24 * 60 * 60; // 10 years
				setcookie( 'age_verification', 1, time() + $time_remember_me, COOKIEPATH, COOKIE_DOMAIN );
			} else {
				setcookie( 'age_verification', 1, time() + ( 360 ), COOKIEPATH, COOKIE_DOMAIN );
			}
		}
	}

	/**
	 * Function age_verification_popup.
	 */
	public function age_verification_popup() {
		if ( ( 'yes' === $this->age_gate_mode ) ) {
			if ( ! isset( $_COOKIE['age_verification'] ) ) {
				$client_ava    = '';
				$age_gate_info = $this->get_age_gate_info_categories();
				if ( $age_gate_info ) {
					$client_ava = $age_gate_info['client_logo']['original'];
					if ( isset( $age_gate_info['data'] ) ) {
						$text_popup = '';
						foreach ( $age_gate_info['data'] as $vertical ) {
							$text_popup = $text_popup . 'You must be ' . $vertical['age'] . '+ to purchase ' . $vertical['label'] . ' products </br>';
						}
						if ( '' === $client_ava ) {
							$client_ava = $this->api_url . '/images/no-image.png';
						}
						echo "
						<div class='age-verification-overlay'>
							<div class='age-verification-popup'>
								<img class='age-verification-client-img' src='" . esc_url( $client_ava ) . "' alt=''>
								<h3 class='age-verification-title'>Welcome!</h3>
								<p class='age-verification-q'>" . wp_kses( $text_popup, array( 'br' => array() ) ) . "
								</p>
								<p>
									<a class='age-verification-btn age-verification-btn-yes' href='#'>I am</a>
									<a class='age-verification-btn age-verification-btn-no' href='https://google.com'>I am not</a>
								</p>
								<div class='age-gate-remember-me'>
									<input type='checkbox' class='age-gate-remember-me-checkbox'>
									<span>Remember Me</span>
								</div>
							</div>
						</div>";
					}
				}
			}
		}
	}

	/**
	 * Function show_disclaimers_footer.
	 */
	public function show_disclaimers_footer() {
		if ( 'yes' === $this->disclaimers_mode ) {
			$mss_disclaimers = '';
			$verticals       = get_option( 'waave_disclaimer' );
			if ( ! $verticals ) {
				$verticals = $this->get_age_gate_info_categories();
				update_option( 'waave_disclaimer', $verticals );
			}
			if ( isset( $verticals['data'] ) ) {
				$mss_disclaimers = 'Disclaimer: ';
				foreach ( $verticals['data'] as $vertical ) {
					if ( $vertical['disclaimer'] ) {
						$mss_disclaimers = $mss_disclaimers . $vertical['disclaimer'] . '&nbsp;';
					}
				}
			}
			if ( '' !== $mss_disclaimers ) {
				echo wp_kses(
					"<div class='waave-compliance-mode' style='margin: unset; background-color: $this->waave_background_color; color: $this->waave_color;'>
					<div class='waave-compliance-disclaimer-content'>
						$mss_disclaimers
					</div>
					<hr class='waave-compliance-disclaimer-hr'>
				</div>",
					array(
						'div'    => array(
							'style' => array(),
							'class' => array(),
						),
						'span'   => array(),
						'hr'     => array( 'class' => array() ),
						'br'     => array(),
						'&nbsp;' => array(),
					)
				);
			}
		}
	}

	/**
	 * Function get_age_gate_info_categories.
	 */
	private function get_age_gate_info_categories() {
		$venue_id = get_option( 'waave_compliance_venue_id' );
		if ( is_plugin_inactive( 'waave-compliance/waave-compliance.php' ) ) {
			$venue_id = $this->venue_id;
		}
		if ( ! $venue_id ) {
			return array();
		}
		$url      = $this->api_url . '/compliance/categories/age-gate/' . $venue_id;
		$request  = wp_remote_get( $url );
		$response = json_decode( wp_remote_retrieve_body( $request ), true );
		return $response;
	}

}

