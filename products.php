<?php
/*
Plugin Name: Products
Plugin URI: http://arcanepalette.com
Description: A simple shop plugin based on custom post types with integrated support for Cart66
Version: 0.3
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

/* 	Author's note
	Before you start yelling about how this plugin is barely functional
	or requires too much configuration (which, hopefully, when it's 
	finished, it won't, really), the reason I developed this plugin was
	primarily with Cart66 in mind.  Cart66 is an awesome shop plugin for 
	WordPress, but it lacks some of the robust features of some of those
	other shopping cart plugins and themes, like a dedicated storefront.
	Cart66 takes the stance of "we'll set you up with the system, but 
	you're on your own with how you want to run that on your site."  
	Which is fine.  This plugin is intended to step in the middle and 
	create a dedicated product area for the stuff you're selling with
	Cart66.  And if you're not using Cart66, this plugin will (hopefully)
	support PayPal and Google Checkout, as well.
	It might bear reiterating that any time a plugin tries to make
	assumptions about the formatting and layout of your theme, there's
	only a very small possibility the plugin will get it right. Therefore,
	some theme customization will most likely be required no matter what
	you're using, just to make the divs and headings match up. By using
	this plugin, you're acknowledging that this is your responsibility
	as the user of the plugin and if you have any questions, I will
	do my best to help.
*/	

/* changelog */
/*
	0.1
		Initial development
		added author's note and plugin meta
		created the post type
		added a custom taxonomy
		created meta boxes for button codes
		added custom icons
		defined global variables for plugin path and plugin image path
	
	0.2
		changed tag icon to match the small icon and adjusted credit accordingly
		added cross sales text meta field to use as anchor text for the cross sales link
		added post thumbnail support to not assume the theme enables it (thumbnail size will need to be set within the theme, see the note on line 76)

	0.3
*/

/* 	let's define some global values we're going to use later
	we're going to assume you're using wp 2.6+ and not worry about defining WP_PLUGIN_URL */
	define('product_plugin_path', WP_PLUGIN_URL . '/products/');
	define('product_plugin_images', product_plugin_path . 'images/');
	
/* create the custom post type */

// add post thumbnail support
// note, thumbnail size should be defined in the theme's functions.php file like this:
// set_post_thumbnail_size( 200, 200, true ); // 200 pixels wide by 200 pixels tall, hard crop mode
add_theme_support( 'post-thumbnails' );

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
}

add_action( 'init', 'post_type_products', 0 );

// add a product category taxonomy for products to have their own types
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

function ap_products_add_page() {
    $page = add_submenu_page('edit.php?post_type=ap_products','Products Settings', 'Settings', 'administrator', 'ap_products_settings', 'ap_products_settings_page' );
    //add_action( 'admin_print_scripts-plugins.php', 'espresso_requirements_scripts' );
    //add_action( 'admin_print_scripts-' . $page, 'espresso_requirements_scripts' );
}
add_action( 'admin_menu', 'ap_products_add_page' );

/* let's create some meta boxes */
/* p.s. meta boxes are aw3x0m3 */

function custom_meta_boxes_products() {
    add_meta_box("product-details", "Product Details", "meta_cpt_product", "ap_products", "normal", "low");
}

add_action('admin_menu', 'custom_meta_boxes_products');

function meta_cpt_product() {
    global $post;

	echo '<input type="hidden" name="product_noncename" id="product_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

   	echo '<label for="add_to_cart"><strong>Add to Cart Button</strong></label><br />';

	//ajax upload
	$wud = wp_upload_dir();

?>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			var uploader = new qq.FileUploader({
				element: document.getElementById('add_to_cart_upload'),
				action: '<?php echo get_bloginfo('siteurl'); ?>/',
				onComplete: function (id,fileName,responseJSON) {
					if(responseJSON.success == true)
						jQuery('#add_to_cart').val('<?php echo $wud["url"]; ?>/'+fileName);
				}
			});           
		});	
		</script>
	<input style="width: 95%;" id="add_to_cart" name="add_to_cart" value="<?php echo get_post_meta($post->ID, 'add_to_cart', true); ?>" type="text" /><div id="add_to_cart_upload"></div><em>Upload a custom Add to Cart button.  If left blank, a default Add to Cart button will be used.</em><br /><br />
	<?php	

	echo '<label for="cart66_id"><strong>Cart66 Product ID</strong></label><br />';
	echo '<input style="width: 15%;" type="text" name="cart66_id" value="'.get_post_meta($post->ID, 'cart66_id', true).'" /><br />';
	echo '<em>If using Cart66, enter the product ID number here.  You can get this from the <a href="admin.php?page=cart66-products">Cart66 Products</a> page.  Otherwise, leave blank.</em><br /><br />';
	
	echo '<label for="paypal_button_url"><strong>PayPal Button URL</strong></label><br />';
	echo '<input style="width: 95%;" type="text" name="paypal_button_url" value="'.get_post_meta($post->ID, 'paypal_button_url', true).'" /><br />';
	echo '<em>If using PayPal buttons, enter the URL for your Buy Now button.  You can get this by going to <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_merchant&nav=3" target="_blank">Merchant Services</a> -> Buy Now Buttons, create your button, then click to the Email tab.  You can also go to <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_button-management" target="_blank">My Saved Buttons</a> -> View Code (under Actions) to use a previously-generated button.  <span style="color: red;">Be sure to use the Email URL only, not the full button HTML code.</span></em><br /><br />';	

	echo '<label for="google_button_url"><strong>Google Checkout Button URL</strong></label><br />';
	echo '<input style="width: 95%;" type="text" name="google_button_url" value="'.get_post_meta($post->ID, 'google_button_url', true).'" /><br />';
	echo '<em>If using Google Checkout, enter the URL for your Google Checkout button.  You can get this by going to <a href="https://checkout.google.com/sell/orders" target="_blank">My Sales</a> -> <a href="https://checkout.google.com/sell2/settings?tab=tools&pli=1" target="_blank">Tools</a> -> <a href="https://checkout.google.com/sell2/settings?section=BuyNowButton" target="_blank">Buy Now Buttons</a>, enter your information and click Create Button Code, then copy the destination URL of the generated button or open it in a new tab and copy the url of the Google Checkout page.  <span style="color: red;">Be sure to use the URL of the Google Checkout page, not the button HTML code.</em><br /><br />';		

	echo '<label for="cross_sales"><strong>Cross-sales link</strong></label><br />';
	echo '<input style="width: 95%;" type="text" name="cross_sales" value="'.get_post_meta($post->ID, 'cross_sales', true).'" /><br />';
	echo '<em>By default, the product page will display a short list of possibly related items based on the category and tags.  However, you can also use this field to feature a related item that you want to promote.  <span style="color: red;">Because the related items displayed are automatically generated, it\'s a good idea to leave this blank and check the product page first to see which products are suggested so you do not add a duplicate.</span></em><br /><br />';	

	echo '<label for="cross_sales_text"><strong>Cross-sales text</strong></label><br />';
	echo '<input style="width: 95%;" type="text" name="cross_sales_text" value="'.get_post_meta($post->ID, 'cross_sales_meta', true).'" /><br />';
	echo '<em>This is the anchor text you want to use for the cross-sales link above.  <span style="color: red;">This is required for the cross-sales link. Leaving this blank will mean your cross-sales link will not display.</span><br />';
	
}

/* deal with uploading image */
if(isset ($_GET["qqfile"]) && strlen($_GET["qqfile"]))
{
	$pluginurl = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__));
	include(WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)) . '/' . 'includes/upload.php');
	$wud = wp_upload_dir();

	/* list of valid extensions */
	$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'ico');

	/* max file size in bytes */
	$sizeLimit = 6 * 1024 * 1024;

	$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	$result = $uploader->handleUpload($wud['path'].'/',true);
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	exit;
}


function product_uploader_scripts() {
	
	$pluginurl = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__));

	wp_enqueue_script('fileuploader', $pluginurl.'/includes/fileuploader.js',array('jquery'));
	wp_enqueue_style('fileuploadercss',$pluginurl.'/css/fileuploader.css');
}

function product_uploader_styles() {
	$pluginurl = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__));
	
	wp_enqueue_style('thickbox');
	wp_enqueue_style('fileuploadercss', $pluginurl.'/css/fileuploader.css');
}

add_action('admin_print_scripts', 'product_uploader_scripts');
add_action('admin_print_styles', 'product_uploader_styles');

/* When the post is saved, saves our product data */
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
		$value = implode(',', (array)$value);
		if(get_post_meta($post->ID, $key, FALSE)) {
			update_post_meta($post->ID, $key, $value);
		} else {
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key);
	}
}

add_action('save_post', 'product_save_product_postdata', 1, 2); // save the custom fields

add_action( 'admin_head', 'product_icon' );
function product_icon() {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-approducts .wp-menu-image {
            background: url(<?php echo product_plugin_images; ?>price-tag.png) no-repeat 6px -17px !important;
        }
	#menu-posts-approducts:hover .wp-menu-image, #menu-posts-approducts.wp-has-current-submenu .wp-menu-image {
			background: url(<?php echo product_plugin_images; ?>price-tag.png) no-repeat 6px 7px !important;
        }
    </style>
<?php
	} 

add_action('admin_head', 'product_header');
function product_header() {
        global $post_type;
	?>
	<style>
	<?php if (($_GET['post_type'] == 'ap_products') || ($post_type == 'ap_products')) : ?>
	#icon-edit { background: url(<?php echo product_plugin_images; ?>tag.png) no-repeat!important; }		
	<?php endif; ?>
        </style>
    <?php }
	
		/* price-tag icon by Yusuke Kamiyamane from the Fugue icon set
		released under a CC 3.0 Attribution Unported License http://creativecommons.org/licenses/by/3.0/
		http://p.yusukekamiyamane.com/
		*/
		
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
?>