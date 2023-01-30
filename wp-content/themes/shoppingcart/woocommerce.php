<?php
/**
 * This template to displays woocommerce page
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */

get_header();
	$shoppingcart_settings = shoppingcart_get_theme_options();
	global $shoppingcart_content_layout;
	if( $post ) {
		$layout = get_post_meta( get_queried_object_id(), 'shoppingcart_sidebarlayout', true );
	}
	if( empty( $layout ) || is_archive() || is_search() || is_home() ) {
		$layout = 'default';
	} ?>
<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php shoppingcart_breadcrumb();
			woocommerce_content(); ?>
		</main><!-- end #main -->
	</div> <!-- #primary -->
<?php 
if( 'default' == $layout ) { //Settings from customizer
	if(($shoppingcart_settings['shoppingcart_sidebar_layout_options'] != 'nosidebar') && ($shoppingcart_settings['shoppingcart_sidebar_layout_options'] != 'fullwidth')){ ?>
<aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Secondary', 'shoppingcart' ); ?>">
	<?php }
} 
	if( 'default' == $layout ) { //Settings from customizer
		if(($shoppingcart_settings['shoppingcart_sidebar_layout_options'] != 'nosidebar') && ($shoppingcart_settings['shoppingcart_sidebar_layout_options'] != 'fullwidth')): ?>
		<?php dynamic_sidebar( 'shoppingcart_woocommerce_sidebar' ); ?>
</aside><!-- end #secondary -->
<?php endif;
	}
?>
</div><!-- end .wrap -->
<?php
get_footer();