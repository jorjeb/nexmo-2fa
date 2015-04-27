<?php

final class Nexmo_2FA_API {
	private $key = null;
	private $secret = null;
	private $errors = array();

	public $is_pin_code_valid = false;

	public function __construct( $key, $secret ) {
		$this->key = $key;
		$this->secret = $secret;
	}

	public function send_pin_code( $phone_number, $country_code ) {
		$response = wp_remote_get(
			add_query_arg(
				array(
					'api_key' => $this->key,
					'api_secret' => $this->secret,
					'number' => $phone_number,
					'country' => strtoupper( $country_code ),
					'brand' => get_bloginfo( 'name' )
				),
				NEXMO_VERIFY_ENDPOINT
			)
		);

		return json_decode( $response['body'] );
	}

	public function verify_pin_code( $pin_code, $request_id ) {
		$response = wp_remote_get(
			add_query_arg(
				array(
					'api_key' => $this->key,
					'api_secret' => $this->secret,
					'code' => $pin_code,
					'request_id' => $request_id
				),
				NEXMO_VERIFY_CHECK_ENDPOINT
			)
		);

		$api_response = json_decode( $response['body'] );

		$this->is_pin_code_valid = ( 0 === (int) $api_response->status );

		return $api_response;
	}
}
