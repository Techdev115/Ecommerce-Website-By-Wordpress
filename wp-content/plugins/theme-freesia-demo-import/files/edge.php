<?php

/* Edge Theme */

function edge_import_files() {
  return array(
    array(
      'import_file_name'             => 'Edge Free',
      'categories'                   => array( 'Edge Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/edge/edge.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/edge/edge-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/edge/edge-export.dat',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2016/05/screenshot.png',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/edge/',
    ),
    array(
      'import_file_name'             => 'Edge Plus',
      'categories'                   => array( 'Edge Plus'),
      'import_preview_image_url'    => 'https://themefreesia.com/wp-content/uploads/2017/07/edge-plus-1200x900.jpg',
      'preview_url'                  => 'https://demo.themefreesia.com/edge-plus/',
      'isPlus'                      => true,
      'buy_url'                    => 'https://themefreesia.com/plugins/edge-plus/'
    ),
  );
}
add_filter( 'tf-tfdi/import_files', 'edge_import_files' );

function edge_after_import_setup() {
	// Assign menus to their locations.
	$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
	$footer_menu = get_term_by( 'name', 'footer menu', 'nav_menu' );

	set_theme_mod( 'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
			'footermenu' => $footer_menu->term_id,
		)
	);

}
add_action( 'tf-tfdi/after_import', 'edge_after_import_setup' );