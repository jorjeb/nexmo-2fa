<?php

require_once 'class-nexmo-2fa-api.php';

abstract class Nexmo_2FA {
	protected $plugin_name = 'nexmo-2fa';
	protected $settings = array();
	protected $countries = array();
	protected $api = null;

	public function __construct() {
		$this->settings = get_option( 'nexmo_2fa_settings' );

		if ( ! empty( $this->settings ) ) {
			if ( isset( $this->settings['xmlrpc_status'] ) && 'off' === $this->settings['xmlrpc_status'] ) {
				/*
				 * Disable XML-RPC services on this site
				 *
				 * @see wp_xmlrpc_server:login()
				 */
				add_filter( 'xmlrpc_enabled', '__return_false' );
			}
		}

		$this->countries = require_once 'countries.php';

		if ( $this->is_enabled() ) {
			$this->api = new Nexmo_2FA_API( $this->settings['api_key'], $this->settings['api_secret'] );
		}
	}

	public function is_enabled() {
		if ( ! empty( $this->settings ) ) {
			if ( isset( $this->settings['api_key'] ) && $this->settings['api_key'] &&
				 isset( $this->settings['api_secret'] ) && $this->settings['api_secret'] ) {
				return true;
			}
		}

		return false;
	}

	public function generate_signature( $user_id ) {
		$signature = wp_generate_password( 64, false, false );
		update_user_meta( $user_id, 'n2fa_signature', array( 'signature' => $signature, 'signed_at' => time() ) );

		return $signature;
	}

	public function is_valid_signature( $user_id, $signature ) {
		$user_signature = get_user_meta( $user_id, 'n2fa_signature', true );

		if ( $user_signature ) {
			// make sure that the signature has been invalidated
			if ( delete_user_meta( $user_id, 'n2fa_signature' ) ) {
				// check if the signature has not expired (5 minutes)
				if ( ( time() - $user_signature['signed_at'] ) <= 300 && $user_signature['signature'] === $signature ) {
					return true;
				}
			}
		}

		return false;
	}

	public function hide_phone_number( $phone_number ) {
		$phone_number = preg_replace( '/[^a-z0-9]/i', '', $phone_number );

		$hidden_phone_number = '+' . str_repeat( 'X', strlen( $phone_number ) - 2 );
		$hidden_phone_number .= substr( $phone_number, -2 );

		return $hidden_phone_number;
	}

	public function log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( var_export( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}
