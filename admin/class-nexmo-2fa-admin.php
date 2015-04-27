<?php

final class Nexmo_2FA_Admin extends Nexmo_2FA {
	public function __construct() {
		parent::__construct();

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'show_user_profile', array( $this, 'user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'user_profile' ) );
		add_action( 'wp_ajax_n2fa_send_pin_code', array( $this, 'send_pin_code' ) );
		add_action( 'wp_ajax_n2fa_verify_pin_code', array( $this, 'verify_pin_code' ) );
		add_action( 'user_profile_update_errors', array( $this, 'user_update_errors' ), 10, 3 );
		add_action( 'personal_options_update', array( $this, 'user_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'user_update' ) );

		add_filter( "plugin_action_links", array( $this, 'plugin_action_links' ), 10, 2 );
	}

	public function admin_init() {
		register_setting( 'nexmo_2fa_settings', 'nexmo_2fa_settings', array( $this, 'sanitize' ) );

		add_settings_section( 'api_settings', __( 'API Settings', 'n2fa' ), array( $this, 'print_settings' ), 'nexmo_2fa_settings' );	}

	public function admin_menu() {
		add_options_page( 'Nexmo 2FA', 'Nexmo 2FA', 'manage_options', "{$this->plugin_name}-settings", array( $this, 'settings_page' ) );
	}

	public function plugin_action_links( $links, $plugin_file ) {
		$action_links = array();

		if ( strpos( $plugin_file, 'nexmo-2fa' ) !== false ) {
			$action_links = array(
				'settings' => '<a href="' . admin_url( "options-general.php?page={$this->plugin_name}-settings" ) . '" title="' . esc_attr__( 'Nexmo 2FA Settings', 'n2fa' ) . '">' . __( 'Settings', 'n2fa' ) . '</a>'
			);
		}

		return array_merge( $action_links, $links );
	}

	public function settings_page() {
		require_once __DIR__ . '/partials/settings-page.php';
	}

	public function sanitize( array $input ) {
		if ( isset( $input['xmlrpc_status'] ) ) {
			$input['xmlrpc_status'] = ( $input['xmlrpc_status'] === 'on' ) ? 'on' : 'off';
		}

		return $input;
	}

	public function print_settings() {
		require_once __DIR__ . '/partials/print-settings.php';
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'n2fa-css', plugin_dir_url( __FILE__ ) . 'css/n2fa.css', array(), NEXMO_2FA_VERSION );

		wp_register_script( 'n2fa-js', plugin_dir_url( __FILE__ ) . 'js/n2fa.js', array( 'jquery' ), NEXMO_2FA_VERSION, true );
		wp_localize_script( 'n2fa-js', 'n2fa', array(
			'verified' => __( 'VERIFIED', 'n2fa' ),
			'verification_failed' => __( 'VERIFICATION FAILED', 'n2fa' )
		));
		wp_enqueue_script( 'n2fa-js' );
	}

	private function get_user_of_current_profile() {
		global $user_id;

		$user = null;

		if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE ) {
			$user = wp_get_current_user();
		} elseif ( isset( $_GET['user_id'] ) && (int) $_GET['user_id'] ) {
			$user = get_user_by( 'id', (int) $_GET['user_id'] );
		} elseif ( $user_id ) {
			$user = get_user_by( 'id', (int) $user_id );
		}

		if ( is_a( $user, 'WP_User' ) ) {
			return $user;
		}

		wp_die( __( 'Unknown user', 'n2fa' ) );
	}

	public function admin_notices() {
		global $pagenow;

		$user = wp_get_current_user();

		$phone_number = get_user_meta( $user->ID, 'n2fa_phone_number', true );
		$country_code = get_user_meta( $user->ID, 'n2fa_country_code', true );

		if ( $pagenow !== 'profile.php' && ( ! $phone_number || ! $country_code ) && $this->is_enabled() ) {
			?>
			<div class="enable-2fa notice"><p><?php printf( __( 'Two factor authentication (2FA) adds an extra layer of security to your account. Enable it by <a href="%1$s">adding your phone number</a>.', 'n2fa' ), get_edit_user_link( $user->ID ) . '#n2fa-phone-number' ) ?></p></div>
			<?php
		}
	}

	public function user_profile() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$user = $this->get_user_of_current_profile();

		$phone_number = get_user_meta( $user->ID, 'n2fa_phone_number', true );
		$country_code = get_user_meta( $user->ID, 'n2fa_country_code', true );

		$countries_dropdown = '<select name="n2fa_country_code" id="n2fa-country-code" class="input">';
		$countries_dropdown .= '<option value="">' . __( '- Select -', 'n2fa' ) . '</option>';

		foreach ( $this->countries as $code => $country ) {
			$countries_dropdown .= '<option value="' . esc_attr( $code ) . '"' . selected( $code, $country_code, false ) . '>' . $country . '</option>';
		}

		$countries_dropdown .= '</select>';

		require_once __DIR__ . '/partials/user-profile.php';
	}

	public function send_pin_code() {
		header( 'Content-Type: application/json' );

		$phone_number = isset( $_POST['n2fa_phone_number'] ) ? $_POST['n2fa_phone_number'] : false;
		$country_code = isset( $_POST['n2fa_country_code'] ) ? $_POST['n2fa_country_code'] : false;

		if ( $phone_number && $country_code && $this->is_enabled() ) {
			$response = $this->api->send_pin_code( $phone_number, $country_code );

			echo json_encode( array( 'request_id' => $response->request_id ) );
		}

		wp_die();
	}

	public function verify_pin_code() {
		header( 'Content-Type: application/json' );

		$request_id = isset( $_POST['n2fa_request_id'] ) ? $_POST['n2fa_request_id'] : false;
		$pin_code = isset( $_POST['n2fa_pin_code'] ) ? $_POST['n2fa_pin_code'] : false;

		if ( $request_id && $pin_code && $this->is_enabled() ) {
			$response = $this->api->verify_pin_code( $pin_code, $request_id );

			if ( $this->api->is_pin_code_valid ) {
				echo json_encode( array( 'success' => true ) );
			} else {
				echo json_encode( array( 'error' => $response->error_text ) );
			}
		}

		wp_die();
	}

	private function phone_number_in_use( $user_id, $phone_number ) {
		if ( ! $phone_number ) {
			return false;
		}

		$users = get_users(
			array(
				'meta_key' => 'n2fa_phone_number',
				'meta_value' => $phone_number,
				'number' => 1
			)
		);

		if ( 0 < count( $users ) && $user_id !== $users[0]->ID ) {
			return true;
		}

		return false;
	}

	public function user_update_errors( &$errors, $update, &$user ) {
		$phone_number = isset( $_POST['n2fa_phone_number'] ) ? $_POST['n2fa_phone_number'] : false;
		$country_code = isset( $_POST['n2fa_country_code'] ) ? $_POST['n2fa_country_code'] : false;

		if ( $user && $phone_number && $country_code ) {
			if ( $this->phone_number_in_use( $user->ID, $phone_number ) ) {
				$errors->add( 'n2fa_user_update_error', __( 'Phone number already in use.', 'n2fa' ) );
			}
		}
	}

	public function user_update( $user_id ) {
		$phone_number = $_POST['n2fa_phone_number'];
		$country_code = $_POST['n2fa_country_code'];

		if ( current_user_can( 'edit_user', $user_id ) && ! $this->phone_number_in_use( $user_id, $phone_number ) ) {
			update_user_meta( $user_id, 'n2fa_phone_number', $phone_number );
			update_user_meta( $user_id, 'n2fa_country_code', $country_code );
		}
	}
}
