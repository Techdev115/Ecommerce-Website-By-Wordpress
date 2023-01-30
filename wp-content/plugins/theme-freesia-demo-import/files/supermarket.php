<?php

/* Supermarket Theme */

function supermarket_import_files() {
		return array(
    array(
      'import_file_name'             => 'Default Supermarket',
      'categories'                   => array( 'Default'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/supermarket/supermarket.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/supermarket/supermarket-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/supermarket/supermarket-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2020/07/screenshot.png',
      'import_notice'                => __( 'Recommended Plugins: WooCommerce, Max Mega Menu, YITH WooCommerce Wishlist, YITH WooCommerce Quick View and Contact Form 7 Plugins  to look exactly as in our demo. 
        <br>
       <strong>  After the demo is installed, your site look a bit messy. So you need to setup slider and Frontpage template from customizer and also setup category from widgets. </strong>
        
        <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/supermarket/reviews/" target="_blank" rel="nofollow">rating</a> our theme. <br>
        <a href="https://themefreesia.com/theme-instruction/supermarket/" target="_blank" rel="nofollow">Documentation</a>
        <br>
        <a href="https://youtu.be/vBtlcfVWkkU" target="_blank" rel="nofollow">Video Setup</a>
        ', 'supermarket' ),

       'preview_url'                  => 'https://demo.themefreesia.com/supermarket/',
    ),
    array(
      'import_file_name'             => 'Supermarket Plus',
      'categories'                   => array( 'Supermarket Plus' ),
      'preview_url'                  => 'https://demo.themefreesia.com/supermarket-plus/',
      'isPlus'                      => true,
      'buy_url'                    => 'https://themefreesia.com/plugins/supermarket-plus/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2020/08/supermarket-screenshot-plus.png',
    )
  ); 
	  
}
add_filter( 'tf-tfdi/import_files', 'supermarket_import_files' );

function supermarket_after_import_setup($selected_import) {
    // Assign menus to their locations.
    $top_menu = get_term_by( 'name','Top Menu', 'nav_menu' );
    $main_menu = get_term_by( 'name','Main menu', 'nav_menu' );
    $catlog_nav_menu = get_term_by( 'name','Catalog Menu', 'nav_menu' );
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
add_action( 'tf-tfdi/after_import', 'supermarket_after_import_setup' );