<?php

/* Pixgraphy Plus */

function pixgraphy_import_files() {
  return array(
    array(
      'import_file_name'             => 'Pixgraphy Free',
      'categories'                   => array( 'Pixgraphy Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/pixgraphy/pixgraphy.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/pixgraphy/demo.pixgraphy-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/pixgraphy/pixgraphy-export.dat',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import' ),
       'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2016/05/screenshot-1.png',
      'preview_url'                  => 'https://demo.themefreesia.com/pixgraphy/',
    ),
    array(
      'import_file_name'             => 'Pixgraphy Plus',
      'categories'                   => array( 'Pixgraphy Plus' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/pixgraphy/pixgraphyplus.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/pixgraphy/pixgraphy-plus-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/pixgraphy/pixgraphy-export.dat',
      'import_notice'                => __( 'After you import this demo, you will have to setup the slider separately.', 'theme-freesia-demo-import' ),
       'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/07/pixgraphy-plus-1200x900.jpg',
      'preview_url'                  => 'https://demo.themefreesia.com/pixgraphy-plus/',
    ),
  );
}
add_filter( 'tf-tfdi/import_files', 'pixgraphy_import_files' );

function pixgraphy_after_import_setup() {
  if ( 'Pixgraphy Free' === $selected_import['import_file_name'] ) {
    // Assign menus to their locations.
    $main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
        'primary' => $main_menu->term_id,
      )
    );
  } else {
    // Assign menus to their locations.
    $main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
    $footer_menu = get_term_by( 'name', 'Footer Menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
        'primary' => $main_menu->term_id,
        'footermenu' => $footer_menu->term_id,
      )
    );
  }

}
add_action( 'tf-tfdi/after_import', 'pixgraphy_after_import_setup' );