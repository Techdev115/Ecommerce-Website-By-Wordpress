<?php
/**
 * The template for displaying search results.
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
get_header(); ?>
<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
			if( have_posts() ) { ?>
			<header class="page-header">
				<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'shoppingcart' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1>
			</header><!-- .page-header -->
				<?php while( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'excerpt' );
				}
			} else { ?>
			<h2 class="entry-title">
				<?php get_search_form(); ?>
				<p>&nbsp; </p>
				<?php esc_html_e( 'No Posts Found.', 'shoppingcart' ); ?>
			</h2>
			<?php }
			get_template_part( 'pagination', 'none' ); ?>
		</main><!-- end #main -->
	</div> <!-- #primary -->
<?php
get_sidebar();
?>
</div><!-- end .wrap -->
<?php
get_footer();