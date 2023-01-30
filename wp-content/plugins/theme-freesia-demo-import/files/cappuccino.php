<?php

/* Cappuccino Theme */

function cappuccino_import_files() {
  return array(
    array(
      'import_file_name'             => 'Cappuccino Free',
      'categories'                   => array( 'Cappuccino Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/cappuccino/cappuccino.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/cappuccino/cappuccino-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/cappuccino/cappuccino-export.dat',
       'import_notice'                => __( 'Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/cappuccino/reviews/" target="_blank" rel="nofollow">rating</a> our theme.
        ', 'cappuccino' ),
      'preview_url'                  => 'https://demo.themefreesia.com/cappuccino/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/01/screenshot.jpg',
    ),

     array(
        'import_file_name'             => 'Cocktail Plus',
        'categories'                   => array( 'Cocktail Plus'),
        'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/12/screenshot-1.jpg',
        'preview_url'                  => 'https://demo.themefreesia.com/cocktail-plus/',
        'isPlus'                      => true,
        'buy_url'                    => 'https://themefreesia.com/wp-content/uploads/2018/12/screenshot-1.jpg'

      ),
  );
}
add_filter( 'tf-tfdi/import_files', 'cappuccino_import_files' );

function cappuccino_after_import_setup() {
  // Assign menus to their locations.
  $main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
  $side_menu = get_term_by( 'name', 'side menu', 'nav_menu' );
  $social_link = get_term_by( 'name', 'Add Social Icons Only', 'nav_menu' );

  set_theme_mod( 'nav_menu_locations', array(
      'primary' => $main_menu->term_id,
      'side-nav-menu' => $side_menu->term_id,
      'social-link' => $social_link->term_id,
    )
  );

}
add_action( 'tf-tfdi/after_import', 'cappuccino_after_import_setup' );