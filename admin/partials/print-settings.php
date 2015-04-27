<table class="form-table">
	<tbody>
		<tr>
			<th><label for="api_key"><?php _e( 'Key', 'n2fa' ) ?></label></th>
			<td><input type="text" id="api_key" name="nexmo_2fa_settings[api_key]" value="<?php echo esc_attr( isset( $this->settings['api_key'] ) ? esc_attr( $this->settings['api_key']) : '' ) ?>" /></td>
		</tr>
		<tr>
			<th><label for="api_secret"><?php _e( 'Secret', 'n2fa' ) ?></label></th>
			<td><input type="text" id="api_secret" name="nexmo_2fa_settings[api_secret]" value="<?php echo esc_attr( isset( $this->settings['api_secret'] ) ? esc_attr( $this->settings['api_secret']) : '' ) ?>" /></td>
		</tr>
		<tr>
			<th><label for="xmlrpc_status"><?php _e( 'Disable XML-RPC (recommended)', 'n2fa' ) ?></label></th>
			<td>
				<input type="checkbox" id="xmlrpc_status" name="nexmo_2fa_settings[xmlrpc_status]" value="off" <?php
				checked( ( ! isset( $this->settings['xmlrpc_status'] ) || $this->settings['xmlrpc_status'] !== 'on' ) ? 'off' : 'on', 'off', true ) ?> />
				<?php _e( 'Enabling XML-RPC will allow external apps to link up to your WordPress website without two-factor authentication. We recommend you disable it and manage your website solely through the WordPress admin interface.', 'n2fa' ) ?>
			</td>
		</tr>
	</tbody>
</table>
