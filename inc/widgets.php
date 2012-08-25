<?php
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
		//TODO this needs work

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

			/* Display quote from widget settings. */
			echo '<div class="products-testimonial">'.$testimonial.'</div>';

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

				while ($wp_query->have_posts()) :
				/* Before widget (defined by themes). */
				echo $before_widget;

				/* Title of widget (before and after defined by themes). */
				if ( $title ) {
					echo $before_title . $title . $after_title;
				} else {
					echo $before_title . __( 'Testimonials', 'products' ) . $after_title;
				}

				$wp_query->the_post();
				if ( get_post_meta( $post->ID,'' ) )

				?>
				<aside class="testimonial">
					<?php the_content(); ?>

				
				<?php
				endwhile;
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
		$instance['shop-only'] = $new_instance['shop-only'];
		$instance['num_posts'] = strip_tags( $new_instance['num_posts'] );

		return $instance;
	}
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( 'Testimonials', 'products' ), 'shop-only' => false, 'num_posts' => 3 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<?php
		wp_enqueue_script('jquery');
		wp_enqueue_script('thickbox',null,array('jquery'));
		wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
		?>
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

		/* Before widget (defined by themes). */
		echo $before_widget;
		if ( is_page_template( 'page-shop.php' ) ) {
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
					<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail(array(63,63,true)); ?></a>
			<?php endwhile;
			$wp_query = $temp;
			$temp = null;
		} else {
			if ( 'ap_products' == get_post_type() || is_page_template( 'taxonomy-product_category.php' ) ) {

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
						<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail(array(63,63,true)); ?></a>
				<?php endwhile;
				$wp_query = $temp;
				$temp = null;
			}
		}
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['num_posts'] = strip_tags( $new_instance['num_posts'] );

		return $instance;
	}
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'You might also like:', 'num_posts' => '6' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'products' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php if ( !$instance['title'] ) { echo $defaults['title']; } else { echo $instance['title'];  } ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'num_posts' ); ?>"><?php _e('Number of products to show:', 'products'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'num_posts' ); ?>" name="<?php echo $this->get_field_name( 'num_posts' ); ?>" size="3" value="<?php echo $instance['num_posts']; ?>" />
		</p>
		<?php
	}
}
add_action( 'widgets_init', 'ap_products_related_widget' );
?>