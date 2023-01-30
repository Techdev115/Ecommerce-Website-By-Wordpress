<?php

/* Alternative Theme */

function alternative_import_files() {

  return array(
    array(
      'import_file_name'             => 'Alternative Free',
      'categories'                   => array( 'Alternative Free'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/alternative/alternative.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/alternative/alternative-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/alternative/alternative-export.dat',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/05/screenshot.jpg',
      'import_notice'                => __( 'Recommended Plugins: Install Contact Form 7 Plugins. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/alternative/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
      'preview_url'                  => 'https://demo.themefreesia.com/alternative/',
    ),
    array(
      'import_file_name'             => 'Edge Plus',
      'categories'                   => array( 'Edge Plus'),
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/07/edge-plus-1200x900.jpg',
      'preview_url'                  => 'https://demo.themefreesia.com/edge-plus/',
      'isPlus'                      => true,
      'buy_url'                    => 'https://themefreesia.com/plugins/edge-plus/'
    ),
  );
}
add_filter( 'tf-tfdi/import_files', 'alternative_import_files' );

function alternative_after_import_setup() {
	// Assign menus to their locations.
	$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
	$footer_menu = get_term_by( 'name', 'Footer Menu', 'nav_menu' );

	set_theme_mod( 'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
			'footermenu' => $footer_menu->term_id,
		)
	);

}
add_action( 'tf-tfdi/after_import', 'alternative_after_import_setup' );