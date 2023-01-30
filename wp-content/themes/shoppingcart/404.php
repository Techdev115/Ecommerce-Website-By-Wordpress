<?php
/**
 * The template for displaying 404 pages
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
get_header(); ?>
<div class="wrap">
	<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
			<?php if ( is_active_sidebar( 'shoppingcart_404_page' ) ) :
				dynamic_sidebar( 'shoppingcart_404_page' );
			else:?>
			<section class="error-404 not-found">
				<header class="page-header">
					<h2 class="page-title"> <?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'shoppingcart' ); ?> </h2>
				</header> <!-- .page-header -->
				<div class="page-content">
					<p> <?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'shoppingcart' ); ?> </p>
						<?php get_search_form(); ?>
				</div> <!-- .page-content -->
			</section> <!-- .error-404 -->
		<?php endif; ?>
	</main><!-- end #main -->
	</div> <!-- #primary -->
<?php
get_sidebar();
?>
</div><!-- end .wrap -->
<?php
get_footer();