<?php
/**
 * Front Page Features
 *
 * Displays in Corporate template.
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
/* Frontpage Product Featured Brands */
add_action('shoppingcart_display_front_page_product_brand','shoppingcart_frontpage_product_brand');
function shoppingcart_frontpage_product_brand(){
	$shoppingcart_settings = shoppingcart_get_theme_options();
	$shoppingcart_features_title = $shoppingcart_settings['shoppingcart_features_title'];
	$shoppingcart_features_description = $shoppingcart_settings['shoppingcart_features_description'];
	$shoppingcart_list_product_category	= array();
	for ( $i=1; $i <= $shoppingcart_settings['shoppingcart_total_brand_features'] ; $i++ ) {
		if( isset ( $shoppingcart_settings['shoppingcart_featured_product_brand_' . $i] ) && $shoppingcart_settings['shoppingcart_featured_product_brand_' . $i] !='-' ){

			$shoppingcart_list_product_category	=	array_merge( $shoppingcart_list_product_category, array( $shoppingcart_settings['shoppingcart_featured_product_brand_' . $i] ) );
		}
	}
	if ( (!empty( $shoppingcart_list_product_category ) || !empty($shoppingcart_settings['shoppingcart_features_title']) || !empty($shoppingcart_settings['shoppingcart_features_description'])) && ($shoppingcart_settings['shoppingcart_disable_product_brand'] == 0) ) { ?>
			<div class="brand-content-box">
				<div class="wrap">
					<div class="brand-wrap">
					<?php

					if($shoppingcart_features_title  != '' || $shoppingcart_features_description != ''){
						echo '<div class="box-header">';
						if($shoppingcart_features_title  != ''){ ?>
							<h2 class="box-title"><?php echo esc_html($shoppingcart_features_title );?> </h2>
						<?php }
						if($shoppingcart_features_description != ''){ ?>
							<p class="box-sub-title"><?php echo esc_html($shoppingcart_features_description); ?></p>
						<?php }
						echo '</div><!-- end .box-header -->';
					} ?>
					<div class="brand-slider">
						<ul class="slides">
							<?php
								$i = 1;

								foreach ($shoppingcart_list_product_category as $category) {
									$thumbnail_id = get_term_meta( $category, 'thumbnail_id', true );
									$category_link = get_category_link( $category );
									$category_name = get_term( $category );

									$image_attribute = wp_get_attachment_image_src( $thumbnail_id, 'shoppingcart-featured-brand-image' );
									if ( !empty( $image_attribute[0] )) { ?>
									<li>
										<a href="<?php echo esc_url( $category_link ); ?>" title="<?php echo esc_attr($category_name->name); ?>" target="_blank">
											<img src="<?php echo esc_url( $image_attribute[0] ); ?>" alt="<?php echo esc_attr($category_name->name); ?>" />
										</a>
									</li>
									<?php }
									$i++;
								}; ?>
						</ul>
					</div><!-- end .brand-slider -->
				</div><!-- end .brand-wrap -->
			</div><!-- end .wrap -->
		</div><!-- end .brand-content-box -->
	<?php }
wp_reset_postdata();
}

/* Frontpage Product Categories */
add_action('shoppingcart_display_front_page_product_categories','shoppingcart_frontpage_product_categories');
function shoppingcart_frontpage_product_categories(){
	$shoppingcart_settings = shoppingcart_get_theme_options();
	$shoppingcart_categories_features_title = $shoppingcart_settings['shoppingcart_categories_features_title'];
	$shoppingcart_categories_features_description = $shoppingcart_settings['shoppingcart_categories_features_description'];
	$shoppingcart_list_product_category	= array();
	for ( $i=1; $i <= $shoppingcart_settings['shoppingcart_total_features'] ; $i++ ) {
		if( isset ( $shoppingcart_settings['shoppingcart_featured_category_' . $i] ) && $shoppingcart_settings['shoppingcart_featured_category_' . $i] !='-' ){

			$shoppingcart_list_product_category	=	array_merge( $shoppingcart_list_product_category, array( $shoppingcart_settings['shoppingcart_featured_category_' . $i] ) );

		}
	}
	if ( (!empty( $shoppingcart_list_product_category ) || !empty($shoppingcart_settings['shoppingcart_shoppingcart_features_title']) || !empty($shoppingcart_settings['shoppingcart_shoppingcart_features_description'])) && ($shoppingcart_settings['shoppingcart_disable_product_categories'] == 0) ) { ?>
			<div class="promo-category-area <?php if ($shoppingcart_settings['shoppingcart_product_background_color'] == 'on'){ echo ' promo-category-bg-color '; } if ($shoppingcart_settings ['shoppingcart_big_promo_category'] == 'on'){ echo ' big-promo-category '; } ?> ">
				<div class="promo-category-wrap">
					<?php
					
					if($shoppingcart_categories_features_title  != '' || $shoppingcart_categories_features_description != ''){
						echo '<div class="box-header">';
						if($shoppingcart_categories_features_title  != ''){ ?>
							<h2 class="box-title"><?php echo esc_html($shoppingcart_categories_features_title );?> </h2>
						<?php }
						if($shoppingcart_categories_features_description != ''){ ?>
							<p class="box-sub-title"><?php echo esc_html($shoppingcart_categories_features_description); ?></p>
						<?php }
						echo '</div><!-- end .box-header -->';
					} ?>
					<div class="promo-content-wrap">
					<?php
						$i = 1;
						foreach ($shoppingcart_list_product_category as $category) {

							 
							$thumbnail_id = get_term_meta( $category, 'thumbnail_id', true );
							$category_link = get_category_link( $category );
							$category_name = get_term( $category );
							$promo_image_attribute = wp_get_attachment_image_src( $thumbnail_id, 'shoppingcart-product-cat-image' ); ?>
							<div class="promo-category-content">
								<?php if ( !empty($promo_image_attribute[0] )) { ?>
							<div class="promo-category-img">
								<a class="promo-category-link" href="<?php echo esc_url( $category_link ); ?>">
									<img src="<?php echo esc_url( $promo_image_attribute[0] ); ?>" alt="<?php echo esc_attr( $category_name->name ); ?>" />
									</a>
								</div>
								<?php } ?>
								<div class="promo-category-text">
									<h4><a title="<?php echo esc_attr($category_name->name); ?>" href="<?php echo esc_url( $category_link ); ?>"><?php echo esc_html( $category_name->name ); ?></a></h4>
									<p><?php echo category_description($category); ?></p>
									<div class="more-products"><a href="<?php echo esc_url( $category_link ); ?>"><?php if ($category_name->count) { echo absint ($category_name->count); } ?> <?php _e('Products','shoppingcart'); ?></a></div>
								</div>
							</div> <!-- end .promo-category-content -->
						<?php $i++;
						 }; ?>
					</div><!-- end .promo-content-wrap -->
				</div><!-- end .promo-category-wrap -->
			</div><!-- end .promo-category-area -->
		<?php }
	wp_reset_postdata();
}

/* Advertisement Banner */
add_action ('shoppingcart_advertisement_display','shoppingcart_advertisement_banner_position');

function shoppingcart_advertisement_banner_position(){

	if ( (is_active_sidebar('shoppingcart_ad_banner')) && is_front_page() ){ ?>

		<div class="advertisement-banner-one">
			<div class="ad-banner-one-wrap">

				<?php dynamic_sidebar ('shoppingcart_ad_banner'); ?>

			</div>
		</div> <!-- end .advertisement-banner-one -->
	<?php }

}