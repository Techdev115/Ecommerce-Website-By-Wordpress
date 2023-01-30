<?php
if(!function_exists('shoppingcart_get_option_defaults_values')):
	/******************** SHOPPINGCART DEFAULT OPTION VALUES ******************************************/
	function shoppingcart_get_option_defaults_values() {
		global $shoppingcart_default_values;
		$shoppingcart_default_values = array(
			'shoppingcart_responsive'	=> 'on',
			'shoppingcart_design_layout' => 'full-width-layout',
			'shoppingcart_post_category' => 0,
			'shoppingcart_post_author' => 0,
			'shoppingcart_post_date' => 0,
			'shoppingcart_post_comments' => 0,
			'shoppingcart_sidebar_layout_options' => 'right',
			'shoppingcart_search_custom_header' => 0,
			'shoppingcart_header_display'=> 'header_text',
			'shoppingcart_scroll'	=> 0,
			'shoppingcart_tag_text' => esc_html__('Continue Reading','shoppingcart'),
			'shoppingcart_excerpt_length'	=> '50',
			'shoppingcart_reset_all' => 0,
			'shoppingcart_stick_menu'	=>0,
			'shoppingcart_blog_post_image' => 'on',
			'shoppingcart_search_text' => esc_html__('Search &hellip;','shoppingcart'),
			'shoppingcart_blog_content_layout'	=> 'fullcontent_display',
			'shoppingcart_entry_meta_single' => 'show',
			'shoppingcart_entry_meta_blog' => 'show-meta',
			'shoppingcart_footer_column_section'	=>'4',
			'shoppingcart_disable_main_menu' => 0,
			'shoppingcart_disable_top_bar' => 0,
			'shoppingcart_img-product-promotion-image-1' => '',
			'shoppingcart_img-product-promotion-image-2' => '',
			'shoppingcart_img-product-promotion-image-3' => '',
			'shoppingcart_product_promotion_url_1' => '',
			'shoppingcart_product_promotion_url_2' => '',
			'shoppingcart_product_promotion_url_3' => '',
			'shoppingcart_product_background_color' => 'off',
			'shoppingcart_big_promo_category'	=> 'off',
			'shoppingcart_display_featured_brand'	=> 'below-widget',
			'shoppingcart_display_advertisement'	=> 'above-slider',

			/* Slider Settings */
			'shoppingcart_default_category'	=> 'post_category',
			'shoppingcart_slider_type'	=> 'default_slider',
			'shoppingcart_enable_slider' => 'disable',
			'shoppingcart_category_slider' =>array(),
			'shoppingcart_default_category_slider' => '',
			'shoppingcart_slider_number'	=> '3',

			/* Layer Slider */
			'shoppingcart_animation_effect' => 'fade',
			'shoppingcart_slideshowSpeed' => '5',
			'shoppingcart_animationSpeed' => '7',
			'shoppingcart_display_page_single_featured_image'=>0,

			/* Front page feature */
			/* Frontpage Product Featured Brands */
			'shoppingcart_disable_product_brand'	=> 1,
			'shoppingcart_total_brand_features'	=> '8',
			'shoppingcart_features_title'	=> '',
			'shoppingcart_features_description'	=> '',

			/* Frontpage Product Categories */
			'shoppingcart_disable_product_categories'	=> 1,
			'shoppingcart_total_features'	=> '5',
			'shoppingcart_categories_features_title'	=> '',
			'shoppingcart_categories_features_description'	=> '',
			/*Social Icons */
			'shoppingcart_top_social_icons' =>0,
			'shoppingcart_buttom_social_icons'	=>0,
			'shoppingcart_adv_ban_position' => 'above-slider',
			);
		return apply_filters( 'shoppingcart_get_option_defaults_values', $shoppingcart_default_values );
	}
endif;