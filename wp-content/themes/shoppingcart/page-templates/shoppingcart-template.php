<?php
/**
 * Template Name: ShoppingCart Template
 *
 * Displays the contact page template.
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
$shoppingcart_settings = shoppingcart_get_theme_options();

get_header(); ?>

<div class="product-widget-box">
	<div class="product-widget-wrap">
		<div class="wrap">
		<?php 
		if (is_active_sidebar('shoppingcart_template')):

			dynamic_sidebar('shoppingcart_template');

		endif;

		if ( have_posts() ) {
			the_post();

			the_content (); 
			
		}  ?>
		</div> <!-- end .wrap -->
	</div> <!-- end .shoppingcart-grid-widget-wrap -->
</div> <!-- end .product-widget-box -->

<?php

if(class_exists('woocommerce')){

	if($shoppingcart_settings['shoppingcart_display_featured_brand'] =='below-widget') {
		do_action('shoppingcart_display_front_page_product_brand'); // Display just before footer column
	}

}

if( is_active_sidebar( 'shoppingcart_template_footer_col_1' ) || is_active_sidebar( 'shoppingcart_template_footer_col_2' ) || is_active_sidebar( 'shoppingcart_template_footer_col_3' ) || is_active_sidebar( 'shoppingcart_template_footer_col_4' )) { ?>

	<div class="shoppingcart-template-footer-column">
		<div class="wrap">
			<div class="sc-template-footer-wrap">

				<?php
					for($i =1; $i<= 4; $i++){
						if ( is_active_sidebar( 'shoppingcart_template_footer_col_'.$i ) ) : ?>
							<div class="sc-footer-column">

								<?php dynamic_sidebar( 'shoppingcart_template_footer_col_'.$i ); ?>

							</div>

						<?php endif;
					}
				?>
			</div> <!-- end .sc-template-footer-wrap -->
		</div> <!-- end .wrap -->
	</div> <!-- end .shoppingcart-template-footer-column -->
<?php }
get_footer();