<?php
/**
 * Merchant options setting
 * displays the dropdown for merchant setting
 * @author Chris Reynolds
 * @since 0.3
 * @uses products_merchant_options
 * @uses products_get_defaults
 */
function products_merchant_option_display() {
	$defaults = products_get_defaults();
	$options = get_option( 'ap_products_settings', $defaults );
	?>
	<tr valign="top"><th scope="row"><?php _e( 'Merchant', 'products' ); ?></th>
		<td>
			<select name="ap_products_settings[products-merchant]" id="merchant">
			<?php
				$selected = $options['products-merchant'];
				foreach ( products_merchant_options() as $option ) {
					$label = $option['label'];
					$value = $option['value'];
					echo '<option value="' . $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
				} ?>
			</select><br />
			<label class="description" for="ap_products_settings[products-merchant]"><?php _e( 'Select which merchant you will be using for your purchases.', 'products' ); ?></label>
		</td>
	</tr>
	<?php
}

/**
 * Embedded HTML or url option for Google/PayPal
 * displays an option to use full HTML embed or just a url if Google or PayPal are selected
 * @author Chris Reynolds
 * @since 0.3.1
 * @uses products_HTML_URI_option
 * @uses products_merchant_option_display
 * @uses products_get_defaults
 * @link http://jsfiddle.net/wwTxw/
 */
function products_HTML_URI_option_display() {
	$defaults = products_get_defaults();
	$options = get_option( 'ap_products_settings', $defaults );
	?>
	<script type="text/javascript">
		jQuery(function() {
			jQuery('#merchant').change(function(){
				if (jQuery(this).val() == "cart66") {
					jQuery('#html_uri').hide();
				} else {
					jQuery('#html_uri').show();
				}
			});
		});
	</script>
	<?php if ( $options['products-merchant'] != 'cart66' ) { ?>
		<tr valign="top" id="html_uri"><th scope="row"><?php _e( 'Use HTML code or URL?', 'products' ); ?></th>
			<td>
				<select name="ap_products_settings[products-html]">
				<?php
					$selected = $options['products-html'];
					$help = __( 'If HTML is selected, you can copy and paste the HTML code for your button from PayPal or Google into the box on the products page.  If URL is selected, you can use the direct URL to the checkout page on either PayPal or Google.', 'products' );
					foreach ( products_HTML_URI_option() as $option ) {
						$label = $option['label'];
						$value = $option['value'];
						echo '<option value="'. $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
					} ?>
				</select><br />
				<label class="description" for="ap_products_settings[products-html]"><?php echo $help; ?></label>
			</td>
		</tr>
	<?php }
}

/**
 * Display Cross-sales?
 * option to define an item to display as a cross-sale for a specific product
 * @author Chris Reynolds
 * @since 0.3.1
 * @uses products_true_false
 * @uses products_get_defaults
 */
function products_cross_sales_option_display() {
	$defaults = products_get_defaults();
	$options = get_option( 'ap_products_settings', $defaults );
	?>
	<tr valign="top"><th scope="row"><?php _e( 'Use Cross-sales?', 'products' ); ?></th>
		<td>
			<select name="ap_products_settings[cross-sales]">
			<?php
				$selected = $options['cross-sales'];
				$help = __( 'Use this field to display an option for cross-sales link on the add/edit product page to feature a related item that you want to promote on that product page.', 'products' );
				foreach ( products_true_false() as $option ) {
					$label = $option['label'];
					$value = $option['value'];
					echo '<option value="' . $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
				} ?>
			</select><br />
			<label class="description" for="ap_products_settings[cross-sales]"><?php echo $help; ?></label>
		</td>
	</tr>
	<?php
}
/**
 * Shop testimonials option
 * @since 0.5
 * @author Chris Reynolds
 * displays an option to display shop testimonials
 */
function products_shop_testimonials_option_display() {
	$defaults = products_get_defaults();
	$options = get_option( 'ap_products_settings', $defaults );
	?>
	<tr valign="top"><th scope="row"><?php _e( 'Display Shop Testimonials?', 'products' ); ?></th>
		<td>
			<select name="ap_products_settings[shop-testimonials]">
			<?php
				$selected = $options['shop-testimonials'];
				$help = __( 'Displays store testimonials on shop pages (and product pages, where applicable).', 'products' );
				foreach ( products_true_false() as $option ) {
					$label = $option['label'];
					$value = $option['value'];
					echo '<option value="' . $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
				} ?>
			</select><br />
			<label class="description" for="ap_products_settings[shop-testimonials]"><?php echo $help; ?></label>
		</td>
	</tr>
	<?php
}

/**
 * Product testimonials option
 * @since 0.5
 * @author Chris Reynolds
 * displays an option to display product testimonials (on product pages)
 */
function products_product_testimonials_option_display() {
	$defaults = products_get_defaults();
	$options = get_option( 'ap_products_settings', $defaults );
	?>
	<tr valign="top"><th scope="row"><?php _e( 'Display Product Testimonials?', 'products' ); ?></th>
		<td>
			<select name="ap_products_settings[product-testimonials]">
			<?php
				$selected = $options['product-testimonials'];
				$help = __( 'Displays product testimonials on product pages (defaults to shop testimonials when no product testimonial exists).', 'products' );
				foreach ( products_true_false() as $option ) {
					$label = $option['label'];
					$value = $option['value'];
					echo '<option value="' . $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
				} ?>
			</select><br />
			<label class="description" for="ap_products_settings[product-testimonials]"><?php echo $help; ?></label>
		</td>
	</tr>
	<?php
}

/**
 * Add to Cart Button
 * @since 0.3.1
 * @author Chris Reynolds
 * @uses get_option
 * @uses thickbox
 * @uses media-upload
 * @uses products_get_defaults
 */
function products_add_to_cart_button_display() {
	$defaults = products_get_defaults();
	$options = get_option( 'ap_products_settings', $defaults );
	if ( !isset($options['add-to-cart']) || $options['add-to-cart'] == '' ) {
		if ( $options['products-merchant'] == 'paypal' ) {
			$add_to_cart = 'https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif';
		}
		if ( $options['products-merchant'] == 'google' ) {
			$add_to_cart = 'https://checkout.google.com/buttons/buy.gif?w=117&h=48&style=white&variant=text&loc=en_US';
		}
		if ( $options['products-merchant'] == 'cart66' ) {
			$add_to_cart = product_plugin_images . 'add-to-cart.png';
		}
	}
	$help = __( 'Use the uploader to upload a PNG, JPG, or GIF file to use as an add to cart button.  If no image is specified, the default will be used.', 'products' )
	?>
		<tr valign="top"><th scope="row"><?php _e( 'Add to Cart button', 'products' ); ?></th>
			<td>
				<input id="upload_image" type="text" size="36" name="ap_products_settings[add-to-cart]" value="<?php esc_attr_e( $options['add-to-cart'] ); ?>" />
				<input id="upload_image_button" type="button" class="button" value="<?php _e('Upload Image','products'); ?>" />
				<br />
				<label class="description" for="ap_products_settings[favicon]"><?php echo $help; ?></label><br />
				<?php
				if ( isset($options['add-to-cart']) && $options['add-to-cart'] != '' )
					$add_to_cart = $options['add-to-cart'];
				?>
				<img src="<?php echo $add_to_cart; ?>" alt="current button image" />
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
	products_HTML_URI_option_display();
	products_cross_sales_option_display();
	products_add_to_cart_button_display();
	products_shop_testimonials_option_display();
	products_product_testimonials_option_display();
	echo $options_after;
}
?>