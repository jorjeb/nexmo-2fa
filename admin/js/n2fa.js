/* global jQuery:false, ajaxurl: false, n2fa: false */
(function($) {
	'use strict';

	$(function() {
		// micro optimization
		var $id = function( id ) {
			return $( document.getElementById( id ) );
		};

		$id( 'n2fa-phone-number' ).on( 'input propertychange', function() {
			$id( 'n2fa-verified' ).hide();

			if ( $( this ).val() !== '' ) {
				$id( 'n2fa-verify-phone-number' ).fadeIn();
			} else {
				$id( 'n2fa-verify-phone-number' ).fadeOut();
			}
		});

		$id( 'n2fa-verify-phone-number' ).click(function() {
			$.post( ajaxurl, {
				action: 'n2fa_send_pin_code',
				n2fa_country_code: $id( 'n2fa-country-code' ).val(),
				n2fa_phone_number: $id( 'n2fa-phone-number' ).val()
			})
			.done(function( response ) {
				if ( response.request_id !== null ) {
					$id( 'n2fa-request-id' ).val( response.request_id );
				}
			});

			$id( 'n2fa-verify-pin-code-fields' ).fadeIn();
			$( this ).fadeOut();
		});

		$id( 'n2fa-verify-pin-code' ).click(function() {
			$id( 'n2fa-verify-pin-code-status' )
				.removeClass( 'n2fa-verify-success n2fa-verify-fail' )
				.fadeOut();

			$.post( ajaxurl, {
				action: 'n2fa_verify_pin_code',
				n2fa_request_id: $id( 'n2fa-request-id' ).val(),
				n2fa_pin_code: $id( 'n2fa-pin-code' ).val()
			})
			.done(function( response ) {
				if ( response.success === true ) {
					$id( 'n2fa-verify-pin-code-status' )
						.text( n2fa.verified )
						.addClass( 'n2fa-verify-success' )
						.fadeIn();
				} else {
					$id( 'n2fa-verify-pin-code-status' )
						.text( response.error )
						.addClass( 'n2fa-verify-fail' )
						.fadeIn();
				}
			})
			.fail(function() {
				$id( 'n2fa-verify-pin-code-status' )
					.text( n2fa.verification_failed )
					.addClass( 'n2fa-verify-fail' )
					.fadeIn();
			});
		});
	});
})( jQuery );
