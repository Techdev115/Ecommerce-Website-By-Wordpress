<?php

/* Cocktail Theme */

function cocktail_import_files() {
  return array(
    array(
      'import_file_name'             => esc_html__('Cocktail Demo','cocktail'),
        'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/cocktail/cocktail.wordpress.xml',
        'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/cocktail/cocktail-widgets.wie',
        'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/cocktail/cocktail-export.dat',
        'import_notice'                => __( 'Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/cocktail/reviews/" target="_blank" rel="nofollow">rating</a> our theme.
        ', 'cocktail' ),
      'preview_url'                  => 'https://demo.themefreesia.com/cocktail/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/12/screenshot-2.jpg',
    ),

    array(
      'import_file_name'             => 'Cappuccino Free',
      'categories'                   => array( 'Cappuccino Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/cappuccino/cappuccino.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/cappuccino/cappuccino-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/cappuccino/cappuccino-export.dat',
       'import_notice'                => __( 'Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/cappuccino/reviews/" target="_blank" rel="nofollow">rating</a> our theme.
        ', 'cappuccino' ),
      'preview_url'                  => 'https://demo.themefreesia.com/cappuccino/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/01/screenshot.jpg',
    ),

    array(
      'import_file_name'             => 'Mocktail Free',
      'categories'                   => array( 'Mocktail Free' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/mocktail/mocktail.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/mocktail/mocktail-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/mocktail/mocktail-export.dat',
       'import_notice'                => __( 'Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/mocktail/reviews/" target="_blank" rel="nofollow">rating</a> our theme.
        ', 'mocktail' ),
      'preview_url'                  => 'https://demo.themefreesia.com/mocktail/',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/12/screenshot-3.jpg',
    ),

     array(
        'import_file_name'             => 'Cocktail Plus',
        'categories'                   => array( 'Cocktail Plus'),
        'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/cocktail/cocktail.wordpress.xml',
        'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/cocktail/cocktail-widgets.wie',
        'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/cocktail/cocktail-export.dat',
        'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2018/12/screenshot-1.jpg',
        'preview_url'                  => 'https://demo.themefreesia.com/cappuccino-plus/',

      ),
  );
}
add_filter( 'tf-tfdi/import_files', 'cocktail_import_files' );

function cocktail_after_import_setup() {

  if ( 'Cocktail Plus' === $selected_import['import_file_name'] ) {

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
    $side_menu = get_term_by( 'name', 'side menu', 'nav_menu' );
    $social_link = get_term_by( 'name', 'Add Social Icons Only', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
        'primary' => $main_menu->term_id,
        'side-nav-menu' => $side_menu->term_id,
        'social-link' => $social_link->term_id,
      )
    );
   }

}
add_action( 'tf-tfdi/after_import', 'cocktail_after_import_setup' );