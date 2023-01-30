<?php

/* Pixgraphy Theme */

function pixgraphy_import_files() {
  return array(
    array(
      'import_file_name'             => 'Pixgraphy Free',
      'categories'                   => array( 'Pixgraphy Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/pixgraphy/pixgraphy.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/pixgraphy/demo.pixgraphy-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/pixgraphy/pixgraphy-export.dat',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import' ),
       'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2016/05/screenshot-1.png',
      'preview_url'                  => 'https://demo.themefreesia.com/pixgraphy/',
    ),

    array(
        'import_file_name'             => 'Pixgraphy Plus',
        'categories'                   => array( 'Pixgraphy Plus'),
        'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/07/pixgraphy-plus-1200x900.jpg',
        'preview_url'                  => 'https://demo.themefreesia.com/pixgraphy-plus/',
        'isPlus'                      => true,
        'buy_url'                    => 'https://themefreesia.com/plugins/pixgraphy-plus/'
      ),
  );
}
add_filter( 'tf-tfdi/import_files', 'pixgraphy_import_files' );

function pixgraphy_after_import_setup() {
	// Assign menus to their locations.
	$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );

	set_theme_mod( 'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
		)
	);

}
add_action( 'tf-tfdi/after_import', 'pixgraphy_after_import_setup' );