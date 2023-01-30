<?php
/**
 * Custom functions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */


/*********************** shoppingcart Side Menus ***********************************/
function shoppingcart_side_menus() {

if (has_nav_menu('catalog-menu') ){
			$args = array(
				'theme_location' => 'catalog-menu',
				'container'      => '',
				'items_wrap'     => '<ul class="cat-nav-menu">%3$s</ul>',
				);

			$locations = get_nav_menu_locations();
			$menu_object = get_term( $locations['catalog-menu'], 'nav_menu' );

			 ?>
		<div class="catalog-menu-box">
			<div class="catalog-menu-wrap">
				<button class="hide-menu-toggle" type="button">
					<span class="screen-reader-text"><?php esc_html_e('Catalog Menu','shoppingcart')?></span>
					<span class="bars"></span>
				</button>
				<nav class="catalog-menu" role="navigation" aria-label="<?php esc_attr_e('Catalog Menu','shoppingcart');?>">
				<div class="catalog-menu">
					<h3 class="catalog-menu-title"><?php echo esc_html($menu_object->name);  ?></h3>
					<?php wp_nav_menu($args); ?>

				</nav> <!-- end .catalog-menu -->
			</div> <!-- end .catalog-menu-wrap -->
		</div> <!-- end .catalog-menu-box -->
<?php }
}

add_action ('shoppingcart_side_nav_menu','shoppingcart_side_menus');

/*********************** shoppingcart Category SLIDERS ***********************************/
function shoppingcart_category_sliders() {
	$shoppingcart_settings = shoppingcart_get_theme_options();
	if($shoppingcart_settings['shoppingcart_default_category']=='post_category'){
		$category = $shoppingcart_settings['shoppingcart_default_category_slider'];
		$query = new WP_Query(array(
					'posts_per_page' =>  intval($shoppingcart_settings['shoppingcart_slider_number']),
					'post_type' => array(
						'post'
					) ,
					'category_name' => esc_attr($shoppingcart_settings['shoppingcart_default_category_slider']),
				));
	} else {
		$category = $shoppingcart_settings['shoppingcart_category_slider'];
		$query = new WP_Query( array(
			'post_type' => 'product',
			'orderby'   => 'date',
			'tax_query' => array(
				array(
					'taxonomy'  => 'product_cat',
					'field'     => 'id',
					'terms'     => intval($category),
				)
			),
			'posts_per_page' => intval($shoppingcart_settings['shoppingcart_slider_number']),
			) );
	}
	

	if($query->have_posts() && !empty($category)){
		$category_sliders_display = '';
		$category_sliders_display 	.= '<!-- Main Slider ============================================= -->';
		$category_sliders_display 	.= ' <div class="main-slider">';
		$category_sliders_display 	.= '<div class="layer-slider">';
		$category_sliders_display 	.= '<ul class="slides">';
		while ($query->have_posts()):$query->the_post();

				$category_sliders_display    	.= '<li>';
				$category_sliders_display 	.= '<div class="image-slider">';
				$category_sliders_display 	.= '<article class="slider-content">';
				$category_sliders_display 	.= '<div class="slider-image-content">';
				$category_sliders_display 	.= '<a href="'.esc_url(get_permalink()).'" title="'.the_title_attribute('echo=0').'">';
				$category_sliders_display 	.= get_the_post_thumbnail();
				$category_sliders_display 	.= '</a>';					
											
				$category_sliders_display 	.='</div> <!-- .slider-image-content -->';
				$category_sliders_display 	.='</article> <!-- end .slider-content -->';
				$category_sliders_display 	.='</div> <!-- end .image-slider -->';
				$category_sliders_display 	.='</li>';
			endwhile;
			wp_reset_postdata();
			$category_sliders_display .= '</ul><!-- end .slides -->
				</div> <!-- end .layer-slider -->
			</div> <!-- end .main-slider -->';
				echo $category_sliders_display;
			}
}

/*********************** shoppingcart Product Promotion ***********************************/
function shoppingcart_product_promotion() {
	$shoppingcart_settings = shoppingcart_get_theme_options();

	?>

	<div class="product-promotion">
		<div class="product-promotion-wrap">
			<?php for ( $i=1; $i <= 3; $i++ ) {
			if( !empty( $shoppingcart_settings[ 'shoppingcart_img-product-promotion-image-'. $i ] ) ) { 
				$product_promotion = $shoppingcart_settings[ 'shoppingcart_img-product-promotion-image-'. $i ]; } else { $featured_slider = ''; }
				if (!empty ($product_promotion)): ?>
				<div class="product-promotion-content">
					<div class="product-promotion-img">
						<a class="product-promotion-link" href="<?php echo esc_url ($shoppingcart_settings['shoppingcart_product_promotion_url_'.$i]); ?>"><img src="<?php echo esc_url ($product_promotion); ?>"></a>
					</div>
				</div> <!-- end .product-promotion-content -->
				<?php endif;
			} ?>
		</div>
		<!-- end .product-promotion-wrap -->
	</div>
	<!-- end .product-promotion -->
<?php
}

add_action ('shoppingcart_product_promotions','shoppingcart_product_promotion');