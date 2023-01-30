<?php

/* Excellent Theme */

function excellent_import_files() {
  return array(
    array(
      'import_file_name'             => 'Default Demo Content',
      'categories'                   => array( 'Default'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/default/excellent.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/default/excellent-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/default/excellent-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2017/02/screenshot.png',
      'import_notice'                => __( 'Important: You have to set up slider from Customize > Slider Options > Select Category Slider. Recommended Plugins: Install WooCommerce Plugins, Contact Form 7 and Recent Posts Widget With Thumbnails to look exactly as in our demo.', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/excellent/',
    ),
    array(
      'import_file_name'             => 'Video Demo Content',
      'categories'                   => array( 'Video' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/video/excellent-video.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/video/excellent-video-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/video/excellent-video-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2017/02/excellent-demoimg-2.jpg',
      'import_notice'                => __( 'Important: Recommended Plugins:Install Contact Form 7, Recent Posts Widget With Thumbnails to look exactly as in our demo.', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/excellent-video/',
    ),
    array(
      'import_file_name'             => 'CV Demo Content',
      'categories'                   => array( 'CV' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/cv/excellent-cv.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/cv/excellent-cv-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/cv/excellent-cv-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2017/02/excellent-demoimg-3.jpg',
      'import_notice'                => __( 'Important: Recommended Plugins:Install Contact Form 7, Recent Posts Widget With Thumbnails to look exactly as in our demo.', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/excellent-cv/',
    ),
    array(
      'import_file_name'             => 'Shop Demo Content',
      'categories'                   => array( 'Shop' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/shop/excellent-shop.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/shop/excellent-shop-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/excellent/shop/excellent-shop-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2017/02/excellent-demoimg-1-1.jpg',
      'import_notice'                => __( 'Important: After importing demo content do not forget to run WooCommerce setup wizard. Doing it only your WooCommerce page will be set as in our demo. You have to set up slider from Customize > Slider Options > Select Category Slider. Recommended plugins : Install WooCommerce Plugins, Contact Form 7 and Recent Posts Widget With Thumbnails to look exactly as in our demo.', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/excellent-shop/',
    ),
    array(
	      'import_file_name'             => 'Excellent Plus',
	      'categories'                   => array( 'Excellent Plus'),
	      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/07/excellent-plus-1200x900.jpg',
	      'preview_url'                  => 'https://demo.themefreesia.com/excellent-plus/',
	      'isPlus'                      => true,
      	'buy_url'                    => 'https://themefreesia.com/plugins/excellent-plus/'
	    ),
  );
}
add_filter( 'tf-tfdi/import_files', 'excellent_import_files' );

function excellent_after_import_setup($selected_import) {
	if ( 'Default Demo Content' === $selected_import['import_file_name'] ) {
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Social Icons', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'topmenu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
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
	}elseif('Video Demo Content' === $selected_import['import_file_name'] ){
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'No menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Social Icons', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'topmenu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);

	}elseif('CV Demo Content' === $selected_import['import_file_name'] ){
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Social Icons', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'topmenu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'My Page' );
		$blog_page_id  = get_page_by_title( 'Blog' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );
		
	}else{
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer Main Menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Social Icons', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'topmenu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'footermenu' => $footer_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Shop' );
		$blog_page_id  = get_page_by_title( 'Blog' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );
	}

}
add_action( 'tf-tfdi/after_import', 'excellent_after_import_setup' );