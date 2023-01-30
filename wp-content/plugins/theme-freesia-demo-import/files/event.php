<?php

/* Event Theme */

function eventrt_files() {
  return array(
    array(
      'import_file_name'             => 'Event Free',
      'categories'                   => array( 'Event Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/event/event.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/event/event-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/event/event-export.dat',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import'    ),
      'preview_url'                  => 'https://demo.themefreesia.com/event/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2016/09/screenshot.png',

	  ),
	 array(
	      'import_file_name'             => 'Event Plus',
	      'categories'                   => array( 'Event Plus'),
	      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/07/event-plus-1200x900.jpg',
	      'preview_url'                  => 'https://demo.themefreesia.com/event-plus/',
	      'isPlus'                      => true,
      	'buy_url'                    => 'https://themefreesia.com/plugins/event-plus/'
	    ),
	);
}
add_filter( 'tf-tfdi/import_files', 'eventrt_files' );

function eventr_import_setup() {
	// Assign menus to their locations.
	$main_menu = get_term_by( 'name', 'Menu 1', 'nav_menu' );
	$social_menu = get_term_by( 'name', 'Social Icons', 'nav_menu' );

	set_theme_mod( 'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
			'social-link' => $social_menu->term_id,
		)
	);

	// Assign front page and posts page (blog page).
	$front_page_id = get_page_by_title( 'Home' );
	$blog_page_id  = get_page_by_title( 'Blog' );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_page_id->ID );
	update_option( 'page_for_posts', $blog_page_id->ID );

}
add_action( 'tf-tfdi/after_import', 'eventr_import_setup' );