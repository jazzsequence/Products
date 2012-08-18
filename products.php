<?php
/*
Plugin Name: Products
git uri: https://github.com/jazzsequence/Products
Plugin URI: http://www.museumthemes.com
Description: A simple shop plugin based on custom post types with integrated support for Cart66
Version: 0.5.1
Author: Arcane Palette Creative Design
Author URI: http://arcanepalette.com/
License: GPL3
*/

/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    http://www.opensource.org/licenses/gpl-3.0.html
*/

/* 	let's define some global values we're going to use later
	we're going to assume you're using wp 2.6+ and not worry about defining WP_PLUGIN_URL */
	define('product_plugin_path', WP_PLUGIN_URL . '/products/');
	define('product_plugin_dir', WP_PLUGIN_DIR . '/products/');
	define('product_plugin_images', product_plugin_path . 'images/');
	include_once( product_plugin_dir . 'inc/updater.php' );


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
		'menu_position' => null,
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

/**
 * Testimonials Post Type
 * @author Chris Reynolds
 * @link http://justintadlock.com/archives/2010/04/29/custom-post-types-in-wordpress
 * @since 0.5
 * create a custom post type for testimonials
 */
function ap_post_type_testimonials() {
	$labels = array(
		'name' => __('Testimonials', 'products'),
		'singular_name' => __('Testimonial', 'products'),
		'add_new' => __('Add New', 'products'),
		'add_new_item' => __('Add New Testimonial','products'),
		'edit_item' => __('Edit Testimonial','products'),
		'edit' => __('Edit', 'products'),
		'new_item' => __('New Testimonial','products'),
		'view_item' => __('View Testimonial','products'),
		'search_items' => __('Search Testimonials','products'),
		'not_found' =>  __('No testimonials found','products'),
		'not_found_in_trash' => __('No testimonials found in Trash','products'),
		'view' =>  __('View Testimonial','products'),
		'parent_item_colon' => ''
  );
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 6,
		'supports' => array( 'editor' ),
		'exclude_from_search' => true
  );

  register_post_type( 'ap_testimonials', $args );
}
$options = get_option( 'ap_products_settings' );
if ( $options['shop-testimonials'] ) {
	/* add the custom post type */
	add_action( 'init', 'ap_post_type_testimonials', 0 );
}


/**
 * Testimonials Meta Boxes
 * @author Chris Reynolds
 * @since 0.5
 * this creates the meta boxes for additional information that we'll be using for the testimonials
 */
function ap_testimonials_meta() {
	add_meta_box( 'testimonials_meta', 'Testimonial Author Info', 'ap_testimonials_author_meta', 'ap_testimonials', 'normal', 'low' );
}
add_action( 'admin_menu', 'ap_testimonials_meta' );

function ap_testimonials_author_meta() {
	global $post;

	echo '<input type="hidden" name="ap_noncename" id="ap_noncename" value="' .
	wp_create_nonce( wp_basename(__FILE__) ) . '" />';

	echo '<p><label for="testimonial_author"><strong>Testimonial Author</strong></label><br />';
	echo '<input style="width: 60%;" type="text" name="testimonial_author" value="' . get_post_meta( $post->ID, 'testimonial_author', true ) . '" /><br />';
	echo '<em>The testimonial author\'s name.</em></p>';

	echo '<p><label for="testimonial_author_website"><strong>Author\'s Website Name</strong></label><br />';
	echo '<input style="width: 60%;" type="text" name="testimonial_author_website" value="' . get_post_meta( $post->ID, 'testimonial_author_website', true ) . '" /><br />';
	echo '<em>(Optional) If not blank, will display author\'s website under his/her name.</em></p>';

	echo '<p><label for="testimonial_author_website_url"><strong>Author\'s Website URL</strong></label><br />';
	echo '<input style="width: 60%;" type="text" name="testimonial_author_website_url" value="' . get_post_meta( $post->ID, 'testimonial_author_website_url', true ) . '" /><br />';
	echo '<em>(Optional) If not blank, will link website name to author\'s website.</em></p>';
}

/**
 * Custom columns for custom post type
 * @link http://devpress.com/blog/custom-columns-for-custom-post-types/
 * @author Chris Reynolds
 * @since 0.5
 * adds custom columns for testimonials post type so unused "title" column is not displayed
 */
function ap_edit_testimonials_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'testimonial_author' => __( 'Testimonial','products' ),
		'date' => __( 'Date','products' )
	);
	return $columns; // yeah, that one's important *smacks forehead*
}
add_filter( 'manage_edit-ap_testimonials_columns', 'ap_edit_testimonials_columns' );

function ap_manage_testimonials_columns( $column, $post_id ) {
	global $post;

	/* using a switch because that's what the stupid tut uses and I haven't yet figured
	out how to do it without the switch when I only want to return a single custom column */
	switch( $column ) {
		case 'testimonial_author' :
			$author = get_post_meta( $post_id, 'testimonial_author', true );
			/* if the author is blank, use the website name instead */
			if ( empty( $author )) {
				$author = get_post_meta( $post_id, 'testimonial_author_website', true );
			}
			echo '<a class="row-title" href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id . '&action=edit">' . $author . '</a>';
			break;
		default :
			break;
	}
}
add_action( 'manage_ap_testimonials_posts_custom_column', 'ap_manage_testimonials_columns', 10, 2 );
/* TODO make a product testimonials widget */

/**
 * Testimonials widget
 * @since 0.5
 * @author Chris Reynolds
 * @uses register_widget
 * @uses WP_Widget
 * creates a sidebar widget for testimonials
 * defaults to display shop testimonials on shop page and product testimonials on products page
 * if no product testimonials exist, will fall back to shop testimonials
 * if neither exist, won't display anything
 */
function ap_products_testimonials_widget() {
	register_widget( 'product_testimonials_widget' );
}

class product_testimonials_widget extends WP_Widget {
	function product_testimonials_widget() {
		global $ap_textdomain;
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'products_testimonial', 'description' => __('A widget for displaying quotes or product testimonials.',$ap_textdomain) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'opal-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'opal-widget', 'Opal E-Commerce Quote Widget', $widget_ops, $control_ops );
	}
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$quote = $instance['quote'];
		$source = $instance['source'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display quote from widget settings. */
		if ( $quote )
			echo '<div class="products-testimonial">&ldquo;'.$quote.'&rdquo;';

		/* Show quote source */
		if ( $source )
			echo '<p class="products-testimonial-source">&mdash;&nbsp;' . $source . '</p>';

		/* After widget (defined by themes). */
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['quote'] = strip_tags( $new_instance['quote'] );
		$instance['source'] = strip_tags( $new_instance['source'] );

		return $instance;
	}
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => '', 'quote' => '', 'source' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'quote' ); ?>"><?php _e('Quote:',$ap_textdomain); ?></label>
			<textarea id="<?php echo $this->get_field_id( 'quote' ); ?>" name="<?php echo $this->get_field_name( 'quote' ); ?>" style="width:100%;" /><?php echo $instance['quote']; ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'soruce' ); ?>"><?php _e('Quote Source:',$ap_textdomain); ?></label>
			<input id="<?php echo $this->get_field_id( 'source' ); ?>" name="<?php echo $this->get_field_name( 'source' ); ?>" value="<?php echo $instance['source']; ?>" style="width:100%;" />
		</p><?php
	}
}
add_action( 'widgets_init', 'ap_products_testimonials_widget' );

/**
 * Register Product Options
 * sets up the settings for the options page
 * @author Chris Reynolds
 * @since 0.3
 * @uses register_setting
 */
function ap_products_settings_init() {
	register_setting( 'ap_products_settings', 'ap_products_settings' /*, add sanitization callback */ );
}
add_action( 'admin_init', 'ap_products_settings_init' );

/**
 * Add Products Settings Page
 * adds the Settings page menu item in the Products menu
 * @author Chris Reynolds
 * @since 0.3
 * @uses add_submenu_page
 * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
 */
function ap_products_add_page() {
    $page = add_submenu_page('edit.php?post_type=ap_products','Products Options', 'Options', 'administrator', 'ap_products_settings', 'ap_products_settings_page' );
    //add_action( 'admin_print_scripts-plugins.php', 'espresso_requirements_scripts' );
    add_action( 'admin_print_scripts-' . $page, 'products_load_admin_scripts' );
}
add_action( 'admin_menu', 'ap_products_add_page' );

/**
 * Products Options Page
 * this is where the real settings page business is
 * @author Chris Reynolds
 * @since 0.3
 */
function ap_products_settings_page() {
	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	require_once( product_plugin_dir . 'inc/option-setup.php' );
	// we're using standard WP admin page markup
	?>
	<div class="wrap">
		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
			<div class="updated fade"><p><strong><?php _e( 'Options saved', 'products' ); ?></strong></p></div>
		<?php endif; ?>
		<div id="icon-edit" class="icon32 icon32-posts-ap_products"><br></div>
		<h2>Products Plugin Options</h2>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div id="post-body" class="metabox-holder columns-2">
			<?php //opal_side_box(); ?>
			<div id="post-body-content">
					<form method="post" action="options.php">
						<?php settings_fields( 'ap_products_settings' ); ?>
						<?php ap_products_do_options(); ?>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'products' ); ?>" />
							<input type="hidden" name="ap-core-settings-submit" value="Y" />
						</p>
					</form>
				</div><!-- closes post-body-content -->
			</div><!-- closes post-body -->
		</div><!-- closes poststuff -->
	</div>
	<?php
}

/**
 * Merchant options
 * determines which merchant you want to use.  Currently-supported options are Cart66, PayPal & Google Checkout
 * @author Chris Reynolds
 * @since 0.3
 */
function products_merchant_options() {
	$products_merchant = array(
		'cart66' => array(
			'value' => 'cart66',
			'label' => __( 'Cart66', 'products' )
		),
		'google' => array(
			'value' => 'google',
			'label' => __( 'Google Wallet', 'products' )
		),
		'paypal' => array(
			'value' => 'paypal',
			'label' => __( 'PayPal Standard', 'products' )
		)
	);
	return $products_merchant;
}

/**
 * Embed vs. URL options
 * option to use HTML code or URL for PayPal or Google
 * @author Chris Reynolds
 * @since 0.3.1
 */
function products_HTML_URI_option() {
	$products_html = array(
		'html' => array(
			'value' => 'html',
			'label' => __( 'HTML', 'products' ),
			'help' => __( 'some help text goes here', 'products' )
		),
		'url' => array(
			'value' => 'url',
			'label' => __( 'URL', 'products' ),
		)
	);
	return $products_html;
}

/**
 * True/False option
 * @since museum-core/1.0.2
 * @author Chris Reynolds
 * generic yes/no function used for true/false options
 */
function products_true_false() {
    $products_true_false = array(
        'true' => array(
            'value' => true,
            'label' => __('Yes', 'products')
        ),
        'false' => array(
            'value' => false,
            'label' => __('No', 'products')
        )
    );
    return $products_true_false;
}

/**
 * Default options
 * an array of defaults
 * @author Chris Reynolds
 * @since 0.3.1
 */
function products_get_defaults() {
	$products_defaults = array(
		'products-merchant' => 'paypal',
		'products-html' => 'html',
		'cross-sales' => false,
		'add-to-cart' => null,
		'cart66_id' => '',
		'shop-testimonials' => false,
		'product-testimonials' => false
	);
	return $products_defaults;
}

/**
 * Custom meta boxes
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
    // TODO add defaults array
    $defaults = '';
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
    if ( $options['cross-sales'] ) {
		echo '<p><label for="cross_sales"><strong>Cross-sales item</strong></label><br />';
	    $cross_sales_selected = get_post_meta( $post->ID, 'cross_sales', true );
		?>
		<select name="cross_sales" id="cross_sales">
		  <?php
		  $my_loop = new WP_Query( array( 'post_type' => 'ap_products', 'posts_per_page' => -1 ) );
		  while ( $my_loop->have_posts() ) : $my_loop->the_post();
		  	$title = get_the_title();
		  	$permalink = get_permalink();
		  	?>
		  	<option value="<?php echo $permalink ?>" <?php selected( $cross_sales_selected, $permalink ); ?>><?php echo $title ?></option>
		<?php endwhile; ?>
	  	</select><br />
	  	<?php wp_reset_postdata();
		echo '<em>Select the item you would like to feature for cross-sales on this product\'s page by choosing from the list above.</p>';
	}
	if ( $options['product-testimonials'] ) {
		echo '<p><label for="testimonials"><strong>Product testimonials</strong></label><br />';
		wp_editor( get_post_meta( $post->ID, 'testimonials', true ), 'testimonials', array( 'textarea_rows' => 5 ) );
		echo '<em>To be displayed in the sidebar on the product page.  If left blank, shop testimonials will be used instead (if any exist).</em></p>';
	}

}

/**
 * Load admin scripts
 * @author Chris Reynolds
 * @since 0.3.1
 * loads uploader scripts for the Options page
 */
function products_load_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
	wp_enqueue_script('products_uploader', product_plugin_path . 'js/uploader.js', array( 'jquery', 'media-upload', 'thickbox' ) );
}

/**
 * Save product postdata
 * deal with saving the post and meta
 * @author Chris Reynolds
 * @since 0.1
 * @uses wp_verify_nonce
 * @uses plugin_basename
 * @uses current_user_can
 * @uses save_post
 * @uses update_post_meta
 * @uses add_post_meta
 * @uses delete_post_meta
 */
function product_save_product_postdata($post_id, $post) {
   	if ( !wp_verify_nonce( $_POST['product_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}

	/* confirm user is allowed to save page/post */
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post->ID ))
		return $post->ID;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	}

	/* ready our data for storage */
	foreach ($_POST as $key => $value) {
        $mydata[$key] = $value;
    }

	/* Add values of $mydata as custom fields */
	foreach ($mydata as $key => $value) {
		if( $post->post_type == 'revision' ) return;
		//$value = implode(',', (array)$value);
		if(get_post_meta($post->ID, $key, FALSE)) {
			update_post_meta($post->ID, $key, $value);
		} else {
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key);
	}
}

add_action('save_post', 'product_save_product_postdata', 1, 2); // save the custom fields

/**
 * Product icons
 * deals with the custom icons for the product pages
 * @author Chris Reynolds
 * @since 0.1
 * @uses admin_head
 * price-tag icon by Yusuke Kamiyamane from the Fugue icon set
 * released under a CC 3.0 Attribution Unported License http://creativecommons.org/licenses/by/3.0/
 * @link http://p.yusukekamiyamane.com/
 */
function product_icons() {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-ap_products .wp-menu-image {
            background: url(<?php echo product_plugin_images; ?>price-tag.png) no-repeat 6px -17px !important;
        }
		#menu-posts-ap_products:hover .wp-menu-image, #menu-posts-ap_products.wp-has-current-submenu .wp-menu-image {
			background: url(<?php echo product_plugin_images; ?>price-tag.png) no-repeat 6px 7px !important;
        }
        #menu-posts-ap_testimonials .wp-menu-image {
            background: url(<?php echo product_plugin_images; ?>balloon-quotation.png) no-repeat 6px -17px !important;
        }
		#menu-posts-ap_testimonials:hover .wp-menu-image, #menu-posts-ap_testimonials.wp-has-current-submenu .wp-menu-image {
			background: url(<?php echo product_plugin_images; ?>balloon-quotation.png) no-repeat 6px 7px !important;
        }
		#icon-edit.icon32-posts-ap_products { background: url(<?php echo product_plugin_images; ?>tag.png) no-repeat!important; }
		#icon-edit.icon32-posts-ap_testimonials { background: url(<?php echo product_plugin_images; ?>testimonial.png) no-repeat!important; }
    </style>
<?php
}
add_action( 'admin_head', 'product_icons' );

/* move template files on activation */
/* commenting this out until we're ready to move the files
register_activation_hook(__FILE__, "products_activation");

function products_activation()
{
	//move files around
	$tmp = wp_upload_dir();
	$uploadpath = $tmp["basedir"].'/';
	$themepath =  get_template_directory()."/";

	$templates = glob(product_plugin_path."template*");
	foreach($templates as $t)
	{
		copy($t,$themepath.basename($t));
	}

	$singles = glob(product_plugin_path."single-ap_products.php");
	foreach($singles as $s)
	{
		copy($s,$themepath.basename($s));
	}
}
*/
// Start of Presstrends Magic
function presstrends_plugin() {

// PressTrends Account API Key
$api_key = 'i93727o4eba1lujhti5bjgiwfmln5xm5o0iv';
$auth = 'wcc72j1n6tao34jp4zhby0y6zrycf3jlz';

// Start of Metrics
global $wpdb;
$data = get_transient( 'presstrends_data' );
if (!$data || $data == ''){
$api_base = 'http://api.presstrends.io/index.php/api/pluginsites/update/auth/';
$url = $api_base . $auth . '/api/' . $api_key . '/';
$data = array();
$count_posts = wp_count_posts();
$count_pages = wp_count_posts('page');
$comments_count = wp_count_comments();
$theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');
$plugin_count = count(get_option('active_plugins'));
$all_plugins = get_plugins();
$plugin_name = '&';
foreach($all_plugins as $plugin_file => $plugin_info){
$plugin_name .= $plugin_info['Name'];
$plugin_name .= '&';}
$plugin_data = get_plugin_data( __FILE__ );
$plugin_version = $plugin_data['Version'];
$posts_with_comments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type='post' AND comment_count > 0");
$comments_to_posts = number_format(($posts_with_comments / $count_posts->publish) * 100, 0, '.', '');
$pingback_result = $wpdb->get_var('SELECT COUNT(comment_ID) FROM '.$wpdb->comments.' WHERE comment_type = "pingback"');
$data['url'] = stripslashes(str_replace(array('http://', '/', ':' ), '', site_url()));
$data['posts'] = $count_posts->publish;
$data['pages'] = $count_pages->publish;
$data['comments'] = $comments_count->total_comments;
$data['approved'] = $comments_count->approved;
$data['spam'] = $comments_count->spam;
$data['pingbacks'] = $pingback_result;
$data['post_conversion'] = $comments_to_posts;
$data['theme_version'] = $plugin_version;
$data['theme_name'] = urlencode($theme_data['Name']);
$data['site_name'] = str_replace( ' ', '', get_bloginfo( 'name' ));
$data['plugins'] = $plugin_count;
$data['plugin'] = urlencode($plugin_name);
$data['wpversion'] = get_bloginfo('version');
foreach ( $data as $k => $v ) {
$url .= $k . '/' . $v . '/';}
$response = wp_remote_get( $url );
set_transient('presstrends_data', $data, 60*60*24);}
}

// PressTrends WordPress Action
add_action('admin_init', 'presstrends_plugin');
?>