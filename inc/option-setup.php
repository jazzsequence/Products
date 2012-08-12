<?php
/**
 * Merchant options setting
 * displays the dropdown for merchant setting
 * @author Chris Reynolds
 * @since 0.3
 * @uses products_merchant_options
 */
function products_merchant_option_display() {
	// TODO add a defaults array
	$defaults = '';
	$options = get_option( 'ap_products_settings', $defaults );

	ob_start();
	?>
	<tr valign="top"><th scope="row"><?php _e( 'Merchant', 'products' ); ?></th>
		<td>
			<select name="ap_products_settings[products-merchant]">
			<?php
				$selected = $options['products-merchant'];
				foreach ( products_merchant_options() as $option ) {
					$label = $option['label'];
					$value = $option['value'];
					echo '<option value="' . $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
				} ?>
			</select><br />
			<label class="description" for="ap_products_settings[products-merchant]"><?php _e( 'Select which merchant you will be using for your purchases.', 'products' ); ?>
		</td>
	</tr>

	<?php
}

/**
 * Do Product option stuff
 * @author Chris Reynolds
 * @since 0.3
 * loads the options
 */
function ap_products_do_options() {
	$options_before = '<table class="form-table">';
	$options_after = '</table>';

	echo $options_before;
	products_merchant_option_display();
	// do stuff
	echo $options_after;
}
?>