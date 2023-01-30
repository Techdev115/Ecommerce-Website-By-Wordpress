<?php

/* ShoppingCart Plus */

function extension_import_files() {
  return array(
    array(
      'import_file_name'             => 'Default ShoppingCart',
      'categories'                   => array( 'Default'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/shoppingcart/shoppingcart.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/shoppingcart/shoppingcart-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/shoppingcart/shoppingcart-export.dat',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/03/screenshot-1.png',
      'import_notice'                => __( 'Recommended Plugins: WooCommerce, Max Mega Menu, Once Click Demo import, YITH WooCommerce Wishlist and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/shoppingcart/reviews/" target="_blank" rel="nofollow">rating</a> our theme.
        ', 'shoppingcart' ),
       'preview_url'                  => 'https://demo.themefreesia.com/shoppingcart/',
    ),

    array(
      'import_file_name'             => 'ShoppingCart Plus',
      'categories'                   => array( 'ShoppingCart Plus'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/dummy/shoppingcart.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/dummy/shoppingcart-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/dummy/shoppingcart-export.dat',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/03/screenshot-2.png',
      'import_notice'                => __( 'Recommended Plugins: WooCommerce, Max Mega Menu, Once Click Demo import, YITH WooCommerce Wishlist and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/shoppingcart/reviews/" target="_blank" rel="nofollow">rating</a> our theme.
        ', 'shoppingcart' ),
       'preview_url'                  => 'https://demo.themefreesia.com/shoppingcart-plus/',
    ),
    array(
      'import_file_name'             => 'Diamond Store ShoppingCart Plus',
      'categories'                   => array( 'ShoppingCart Plus'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/diamond-store/shoppingcart.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/diamond-store/shoppingcart-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/shoppingcart/diamond-store/shoppingcart-export.dat',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/04/screenshot.png',
      'import_notice'                => __( 'Recommended Plugins: WooCommerce, Max Mega Menu, Once Click Demo import, YITH WooCommerce Wishlist and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/shoppingcart/reviews/" target="_blank" rel="nofollow">rating</a> our theme.
        ', 'shoppingcart' ),
       'preview_url'                  => 'https://demo.themefreesia.com/diamond-store/',
    ),
  );
}
add_filter( 'tf-tfdi/import_files', 'extension_import_files' );

function extension_after_import_setup($selected_import) {
   if ( 'Default ShoppingCart Plus' === $selected_import['import_file_name'] ) {

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

   } elseif ( 'ShoppingCart Plus' === $selected_import['import_file_name'] ) {
        // Assign menus to their locations.
        $top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
        $main_menu = get_term_by( 'name', 'Main menu', 'nav_menu' );
        $catlog_nav_menu = get_term_by( 'name', 'All Products', 'nav_menu' );
        $social_icon = get_term_by( 'name', 'Social Links', 'nav_menu' );

        set_theme_mod( 'nav_menu_locations', array(
            'top-menu' => $top_menu->term_id,
            'primary' => $main_menu->term_id,
            'catalog-menu' => $catlog_nav_menu->term_id,
            'social-link' => $social_icon->term_id,
          )
        );

    } else {

        // Assign menus to their locations.
        $top_menu = get_term_by( 'name', esc_html__('Top Menu','shoppingcart-plus'), 'nav_menu' );
        $main_menu = get_term_by( 'name', esc_html__('Main menu','shoppingcart-plus'), 'nav_menu' );
        $social_icon = get_term_by( 'name', esc_html__('Social Links','shoppingcart-plus'), 'nav_menu' );

        set_theme_mod( 'nav_menu_locations', array(
            'top-menu' => $top_menu->term_id,
            'primary' => $main_menu->term_id,
            'social-link' => $social_icon->term_id,
          )
        );

    }

     // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );
    $blog_page_id  = get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

}
add_action( 'tf-tfdi/after_import', 'extension_after_import_setup' );