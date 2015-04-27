<?php
login_header(
	__( 'Nexmo Two-Factor Authentication', 'n2fa' ),
	'<p class="message">' . sprintf( __( 'Enter the PIN code sent to your phone number ending in <strong>%1$s</strong>', '2fa' ) , $phone_number ) . '</p>'
);
?>

<?php if ( ! empty( $errors ) ) : ?>
	<div id="login_error"><?php echo implode( '<br />', $errors ) ?></div>
<?php endif; ?>

<form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ) ?>" method="post" autocomplete="off">
	<p>
		<label for="n2fa_pin_code"><?php _e( 'PIN code:', 'n2fa' ) ?>
			<br />
			<input type="number" name="n2fa_pin_code" id="n2fa_pin_code" class="input" value="" size="6" />
		</label>
	</p>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Verify', 'n2fa' ) ?>" />
		<input type="hidden" name="log" value="<?php echo esc_attr( $username ) ?>" />
		<input type="hidden" name="n2fa_signature" value="<?php echo esc_attr( $signature ) ?>" />
		<input type="hidden" name="n2fa_request_id" value="<?php echo esc_attr( $response->request_id ) ?>" />
		<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ) ?>" />

		<?php if ( $remember_me ) : ?>
			<input type="hidden" name="rememberme" value="forever" />
		<?php endif; ?>
	</p>
</form>

<?php login_footer( 'n2fa_pin_code' ) ?>
