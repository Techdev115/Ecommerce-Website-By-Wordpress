<?php
/**
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
/**************** SHOPPINGCART REGISTER WIDGETS ***************************************/
add_action('widgets_init', 'shoppingcart_widgets_init');
function shoppingcart_widgets_init() {

	register_sidebar(array(
			'name' => __('Main Sidebar', 'shoppingcart'),
			'id' => 'shoppingcart_main_sidebar',
			'description' => __('Shows widgets at Main Sidebar.', 'shoppingcart'),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		));
	register_sidebar(array(
			'name' => __('Top Header Info', 'shoppingcart'),
			'id' => 'shoppingcart_header_info',
			'description' => __('Shows widgets on all page.', 'shoppingcart'),
			'before_widget' => '<aside id="%1$s" class="widget widget_contact">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	register_sidebar(array(
			'name' => __('Advertisement Banner One', 'shoppingcart'),
			'id' => 'shoppingcart_ad_banner',
			'before_widget' => '<div id="%1$s" class="ad-banner-one-img %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	register_sidebar(array(
			'name' => __('Shopping Cart Template', 'shoppingcart'),
			'id' => 'shoppingcart_template',
			'description' => __('Shows widgets on Shopping Cart Template.', 'shoppingcart'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	register_sidebar(array(
			'name' => __('Contact Page Sidebar', 'shoppingcart'),
			'id' => 'shoppingcart_contact_page_sidebar',
			'description' => __('Shows widgets on Contact Page Template.', 'shoppingcart'),
			'before_widget' => '<aside id="%1$s" class="widget widget_contact">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	register_sidebar(array(
			'name' => __('Iframe Code For Google Maps', 'shoppingcart'),
			'id' => 'shoppingcart_form_for_contact_page',
			'description' => __('Add Iframe Code using text widgets', 'shoppingcart'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		));
	register_sidebar(array(
			'name' => __('WooCommerce Sidebar', 'shoppingcart'),
			'id' => 'shoppingcart_woocommerce_sidebar',
			'description' => __('Add WooCommerce Widgets Only', 'shoppingcart'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		));

	for($i =1; $i<= 4; $i++){

		// Registering for ShoppingCart Template Footer Column
		register_sidebar(array(
			'name'          => __(' ShoppingCart Template Footer Column ', 'shoppingcart') . $i,
			'id'            => 'shoppingcart_template_footer_col_'.$i,
			'description'   => __(' Add WooCommerce widgets at ShoppingCart Template Footer Column ', 'shoppingcart').$i,
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));

	}

	$shoppingcart_settings = shoppingcart_get_theme_options();
	for($i =1; $i<= $shoppingcart_settings['shoppingcart_footer_column_section']; $i++){
	register_sidebar(array(
			'name' => __('Footer Column ', 'shoppingcart') . $i,
			'id' => 'shoppingcart_footer_'.$i,
			'description' => __('Shows widgets at Footer Column ', 'shoppingcart').$i,
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	}

	register_widget( 'ShoppingCart_popular_Widgets' );

	if ( class_exists('woocommerce')) {
		//Register Widget.
		register_widget( 'Shoppingcart_product_grid_column_Widget' );
	}
}