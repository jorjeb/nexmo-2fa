<?php

final class Nexmo_2FA_Public extends Nexmo_2FA {
	public function __construct() {
		parent::__construct();

		add_action( 'authenticate', array( $this, 'authenticate' ), 10, 3 );
	}

	public function authenticate( $user, $username, $password ) {
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			return $user;
		}

		if ( ! $this->is_enabled() ) {
			return $user;
		}

		$redirect_to = isset( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : admin_url();
		$remember_me = ( isset( $_POST['rememberme'] ) && $_POST['rememberme'] === 'forever' ) ? true : false;

		$_user = get_user_by( 'login', $username );
		$signature = isset( $_POST['n2fa_signature'] ) ? $_POST['n2fa_signature'] : false;

		if ( $signature && is_a( $_user, 'WP_User' ) && $this->is_valid_signature( $_user->ID, $signature ) ) {
			$pin_code = isset( $_POST['n2fa_pin_code'] ) ? $_POST['n2fa_pin_code'] : false;
			$request_id = isset( $_POST['n2fa_request_id'] ) ? $_POST['n2fa_request_id'] : false;

			$errors = array();

			if ( $pin_code && $request_id ) {
				$response = $this->api->verify_pin_code( $pin_code, $request_id );

				if ( $this->api->is_pin_code_valid ) {
					wp_set_auth_cookie( $_user->ID, $remember_me );

					wp_safe_redirect( $redirect_to );
					exit;
				}

				$errors = array( $response->error_text );
			}

			$this->start_2FA( $_user, $redirect_to, $remember_me, $phone_number, $country_code, $errors );
		}

		if ( is_null( $user ) ) {
			// remove default authentication
			remove_action( 'authenticate', 'wp_authenticate_username_password', 20 );

			$user = wp_authenticate_username_password( null, $username, $password );
		}

		if ( is_a( $user, 'WP_User' ) ) {
			$this->start_2FA( $user, $redirect_to, $remember_me, $phone_number, $country_code );
		}

		return $user;
	}

	private function start_2FA( WP_User $user, $redirect_to, $remember_me, $phone_number, $country_code, $errors = array() ) {
		$phone_number = get_user_meta( $user->ID, 'n2fa_phone_number', true );
		$country_code = get_user_meta( $user->ID, 'n2fa_country_code', true );

		if ( ! $phone_number || ! $country_code ) {
			return;
		}

		wp_logout();

		$signature = $this->generate_signature( $user->ID );
		$username = $user->user_login;

		// tell browsers not to cache this page
		nocache_headers();

		header('Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) );

		$response = $this->api->send_pin_code( $phone_number, $country_code );

		$phone_number = $this->hide_phone_number( $phone_number );

		require_once __DIR__ . '/partials/auth-form.php';

		exit;
	}
}
