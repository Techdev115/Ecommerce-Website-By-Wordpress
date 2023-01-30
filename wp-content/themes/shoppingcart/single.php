<?php
/**
 * The template for displaying all single posts.
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
get_header();
$shoppingcart_settings = shoppingcart_get_theme_options();
$shoppingcart_display_page_single_featured_image = $shoppingcart_settings['shoppingcart_display_page_single_featured_image']; ?>
<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php global $shoppingcart_settings;
			while( have_posts() ) {
				the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class();?>>
					<?php if(has_post_thumbnail() && $shoppingcart_display_page_single_featured_image == 0 ){ ?>
						<div class="post-image-content">
							<figure class="post-featured-image">
								<?php the_post_thumbnail(); ?>
							</figure>
						</div><!-- end.post-image-content -->
					<?php } ?>
					<div class="post-all-content">
						<?php $shoppingcart_entry_meta_single = $shoppingcart_settings['shoppingcart_entry_meta_single']; ?>
						<header class="entry-header">
							<?php if($shoppingcart_entry_meta_single=='show'){
								if ( $shoppingcart_settings['shoppingcart_post_date'] != 1){ ?>
									<div class="entry-meta">
										<?php printf( '<span class="posted-on"><a href="%1$s" title="%2$s"><i class="fa fa-calendar-check-o"></i>%3$s</a></span>',
											esc_url(get_the_permalink()),
											esc_attr( get_the_time() ),
											esc_html(get_the_time( get_option( 'date_format' ) ))
										); ?>
									</div>
								<?php }
								} ?>
								<h1 class="entry-title"><?php the_title();?></h1> <!-- end.entry-title -->
								<?php shoppingcart_breadcrumb();
								if($shoppingcart_entry_meta_single=='show'){ ?>
								<div class="entry-meta">
									<?php if ( $shoppingcart_settings['shoppingcart_post_author'] != 1){ ?>
									<span class="author vcard"><?php esc_html_e('Post By','shoppingcart');?><a href="<?php echo esc_url ( get_author_posts_url( get_the_author_meta( 'ID' )) ); ?>" title="<?php the_title_attribute(); ?>">
									<?php the_author(); ?> </a></span>
									<?php  }
									$format = get_post_format();
									if ( current_theme_supports( 'post-formats', $format ) ) {
											printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
												sprintf( ''),
												esc_url( get_post_format_link( $format ) ),
												esc_html(get_post_format_string( $format ))
											);
										} 
										if ( is_singular( 'post' ) ) { 
											if ( $shoppingcart_settings['shoppingcart_post_category'] != 1){ ?>
											<span class="cat-links">
												<?php the_category(', '); ?>
											</span> <!-- end .cat-links -->
											<?php }
											 $tag_list = get_the_tag_list( '', __( ', ', 'shoppingcart' ) );
											if(!empty($tag_list)){ ?>
												<span class="tag-links">
												<?php   echo get_the_tag_list( '', __( ', ', 'shoppingcart' ) ); ?>
												</span> <!-- end .tag-links -->
											<?php }
										}else{ ?>
										<nav id="image-navigation" class="navigation image-navigation">
											<div class="nav-links">
												<div class="nav-previous"><?php previous_image_link( false, __( 'Previous Image', 'shoppingcart' ) ); ?></div>
												<div class="nav-next"><?php next_image_link( false, __( 'Next Image', 'shoppingcart' ) ); ?></div>
											</div><!-- .nav-links -->
										</nav><!-- .image-navigation -->
									<?php	} ?>
									<?php if ( comments_open() ) {
										if ( $shoppingcart_settings['shoppingcart_post_comments'] != 1){ ?>
											<span class="comments"><i class="fa fa-comments-o"></i>
											<?php comments_popup_link( __( 'No Comments', 'shoppingcart' ), __( '1 Comment', 'shoppingcart' ), __( '% Comments', 'shoppingcart' ), '', __( 'Comments Off', 'shoppingcart' ) ); ?> </span>
										<?php }
									} ?>
								</div><!-- end .entry-meta -->
							<?php } ?>
						</header>
						<!-- end .entry-header -->
						<div class="entry-content">
								<?php the_content(); ?>			
						</div><!-- end .entry-content -->
					</div> <!-- end .post-all-content -->
				</article><!-- end .post -->
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				if ( is_singular( 'attachment' ) ) {
					// Parent post navigation.
					the_post_navigation( array(
								'prev_text' => _x( '<span class="meta-nav">Published in</span><span class="post-title">%title</span>', 'Parent post link', 'shoppingcart' ),
							) );
				} elseif ( is_singular( 'post' ) ) {
				the_post_navigation( array(
						'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'shoppingcart' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Next post:', 'shoppingcart' ) . '</span> ' .
							'<span class="post-title">%title</span>',
						'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'shoppingcart' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Previous post:', 'shoppingcart' ) . '</span> ' .
							'<span class="post-title">%title</span>',
					) );
				}
			} ?>
		</main><!-- end #main -->
	</div> <!-- #primary -->
<?php
get_sidebar();
?>
</div><!-- end .wrap -->
<?php
get_footer();