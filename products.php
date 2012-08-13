<?php
/*
Plugin Name: Products
Plugin URI: http://arcanepalette.com
Description: A simple shop plugin based on custom post types with integrated support for Cart66
Version: 0.3.1
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
		'name' => _x('Products', 'post type general name'),
		'singular_name' => _x('Product', 'post type singular name'),
		'add_new' => _x('Add New', 'product'),
		'add_new_item' => __('Add New Product'),
		'edit_item' => __('Edit Product'),
		'edit' => _x('Edit', 'products'),
		'new_item' => __('New Product'),
		'view_item' => __('View Product'),
		'search_items' => __('Search Products'),
		'not_found' =>  __('No products found'),
		'not_found_in_trash' => __('No products found in Trash'),
		'view' =>  __('View Product'),
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
	'name' => __( 'Product Categories' ),
	'singular_name' => __( 'Product Category' ),
	'search_items' => __( 'Search Categories' ),
	'all_items' => __( 'All Categories' ),
	'edit_item' => __( 'Edit Category' ),
	'update_item' => __( 'Update ' ),
	'add_new_item' => __( 'Add New Product Category' ),
	'new_item_name' => __( 'New Product Category Name' ),
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
			'label' => __( 'Google Checkout', 'products' )
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
		'add-to-cart' => null
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
    			echo '<p><label for="cart66_id"><strong>Cart66 Product ID</strong></label><br />';
				echo '<input style="width: 15%;" type="text" name="cart66_id" value="'.get_post_meta($post->ID, 'cart66_id', true).'" /><br />';
				echo '<em>Enter the Cart66 product ID number here.  You can get this from the <a href="admin.php?page=cart66-products">Cart66 Products</a> page.</em></p>';
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
    	if ( $options['products-html'] == 'html' ) {
			echo '<p><label for="button_html"><strong>Button HTML</strong></label><br />';
			echo '<textarea style="width: 55%; height: 100px; font-family: monospace;" name="google_button_html">' . wp_kses( get_post_meta($post->ID, 'button_html', true), $form_html ) . '</textarea>';
		}
    }
    if ( $options['cross-sales'] ) {
		echo '<label for="cross_sales"><strong>Cross-sales item</strong></label><br />';
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
	<?php if (($_GET['post_type'] == 'ap_products') || ($post_type == 'ap_products')) : ?>
		#icon-edit { background: url(<?php echo product_plugin_images; ?>tag.png) no-repeat!important; }
	<?php endif; ?>
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