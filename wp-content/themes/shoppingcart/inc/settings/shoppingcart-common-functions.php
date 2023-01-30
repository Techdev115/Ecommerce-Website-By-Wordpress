<?php
/**
 * Custom functions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
/****************** SHOPPINGCART DISPLAY COMMENT NAVIGATION *******************************/
function shoppingcart_comment_nav() {
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
	?>
	<nav class="navigation comment-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'shoppingcart' ); ?></h2>
		<div class="nav-links">
			<?php
				if ( $prev_link = get_previous_comments_link( __( 'Older Comments', 'shoppingcart' ) ) ) :
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				endif;
				if ( $next_link = get_next_comments_link( __( 'Newer Comments', 'shoppingcart' ) ) ) :
					printf( '<div class="nav-next">%s</div>', $next_link );
				endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .comment-navigation -->
	<?php
	endif;
}
/******************** Remove div and replace with ul**************************************/
add_filter('wp_page_menu', 'shoppingcart_wp_page_menu');
function shoppingcart_wp_page_menu($page_markup) {
	preg_match('/^<div class=\"([a-z0-9-_]+)\">/i', $page_markup, $matches);
	$divclass   = $matches[1];
	$replace    = array('<div class="'.$divclass.'">', '</div>');
	$new_markup = str_replace($replace, '', $page_markup);
	$new_markup = preg_replace('/^<ul>/i', '<ul class="'.$divclass.'">', $new_markup);
	return $new_markup;
}
/********************* Custom Header setup ************************************/
function shoppingcart_custom_header_setup() {
	$args = array(
		'default-text-color'     => '',
		'default-image'          => '',
		'height'                 => apply_filters( 'shoppingcart_header_image_height', 720 ),
		'width'                  => apply_filters( 'shoppingcart_header_image_width', 1280 ),
		'random-default'         => false,
		'max-width'              => 2500,
		'flex-height'            => true,
		'flex-width'             => true,
		'random-default'         => false,
		'header-text'				 => false,
		'uploads'				 => true,
		'wp-head-callback'       => '',
		'admin-preview-callback' => 'shoppingcart_admin_header_image',
	);
	add_theme_support( 'custom-header', $args );
}
add_action( 'after_setup_theme', 'shoppingcart_custom_header_setup' );


/**************** Categoy Lists ***********************/

if( !function_exists( 'shoppingcart_categories_lists' ) ):
    function shoppingcart_categories_lists() {
        $shoppingcart_cat_args = array(
            'type'       => 'post',
            'taxonomy'   => 'category',
        );
        $shoppingcart_categories = get_categories( $shoppingcart_cat_args );
        $shoppingcart_categories_lists = array('' => esc_html__('--Select--','shoppingcart'));
        foreach( $shoppingcart_categories as $category ) {
            $shoppingcart_categories_lists[esc_attr( $category->slug )] = esc_html( $category->name );
        }
        return $shoppingcart_categories_lists;
    }
endif;

/**************** Product Categoy Lists ***********************/
if( !function_exists( 'shoppingcart_product_categories_lists' ) ):

    function shoppingcart_product_categories_lists() {
		$shoppingcart_prod_categories_lists = array(
			'-' => __( '--Select Category--', 'shoppingcart' ),
		);

		$shoppingcart_prod_categories = get_categories(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => 0,
				'title_li'   => '',
			)
		);

		if ( ! empty( $shoppingcart_prod_categories ) ) :
			foreach ( $shoppingcart_prod_categories as $shoppingcart_prod_cat ) :

				if ( ! empty( $shoppingcart_prod_cat->term_id ) && ! empty( $shoppingcart_prod_cat->name ) ) :
					$shoppingcart_prod_categories_lists[ $shoppingcart_prod_cat->term_id ] = $shoppingcart_prod_cat->name;
				endif;

			endforeach;
		endif;
		return $shoppingcart_prod_categories_lists;
	}

endif;

/* Header Right WooCommerce Cart and WishList Icon */
add_action ('shoppingcart_cart_wishlist_icon_display','shoppingcart_cart_wishlist_icon');

function shoppingcart_cart_wishlist_icon(){

	if ( class_exists( 'woocommerce' ) ) { ?>
		<div class="cart-box">
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
			
			<?php the_widget( 'WC_Widget_Cart', '' ); ?>
		</div> <!-- end .cart-box -->
	<?php }

	if ( function_exists( 'YITH_WCWL' ) ) {

		$wishlist_url = YITH_WCWL()->get_wishlist_url(); ?>
		<div class="wishlist-box">
			<div class="wishlist-wrap">
				<a class="wishlist-btn" href="<?php echo esc_url( $wishlist_url ); ?>">
					<i class="fa fa-heart-o"> </i>
					<span class="wl-counter"><?php echo absint( yith_wcwl_count_products() ); ?></span>
				</a>
			</div>
		</div> <!-- end .wishlist-box -->

	<?php }

}