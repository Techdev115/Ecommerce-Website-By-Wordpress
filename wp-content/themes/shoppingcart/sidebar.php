<?php
/**
 * The sidebar containing the main Sidebar area.
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
	$shoppingcart_settings = shoppingcart_get_theme_options();
	global $shoppingcart_content_layout;
	if( $post ) {
		$layout = get_post_meta( get_queried_object_id(), 'shoppingcart_sidebarlayout', true );
	}
	if( empty( $layout ) || is_archive() || is_search() || is_home() ) {
		$layout = 'default';
	}

if( 'default' == $layout ) { //Settings from customizer
	if(($shoppingcart_settings['shoppingcart_sidebar_layout_options'] != 'nosidebar') && ($shoppingcart_settings['shoppingcart_sidebar_layout_options'] != 'fullwidth')){ ?>

<aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Secondary', 'shoppingcart' ); ?>">
<?php }
}else{ // for page/ post
		if(($layout != 'no-sidebar') && ($layout != 'full-width')){ ?>
<aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Secondary', 'shoppingcart' ); ?>">
  <?php }
	}?>
  <?php 
	if( 'default' == $layout ) { //Settings from customizer
		if(($shoppingcart_settings['shoppingcart_sidebar_layout_options'] != 'nosidebar') && ($shoppingcart_settings['shoppingcart_sidebar_layout_options'] != 'fullwidth')): ?>
  <?php dynamic_sidebar( 'shoppingcart_main_sidebar' ); ?>
</aside><!-- end #secondary -->
<?php endif;
	}else{ // for page/post
		if(($layout != 'no-sidebar') && ($layout != 'full-width')){
			dynamic_sidebar( 'shoppingcart_main_sidebar' );
			echo '</aside><!-- end #secondary -->';
		}
	}