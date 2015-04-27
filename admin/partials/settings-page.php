<div class="wrap">
	<h2><?php _e( 'Nexmo 2FA Settings', 'n2fa' ) ?></h2>

	<p><?php printf( __( 'Nexmo Verify makes it easy to verify phone numbers for second factor authentication. Simply register an account now at <a href="%1$s">%1$s</a>.', 'n2fa' ), 'https://dashboard.nexmo.com/register' ) ?></p>
	<p><?php printf( __( 'You can retrieve your key and secret by logging in to the <a href="%1$s">Nexmo dashboard</a>.', 'n2fa' ), 'https://dashboard.nexmo.com/login' ) ?></p>

	<form method="post" action="options.php">
		<?php settings_fields( 'nexmo_2fa_settings' ); ?>
		<?php do_settings_sections( 'nexmo_2fa_settings' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
