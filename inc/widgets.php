<?php
/**
 * Product meta widget
 * @since 0.7
 * @author Chris Reynolds
 * @uses register_widget
 * @uses WP_Widget
 * @uses get_post_meta
 * creates a widget for the post meta, including payment button and any other
 */
function ap_products_meta_widget() {
	register_widget( 'ap_product_meta_widget' );
}
class ap_product_meta_widget extends WP_Widget {
	function ap_product_meta_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'product_meta', 'description' => __('Displays the price, add to cart button, and any available product information.','products') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 200, 'id_base' => 'product-meta' );

		/* Create the widget. */
		$this->WP_Widget( 'product-meta', 'Product Meta', $widget_ops, $control_ops );
	}
	function widget($args, $instance) {
		global $wp_query;
		extract($args);
		// get options
		$options = get_option( 'ap_products_settings' );
		$post = $wp_query->post;

		// widget options
		$price_label = $instance['price-label'];
		$item_num_label = $instance['item-num-label'];
		$brand_label = $instance['brand-label'];
		$model_label = $instance['model-label'];
		$dimensions_label = $instance['dimensions-label'];
		$notes_label = $instance['notes-label'];
		$shipping_info_label = $instance['shipping-info-label'];

		// product meta
		$price = get_post_meta( $post->ID, 'price', true );
		$item_num = get_post_meta( $post->ID, 'item_num', true );
		$brand = get_post_meta( $post->ID, 'brand', true );
		$model = get_post_meta( $post->ID, 'model', true );
		$dimensions = get_post_meta( $post->ID, 'dimensions', true );
		$notes = get_post_meta( $post->ID, 'notes', true );
		$shipping_info = get_post_meta( $post->ID, 'shipping_info', true );
		$member_price = get_post_meta( $post->ID, 'member_price', true );
		$is_members_active = $options['members'];
		$inquire_link = $options['inquire-link'];

		// determine whether we need to display the add to cart button or not
		$inquire_sold_out = get_post_meta( $post->ID, 'inquire-sold-out', true );
		switch ( $inquire_sold_out ) {
			case 'none' :
				// inquire for price or sold out are not set, so we're displaying the cart button ?>
				<div class="add_to_cart">
				<?php
					echo '<h3 itemprop="price">';
					if ( $price_label )
						echo $price_label;
					if ( $is_members_active && is_user_logged_in() ) {
						$price = $member_price;
					}
					echo $price . '</h3>';
					/* 	get the appropriate add to cart button and link
						if a custom add to cart button has been uploaded, use that, otherwise, use the default image
					*/
					$is_cart66 = false;
					$is_google = false;
					$is_paypal = false;
					switch ( $options['products-merchant'] ) {
						case 'cart66' : // if we're using cart66
							$is_cart66 = true;
							if ( !$options['add-to-cart'] ) {
								$add_to_cart_path = product_plugin_images . 'add-to-cart.png';
							} else {
								$add_to_cart_path = $options['add-to-cart'];
							}
						break;
						case 'google' : // if we're using google
							$is_google = true;
							if ( !$options['add-to-cart'] ) {
								$add_to_cart_path = 'https://checkout.google.com/buttons/buy.gif?w=117&h=48&style=white&variant=text&loc=en_US';
							} else {
								$add_to_cart_path = $options['add-to-cart'];
							}
						break;
						case 'paypal' : // if we're using paypal
							$is_paypal = true;
							if ( !$options['add-to-cart'] ) {
								$add_to_cart_path = 'https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif';
							} else {
								$add_to_cart_path = $options['add-to-cart'];
							}
						break;
					} ?>
					<div class="purchase-button">
						<?php
						if ( !$is_cart66 ) {
							$paypal_button_url = get_post_meta( $post->ID, 'paypal_button_url', true );
							$paypal_button_url_members = get_post_meta( $post->ID, 'paypal_button_url_members', true );
							$google_button_url = get_post_meta( $post->ID, 'google_button_url', true );
							$google_button_url_members = get_post_meta( $post->ID, 'google_button_url_members', true );
							$cart66_prod_id = get_post_meta($post->ID,'cart66_id', true);
							switch ( $options['products-html'] ) {
								case 'url' : // if we're using a direct url
									if( $is_paypal && $paypal_button_url ) {
										if ( $paypal_button_url_members && is_user_logged_in() ) {
											echo '<a class="nostyle" href="' . $paypal_button_url_members . '" title="Pay via PayPal"><img src="' . $add_to_cart_path . '" alt="Pay via PayPal" /></a>';
										} else {
											echo '<a class="nostyle" href="' . $paypal_button_url . '" title="Pay via PayPal"><img src="' . $add_to_cart_path . '" alt="Pay via PayPal" /></a>';
										}
									}
									if( $is_google && $google_button_url ) {
										if ( $google_button_url_members && is_user_logged_in() ) {
											echo '<a class="nostyle" href="' . $google_button_url_members . '" title="Pay via Google Wallet"><img src="' . $add_to_cart_path . '" alt="Pay via Google Wallet" /></a>';
										} else {
											echo '<a class="nostyle" href="' . $google_button_url . '" title="Pay via Google Wallet"><img src="' . $add_to_cart_path . '" alt="Pay via Google Wallet" /></a>';
										}
									}
								break;
								case 'html' : // if we're using an embed code
									$button_html = get_post_meta( $post->ID, 'button_html', true );
									$button_html_members = get_post_meta( $post->ID, 'button_html_members', true );
									if( $button_html_members && is_user_logged_in() ) {
										echo $button_html_members;
									} elseif ( $button_html ) {
										echo $button_html;
									}
								break;
							}
						} else { // we're using cart66
							if(get_post_meta($post->ID,'cart66_id')) // but we'll check to make sure there's a cart66 product id
								echo do_shortcode('[add_to_cart item="' . $cart66_prod_id . '" img="' . $add_to_cart_path . '"]');
						}
					?>
					</div>
				</div>
			<?php break;
				case 'inquire' :
					// display an inquire for price ?>
					<div class="inquire_for_price">
						<?php if ( $inquire_link ) { ?>
							<h3><a href="<?php echo $inquire_link; ?>"><?php _e( 'Inquire for price', 'products'); ?></a></h3>
						<?php } else { ?>
							<h3><?php _e( 'Inquire for price', 'products' ); ?></h3>
						<?php } ?>
					</div>
				<?php break;
				case 'soldout' :
					// display a sold out ?>
					<div class="sold_out">
						<h3 class="strike">
							<?php if ( $price_label )
								echo $price_label;
							echo $price; ?>
						</h3>
						<h3 class="sold"><?php _e( 'Sold Out!', 'products' ); ?></h3>
					</div>
				<?php break;
		} // ends $inquire_sold_out switch
	?>
	<?php if ( ($price) || ($item_num) || ($brand) || ($model) || ($dimensions) || ($notes) || ($inquire_link) || ($shipping_info) ) { ?>
			<section class="product-info" id="meta">
				<?php echo $before_widget; ?>
				<div class="productmeta">
					<?php if ( $item_num ) {
						echo '<span class="product-id" id="product-' . $item_num . '" itemprop="productID">';
						if ( $item_num_label )
							echo $item_num_label;
						echo $item_num . '</span>';
					}
					if ( $model ) {
						echo '<span class="model" id="model-' . $model . '" itemprop="model">';
						if ( $model_label )
							echo $model_label;
						echo $model . '</span>';
					}
					if ( $brand ) {
						echo '<span class="brand" itemprop="brand">';
						if ( $brand_label )
							echo $brand_label;
						echo $brand . '</span>';
					}
					if ( $dimensions ) {
						echo '<span class="dimensions">';
						if ( $dimensions_label )
							echo $dimensions_label;
						echo $dimensions . '</span>';
					}
					if ( $notes ) {
						echo '<span class="notes">';
						if ( $notes_label )
							echo $notes_label;
						echo $notes . '</span>';
					}
					if ( $shipping_info ) {
						if ( $shipping_info_label )
							echo $shipping_info_label;
						echo $shipping_info;
					}?>
				</div>
				<div class="clear"></div>
			</section>
			<?php
			wp_reset_query();
			echo $after_widget;
		}
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */

		$instance['price-label'] = sanitize_text_field( $new_instance['price-label'] );
		$instance['item-num-label'] = sanitize_text_field( $new_instance['item-num-label'] );
		$instance['brand-label'] = sanitize_text_field( $new_instance['brand-label'] );
		$instance['model-label'] = sanitize_text_field( $new_instance['model-label'] );
		$instance['dimensions-label'] = sanitize_text_field( $new_instance['dimensions-label'] );
		$instance['notes-label'] = sanitize_text_field( $new_instance['notes-label'] );

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['num_posts'] = strip_tags( $new_instance['num_posts'] );
		$instance['thumb_size'] = strip_tags( $new_instance['thumb_size'] );

		return $instance;
	}
	function form( $instance ) {
		$options = get_option( 'ap_products_settings' );
		if ( $options['products-merchant'] == 'cart66' )
			$cart66 = true;

		/* Set up some default widget settings. */
		$defaults = array(
			'price-label' => __( 'Price: ', 'products' ),
			'item-num-label' => __( 'Item #: ', 'products' ),
			'brand-label' => __( 'Brand: ', 'products' ),
			'model-label' => __( 'Model: ', 'products' ),
			'dimensions-label' => __( 'Dimensions: ', 'products' ),
			'notes-label' => __( 'Notes: ', 'products' ),
			'shipping-info-label' => __( 'Shipping Info: ', 'products' )
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<?php if ( $cart66 ) { ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'price-label' ); ?>"><?php _e( 'Price label:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'price-label' ); ?>" name="<?php echo $this->get_field_name( 'price-label' ); ?>" class="widefat" value="<?php echo $instance['price-label']; ?>" />
		</p>
		<?php } ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'item-num-label' ); ?>"><?php _e( 'Item number label:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'item-num-label' ); ?>" name="<?php echo $this->get_field_name( 'item-num-label' ); ?>" class="widefat" value="<?php echo $instance['item-num-label']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'brand-label' ); ?>"><?php _e( 'Brand label:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'brand-label' ); ?>" name="<?php echo $this->get_field_name( 'brand-label' ); ?>" class="widefat" value="<?php echo $instance['brand-label']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'model-label' ); ?>"><?php _e( 'Model label:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'model-label' ); ?>" name="<?php echo $this->get_field_name( 'model-label' ); ?>" class="widefat" value="<?php echo $instance['model-label']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'dimensions-label' ); ?>"><?php _e( 'Dimensions label:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'dimensions-label' ); ?>" name="<?php echo $this->get_field_name( 'dimensions-label' ); ?>" class="widefat" value="<?php echo $instance['dimensions-label']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'notes-label' ); ?>"><?php _e( 'Notes label:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'notes-label' ); ?>" name="<?php echo $this->get_field_name( 'notes-label' ); ?>" class="widefat" value="<?php echo $instance['notes-label']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'shipping-info-label' ); ?>"><?php _e( 'Shipping Info label:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'shipping-info-label' ); ?>" name="<?php echo $this->get_field_name( 'shipping-info-label' ); ?>" class="widefat" value="<?php echo $instance['shipping-info-label']; ?>" />
		</p>
			<p>
				<label class="description" for="product-meta"><?php _e( 'If left blank, no label will be used.', 'products' ); ?></label>
			</p>
		<?php
	}
}
add_action( 'widgets_init', 'ap_products_meta_widget' );

/**
 * Cross Sales Widget
 * @since 0.7
 * @author Chris Reynolds
 * @uses register_widget
 * @uses WP_Widget
 * @uses wp_get_post_tags
 * @uses get_post_meta
 * Displays cross sales links (if active in the options)
 */
function ap_products_cross_sales_widget() {
	$defaults = products_get_defaults();
	$options = get_option( 'ap_products_settings', $defaults );
	if ( $options['cross-sales'] ) {
		register_widget( 'product_cross_sales_widget' );
	}
}
class product_cross_sales_widget extends WP_Widget {
	function product_cross_sales_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'product_cross_sales', 'description' => __('Displays cross-sales links based on tags and any cross-sales link in the product.','products') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 200, 'id_base' => 'product-cross-sales' );

		/* Create the widget. */
		$this->WP_Widget( 'product-cross-sales', 'Product Cross-sales', $widget_ops, $control_ops );
	}
	function widget($args, $instance) {
		global $wp_query;
		extract($args);

		$post = $wp_query->post;
		$cross_sales_id = get_post_meta($post->ID, 'cross_sales', true);
		$cross_sales = get_post( $cross_sales_id, ARRAY_A );
		$cross_sales_title = $cross_sales['post_title'];
		$cross_sales_link = get_permalink( $cross_sales_id );

		// list 3 post titles related to first two tags on current post
		$tags = wp_get_post_tags($post->ID);

		if ( $instance['related-label'] ) {
			$related_label = $instance['related-label'];
		} else {
			$related_label = __('You might be interested in', 'products');
		}
		if ($tags) {
			$first_tag = $tags[0]->term_id;
			$second_tag = $tags[1]->term_id;
			$args=array(
				'tag__in' => array($first_tag,$second_tag),
				'post__not_in' => array($post->ID),
				'showposts'=>3,
				'caller_get_posts'=>1,
				'orderby'=>'rand'
			);
			$tag_query = new WP_Query($args);
			if(( $tag_query->have_posts() ) )  {
				echo $before_widget;
				echo $related_label . '<br />';

				if( $cross_sales_id ) {
					echo '<a href="' . $cross_sales_link . ' rel="bookmark" title="Permanent Link to ' . $cross_sales_title . '">' . $cross_sales_title . '</a><br />';
				}
				while ($tag_query->have_posts()) : $tag_query->the_post(); ?>
					<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a><br />
				<?php endwhile;
				wp_reset_query();
				echo $after_widget;
			}
		} elseif ( !$tags && $cross_sales_id ) {
			echo $before_widget;
			echo $related_label . '<br />';

			if( $cross_sales_id ) {
				echo '<a href="' . $cross_sales_link . ' rel="bookmark" title="Permanent Link to ' . $cross_sales_title . '">' . $cross_sales_title . '</a><br />';
				}
			echo $after_widget;
		}
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['related-label'] = sanitize_text_field( $new_instance['related-label'] );

		return $instance;
	}
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'related-label' => __( 'You might be interested in', 'products' ) );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'related-label' ); ?>"><?php _e( 'Cross-sales label:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'related-label' ); ?>" name="<?php echo $this->get_field_name( 'related-label' ); ?>" class="widefat" value="<?php if ( !$instance['related-label'] ) { echo $defaults['related-label']; } else { echo $instance['related-label']; } ?>" /><br />
			<label class="description" for="<?php echo $this->get_field_id( 'related-label' ); ?>"><?php _e( 'Text to display above cross-sales items.', 'products' ); ?></label>
		</p>
		<?php
	}
}

add_action( 'widgets_init', 'ap_products_cross_sales_widget' );


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
	$defaults = products_get_defaults();
	$options = get_option( 'ap_products_settings', $defaults );
	if ( $options['shop-testimonials'] || $options['product-testimonials'] )
		register_widget( 'product_testimonials_widget' );
}

class product_testimonials_widget extends WP_Widget {
	function product_testimonials_widget() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'products_testimonial', 'description' => __('A widget for displaying quotes or product testimonials (if enabled in Product Options).  Will display shop testimonials on all shop pages and product testimonials (if they exist and are enabled) on individual product pages.','products') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'products-testimonial' );

		/* Create the widget. */
		$this->WP_Widget( 'products-testimonial', 'Testimonials widget', $widget_ops, $control_ops );
	}
	function widget( $args, $instance ) {
		global $wp_query;
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$shop_only = $instance['shop-only'];
		$num_posts = $instance['num_posts'];

		/* get options */
		$defaults = products_get_defaults();
		$options = get_option( 'ap_products_settings', $defaults );
		$post = $wp_query->post;
		$testimonial = get_post_meta( $post->ID,'testimonials',true );
		if ( $options['product-testimonials'] && ( $testimonial != '' ) && ( 'ap_products' == get_post_type() ) ) {
			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Title of widget (before and after defined by themes). */
			if ( $title ) {
				echo $before_title . $title . $after_title;
			} else {
				echo $before_title . __( 'Testimonials', 'products' ) . $after_title;
			}
				$testimonial_author_website = null;
				$testimonial_author_website_url = null;
				$source = __('anonymous','products');
				$testimonial_author = '<span itemprop="author" class="author">' . get_post_meta( $post->ID, 'testimonial_author', true ) . '</span>';
				if ( get_post_meta( $post->ID, 'testimonial_author_website' ) )
					$testimonial_author_website = get_post_meta( $post->ID, 'testimonial_author_website', true );
				if ( get_post_meta( $post->ID, 'testimonial_author_website_url' ) )
					$testimonial_author_website_url = get_post_meta( $post->ID, 'testimonial_author_website_url', true );
					$website_before = '<a itemprop="url" href="' . $testimonial_author_website_url . '" target="_blank">';
					$website_after = '</a>';

				if ( $testimonial_author && $testimonial_author_website && $testimonial_author_website_url ) {
					$source = $testimonial_author . '<br />' . $website_before . $testimonial_author_website . $website_after;
				}
				if ( $testimonial_author && $testimonial_author_website && !$testimonial_author_website_url ) {
					$source = $testimonial_author . '<br />' . $testimonial_author_website;
				}
				if ( $testimonial_author && $testimonial_author_website_url && !$testimonial_author_website ) {
					$source = $website_before . $testimonial_author . $website_after;
				}
				if ( !$testimonial_author && $testimonial_author_website && $testimonial_author_website_url ) {
					$source = $website_before . $testimonial_author_website . $website_after;
				}
				if ( !$testimonial_author && $testimonial_author_website && !$testimonial_author_website_url ) {
					$source = $testimonial_author_website;
				}

			/* Display quote from widget settings. */ ?>
			<aside class="testimonial" itemprop="review" itemscope itemtype="http://schema.org/Review">
				<span itemprop="description" class="description">
					<?php echo $testimonial; ?>
				</span>
				<?php if ( $source ) { echo '<div class="source">' . $source . '</div>'; } ?>
			</aside>
			<?php
			/* After widget (defined by themes). */
			echo $after_widget;
		} else {
			if ( ( $options['shop-testimonials'] && $shop_only && ( is_page_template( 'page-shop.php' ) || is_page_template( 'taxonomy-product_category.php' ) ) ) || $options['shop-testimonials'] && !$shop_only ) {
				global $wp_query, $post;
				$post_id = $post->ID;

				$args = array(
					'post_type' => 'ap_testimonials',
					'posts_per_page' => $num_posts,
					'orderby' => 'rand',
				);
				$temp = $wp_query;
				$wp_query = null;
				$wp_query = new WP_Query();
				$wp_query->query($args);

				echo $before_widget;

				if ($wp_query->have_posts() ) :
					/* Title of widget (before and after defined by themes). */
					if ( $title ) {
						echo $before_title . $title . $after_title;
					} else {
						echo $before_title . __( 'Testimonials', 'products' ) . $after_title;
					}

				while ($wp_query->have_posts()) :
				/* Before widget (defined by themes). */

				$wp_query->the_post();
				$testimonial_author_website = null;
				$testimonial_author_website_url = null;
				$source = __('anonymous','products');
				$testimonial_author = '<span itemprop="author" class="author">' . get_post_meta( $post->ID, 'testimonial_author', true ) . '</span>';
				if ( get_post_meta( $post->ID, 'testimonial_author_website' ) )
					$testimonial_author_website = get_post_meta( $post->ID, 'testimonial_author_website', true );
				if ( get_post_meta( $post->ID, 'testimonial_author_website_url' ) )
					$testimonial_author_website_url = get_post_meta( $post->ID, 'testimonial_author_website_url', true );
					$website_before = '<a itemprop="url" href="' . $testimonial_author_website_url . '" target="_blank">';
					$website_after = '</a>';

				if ( $testimonial_author && $testimonial_author_website && $testimonial_author_website_url ) {
					$source = $testimonial_author . '<br />' . $website_before . $testimonial_author_website . $website_after;
				}
				if ( $testimonial_author && $testimonial_author_website && !$testimonial_author_website_url ) {
					$source = $testimonial_author . '<br />' . $testimonial_author_website;
				}
				if ( $testimonial_author && $testimonial_author_website_url && !$testimonial_author_website ) {
					$source = $website_before . $testimonial_author . $website_after;
				}
				if ( !$testimonial_author && $testimonial_author_website && $testimonial_author_website_url ) {
					$source = $website_before . $testimonial_author_website . $website_after;
				}
				if ( !$testimonial_author && $testimonial_author_website && !$testimonial_author_website_url ) {
					$source = $testimonial_author_website;
				}
				?>
				<aside class="testimonial" itemprop="review" itemscope itemtype="http://schema.org/Review">
					<span itemprop="description" class="description">
						<?php the_content(); ?>
					</span>
					<?php if ( $source ) { echo '<div class="source">' . $source . '</div>'; } ?>
				</aside>
				<?php
				endwhile; endif;
				$wp_query = $temp;
				$temp = null;
				wp_reset_query();
				/* After widget (defined by themes). */
				echo $after_widget;
			}
		}
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['shop-only'] = $new_instance['shop-only'];
		$instance['num_posts'] = strip_tags( $new_instance['num_posts'] );

		return $instance;
	}
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( 'Testimonials', 'products' ), 'shop-only' => false, 'num_posts' => 3 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php if ( !$instance['title'] ) { echo $defaults['title']; } else { echo $instance['title'];  } ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'shop-only' ); ?>"><?php _e( 'Display on shop pages only?', 'products' ); ?></label>
			<select name="<?php echo $this->get_field_id( 'shop-only' ); ?>">
			<?php
				$selected = $instance['shop-only'];
				$help = __( 'If set to No, will display testimonials on all pages and single product testimonials on single product pages.  If set to Yes, testimonials will <em>only</em> display on shop pages and single product pages.', 'products' );
				foreach ( products_true_false() as $option ) {
					$label = $option['label'];
					$value = $option['value'];
					echo '<option value="' . $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
				} ?>
			</select> [<a href="#TB_inline?height=250&width=400&inlineId=products-help" class="thickbox">?</a>]
			<div id="products-help" style="display: none;">
				<h3>Display on all pages?</h3>
				<p style="width: 400px;">
					<?php echo $help; ?>
				</p>
			</div>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'num_posts' ); ?>"><?php _e('Number of testimonials to show:', 'products'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'num_posts' ); ?>" name="<?php echo $this->get_field_name( 'num_posts' ); ?>" size="3" value="<?php echo $instance['num_posts']; ?>" />
		</p><?php
	}
}
add_action( 'widgets_init', 'ap_products_testimonials_widget' );

/**
 * Related products widget
 * @since 0.5.1
 * @author Chris Reynolds
 * @uses register_widget
 * @uses WP_Widget
 * @uses wp_query
 * displays random related products based on taxonomy of current product if looking at a product page
 */
function ap_products_related_widget() {
	register_widget( 'products_related_widget' );
}
class products_related_widget extends WP_Widget {
	function products_related_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'products_related', 'description' => __('A widget for displaying related products based on the currently displayed product.','products') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'products-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'products-widget', 'Related Products Widget', $widget_ops, $control_ops );
	}
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$num_posts = $instance['num_posts'];
		$thumb_size = $instance['thumb_size'];

		if ( !$thumb_size ) {
			$thumb_size = 63;
		}

		if ( is_page_template( 'page-shop.php' ) ) {
			/* Before widget (defined by themes). */
			echo $before_widget;
			/* Title of widget (before and after defined by themes). */
			if ( $title ) {
				echo $before_title . $title . $after_title;
			} else {
				echo $before_title . __( 'You might also like:', 'products' ) . $after_title;
			}

			global $wp_query, $post;
			$exclude = $wp_query->post->ID;
			$post_id = $post->ID;
			$args = array(
				'post_type' => 'ap_products',
				'posts_per_page' => $num_posts,
				'orderby' => 'rand',
				'post__not_in' => array($exclude)
			);
			$temp = $wp_query;
			$wp_query = null;
			$wp_query = new WP_Query();
			$wp_query->query($args);
				while ($wp_query->have_posts()) : $wp_query->the_post(); ?>			
					<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail(array($thumb_size,$thumb_size,true)); ?></a>
			<?php endwhile;
			$wp_query = $temp;
			$temp = null;
			/* After widget (defined by themes). */
			echo $after_widget;
		} else {
			if ( 'ap_products' == get_post_type() || is_page_template( 'taxonomy-product_category.php' ) ) {
				/* Before widget (defined by themes). */
				echo $before_widget;
				/* Title of widget (before and after defined by themes). */
				if ( $title ) {
					echo $before_title . $title . $after_title;
				} else {
					echo $before_title . __( 'You might also like:', 'products' ) . $after_title;
				}

				global $wp_query, $post;
				$exclude = $wp_query->post->ID;
				$post_id = $post->ID;
				$taxonomy = get_the_terms($post->ID, 'product_category');  // declares a $term variable that we'll use later that calls in the taxonomies
				$tax_slug = null;
				foreach ( $taxonomy as $value ) // loop through the taxonomy meta to get the slug
					$tax_slug = $value->slug;

				$args = array(
					'post_type' => 'ap_products',
					'product_category' => $tax_slug,
					'posts_per_page' => $num_posts,
					'orderby' => 'rand',
					'post__not_in' => array($exclude)
				);
				$temp = $wp_query;
				$wp_query = null;
				$wp_query = new WP_Query();
				$wp_query->query($args);
					while ($wp_query->have_posts()) : $wp_query->the_post(); ?>			
						<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail(array($thumb_size,$thumb_size,true)); ?></a>
				<?php endwhile;
				$wp_query = $temp;
				$temp = null;
				/* After widget (defined by themes). */
				echo $after_widget;
			}
		}
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['num_posts'] = strip_tags( $new_instance['num_posts'] );
		$instance['thumb_size'] = strip_tags( $new_instance['thumb_size'] );

		return $instance;
	}
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'You might also like:', 'num_posts' => '6', 'thumb_size' => '63' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php if ( !$instance['title'] ) { echo $defaults['title']; } else { echo $instance['title'];  } ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'num_posts' ); ?>"><?php _e('Number of products to show:', 'products'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'num_posts' ); ?>" name="<?php echo $this->get_field_name( 'num_posts' ); ?>" size="3" value="<?php echo $instance['num_posts']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'thumb_size' ); ?> "><?php _e( 'Thumbnail size:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" size="3" value="<?php if ( !$instance['thumb_size'] ) { echo $defaults['thumb_size']; } else { echo $instance['thumb_size']; } ?>" />
		</p>
		<?php
	}
}
add_action( 'widgets_init', 'ap_products_related_widget' );
?>