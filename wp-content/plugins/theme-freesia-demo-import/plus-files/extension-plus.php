<?php

/* Extension Plus */

function extension_import_files() {
  return array(
  	array(
      'import_file_name'             => 'Default Extension',
      'categories'                   => array( 'Default'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/default/extension.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/default/extension-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/default/extension-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2019/07/screenshot.jpg',
      'import_notice'                => __( 'Important: You must activate Elementor Plugin to display exactly as in our demo site. <br> Recommended Plugins: Install Elementor Page Builder to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/extension/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
       'preview_url'                  => 'https://demo.themefreesia.com/extension/',
    ),
    array(
      'import_file_name'             => 'Gutenberg',
      'categories'                   => array( 'Gutenberg' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/gutenberg/extension-gutenberg.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/gutenberg/extension-gutenberg-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/gutenberg/extension-gutenberg-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2019/07/Gutenberg.png',
      'import_notice'                => __( 'Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/extension/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
       'preview_url'                  => 'https://demo.themefreesia.com/gutenberg/',
    ),
    array(
      'import_file_name'             => 'Extension App',
      'categories'                   => array( 'Extension App' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/app/extension-app.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/app/extension-app-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/app/extension-app-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2019/07/App.png',
      'import_notice'                => __( 'Important: You must activate Elementor Plugin to display exactly as in our demo site. <br> Recommended Plugins: Install Elementor Page Builder to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/extension/reviews/" target="_blank" rel="nofollow">rating</a> our theme
        ', 'theme-freesia-demo-import' ),
       'preview_url'                  => 'https://demo.themefreesia.com/extension-app/',
    ),
    array(
      'import_file_name'             => 'Extension Education',
      'categories'                   => array( 'Education' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/education/extension-education.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/education/extension-education-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/education/extension-education-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2019/07/Education.png',
      'import_notice'                => __( 'Important: You must activate Elementor Plugin to display exactly as in our demo site. <br> Recommended Plugins: Install Elementor Page Builder to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/extension/reviews/" target="_blank" rel="nofollow">rating</a> our theme
        ', 'theme-freesia-demo-import' ),
       'preview_url'                  => 'https://demo.themefreesia.com/extension-education/',
    ),
    array(
      'import_file_name'             => 'Extension Construction',
      'categories'                   => array( 'Construction' ),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/construction/extension-construction.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/construction/extension-construction-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/extension/construction/extension-construction-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2019/07/Construction.png',
      'import_notice'                => __( 'Important: You must activate Elementor Plugin to display exactly as in our demo site. <br> Recommended Plugins: Install Elementor Page Builder to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/extension/reviews/" target="_blank" rel="nofollow">rating</a> our theme
        ', 'theme-freesia-demo-import' ),
       'preview_url'                  => 'https://demo.themefreesia.com/extension-construction/',
    ),
    array(
      'import_file_name'             => 'Extension Plus',
      'categories'                   => array( 'Extension Plus'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/extension/extension.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/extension/extension-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/extension/extension-export.dat',
      'import_notice'                => __( 'Recommended Plugins: Install Elementor, Contact Form 7 and Extension Plus Plugins  to look exactly as in our demo. <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/extension/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
       'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2019/07/extension-plus-screenshot.png',
      'preview_url'                  => 'https://demo.themefreesia.com/extension-plus/',
    ),
  ); 
}
add_filter( 'tf-tfdi/import_files', 'extension_import_files' );

function extension_after_import_setup($selected_import) {
	if ( 'Default Extension' === $selected_import['import_file_name'] ) {
    // Assign menus to their locations.
    $main_menu = get_term_by( 'name', 'Main menu', 'nav_menu' );
    $side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
    $social_link = get_term_by( 'name', 'Social Links', 'nav_menu' );

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
  } elseif('Gutenberg' === $selected_import['import_file_name'] ){
    // Assign menus to their locations.
    $main_menu = get_term_by( 'name', 'Main menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
        'primary' => $main_menu->term_id,
      )
    );
    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );
    $blog_page_id  = get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

  }elseif('Extension App' === $selected_import['import_file_name'] ){
    // Assign menus to their locations.
    // Assign menus to their locations.
    $main_menu = get_term_by( 'name', 'Main menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
        'primary' => $main_menu->term_id,
      )
    );

    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    
  }elseif('Extension Education' === $selected_import['import_file_name'] ){
    // Assign menus to their locations.
    $main_menu = get_term_by( 'name', 'Main menu', 'nav_menu' );
    $side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
    $social_link = get_term_by( 'name', 'Social Links', 'nav_menu' );

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
    
   }elseif('Extension Construction' === $selected_import['import_file_name'] ){
    // Assign menus to their locations for non profits 
    $main_menu = get_term_by( 'name', 'Main menu', 'nav_menu' );
    $side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
    $social_link = get_term_by( 'name', 'Social Links', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
        'primary' => $main_menu->term_id,
        'side-nav-menu' => $side_menu->term_id,
        'social-link' => $social_link->term_id,
      )
    );
    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
  } else {
		// Assign menus to their locations.
		$main_menu = get_term_by( 'name', 'Main menu', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Social Links', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'primary' => $main_menu->term_id,
				'side-nav-menu' => $side_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Home' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
	}

}
add_action( 'tf-tfdi/after_import', 'extension_after_import_setup' );