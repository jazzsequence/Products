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
// TODO add item number, brand, model(?), price meta fields
// TODO add inquire for price option
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
	include_once( product_plugin_dir . 'inc/post_type_products.php' );
	include_once( product_plugin_dir . 'inc/post_type_testimonials.php' );
	include_once( product_plugin_dir . 'inc/widgets.php' );

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
 * Insert post data
 * @author Chris Reynolds
 * @since 0.5.1
 * @link http://wordpress.stackexchange.com/a/7522
 * @uses update_post_meta
 * @uses wp_insert_post_data
 * stores post meta data for the custom post types
 */
function ap_products_insert_post_data($data,$postarr) {
	if ($postarr['post_type'] == 'ap_testimonials') {
		update_post_meta($postarr['ID'], 'testimonial_author', $postarr['testimonial_author']);
		update_post_meta($postarr['ID'], 'testimonial_author_website', $postarr['testimonial_author_website']);
		update_post_meta($postarr['ID'], 'testimonial_author_website_url', $postarr['testimonial_author_website_url']);
	}
	if ( $postarr['post_type'] == 'ap_products' ) {
		update_post_meta($postarr['ID'], 'cart66_id', $postarr['cart66_id']);
		update_post_meta($postarr['ID'], 'paypal_button_url', $postarr['paypal_button_url']);
		update_post_meta($postarr['ID'], 'google_button_url', $postarr['google_button_url']);
		update_post_meta($postarr['ID'], 'button_html', $postarr['button_html']);
		update_post_meta($postarr['ID'], 'cross_sales', $postarr['cross_sales']);
		update_post_meta($postarr['ID'], 'testimonials', $postarr['testimonials']);
		update_post_meta($postarr['ID'], 'testimonial_author', $postarr['testimonial_author']);
		update_post_meta($postarr['ID'], 'testimonial_author_website', $postarr['testimonial_author_website']);
		update_post_meta($postarr['ID'], 'testimonial_author_website_url', $postarr['testimonial_author_website_url']);
	}
	return $data;
}
add_action('wp_insert_post_data','ap_products_insert_post_data',10,2);

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