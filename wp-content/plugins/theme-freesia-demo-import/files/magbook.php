<?php

/* Magbook Theme */

function magbook_import_files() {
  return array(
    array(
      'import_file_name'             => 'Default Demo Content',
      'categories'                   => array( 'Default'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/default/magbook.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/default/magbook-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/default/magbook-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2018/03/screenshot-1.jpg',
      'import_notice'                => __( 'Recommended Plugins: Install WooCommerce and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/magbook/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/magbook/',
    ),
    array(
      'import_file_name'             => 'Magbook NewsPaper',
      'categories'                   => array( 'NewsPaper' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/newspaper/magbook.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/newspaper/magbook-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/newspaper/magbook-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2018/03/magbook-newspaper-screenshot.jpg',
      'import_notice'                => __( 'Recommended Plugins: Install WooCommerce , Recent Posts Widget With Thumbnails and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/magbook/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/magbook-newspaper/',
    ),
    array(
      'import_file_name'             => 'Magbook Journal',
      'categories'                   => array( 'Journal' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/journal/magbook.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/journal/magbook-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/journal/magbook-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2018/03/magbook-journal-screenshot.jpg',
      'import_notice'                => __( 'Recommended Plugins: Install WooCommerce , Recent Posts Widget With Thumbnails and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/magbook/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/magbook-journal/',
    ),
    array(
      'import_file_name'             => 'Magbook Publication',
      'categories'                   => array( 'Publication' ),
     'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/publication/magbook.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/publication/magbook-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/magbook/publication/magbook-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2018/03/Publication-screenshot.jpg',
      'import_notice'                => __( 'Recommended Plugins: Install WooCommerce , Recent Posts Widget With Thumbnails and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/magbook/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/magbook-publication/',
    ),

    array(
	      'import_file_name'             => 'Magbook Plus',
	      'categories'                   => array( 'Magbook Plus'),
	      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/03/screenshot-2.jpg',
	      'preview_url'                  => 'https://demo.themefreesia.com/magbook-plus/',
	      'isPlus'                      => true,
      	'buy_url'                    => 'https://themefreesia.com/plugins/magbook-plus/'
	    ),
  ); 
}
add_filter( 'tf-tfdi/import_files', 'magbook_import_files' );

function magbook_after_import_setup($selected_import) {
	if ( 'Default Demo Content' === $selected_import['import_file_name'] ) {
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Add Social Icons Only', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'top-menu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'side-nav-menu' => $side_menu->term_id,
				'footermenu' => $footer_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Home' );
		$blog_page_id  = get_page_by_title( 'Blog' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );
	} 
	elseif('Magbook NewsPaper' === $selected_import['import_file_name'] ){
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Add Social Icons Only', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'top-menu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'side-nav-menu' => $side_menu->term_id,
				'footermenu' => $footer_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);
		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Home' );
		$blog_page_id  = get_page_by_title( 'Blog' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );

	}elseif('Magbook Journal' === $selected_import['import_file_name'] ){
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Add Social Icons Only', 'nav_menu' );
		set_theme_mod( 'nav_menu_locations', array(
				'top-menu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'side-nav-menu' => $side_menu->term_id,
				'footermenu' => $footer_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Home' );
		$blog_page_id  = get_page_by_title( 'Blog' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );
		
	} else {
		// Assign menus to their locations for non profits 
		$top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Add Social Icons Only', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'top-menu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'side-nav-menu' => $side_menu->term_id,
				'footermenu' => $footer_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Home' );
		$blog_page_id  = get_page_by_title( 'Blog' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );
	}

}
add_action( 'tf-tfdi/after_import', 'magbook_after_import_setup' );