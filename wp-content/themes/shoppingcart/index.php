<?php
/**
 * The main template file.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
get_header(); ?>
<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<header class="page-header">
				<h2 class="page-title"><?php single_post_title();?></h2>
				<!-- .page-title -->
				<?php shoppingcart_breadcrumb(); ?><!-- .breadcrumb -->
			</header><!-- .page-header -->
			<?php
			if( have_posts() ) {
				while(have_posts() ) {
					the_post();
					get_template_part( 'content', get_post_format() );
				}
			}
			else { ?>
			<h2 class="entry-title"> <?php esc_html_e( 'No Posts Found.', 'shoppingcart' ); ?> </h2>
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