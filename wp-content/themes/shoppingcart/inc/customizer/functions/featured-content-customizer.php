<?php
/**
 * Theme Customizer Functions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
$shoppingcart_categories_lists = shoppingcart_categories_lists();

/******************** SHOPPINGCART SLIDER SETTINGS ******************************************/
$shoppingcart_settings = shoppingcart_get_theme_options();
$wp_customize->add_section( 'featured_content', array(
	'title' => __( 'Slider Settings', 'shoppingcart' ),
	'priority' => 140,
	'panel' => 'shoppingcart_featuredcontent_panel'
));

$wp_customize->add_section( 'product_promotion', array(
	'title' => __( 'Product Promotion', 'shoppingcart' ),
	'priority' => 150,
	'panel' => 'shoppingcart_featuredcontent_panel'
));


/* WooCommerce Slider Category Section */
$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_enable_slider]', array(
	'default' => $shoppingcart_settings['shoppingcart_enable_slider'],
	'sanitize_callback' => 'shoppingcart_sanitize_select',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_enable_slider]', array(
	'priority'=>5,
	'label' => __('Enable Slider/ Promotions', 'shoppingcart'),
	'description' => __('This section includes Catalog Menu, Slider and Product Promotion', 'shoppingcart'),
	'section' => 'featured_content',
	'type' => 'select',
	'choices' => array(
		'frontpage' => __('Front Page','shoppingcart'),
		'enitresite' => __('Entire Site','shoppingcart'),
		'disable' => __('Disable Slider Promotions','shoppingcart'),
	),
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_default_category]', array(
	'default' => $shoppingcart_settings['shoppingcart_default_category'],
	'sanitize_callback' => 'shoppingcart_sanitize_select',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_default_category]', array(
	'priority'=>10,
	'label' => __('Category/ Product Category Slider', 'shoppingcart'),
	'description' => __('You need to enable WooCommerce Plugins to display Products on Slider','shoppingcart'),
	'section' => 'featured_content',
	'type' => 'select',
	'choices' => array(
		'post_category' => __('Default Category','shoppingcart'),
		'product_category' => __('Product Category','shoppingcart'),
	),
));



if(class_exists( 'woocommerce' )) {
	$shoppingcart_prod_categories_lists = shoppingcart_product_categories_lists();

		$wp_customize->add_setting(
			'shoppingcart_theme_options[shoppingcart_category_slider]', array(
				'default'				=>array(),
				'capability'			=> 'manage_options',
				'sanitize_callback'	=> 'shoppingcart_sanitize_category_select',
				'type'				=> 'option'
			)
		);
		$wp_customize->add_control(
			'shoppingcart_theme_options[shoppingcart_category_slider]',
			array(
				'priority'    => 20,
				'label'       => __( 'Select Products Category Slider', 'shoppingcart' ),
				'description' => __('Slider Recommended image size is ( 1500 X 850 )','shoppingcart'),
				'section'     => 'featured_content',
				'settings'				=> 'shoppingcart_theme_options[shoppingcart_category_slider]',
				'choices'     => $shoppingcart_prod_categories_lists,
				'type'        => 'select',
				'active_callback' => 'shoppingcart_product_category_callback',
			)
		);
}

		$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_default_category_slider]', array(
				'default'				=>$shoppingcart_settings['shoppingcart_default_category_slider'],
				'capability'			=> 'manage_options',
				'sanitize_callback'	=> 'shoppingcart_sanitize_category_select',
				'type'				=> 'option'
			));
		$wp_customize->add_control(
			
			'shoppingcart_theme_options[shoppingcart_default_category_slider]',
				array(
					'priority' 				=> 10,
					'label'					=> __('Select Post Category Slider','shoppingcart'),
					'description'					=> __('By default no slider is displayed. Slider Recommended image size is ( 1500 X 850 )','shoppingcart'),
					'section'				=> 'featured_content',
					'settings'				=> 'shoppingcart_theme_options[shoppingcart_default_category_slider]',
					'type'					=>'select',
					'active_callback' => 'shoppingcart_post_category_callback',
					'choices'	=>  $shoppingcart_categories_lists 
			)
		);

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_animation_effect]', array(
	'default' => $shoppingcart_settings['shoppingcart_animation_effect'],
	'sanitize_callback' => 'shoppingcart_sanitize_select',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_animation_effect]', array(
	'priority'=>30,
	'label' => __('Animation Effect', 'shoppingcart'),
	'section' => 'featured_content',
	'type' => 'select',
	'choices' => array(
		'slide' => __('Slide','shoppingcart'),
		'fade' => __('Fade','shoppingcart'),
	),
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_slideshowSpeed]', array(
	'default' => $shoppingcart_settings['shoppingcart_slideshowSpeed'],
	'sanitize_callback' => 'shoppingcart_numeric_value',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_slideshowSpeed]', array(
	'priority'=>40,
	'label' => __('Set the speed of the slideshow cycling', 'shoppingcart'),
	'section' => 'featured_content',
	'type' => 'text',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_animationSpeed]', array(
	'default' => $shoppingcart_settings['shoppingcart_animationSpeed'],
	'sanitize_callback' => 'shoppingcart_numeric_value',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_animationSpeed]', array(
	'priority'=>50,
	'label' => __(' Set the speed of animations', 'shoppingcart'),
	'description' => __('This feature will not work on Animation Effect set to fade','shoppingcart'),
	'section' => 'featured_content',
	'type' => 'text',
));

/********************** Product Promotion Image ***********************************/
for ( $i=1; $i <= 3; $i++ ) {
	$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_img-product-promotion-image-'.$i.']',array(
		'default'	=> $shoppingcart_settings['shoppingcart_img-product-promotion-image-'.$i],
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'esc_url_raw',
		'type' => 'option',
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'shoppingcart_theme_options[shoppingcart_img-product-promotion-image-'.$i.']', array(
		'label' => __('Product Promotion #','shoppingcart') .$i,
		'priority'=>10 .$i,
		'description' => __('Recommended Image size ( 450 X 250 )','shoppingcart'),
		'section' => 'product_promotion',
		)
	));

	$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_product_promotion_url_'.$i.']', array(
		'default' => $shoppingcart_settings['shoppingcart_product_promotion_url_'.$i],
		'sanitize_callback' => 'esc_url_raw',
		'type' => 'option',
	));
	$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_product_promotion_url_'.$i.']', array(
		'priority'=>10 .$i,
		'label' => __(' Enter Product Url #', 'shoppingcart')  .$i,
		'section' => 'product_promotion',
		'type' => 'text',
	));
}


	