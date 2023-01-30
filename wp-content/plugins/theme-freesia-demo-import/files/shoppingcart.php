<?php

/* ShoppingCart Theme */

function shoppingcart_import_files() {
		return array(
    array(
      'import_file_name'             => 'Default ShoppingCart',
      'categories'                   => array( 'Default'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/shoppingcart.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/shoppingcart-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/shoppingcart-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2019/03/screenshot-1.png',
      'import_notice'                => __( 'Recommended Plugins: WooCommerce, Max Mega Menu, Once Click Demo import, YITH WooCommerce Wishlist and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/shoppingcart/reviews/" target="_blank" rel="nofollow">rating</a> our theme.
        ', 'shoppingcart' ),
       'preview_url'                  => 'https://demo.themefreesia.com/shoppingcart/',
    ),
    array(
      'import_file_name'             => 'ShoppingCart Plus',
      'categories'                   => array( 'ShoppingCart Plus' ),
      'preview_url'                  => 'https://demo.themefreesia.com/shoppingcart-plus/',
      'isPlus'                      => true,
      'buy_url'                    => 'https://themefreesia.com/plugins/shoppingcart-plus/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/03/screenshot-2.png',
    ),
     array(
      'import_file_name'             => 'Diamond Store ShoppingCart Plus',
      'categories'                   => array( 'ShoppingCart Plus' ),
      'preview_url'                  => 'https://demo.themefreesia.com/diamond-store/',
      'isPlus'                      => true,
      'buy_url'                    => 'https://themefreesia.com/plugins/shoppingcart-plus/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/04/screenshot.png',
    ),
  ); 
	  
}
add_filter( 'tf-tfdi/import_files', 'shoppingcart_import_files' );

function shoppingcart_after_import_setup($selected_import) {
    // Assign menus to their locations.
    $top_menu = get_term_by( 'name','Top Menu', 'nav_menu' );
    $main_menu = get_term_by( 'name','Main menu', 'nav_menu' );
    $catlog_nav_menu = get_term_by( 'name','All Products', 'nav_menu' );
    $social_icon = get_term_by( 'name','Social Links', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
        'top-menu' => $top_menu->term_id,
        'primary' => $main_menu->term_id,
        'catalog-menu' => $catlog_nav_menu->term_id,
        'social-link' => $social_icon->term_id,
      )
    );

    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );
    $blog_page_id  = get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

}
add_action( 'tf-tfdi/after_import', 'shoppingcart_after_import_setup' );