<?php
/**
 * Displays the header content
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php
$shoppingcart_settings = shoppingcart_get_theme_options(); ?>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php endif;
wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php 
	if ( function_exists( 'wp_body_open' ) ) {

		wp_body_open();

	} else {

		do_action( 'wp_body_open' );

	 } ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#site-content-contain"><?php esc_html_e('Skip to content','shoppingcart'); ?></a>
<!-- Masthead ============================================= -->
<header id="masthead" class="site-header" role="banner">
	<div class="header-wrap">
			<?php the_custom_header_markup(); ?>
		<!-- Top Header============================================= -->
		<div class="top-header">
			<?php 
			if ($shoppingcart_settings['shoppingcart_disable_top_bar'] ==0 ){

				if(is_active_sidebar( 'shoppingcart_header_info' ) || has_nav_menu( 'top-menu' ) || (has_nav_menu( 'social-link' ) )): ?>
					<div class="top-bar">
						<div class="wrap">
							<?php
							if( is_active_sidebar( 'shoppingcart_header_info' )) {

								dynamic_sidebar( 'shoppingcart_header_info' );

							} ?>
							<div class="right-top-bar">

								<?php
								if($shoppingcart_settings['shoppingcart_top_social_icons'] == 0):

										do_action('shoppingcart_social_links');

								endif;


								if(has_nav_menu ('top-menu')){ ?>

									<nav class="top-bar-menu" role="navigation" aria-label="<?php esc_attr_e('Top Bar Menu','shoppingcart');?>">
										<button class="top-menu-toggle" type="button">
											<span class="screen-reader-text"><?php esc_html_e('Topbar Menu','shoppingcart');?></span>
											<i class="fa fa-bars"></i>
									  	</button>
										<?php
											wp_nav_menu( array(
												'container' 	=> '',
												'theme_location' => 'top-menu',
												'depth'          => 1,
												'items_wrap'      => '<ul class="top-menu">%3$s</ul>',
											) );
										?>
									</nav> <!-- end .top-bar-menu -->
								<?php } ?>

							</div> <!-- end .right-top-bar -->
						</div> <!-- end .wrap -->
					</div> <!-- end .top-bar -->
				<?php endif;
			} ?>

			<div id="site-branding">
				<div class="wrap">

					<?php do_action('shoppingcart_site_branding'); ?>

					<div class="header-right">
						<?php
						$search_form = $shoppingcart_settings['shoppingcart_search_custom_header'];
						if (1 != $search_form) { ?>

							<div id="search-box" class="clearfix">
								<?php 
									if (! class_exists('woocommerce')) {

										get_search_form();

									} else {

										the_widget( 'WC_Widget_Product_Search', 'title=' );

									}
								?>
							</div>  <!-- end #search-box -->
						<?php } 

						do_action ('shoppingcart_cart_wishlist_icon_display'); ?>
					</div> <!-- end .header-right -->
				</div><!-- end .wrap -->	
			</div><!-- end #site-branding -->
					

			<!-- Main Header============================================= -->
			<div id="sticky-header" class="clearfix">
				<div class="wrap">
					<div class="main-header clearfix">

						<!-- Main Nav ============================================= -->
						<?php $header_display = $shoppingcart_settings['shoppingcart_header_display']; ?>
							<div id="site-branding">

								<?php
								if ($header_display == 'header_logo' || $header_display == 'show_both') {

									shoppingcart_the_custom_logo();

								}
								if ($header_display == 'header_text' || $header_display == 'show_both') { ?>
								<div id="site-detail">
									<div id="site-title">
										<a href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_html(get_bloginfo('name', 'display'));?>" rel="home"> <?php bloginfo('name');?> </a>
									</div><!-- end .site-title --> 
									<?php
									$site_description = get_bloginfo( 'description', 'display' );
									if ($site_description){ ?>
										<div id="site-description"> <?php bloginfo('description');?> </div> <!-- end #site-description -->
									<?php } ?>
								</div>
							<?php } ?>
							</div><!-- end #site-branding -->

							<?php if (has_nav_menu('catalog-menu') ){
								$locations = get_nav_menu_locations();
								$menu_object = get_term( $locations['catalog-menu'], 'nav_menu' );
							?>

								<button class="show-menu-toggle" type="button">	
								<span class="bars"></span>		
									<span class="sn-text"><?php echo esc_html($menu_object->name);  ?></span>
								</button>

						<?php }
						if($shoppingcart_settings['shoppingcart_disable_main_menu']==0){ ?>

							<nav id="site-navigation" class="main-navigation clearfix" role="navigation" aria-label="<?php esc_attr_e( 'Main Menu', 'shoppingcart' ); ?>">
							<?php if (has_nav_menu('primary')) {
								$args = array(
								'theme_location' => 'primary',
								'container'      => '',
								'items_wrap'     => '<ul id="primary-menu" class="menu nav-menu">%3$s</ul>',
								); ?>
							
								<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
									<span class="line-bar"></span>
								</button><!-- end .menu-toggle -->
								<?php wp_nav_menu($args);//extract the content from apperance-> nav menu
								} else {// extract the content from page menu only ?>
								<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
									<span class="line-bar"></span>
								</button><!-- end .menu-toggle -->
								<?php	wp_page_menu(array('menu_class' => 'menu', 'items_wrap'     => '<ul id="primary-menu" class="menu nav-menu">%3$s</ul>'));
								} ?>
							</nav> <!-- end #site-navigation -->

						<?php } ?>
							<div class="header-right">
								<?php do_action ('shoppingcart_cart_wishlist_icon_display'); ?>
							</div> <!-- end .header-right -->

					</div> <!-- end .main-header -->
				</div> <!-- end .wrap -->
					</div> <!-- end #sticky-header -->
					<?php if (has_nav_menu('catalog-menu') ){ ?>
						<div class="header-catalog-menu-wrap">

							<?php do_action ('shoppingcart_side_nav_menu'); ?>

						</div> <!-- end .header-catalog-menu-wrap -->
					<?php } ?>

				</div>
				<!-- end .top-header -->

	</div> <!-- end .header-wrap -->

	<?php
		if ($shoppingcart_settings['shoppingcart_adv_ban_position'] =='above-slider'){

			do_action ('shoppingcart_adv_banner_top');
		}

		if ($shoppingcart_settings['shoppingcart_display_advertisement'] =='above-slider'){
			do_action ('shoppingcart_advertisement_display');  // Display Advertisemenet banner above slider
		} ?>


	<!-- Main Slider ============================================= -->
	<?php
		$shoppingcart_enable_slider = $shoppingcart_settings['shoppingcart_enable_slider'];
		if ($shoppingcart_enable_slider=='frontpage'|| $shoppingcart_enable_slider=='enitresite'){
			 if(is_front_page() && ($shoppingcart_enable_slider=='frontpage') ) {
			 	echo '<div class="catalog-slider-promotion-box clearfix">
			 	<div class="catalog-slider-promotion-wrap">
			 	<div class="catalog-slider-promotion-inner">';
				do_action ('shoppingcart_side_nav_menu');

			 		if($shoppingcart_settings['shoppingcart_slider_type'] == 'default_slider') {
						shoppingcart_category_sliders();

					} else {

						if(class_exists('ShoppingCart_Plus_Features')):
							do_action('shoppingcart_image_sliders');
						endif;
					}
			 	do_action ('shoppingcart_product_promotions');
			 	echo '</div> <!-- end .catalog-slider-promotion-inner --></div></div> <!-- end .catalog-slider-promotion-wrap -->';
				
			}
			if($shoppingcart_enable_slider=='enitresite'){
				echo '<div class="catalog-slider-promotion-box clearfix">
			 	<div class="catalog-slider-promotion-wrap">
			 	<div class="catalog-slider-promotion-inner">';
				do_action ('shoppingcart_side_nav_menu');

			 		if($shoppingcart_settings['shoppingcart_slider_type'] == 'default_slider') {

							shoppingcart_category_sliders();

					} else {

						if(class_exists('ShoppingCart_Plus_Features')):

							do_action('shoppingcart_image_sliders');

						endif;
					}
			 	do_action ('shoppingcart_product_promotions');
			 	echo '</div> <!-- end .catalog-slider-promotion-inner --></div></div> <!-- end .catalog-slider-promotion-wrap -->';
				
			}
		}
		if ($shoppingcart_settings['shoppingcart_adv_ban_position'] =='below-slider'){

			do_action ('shoppingcart_adv_banner_top');
		}
		if ($shoppingcart_settings['shoppingcart_display_advertisement'] =='below-slider'){ // Display Advertisemenet banner below slider
			do_action ('shoppingcart_advertisement_display');
		} ?>
</header> <!-- end #masthead -->

<!-- Main Page Start ============================================= -->
<div id="site-content-contain"class="site-content-contain">
	<div id="content" class="site-content">
	<?php
	

	if(is_front_page() && class_exists('woocommerce')){
		if($shoppingcart_settings['shoppingcart_display_featured_brand'] =='below-slider') {
			do_action('shoppingcart_display_front_page_product_brand'); // Display below Slider
		}

		do_action('shoppingcart_display_front_page_product_categories');
		if($shoppingcart_settings['shoppingcart_display_featured_brand'] =='below-product-category') {
			do_action('shoppingcart_display_front_page_product_brand');  // Display below Product Category
		}
	}