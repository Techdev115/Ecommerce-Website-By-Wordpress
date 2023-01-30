<?php
/**
 * Custom functions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
/********************* Set Default Value if not set ***********************************/
	if ( !get_theme_mod('shoppingcart_theme_options') ) {
		set_theme_mod( 'shoppingcart_theme_options', shoppingcart_get_option_defaults_values() );
	}
/********************* SHOPPINGCART RESPONSIVE AND CUSTOM CSS OPTIONS ***********************************/
function shoppingcart_responsiveness() {
	$shoppingcart_settings = shoppingcart_get_theme_options();
	if( $shoppingcart_settings['shoppingcart_responsive'] == 'on' ) { ?>
	<meta name="viewport" content="width=device-width" />
	<?php } else { ?>
	<meta name="viewport" content="width=1170" />
	<?php  }
}
add_filter( 'wp_head', 'shoppingcart_responsiveness');

/******************************** EXCERPT LENGTH *********************************/
function shoppingcart_excerpt_length($shoppingcart_excerpt_length) {
	$shoppingcart_settings = shoppingcart_get_theme_options();
	if( is_admin() ){
		return absint($shoppingcart_excerpt_length);
	}

	$shoppingcart_excerpt_length = $shoppingcart_settings['shoppingcart_excerpt_length'];
	return absint($shoppingcart_excerpt_length);
}
add_filter('excerpt_length', 'shoppingcart_excerpt_length');

/********************* CONTINUE READING LINKS FOR EXCERPT *********************************/
function shoppingcart_continue_reading($more) {
	$shoppingcart_settings = shoppingcart_get_theme_options();
	$shoppingcart_tag_text = $shoppingcart_settings['shoppingcart_tag_text'];
	$link = sprintf(
			'<a href="%1$s" class="more-link">%2$s</a>',
			esc_url( get_permalink( get_the_ID() ) ),esc_html($shoppingcart_tag_text),
			/* translators: %s: Name of current post */
			sprintf( __( '<span class="screen-reader-text"> "%s"</span>', 'shoppingcart' ), get_the_title( get_the_ID() ) )
		);

	if( is_admin() ){
		return $more;
	}

	return '&hellip; ';
}
add_filter('excerpt_more', 'shoppingcart_continue_reading');

/***************** USED CLASS FOR BODY TAGS ******************************/
function shoppingcart_body_class($shoppingcart_class) {
	$shoppingcart_settings = shoppingcart_get_theme_options();
	$shoppingcart_site_layout = $shoppingcart_settings['shoppingcart_design_layout'];
	if ($shoppingcart_site_layout =='boxed-layout') {
		$shoppingcart_class[] = 'boxed-layout';
	}elseif ($shoppingcart_site_layout =='small-boxed-layout') {
		$shoppingcart_class[] = 'boxed-layout-small';
	}else{
		$shoppingcart_class[] = '';
	}

	if ( is_singular() && false !== strpos( get_queried_object()->post_content, '<!-- wp:' ) ) {
		$shoppingcart_class[] = 'gutenberg';
	}

	if (is_page_template('page-templates/shoppingcart-template.php')){
		$shoppingcart_class[] = 'shoppingcart-template';

	}

	return $shoppingcart_class;
}
add_filter('body_class', 'shoppingcart_body_class');

/********************** SCRIPTS FOR DONATE/ UPGRADE BUTTON ******************************/
function shoppingcart_customize_scripts() {
	wp_enqueue_style( 'shoppingcart_customizer_custom', get_template_directory_uri() . '/inc/css/shoppingcart-customizer.css');
}
add_action( 'customize_controls_print_scripts', 'shoppingcart_customize_scripts');

/**************************** SOCIAL MENU *********************************************/
function shoppingcart_social_links_display() {
		if ( has_nav_menu( 'social-link' ) ) : ?>
	<div class="social-links clearfix">
	<?php
		wp_nav_menu( array(
			'container' 	=> '',
			'theme_location' => 'social-link',
			'depth'          => 1,
			'items_wrap'      => '<ul>%3$s</ul>',
			'link_before'    => '<span class="screen-reader-text">',
			'link_after'     => '</span>',
		) );
	?>
	</div><!-- end .social-links -->
	<?php endif; ?>
<?php }
add_action ('shoppingcart_social_links', 'shoppingcart_social_links_display');

/******************* DISPLAY BREADCRUMBS ******************************/

function shoppingcart_breadcrumb() {
	if (function_exists('bcn_display')) { 
		?>
		<div class="breadcrumb home">
			<?php bcn_display(); ?>
		</div> <!-- .breadcrumb -->
	<?php }
}

/*************************** ENQUEING STYLES AND SCRIPTS ****************************************/
function shoppingcart_scripts() {
	$shoppingcart_settings = shoppingcart_get_theme_options();
	$shoppingcart_stick_menu = $shoppingcart_settings['shoppingcart_stick_menu'];
	wp_enqueue_script('shoppingcart-main', get_template_directory_uri().'/js/shoppingcart-main.js', array('jquery'), false, true);
	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_template_directory_uri() . '/js/html5.js', array(), '3.7.3' );
	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	wp_enqueue_style( 'shoppingcart-style', get_stylesheet_uri() );
	wp_enqueue_style('font-awesome', get_template_directory_uri().'/assets/font-awesome/css/font-awesome.min.css');

	if( $shoppingcart_stick_menu != 1 ):

		wp_enqueue_script('jquery-sticky', get_template_directory_uri().'/assets/sticky/jquery.sticky.min.js', array('jquery'), false, true);
		wp_enqueue_script('shoppingcart-sticky-settings', get_template_directory_uri().'/assets/sticky/sticky-settings.js', array('jquery'), false, true);

	endif;

	wp_enqueue_script('shoppingcart-navigation', get_template_directory_uri().'/js/navigation.js', array('jquery'), false, true);
	wp_enqueue_script('jquery-flexslider', get_template_directory_uri().'/js/jquery.flexslider-min.js', array('jquery'), false, true);
	wp_enqueue_script('shoppingcart-slider', get_template_directory_uri().'/js/flexslider-setting.js', array('jquery-flexslider'), false, true);

	wp_enqueue_script('shoppingcart-skip-link-focus-fix', get_template_directory_uri().'/js/skip-link-focus-fix.js', array('jquery'), false, true);

	$shoppingcart_animation_effect   = esc_attr($shoppingcart_settings['shoppingcart_animation_effect']);
	$shoppingcart_slideshowSpeed    = absint($shoppingcart_settings['shoppingcart_slideshowSpeed'])*1000;
	$shoppingcart_animationSpeed = absint($shoppingcart_settings['shoppingcart_animationSpeed'])*100;
	wp_localize_script(
		'shoppingcart-slider',
		'shoppingcart_slider_value',
		array(
			'shoppingcart_animation_effect'   => $shoppingcart_animation_effect,
			'shoppingcart_slideshowSpeed'    => $shoppingcart_slideshowSpeed,
			'shoppingcart_animationSpeed' => $shoppingcart_animationSpeed,
		)
	);
	wp_enqueue_script( 'shoppingcart-slider' );
	if( $shoppingcart_settings['shoppingcart_responsive'] == 'on' ) {
		wp_enqueue_style('shoppingcart-responsive', get_template_directory_uri().'/css/responsive.css');
	}
	$shoppingcart_googlefont = array();
	array_push( $shoppingcart_googlefont, 'Roboto');
	$shoppingcart_googlefonts = implode("|", $shoppingcart_googlefont);

	wp_register_style( 'shoppingcart-google-fonts', '//fonts.googleapis.com/css?family='.$shoppingcart_googlefonts .':300,400,400i,500,600,700');
	wp_enqueue_style( 'shoppingcart-google-fonts' );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( function_exists( 'YITH_WCWL' ) ) {

		wp_enqueue_script( 'shoppingcart-yith-wcwl-custom', get_stylesheet_directory_uri() . '/js/yith-wcwl-custom.js', array( 'jquery' ), true, false );
	}


	/* Custom Css */

	$shoppingcart_theme_color_styles = get_theme_mod( 'theme_color_styles', '#f77426' );
	$shoppingcart_internal_css='';

	if($shoppingcart_settings['shoppingcart_header_display']=='header_logo'){
		$shoppingcart_internal_css .= '
		#site-branding #site-title, #site-branding #site-description{
			clip: rect(1px, 1px, 1px, 1px);
			position: absolute;
		}';
	}


	/* Theme Color Styles */
	if($shoppingcart_theme_color_styles !='#f77426'){
		$shoppingcart_internal_css .= '	/* Nav, links and hover */

		a,
		#site-title a,
		ul li a:hover,
		ol li a:hover,
		.main-navigation a:hover, /* Navigation */
		.main-navigation ul li.current-menu-item a,
		.main-navigation ul li.current_page_ancestor a,
		.main-navigation ul li.current-menu-ancestor a,
		.main-navigation ul li.current_page_item a,
		.main-navigation ul li:hover > a,
		.main-navigation li.current-menu-ancestor.menu-item-has-children > a:after,
		.main-navigation li.current-menu-item.menu-item-has-children > a:after,
		.main-navigation ul li:hover > a:after,
		.main-navigation li.menu-item-has-children > a:hover:after,
		.main-navigation li.page_item_has_children > a:hover:after,
		.main-navigation ul li ul li a:hover,
		.main-navigation ul li ul li:hover > a,
		.main-navigation ul li.current-menu-item ul li a:hover,
		.side-menu-wrap .side-nav-wrap a:hover, /* Side Menu */
		.top-bar .top-bar-menu a:hover,
		.entry-title a:hover, /* Post */--
		.entry-title a:focus,
		.entry-title a:active,
		.entry-meta a:hover,
		.image-navigation .nav-links a,
		.widget ul li a:hover, /* Widgets */
		.widget-title a:hover,
		.widget_contact ul li a:hover,
		.site-info .copyright a:hover, /* Footer */
		#colophon .widget ul li a:hover,
		.gutenberg .entry-meta .author a {
			color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		.main-navigation ul li ul,
		#search-box input[type="search"] {
			border-color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		#search-box .woocommerce-product-search button[type="submit"] {
			background-color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		/* Webkit */
		::selection {
			background: '. esc_attr( $shoppingcart_theme_color_styles ).';
			color: #fff;
		}

		/* Gecko/Mozilla */
		::-moz-selection {
			background: '. esc_attr( $shoppingcart_theme_color_styles ).';
			color: #fff;
		}

		/* Accessibility
		================================================== */
		.screen-reader-text:hover,
		.screen-reader-text:active,
		.screen-reader-text:focus {
			background-color: #f1f1f1;
			color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		/* Default Buttons
		================================================== */
		input[type="reset"],/* Forms  */
		input[type="button"],
		input[type="submit"],
		.btn-default,
		.main-slider .flex-control-nav a.flex-active,
		.main-slider .flex-control-nav a:hover,
		.go-to-top .icon-bg,
		.search-submit,
		.vivid-red {
			background-color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		/* #bbpress
		================================================== */
		#bbpress-forums .bbp-topics a:hover {
			color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		.bbp-submit-wrapper button.submit {
			background-color: '. esc_attr( $shoppingcart_theme_color_styles ).';
			border: 1px solid '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		/* Woocommerce
		================================================== */
		.woocommerce #respond input#submit,
		.woocommerce a.button, 
		.woocommerce button.button, 
		.woocommerce input.button,
		.woocommerce #respond input#submit.alt,
		.woocommerce a.button.alt, 
		.woocommerce button.button.alt, 
		.woocommerce input.button.alt,
		.woocommerce span.onsale,
		.woocommerce-demo-store p.demo_store,
		.wl-counter,
		.header-right .cart-value,
		.archive.woocommerce span.onsale:before,
		.woocommerce ul.products li.product .button:hover,
		.woocommerce .woocommerce-product-search button[type="submit"],
		.woocommerce button.button.alt.disabled, 
		.woocommerce button.button.alt.disabled:hover {
			background-color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		.woocommerce .woocommerce-message:before,
		.woocommerce ul.products li.product .price ins,
		.product_list_widget ins,
		.price_slider_amount .price_label,
		.woocommerce div.product .out-of-stock {
			color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		.woocommerce ul.products li.product .button:hover,
		.woocommerce div.product .woocommerce-tabs ul.tabs li.active {
			border-color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		/* Catalog Menu
		================================================== */
		.catalog-slider-promotion-wrap .catalog-menu .title-highlight > a:after,
		.catalog-menu > ul > li:after {
			background-color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		.catalog-menu a:hover {
			color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		/* ShoppingCart Widgets
		================================================== */

		.shoppingcart-grid-product .product-item-action .button:hover,
		.shoppingcart-grid-product .product-item-action .product_add_to_wishlist:hover,
		.sc-grid-product-img .onsale:before {
			background-color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}

		.woocommerce-Price-amount.amount {
			color: '. esc_attr( $shoppingcart_theme_color_styles ).';
		}';

	}

	wp_add_inline_style( 'shoppingcart-style', wp_strip_all_tags($shoppingcart_internal_css) );
}
add_action( 'wp_enqueue_scripts', 'shoppingcart_scripts' );