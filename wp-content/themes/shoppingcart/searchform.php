<?php
/**
 * Displays the searchform
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
?>
<form class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
	<?php
		$shoppingcart_settings = shoppingcart_get_theme_options();
		$shoppingcart_search_form = $shoppingcart_settings['shoppingcart_search_text']; ?>
	<label class="screen-reader-text"><?php echo esc_html($shoppingcart_search_form); ?></label>
	<input type="search" name="s" class="search-field" placeholder="<?php echo esc_attr($shoppingcart_search_form); ?>" autocomplete="off" />
	<button type="submit" class="search-submit"><i class="fa fa-search"></i></button>
</form> <!-- end .search-form -->