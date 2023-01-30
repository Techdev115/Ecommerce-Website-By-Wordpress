<?php
/**
 * Display all shoppingcart functions and definitions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */

/************************************************************************************************/
if ( ! function_exists( 'shoppingcart_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function shoppingcart_setup() {
	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
	global $content_width;
	if ( ! isset( $content_width ) ) {
			$content_width=1170;
	}

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	add_theme_support('post-thumbnails');
	add_image_size( 'shoppingcart-product-cat-image', 512, 512, true );
	add_image_size( 'shoppingcart-featured-brand-image', 400, 200, true );
	add_image_size( 'shoppingcart-grid-product-image', 420, 420, true );
	add_image_size( 'shoppingcart-popular-post', 75, 75, true );

	/*
	 * Let WordPress manage the document title.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	register_nav_menus( array(
		'top-menu' => __( 'Top Menu', 'shoppingcart' ),
		'primary' => __( 'Main Menu', 'shoppingcart' ),
		'catalog-menu' => __( 'Catalog Menu', 'shoppingcart' ),
		'social-link'  => __( 'Add Social Icons Only', 'shoppingcart' ),
	) );

	/* 
	* Enable support for custom logo. 
	*
	*/ 
	add_theme_support( 'custom-logo', array(
		'flex-width' => true, 
		'flex-height' => true,
	) );

	add_theme_support( 'gutenberg', array(
			'colors' => array(
				'#f77426',
			),
		) );
	add_theme_support( 'align-wide' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	//Indicate widget sidebars can use selective refresh in the Customizer. 
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * Switch default core markup for comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form', 'comment-list', 'gallery', 'caption',
	) );



	/**
	 * Add support for the Aside Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio', 'chat' ) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'shoppingcart_custom_background_args', array(
		'default-color' => '#ffffff',
		'default-image' => '',
	) ) );

	add_editor_style( array( 'css/editor-style.css') );

	if ( class_exists( 'WooCommerce' ) ) {

		/**
		 * Load WooCommerce compatibility files.
		 */
			
		require get_template_directory() . '/woocommerce/functions.php';

	}


}
endif; // shoppingcart_setup
add_action( 'after_setup_theme', 'shoppingcart_setup' );

/***************************************************************************************/
function shoppingcart_content_width() {
	if ( is_page_template( 'page-templates/gallery-template.php' ) || is_attachment() ) {
		global $content_width;
		$content_width = 1920;
	}
}
add_action( 'template_redirect', 'shoppingcart_content_width' );

/***************************************************************************************/
if(!function_exists('shoppingcart_get_theme_options')):
	function shoppingcart_get_theme_options() {
	    return wp_parse_args(  get_option( 'shoppingcart_theme_options', array() ), shoppingcart_get_option_defaults_values() );
	}
endif;

/***************************************************************************************/
require get_template_directory() . '/inc/customizer/shoppingcart-default-values.php';
require get_template_directory() . '/inc/settings/shoppingcart-slider-functions.php';
require get_template_directory() . '/inc/settings/shoppingcart-functions.php';
require get_template_directory() . '/inc/settings/shoppingcart-common-functions.php';

/************************ ShoppingCart Sidebar  *****************************/
require get_template_directory() . '/inc/widgets/widgets-functions/register-widgets.php';
require get_template_directory() . '/inc/widgets/widgets-functions/popular-posts.php';

if ( class_exists('woocommerce')) {
	require get_template_directory() . '/inc/widgets/widgets-functions/grid-column-widget.php';
}

/************************ ShoppingCart Customizer  *****************************/
require get_template_directory() . '/inc/customizer/functions/sanitize-functions.php';
require get_template_directory() . '/inc/customizer/functions/register-panel.php';

function shoppingcart_customize_register( $wp_customize ) {
if(!class_exists('ShoppingCart_Plus_Features')){
	class ShoppingCart_Customize_upgrade extends WP_Customize_Control {
		public function render_content() { ?>
			<a title="<?php esc_attr_e( 'Review Us', 'shoppingcart' ); ?>" href="<?php echo esc_url( 'https://wordpress.org/support/view/theme-reviews/shoppingcart/' ); ?>" target="_blank" id="about_shoppingcart">
			<?php esc_html_e( 'Review Us', 'shoppingcart' ); ?>
			</a><br/>
			<a href="<?php echo esc_url( 'https://themefreesia.com/theme-instruction/shoppingcart/' ); ?>" title="<?php esc_attr_e( 'Theme Instructions', 'shoppingcart' ); ?>" target="_blank" id="about_shoppingcart">
			<?php esc_html_e( 'Theme Instructions', 'shoppingcart' ); ?>
			</a><br/>
			<a href="<?php echo esc_url( 'https://tickets.themefreesia.com/' ); ?>" title="<?php esc_attr_e( 'Support Tickets', 'shoppingcart' ); ?>" target="_blank" id="about_shoppingcart">
			<?php esc_html_e( 'Support Tickets', 'shoppingcart' ); ?>
			</a><br/>
		<?php
		}
	}
	$wp_customize->add_section('shoppingcart_upgrade_links', array(
		'title'					=> __('Important Links', 'shoppingcart'),
		'priority'				=> 1000,
	));
	$wp_customize->add_setting( 'shoppingcart_upgrade_links', array(
		'default'				=> false,
		'capability'			=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new ShoppingCart_Customize_upgrade(
		$wp_customize,
		'shoppingcart_upgrade_links',
			array(
				'section'				=> 'shoppingcart_upgrade_links',
				'settings'				=> 'shoppingcart_upgrade_links',
			)
		)
	);
}	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector' => '.site-title a',
			'container_inclusive' => false,
			'render_callback' => 'shoppingcart_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector' => '.site-description',
			'container_inclusive' => false,
			'render_callback' => 'shoppingcart_customize_partial_blogdescription',
		) );
	}
	
	require get_template_directory() . '/inc/customizer/functions/design-options.php';
	require get_template_directory() . '/inc/customizer/functions/theme-options.php';
	require get_template_directory() . '/inc/customizer/functions/color-options.php' ;
	require get_template_directory() . '/inc/customizer/functions/featured-content-customizer.php' ;
	if ( class_exists( 'WooCommerce' ) ) {

		require get_template_directory() . '/inc/customizer/functions/frontpage-features.php' ;

	}
}
if(!class_exists('ShoppingCart_Plus_Features')){
	// Add Upgrade to Plus Button.
	require_once( trailingslashit( get_template_directory() ) . 'inc/upgrade-plus/class-customize.php' );

	/************************ TGM Plugin Activatyion  *****************************/
	require get_template_directory() . '/inc/tgm/tgm.php';
}

/** 
* Render the site title for the selective refresh partial. 
* @see shoppingcart_customize_register() 
* @return void 
*/ 
function shoppingcart_customize_partial_blogname() { 
bloginfo( 'name' ); 
} 

/** 
* Render the site tagline for the selective refresh partial. 
* @see shoppingcart_customize_register() 
* @return void 
*/ 
function shoppingcart_customize_partial_blogdescription() { 
bloginfo( 'description' ); 
}
add_action( 'customize_register', 'shoppingcart_customize_register' );
/******************* ShoppingCart Header Display *************************/
function shoppingcart_header_display(){
	$shoppingcart_settings = shoppingcart_get_theme_options();

$header_display = $shoppingcart_settings['shoppingcart_header_display'];

		if ($header_display == 'header_logo' || $header_display == 'show_both') {
			shoppingcart_the_custom_logo();
		}
		if ($header_display == 'header_text' || $header_display == 'show_both') {
			echo '<div id="site-detail">';
				if (is_home() || is_front_page()){ ?>
					<h1 id="site-title"> <?php }else{?> <h2 id="site-title"> <?php } ?>
					<a href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_html(get_bloginfo('name', 'display'));?>" rel="home"> <?php bloginfo('name');?> </a>
					<?php if(is_home() || is_front_page()){ ?>
					</h1>  <!-- end .site-title -->
					<?php } else { ?> </h2> <!-- end .site-title --> <?php }

					$site_description = get_bloginfo( 'description', 'display' );
					if ($site_description){?>
						<div id="site-description"> <?php bloginfo('description');?> </div> <!-- end #site-description -->
				<?php }
			echo '</div>'; // end #site-detail
		}

}
add_action('shoppingcart_site_branding','shoppingcart_header_display');

if ( ! function_exists( 'shoppingcart_the_custom_logo' ) ) : 
 	/** 
 	 * Displays the optional custom logo. 
 	 * Does nothing if the custom logo is not available. 
 	 */ 
 	function shoppingcart_the_custom_logo() { 
		if ( function_exists( 'the_custom_logo' ) ) { 
			the_custom_logo(); 
		}
 	} 
endif;

require get_template_directory() . '/inc/front-page/front-page-features.php';

/************** YITH_WCWL *************************************/
if ( function_exists( 'YITH_WCWL' ) ) {
	function shoppingcart_update_wishlist_count(){
		wp_send_json( YITH_WCWL()->count_products() );
	}
	add_action( 'wp_ajax_update_wishlist_count', 'shoppingcart_update_wishlist_count' );
	add_action( 'wp_ajax_nopriv_update_wishlist_count', 'shoppingcart_update_wishlist_count' );
}


/************** Add to cart ajax autoload *************************************/
add_filter( 'woocommerce_add_to_cart_fragments', 'shoppingcart_woocommerce_add_to_cart_fragment' );

function shoppingcart_woocommerce_add_to_cart_fragment( $fragments ) {
	ob_start();
	?>
			<div class="sx-cart-views">
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="wcmenucart-contents">
					<i class="fa fa-shopping-basket"></i>
					<span class="cart-value"><?php echo wp_kses_data ( WC()->cart->get_cart_contents_count() ); ?></span>
				</a>
				<div class="my-cart-wrap">
					<div class="my-cart"><?php esc_html_e('Total', 'shoppingcart'); ?></div>
					<div class="cart-total"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></div>
				</div>
			</div>
	<?php

	$fragments['div.sx-cart-views'] = ob_get_clean();

	return $fragments;
}