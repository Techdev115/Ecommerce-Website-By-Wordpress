<?php

/* Photograph Theme */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	function photograph_import_files() {

	  return array(
	    array(
	      'import_file_name'             => 'Photograph Free',
	      'categories'                   => array( 'Photograph Free'),
	      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/photograph/photograph.wordpress.xml',
	      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/photograph/photograph-widgets.wie',
	      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/photograph/photograph-export.dat',
	      'import_notice'                => __( 'Recommended Plugins: Jetpack, Instagram Feed and Contact Form 7 Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/photograph/reviews/" target="_blank" rel="nofollow">rating</a> our theme. Jetpack Plugins is to display Gallery as shown in demo. You need to activate Carousel and Tiled Galleries to display exactly as in our demo site.
	      	', 'theme-freesia-demo-import' ),
	      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2018/04/screenshot.jpg',
	      'preview_url'                  => 'https://demo.themefreesia.com/photograph/',
	    ),

	    array(
	      'import_file_name'             => 'Photograph Plus',
	      'categories'                   => array( 'Photograph Plus'),
	      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/04/screenshot-1.jpg',
	      'preview_url'                  => 'https://demo.themefreesia.com/photograph-plus/',
	      'isPlus'                      => true,
      	'buy_url'                    => 'https://themefreesia.com/plugins/photograph-plus/'
	    ),
	  ); 
	}
	add_filter( 'tf-tfdi/import_files', 'photograph_import_files' );

	function photograph_after_import_setup($selected_import) {
		// Assign menus to their locations.
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Add Social Icons Only', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'primary' => $main_menu->term_id,
				'side-nav-menu' => $side_menu->term_id,
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
add_action( 'tf-tfdi/after_import', 'photograph_after_import_setup' );