<?php

/* Arise Plus */

function arise_import_files() {
  return array(

	array(
		'import_file_name'             => 'Arise Free',
		'categories'                   => array( 'Arise Free' ),
		'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/arise/arise.xml',
		'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/arise/arise-widgets.wie',
		'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/arise/arise-export.dat',
		'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2015/09/screenshot.png',
		'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import' ),
		'preview_url'                  => 'https://demo.themefreesia.com/arise/',
	),

	array(
			'import_file_name'             => 'Arise Plus',
			'categories'                   => array('Arise Plus' ),
			'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/arise/ariseplus.xml',
			'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/arise/arise-plus-widgets.wie',
			'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/arise/arise-export-plus.dat',
			'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/07/arise-Plus-1200x900.jpg',
			'preview_url'                  => 'https://demo.themefreesia.com/arise-plus/',
			'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import' ),
		),
	);
}
add_filter( 'tf-tfdi/import_files', 'arise_import_files' );

function arise_after_import_setup($selected_import) {
	if ( 'Arise Free' === $selected_import['import_file_name'] ) {
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Multilangual Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'main menu', 'nav_menu' );
		$social_menu = get_term_by( 'name', 'Social Icons', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'topmenu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'social-link' => $social_menu->term_id,
			)
		);
	} else {
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'Multilangual Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name', 'main menu', 'nav_menu' );
		$social_menu = get_term_by( 'name', 'Social Icons', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer Menu', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'topmenu' => $top_menu->term_id,
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
add_action( 'tf-tfdi/after_import', 'arise_after_import_setup' );