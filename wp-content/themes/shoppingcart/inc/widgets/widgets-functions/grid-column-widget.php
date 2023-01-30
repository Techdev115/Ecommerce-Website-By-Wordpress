<?php

/**
 * Display Category box widget with layout 1, layout 2 and layout 3
 *
 * @package Theme Freesia
 * @subpackage Magbook
 * @since Magbook 1.0
 */

class Shoppingcart_product_grid_column_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */

	function __construct() {
		$widget_ops = array( 'classname' => 'shoppingcart-grid-widget', 'description' => __( 'Displays Grid Column Widget in Shopping Cart Template', 'shoppingcart') );
		$control_ops = array('width' => 200, 'height' => 250);
		parent::__construct( false, $name=__('TF: Product Grid Column Widget','shoppingcart'), $widget_ops, $control_ops );
	}


	function form($instance) {
		$instance = wp_parse_args(( array ) $instance, array('title' => '','number' => '5','category' => '', 'product_type'=>'latest'));
		$title    = esc_attr($instance['title']);
		$number = absint( $instance[ 'number' ] );
		$category = absint($instance[ 'category' ]);
		$product_type = $instance[ 'product_type' ];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title');?>">
				<?php esc_html_e('Title:', 'shoppingcart');?>
			</label>
			<input id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo esc_attr($title);?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>">
			<?php esc_html_e( 'Number of Post:', 'shoppingcart' ); ?>
			</label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo absint($number); ?>" size="3" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php esc_html_e( 'Select Product Category', 'shoppingcart' ); ?>:</label>
			<?php wp_dropdown_categories( array( 'show_option_none' => __( '--Select Category--', 'shoppingcart' ),'name' => $this->get_field_name( 'category'), 'selected' => $category, 'taxonomy'	=> 'product_cat' ) ); ?>
			<br>
			<span><?php esc_html_e('Product Category will display only  when Category is selected from Options dropdown. ','shoppingcart'); ?></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'product_type' ); ?>"><?php esc_html_e( 'Options:', 'shoppingcart' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'product_type' ); ?>" name="<?php echo $this->get_field_name( 'product_type' ); ?>">
				<option value="latest" <?php selected( $instance['product_type'], 'latest'); ?>><?php esc_html_e( 'All Latest', 'shoppingcart' ); ?></option>
				<option value="category" <?php selected( $instance['product_type'], 'category'); ?>><?php esc_html_e( 'Category', 'shoppingcart' ); ?></option>
			</select>
		</p>

		<?php
	}
	function update($new_instance, $old_instance) {

		$instance  = $old_instance;
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance[ 'number' ] = absint( $new_instance[ 'number' ] );
		$instance[ 'category' ] = absint($new_instance[ 'category' ]);
		$instance[ 'product_type' ] = sanitize_text_field($new_instance[ 'product_type' ]);
		return $instance;
	}
	function widget($args, $instance) {
		extract($args);
		extract($instance);
		global $post;
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
		$number = empty( $instance[ 'number' ] ) ? 5 : $instance[ 'number' ];
		$category = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';
		$product_type = isset( $instance[ 'product_type' ] ) ? $instance[ 'product_type' ] : 'latest';

		if ( $product_type == 'category' ){  // Displays Selected Category
			$args = array(
				'posts_per_page' => absint($number),
				'post_type' => 'product',
				'tax_query' => array(
					array(
						'taxonomy'  => 'product_cat',
						'field'     => 'term_id',
						'terms'     => $category
					)
				),
			);

		} else {
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => absint($number),
				'orderby'	=> 'date',
				'order'	=> 'DESC',
			);
		}

		echo '<!-- ShoppingCart Grid Widget ============================================= -->' .$before_widget;

			if ( $title!=''){ ?>
				<h2 class="widget-title">
					<?php echo esc_html($title); ?>
				</h2><!-- end .widget-title -->
			<?php	} ?>
			<div class="shoppingcart-grid-widget-wrap five-column-grid">
					<?php
					$get_featured_posts = new WP_Query( $args );

					while( $get_featured_posts->have_posts() ):$get_featured_posts->the_post();
						$product = wc_get_product( $get_featured_posts->post->ID );
						$thumbnail_id = get_post_thumbnail_id();
						$image_attribute = wp_get_attachment_image_src($thumbnail_id,'shoppingcart-grid-product-image', false);  ?>
						<div <?php post_class('shoppingcart-grid-product'); ?>>

							<?php if ( !empty( $image_attribute[0] )) { ?>
								<figure class="sc-grid-product-img">
									<?php if ( $product->is_on_sale() ) { ?>
										<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . __( 'Sale!', 'shoppingcart' ) . '</span>', $post, $product ); ?>
									<?php } ?>
										<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" alt="<?php the_title_attribute();?>">
										<img src="<?php echo esc_url( $image_attribute[0] ); ?>" alt="<?php the_title_attribute();?>">
										</a>
										<?php  if ( !$product->is_in_stock() ) { ?>
										 <div class="badge-sold-out"><span><?php esc_html_e('Out of Stock','shoppingcart'); ?></span></div>
										<?php } ?>
								</figure>
								<?php } ?>

								<div class="sc-grid-product-content">
									<?php	if ( $shoppingcart_rating = wc_get_rating_html( $product->get_average_rating() ) ){
										echo '<div class="woocommerce-product-rating woocommerce">' .wp_kses_post( $shoppingcart_rating ) . ' </div>';

										} ?>

										<h2 class="sc-grid-product-title"><a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
											<?php if ( $price_html = $product->get_price_html() ) : ?>
										<span class="price">
											<?php echo $price_html; ?>
										</span>
							    		<?php endif; ?>
							        <div class="product-item-action">
										   <?php woocommerce_template_loop_add_to_cart( $product );
											if( function_exists( 'YITH_WCWL' ) ){
												$wishlist_url = add_query_arg( 'add_to_wishlist', $product->get_id() ); ?>
												<a href="<?php echo esc_url($wishlist_url); ?>" class="product_add_to_wishlist" title="<?php esc_attr_e('Add to Wishlist','shoppingcart'); ?>"><?php esc_html_e('Add to Wishlist','shoppingcart'); ?></a>

										  <?php } ?>
									</div>
								</div> <!-- end .sc-grid-product-content -->
						</div> <!-- end .shoppingcart-grid-product -->

							<?php
					endwhile;
					wp_reset_postdata();
					?>
			</div> <!-- end .shoppingcart-grid-widget-wrap -->

	<?php 	echo $after_widget;
	}
}