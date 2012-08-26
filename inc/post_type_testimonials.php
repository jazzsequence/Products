<?php
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
	add_meta_box( 'testimonial-author-info', 'Testimonial Author Info', 'ap_testimonials_author_meta', 'ap_testimonials', 'side', 'low' );
}
add_action( 'admin_menu', 'ap_testimonials_meta' );

function ap_testimonials_author_meta() {
	global $post;

	echo '<input type="hidden" name="ap_noncename" id="ap_noncename" value="' .
	wp_create_nonce( wp_basename(__FILE__) ) . '" />';

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
?>