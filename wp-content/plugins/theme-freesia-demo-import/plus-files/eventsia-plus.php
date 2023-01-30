<?php

/* Eventsia Plus */

function eventsiart_files() {
  return array(
  	array(
      'import_file_name'             => 'Eventsia Free',
      'categories'                   => array( 'Eventsia Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/eventsia/eventsia.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/eventsia/eventsia-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/eventsia/eventsia-export.dat',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import'    ),
      'preview_url'                  => 'https://demo.themefreesia.com/eventsia/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/12/screenshot.png',

	  ),
    array(
      'import_file_name'             => 'Eventsia Plus',
      'categories'                   => array( 'Eventsia plus' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/eventsia/eventsiaplus.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/eventsia/eventsia-plus-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/eventsia/eventsia-export.dat',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import'    ),
      'preview_url'                  => 'https://demo.themefreesia.com/eventsia-plus/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/12/plus-screenshot.png',
  ));
}
add_filter( 'tf-tfdi/import_files', 'eventsiart_files' );

function eventsia_import_setup() {
	// Assign menus to their locations.
	$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
	$side_menu = get_term_by( 'name', 'Side Menu', 'nav_menu' );
	$social_link = get_term_by( 'name', 'Social Link', 'nav_menu' );

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
add_action( 'tf-tfdi/after_import', 'eventsia_import_setup' );