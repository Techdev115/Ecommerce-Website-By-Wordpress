<?php

/* Freesia Empire Plus */

function freesiaempire_import_files() {
  return array(
  	array(
      'import_file_name'             => 'Freesia Empire Free',
      'categories'                   => array( 'Freesia Empire Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-empire/freesiaempire.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-empire/freesia-empire-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-empire/freesia-empire-export.dat',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/freesia-empire/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2016/01/screenshot.jpg',
    ),

  	array(
      'import_file_name'             => 'Freesia Corporate Free',
      'categories'                   => array( 'Freesia Corporate Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-corporate/freesiacorporate.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-corporate/freesia-corporate-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-corporate/freesia-corporate-export.dat',
      'preview_url'                  => 'https://demo.themefreesia.com/freesia-corporate/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/10/screenshot.jpg',
    ),

  	array(
      'import_file_name'             => 'Freesia Business Free',
      'categories'                   => array( 'Freesia Business Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-business/freesiabusiness.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-business/freesia-business-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/freesia-business/freesia-business-export.dat',
      'preview_url'                  => 'https://demo.themefreesia.com/freesia-business/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/08/screenshot.jpg',
    ),

    array(
      'import_file_name'             => 'Freesia Empire Plus',
      'categories'                   => array( 'Freesia Empire Plus' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/freesia-empire/freesiaempireplus.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/freesia-empire/freesia-empire-plus-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/freesia-empire/freesia-empire-export.dat',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import' ),
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2017/07/empire-Plus-1200x900.jpg',
      'preview_url'                  => 'https://demo.themefreesia.com/freesia-empire-plus/',
    ),
  );
}
add_filter( 'tf-tfdi/import_files', 'freesiaempire_import_files' );

function freesiaempire_after_import_setup() {
	if ( 'Freesia Empire Free' === $selected_import['import_file_name'] ) {
		// Assign menus to their locations.
		$main_menu = get_term_by( 'name', 'Main menu', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Social Links', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
			'side-nav-menu' => $side_menu->term_id,
			'social-link' => $social_link->term_id,
			)
		);

	}elseif ( 'Freesia Corporate Free' === $selected_import['import_file_name'] ) {
		// Assign menus to their locations.
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Social Icons', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'primary' => $main_menu->term_id,
				'social-link' => $footer_menu->term_id,
			)
		);

	}elseif ( 'Freesia Business Free' === $selected_import['import_file_name'] ) {
		// Assign menus to their locations.
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Social Icons', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'primary' => $main_menu->term_id,
				'social-link' => $footer_menu->term_id,
			)
		);

	} else {
		// Assign menus to their locations.
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$social_menu = get_term_by( 'name', 'Social Icons', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'footer menu', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'primary' => $main_menu->term_id,
				'social-link' => $social_menu->term_id,
				'footermenu' => $footer_menu->term_id,
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
add_action( 'tf-tfdi/after_import', 'freesiaempire_after_import_setup' );