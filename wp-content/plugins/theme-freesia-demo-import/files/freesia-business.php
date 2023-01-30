<?php

/* Freesia Business Theme */

function freesiabusiness_import_files() {
  return array(
    array(
      'import_file_name'             => 'Freesia Business Free',
      'categories'                   => array( 'Freesia Business Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/freesia-business/freesiabusiness.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/freesia-business/freesia-business-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/freesia-business/freesia-business-export.dat',
      'preview_url'                  => 'https://demo.themefreesia.com/freesia-business/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/08/screenshot.jpg',
    ),

    array(
	      'import_file_name'             => 'Freesia Empire Plus',
	      'categories'                   => array( 'Freesia Empire Plus'),
	      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/07/empire-Plus-1200x900.jpg',
	      'preview_url'                  => 'https://demo.themefreesia.com/freesia-empire-plus/',
	      'isPlus'                      => true,
      	'buy_url'                    => 'https://themefreesia.com/plugins/freesia-empire-plus/'
	    ),
  );
}
add_filter( 'tf-tfdi/import_files', 'freesiabusiness_import_files' );

function freesiabusiness_after_import_setup() {
	// Assign menus to their locations.
	$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
	$footer_menu = get_term_by( 'name', 'Social Icons', 'nav_menu' );

	set_theme_mod( 'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
			'social-link' => $footer_menu->term_id,
		)
	);

	// Assign front page and posts page (blog page).
	$front_page_id = get_page_by_title( 'Home' );
	$blog_page_id  = get_page_by_title( 'Blog' );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_page_id->ID );
	update_option( 'page_for_posts', $blog_page_id->ID );

}
add_action( 'tf-tfdi/after_import', 'freesiabusiness_after_import_setup' );