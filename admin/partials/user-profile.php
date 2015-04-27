<table class="form-table">
	<tbody>
		<tr>
			<th><label for="n2fa-country-code"><?php _e( 'Choose a country', 'n2fa' ) ?></label></th>
			<td><?php echo $countries_dropdown ?></td>
		</tr>
		<tr>
			<th><label for="n2fa-phone-number"><?php _e( 'Your Phone number', 'n2fa' ) ?></label></th>
			<td>
				<input type="text" id="n2fa-phone-number" name="n2fa_phone_number" value="<?php echo esc_attr( $phone_number ) ?>" />
				<button type="button" id="n2fa-verify-phone-number" class="button button-secondary" style="display: none;">
					<?php _e( '<strong>Test</strong> (strongly recommended to prevent yourself from getting locked out of your account)', 'n2fa' ) ?>
				</button>
				<span id="n2fa-verified" class="n2fa-verified"<?php echo ( ! $phone_number ) ? ' style="display: none;"' : '' ?>><?php _e( 'VERIFIED', 'n2fa' ) ?></span>
			</td>
		</tr>
		<tr id="n2fa-verify-pin-code-fields" style="display: none;">
			<th><label for="n2fa-pin-code"><?php _e( 'Enter the PIN code sent to your phone number', 'n2fa' ) ?></label></th>
			<td>
				<input type="text" id="n2fa-pin-code" value="" />
				<input type="hidden" id="n2fa-request-id" value="" />
				<input type="button" id="n2fa-verify-pin-code" class="button button-secondary" value="<?php esc_attr_e( 'Verify', 'n2fa' ) ?>" />
				<span id="n2fa-verify-pin-code-status" style="display: none;"></span>
			</td>
		</tr>
	</tbody>
</table>
