<?php
/**
 * Products Post Type
 * created the custom post type
 * @author Chris Reynolds
 * @since 0.1
 * @link http://justintadlock.com/archives/2010/04/29/custom-post-types-in-wordpress
 * @uses register_post_type
 * @uses add_theme_support
 */
function post_type_products() {
    $labels = array(
		'name' => __('Products', 'products'),
		'singular_name' => __('Product', 'products'),
		'add_new' => __('Add New', 'products'),
		'add_new_item' => __('Add New Product','products'),
		'edit_item' => __('Edit Product','products'),
		'edit' => __('Edit', 'products'),
		'new_item' => __('New Product','products'),
		'view_item' => __('View Product','products'),
		'search_items' => __('Search Products','products'),
		'not_found' =>  __('No products found','products'),
		'not_found_in_trash' => __('No products found in Trash','products'),
		'view' =>  __('View Product','products'),
		'parent_item_colon' => ''
  	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array("slug" => "products"),
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array( 'title','editor','thumbnail' ),
		'exclude_from_search' => false,
		'menu_position' => 5,
		'taxonomies' => array('post_tag','product_category'),
  	);

  	register_post_type( 'ap_products', $args );

	// add post thumbnail support
	// note, thumbnail size should be defined in the theme's functions.php file like this:
	// set_post_thumbnail_size( 200, 200, true ); // 200 pixels wide by 200 pixels tall, hard crop mode
	add_theme_support( 'post-thumbnails' );
}
add_action( 'init', 'post_type_products', 0 );

/**
 * Product info meta boxes
 * @author Chris Reynolds
 * @since 0.7
 * @uses add_meta_box
 * adds additional meta information for products.  All this stuff is optional but can be used for schema.org schemas
 */
function ap_products_info_metaboxes() {
	add_meta_box( "product-meta", "Product Information", "ap_products_info_meta", "ap_products", "side", "core");
}
add_action( 'admin_menu', 'ap_products_info_metaboxes' );

/**
 * Product Meta
 * @author Chris Reynolds
 * @since 0.7
 * @uses wp_create_nonce
 * @uses get_post_meta
 * creates the actual meta fields on the product pages
 */
function ap_products_info_meta() {
	global $post;
	$options = get_option( 'ap_products_settings' );

	echo '<input type="hidden" name="ap_noncename" id="ap_noncename" value="' .
	wp_create_nonce( wp_basename(__FILE__) ) . '" />';

	if ( $options['products-merchant'] != 'cart66' ) {
		echo '<p><label for="price"><strong>Price</strong></label><br />';
		echo '<input size="5" type="text" name="price" value="' . get_post_meta( $post->ID, 'price', true ) . '" /><br />';
		echo '<em>Item price.</em></p>';
	}

	echo '<p><label for="inquire-sold-out"><strong>Inquire for Price/Sold Out?</strong><label><br />';
	echo '<select name="inquire-sold-out">';
	$selected = get_post_meta( $post->ID, 'inquire-sold-out', true );
	foreach ( products_inquire_sold_out_options() as $option ) {
		$label = $option['label'];
		$value = $option['value'];
		echo '<option value="' . $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
	}
	echo '</select><br />';
	echo '<em>(Optional) Define whether the product should be set to "Inquire for price" or sold out.</em></p>';

	if ( $options['products-merchant'] != 'cart66' ) {
		echo '<p><label for="item_num"><strong>Item Number</strong></label><br />';
		echo '<input class="widefat" type="text" name="item_num" value="' . get_post_meta( $post->ID, 'item_num', true ) . '" /><br />';
		echo '<em>(Optional) Unique item/model number.</em></p>';
	}

	echo '<p><label for="brand"><strong>Brand</strong></label><br />';
	echo '<input class="widefat" type="text" name="brand" value="' . get_post_meta( $post->ID, 'brand', true ) . '" /><br />';
	echo '<em>(Optional) Brand, manufacturer or line.</em></p>';

	echo '<p><label for="model"><strong>Model</strong></label><br />';
	echo '<input class="widefat" type="text" name="model" value="' . get_post_meta( $post->ID, 'model', true ) . '" /><br />';
	echo '<em>(Optional) Model name or number.</em></p>';

	echo '<p><label for="dimensions"><strong>Dimensions</strong></label><br />';
	echo '<input class="widefat" type="text" name="dimensions" value="' . get_post_meta( $post->ID, 'dimensions', true ) . '" /><br />';
	echo '<em>(Optional) Product dimensions.</em></p>';

	echo '<p><label for="notes"><strong>Other notes</strong></label><br />';
	echo '<textarea class="widefat" name="notes">' . wp_kses( get_post_meta($post->ID, 'notes', true) ) . '</textarea>';
	echo '<em>(Optional) Any other notes or product variations.</em></p>';
}

/**
 * Main meta boxes
 * adds some custom meta boxes.  This just declares the meta boxes and the function to handle them
 * @author Chris Reynolds
 * @since 0.1
 * @uses add_meta_box
 * p.s. meta boxes are aw3x0m3
 */
function custom_meta_boxes_products() {
	// let's create some meta boxes
    add_meta_box("product-details", "Product Details", "meta_cpt_product", "ap_products", "normal", "low");
}
add_action('admin_menu', 'custom_meta_boxes_products');

/**
 * Meta CPT Product
 * this actually handles how the meta boxes will appear on the Edit Product pages
 * @author Chris Reynolds
 * @since 0.1
 * @uses wp_create_nonce
 * @uses get_post_meta
 */
function meta_cpt_product() {
    global $post;

    //$defaults = products_get_defaults();
    $options = get_option( 'ap_products_settings' );

	echo '<input type="hidden" name="product_noncename" id="product_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

    if ( $options['products-merchant'] ) {
    	$form_html = array(
    		'form' => array(
    			'action' => array(),
    			'id' => array(),
    			'method' => array(),
    			'name' => array(),
    			'target' => array()
    		),
    		'input' => array(
    			'name' => array(),
    			'type' => array(),
    			'value' => array(),
    			'alt' => array(),
    			'src' => array(),
    			'border' => array(),
    			'height' => array(),
    			'width' => array()
    		)
    	);
    	switch( $options['products-merchant'] ) {
    		case 'cart66' :
    			echo '<p><label for="cart66_id"><strong>Cart66 Product</strong></label><br />';
				$products_selected = get_post_meta( $post->ID, 'cart66_id', true );
				//var_dump($products_selected);?>
				<select id="cart66_id" name="cart66_id">
				<?php
			      	$products = Cart66Product::loadProductsOutsideOfClass();
			      	//$products = $product->getModels("where id>0", "order by name");
			      	if(count($products)):
			        	$i=0;
			        	foreach($products as $p) {
			          		$optionClasses = "";
			          		if($p->item_number==""){
			            		$id=$p->id;
			            		$type='id';
			            		$description = "";
			          		} else {
			            		$id=$p->item_number;
			            		$type='item';
			            		$description = '(# '.$p->item_number.')';
			          		}

			          		$types[] = htmlspecialchars($type);

			          		if(CART66_PRO && $p->is_paypal_subscription == 1) {
					            $sub = new Cart66PayPalSubscription($p->id);
					            $subPrice = strip_tags($sub->getPriceDescription($sub->offerTrial > 0, '(trial)'));
					            $prices[] = htmlspecialchars($subPrice);
					            $optionClasses .= " subscriptionProduct ";
					            //Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] subscription price in dialog: $subPrice");
			          		} else {
					            $priceDescription = __('Price:', 'cart66') . ' ' . CART66_CURRENCY_SYMBOL . $p->price;
					            if($p->price_description != null) {
					              $priceDescription = $p->price_description;
			        		    }

				            $prices[] = htmlspecialchars(strip_tags($priceDescription));
			    		      }
			    		       ?>
			    		      <option value="<?php echo $id; ?>" <?php selected( $products_selected, $id ); ?>><?php echo $p->name . ' ' . $description; ?></option>
			          		<?php
			          		$i++;
			        	}
			      		else: ?>
			      			<option value=""><?php _e( 'No Products', 'cart66' ); ?></option>
			      		<?php endif; ?>
				</select><?php
				break;
			case 'paypal' :
				if ( $options['products-html'] == 'url' ) {
					echo '<p><label for="paypal_button_url"><strong>PayPal Button URL</strong></label><br />';
					echo '<input style="width: 95%;" type="text" name="paypal_button_url" value="'.get_post_meta($post->ID, 'paypal_button_url', true).'" /><br />';
					echo '<em>If using PayPal buttons, enter the URL for your Buy Now button.  You can get this by going to <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_merchant&nav=3" target="_blank">Merchant Services</a> -> Buy Now Buttons, create your button, then click to the Email tab.  You can also go to <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_button-management" target="_blank">My Saved Buttons</a> -> View Code (under Actions) to use a previously-generated button.  <span style="color: red;">Be sure to use the Email URL only, not the full button HTML code.</span></em></p>';
				}
				break;
			case 'google' :
				if ( $options['products-html'] == 'url' ) {
					echo '<p><label for="google_button_url"><strong>Google Checkout Button URL</strong></label><br />';
					echo '<input style="width: 95%;" type="text" name="google_button_url" value="'.get_post_meta($post->ID, 'google_button_url', true).'" /><br />';
					echo '<em>If using Google Checkout, enter the URL for your Google Checkout button.  You can get this by going to <a href="https://checkout.google.com/sell/orders" target="_blank">My Sales</a> -> <a href="https://checkout.google.com/sell2/settings?tab=tools&pli=1" target="_blank">Tools</a> -> <a href="https://checkout.google.com/sell2/settings?section=BuyNowButton" target="_blank">Buy Now Buttons</a>, enter your information and click Create Button Code, then copy the destination URL of the generated button or open it in a new tab and copy the url of the Google Checkout page.  <span style="color: red;">Be sure to use the URL of the Google Checkout page, not the button HTML code.</em><br /><br />';
				}
			break;
    	}
    	if ( $options['products-html'] == 'html' && $options['products-merchant'] != 'cart66' ) {
			echo '<p><label for="button_html"><strong>Button HTML</strong></label><br />';
			echo '<textarea style="width: 55%; height: 100px; font-family: monospace;" name="button_html">' . wp_kses( get_post_meta($post->ID, 'button_html', true), $form_html ) . '</textarea>';
		}
    }
	if ( $options['product-testimonials'] ) {
		echo '<p><label for="testimonials"><strong>Product testimonials</strong></label><br />';
		wp_editor( get_post_meta( $post->ID, 'testimonials', true ), 'testimonials', array( 'textarea_rows' => 5 ) );
		echo '<em>To be displayed in the sidebar on the product page.  If left blank, shop testimonials will be used instead (if any exist).</em></p>';
		echo '<p><label for="testimonial_author"><strong>Testimonial Author</strong></label><br />';
		echo '<input class="widefat" type="text" name="testimonial_author" value="' . get_post_meta( $post->ID, 'testimonial_author', true ) . '" /><br />';
		echo '<em>The testimonial author\'s name.</em></p>';

		echo '<p><label for="testimonial_author_website"><strong>Author\'s Website Name</strong></label><br />';
		echo '<input class="widefat" type="text" name="testimonial_author_website" value="' . get_post_meta( $post->ID, 'testimonial_author_website', true ) . '" /><br />';
		echo '<em>(Optional) If not blank, will display author\'s website under his/her name.</em></p>';

		echo '<p><label for="testimonial_author_website_url"><strong>Author\'s Website URL</strong></label><br />';
		echo '<input class="widefat" type="text" name="testimonial_author_website_url" value="' . get_post_meta( $post->ID, 'testimonial_author_website_url', true ) . '" /><br />';
		echo '<em>(Optional) If not blank, will link website name to author\'s website.</em></p>';
	}
    if ( $options['cross-sales'] ) {
		echo '<p><label for="cross_sales"><strong>Cross-sales item</strong></label><br />';
	    $cross_sales_selected = get_post_meta( $post->ID, 'cross_sales', true );
		?>
		<select name="cross_sales" id="cross_sales">
		  <?php
			$temp = $wp_query;
			$wp_query = null;
			$wp_query = new WP_Query();
		  	$wp_query = new WP_Query( array( 'post_type' => 'ap_products', 'posts_per_page' => -1 ) );
		 	while ( $wp_query->have_posts() ) : $wp_query->the_post();
		  		$title = get_the_title();
		  		$permalink = get_permalink();
		  	?>
		  		<option value="<?php echo $permalink ?>" <?php selected( $cross_sales_selected, $permalink ); ?>><?php echo $title ?></option>
			<?php endwhile;
			$wp_query = $temp;
			$temp = null;
			wp_reset_query();?>
	  	</select><br />
	  	<?php
		echo '<em>Select the item you would like to feature for cross-sales on this product\'s page by choosing from the list above.</p>';
	}
}

/**
 * Product Categories
 * add a product category taxonomy for products to have their own types
 * @author Chris Reynolds
 * @since 0.1
 * @uses register_taxonomy
 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function product_categories() {
$product_category_labels = array(
	'name' => __( 'Product Categories','products' ),
	'singular_name' => __( 'Product Category','products' ),
	'search_items' => __( 'Search Categories','products' ),
	'all_items' => __( 'All Categories','products' ),
	'edit_item' => __( 'Edit Category','products' ),
	'update_item' => __( 'Update','products' ),
	'add_new_item' => __( 'Add New Product Category','products' ),
	'new_item_name' => __( 'New Product Category Name','products' ),
	);
	register_taxonomy(
		'product_category',
		'ap_products',
		array(
			'public' => true,
			'show_ui' => true,
			'hierarchical' => true,
			'labels' => $product_category_labels,
			'query_var' => 'product_category',
			'rewrite' => array( 'slug' => 'product-category' ),
			'show_in_nav_menus' => true
			)
	); // register the product category taxonomy for products

}

add_action( 'init', 'product_categories', 0 ); // taxonomy for product categories
?>