<?php

/* Storexmas plus */

function storexmas_import_files() {
  return array(
  	array(
      'import_file_name'             => 'StoreXmas Free',
      'categories'                   => array( 'StoreXmas Free'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/storexmas/storexmas.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/storexmas/storexmas-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'../files/dummy/storexmas/storexmas-export.dat',
      'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/12/screenshot-1.jpg',
      'preview_url'                  => 'https://demo.themefreesia.com/storexmas/',
      'import_notice'                => __( 'Important: You have to set up slider from Customize > Slider Options > Select Category/ Proudct Category Slider (Select Product slider). <br> To display brand and Promo you need to install WooCommerce Plugin. 
<section id="frontpage">
<h3>Setup Frontpage/ Blog as in Demo</h3>

<h4>Setup Front Page Features (Brand/ Promo)</h4>
<ol><li>Install WooCommerce Plugin and activate it. Create products category and upload Category image. </li>
<li><em>Create WooCommerce Products with featured image</em></li>
<li><em>Repeat this process for other products too.</em></li>
</ol>
<em>After creating Products with featured image</em> Go to Dashboard &gt; Appearance &gt; Customize &gt; Frontpage Template &gt; Product Featured Brand/ Product Category &gt; Select Product Category <strong>just now you have created</strong>


<h4>Front Page Template Section</h4>
<img src="https://themefreesia.com/wp-content/uploads/2017/12/frontpage-template-1.jpg" alt="front page features" width="560" height="748" class="alignnone size-full wp-image-16361">

</section>For more information <a href="https://themefreesia.com/theme-instruction/storexmas/#frontpage" traget="_blank"> Visit Instruction Page</a>   <br>Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/storexmas/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
    ),
    array(
      'import_file_name'             => 'StoreXmas Plus',
      'categories'                   => array( 'StoreXmas Plus'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/storexmas/storexmas.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/storexmas/storexmas-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/storexmas/storexmas-export.dat',
      'import_preview_image_url'   	 => plugins_url( '/storexmas-demo-importer/inc/default/screenshot.png', dirname(__FILE__) ),
      'import_notice'                => __( 'Important: You have to set up slider from Customize > Slider Options > Select Category/ Proudct Category Slider (Select Product slider). <br> To display brand and Promo you need to install WooCommerce Plugin. 
<section id="frontpage">
<h3>Setup Frontpage/ Blog as in Demo</h3>

<h4>Setup Front Page Features (Brand/ Promo)</h4>
<ol><li>Install WooCommerce Plugin and activate it. Create products category and upload Category image. </li>
<li><em>Create WooCommerce Products with featured image</em></li>
<li><em>Repeat this process for other products too.</em></li>
</ol>
<em>After creating Products with featured image</em> Go to Dashboard &gt; Appearance &gt; Customize &gt; Frontpage Template &gt; Product Featured Brand/ Product Category &gt; Select Product Category <strong>just now you have created</strong>


<h4>Front Page Template Section</h4>
<img src="https://themefreesia.com/wp-content/uploads/2017/12/frontpage-template-1.jpg" alt="front page features" width="560" height="748" class="alignnone size-full wp-image-16361">

</section>For more information <a href="https://themefreesia.com/theme-instruction/storexmas/#frontpage" traget="_blank"> Visit Instruction Page</a>   <br>Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/storexmas/reviews/" target="_blank" rel="nofollow">rating</a> our theme
      	', 'theme-freesia-demo-import' ),
   'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2017/12/screenshot-2.jpg',
      'preview_url'                  => 'https://demo.themefreesia.com/storexmas-plus/',
    ),
  ); 
}
add_filter( 'tf-tfdi/import_files', 'storexmas_import_files' );

function storexmas_after_import_setup($selected_import) {
	if ( 'StoreXmas Free' === $selected_import['import_file_name'] ) {
		$main_menu = get_term_by( 'name', 'Main Nav', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Social icon', 'nav_menu' );
		$top_menu = get_term_by('name','top menu','nav_menu');

		set_theme_mod( 'nav_menu_locations', array(
				'primary' => $main_menu->term_id,
				'side-nav-menu' => $side_menu->term_id,
				'footermenu' => $footer_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);
	} else {
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name', 'top menu', 'top-menu' );
		$main_menu = get_term_by( 'name', 'Main Nav', 'nav_menu' );
		$side_menu = get_term_by( 'name', 'Side menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer menu', 'nav_menu' );
		$social_link = get_term_by( 'name', 'Social icon', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'top-menu' => $top_menu->term_id,
				'primary' => $main_menu->term_id,
				'side-nav-menu' => $side_menu->term_id,
				'footermenu' => $footer_menu->term_id,
				'social-link' => $social_link->term_id,
			)
		);
	}
}
add_action( 'tf-tfdi/after_import', 'storexmas_after_import_setup' );