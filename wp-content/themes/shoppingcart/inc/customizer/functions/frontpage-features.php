<?php
/**
 * Theme Customizer Functions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */

/******************** SHOPPINGCART FRONTPAGE  *********************************************/
/* Frontpage ShoppingCart */
$shoppingcart_settings = shoppingcart_get_theme_options();
$shoppingcart_prod_categories_lists = shoppingcart_product_categories_lists();

$wp_customize->add_section( 'shoppingcart_product_category', array(
	'title' => __('Product Categories','shoppingcart'),
	'priority' => 10,
	'panel' =>'shoppingcart_frontpage_panel'
));

$wp_customize->add_section( 'shoppingcart_frontpage_features', array(
	'title' => __('Product Featured Brands','shoppingcart'),
	'priority' => 20,
	'panel' =>'shoppingcart_frontpage_panel'
));

/* Frontpage Product Featured Brands */
$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_disable_product_brand]', array(
	'default' => $shoppingcart_settings['shoppingcart_disable_product_brand'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_disable_product_brand]', array(
	'priority' => 5,
	'label' => __('Disable Product Brand Section', 'shoppingcart'),
	'section' => 'shoppingcart_frontpage_features',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_features_title]', array(
	'default' => $shoppingcart_settings['shoppingcart_features_title'],
	'sanitize_callback' => 'sanitize_text_field',
	'type' => 'option',
	'capability' => 'manage_options'
	)
);
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_features_title]', array(
	'priority' => 10,
	'label' => __( 'Title', 'shoppingcart' ),
	'section' => 'shoppingcart_frontpage_features',
	'type' => 'text',
	)
);

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_features_description]', array(
	'default' => $shoppingcart_settings['shoppingcart_features_description'],
	'sanitize_callback' => 'sanitize_text_field',
	'type' => 'option',
	'capability' => 'manage_options'
	)
);
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_features_description]', array(
	'priority' => 20,
	'label' => __( 'Description', 'shoppingcart' ),
	'section' => 'shoppingcart_frontpage_features',
	'type' => 'text',
	)
);

for ( $i=1; $i <= $shoppingcart_settings['shoppingcart_total_brand_features'] ; $i++ ) {
	$wp_customize->add_setting(
		'shoppingcart_theme_options[shoppingcart_featured_product_brand_'. $i .']', array(
			'default'				=>'',
			'capability'			=> 'manage_options',
			'sanitize_callback'	=> 'shoppingcart_sanitize_category_select',
			'type'				=> 'option'
		)
	);
	$wp_customize->add_control(
		'shoppingcart_theme_options[shoppingcart_featured_product_brand_'. $i .']',
		array(
			'priority' => 20 . absint($i),
			'label'       => __( 'Featured Products Brand #', 'shoppingcart' ) . $i,
			'section'     => 'shoppingcart_frontpage_features',
			'choices'     => $shoppingcart_prod_categories_lists,
			'type'        => 'select',
		)
	);
}

/* Product Categories */
$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_disable_product_categories]', array(
	'default' => $shoppingcart_settings['shoppingcart_disable_product_categories'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_disable_product_categories]', array(
	'priority' => 10,
	'label' => __('Disable Product Category Section', 'shoppingcart'),
	'section' => 'shoppingcart_product_category',
	'type' => 'checkbox',
));

 $wp_customize->add_setting('shoppingcart_theme_options[shoppingcart_product_background_color]', array(
		'default'        => $shoppingcart_settings['shoppingcart_product_background_color'],
		'sanitize_callback' => 'shoppingcart_sanitize_select',
		'type'                  => 'option',
		'capability'            => 'manage_options'
	));
	$wp_customize->add_control('shoppingcart_theme_options[shoppingcart_product_background_color]', array(
		'priority'  =>20,
		'label'      => __('Product Category Text Background Color', 'shoppingcart'),
		'section'    => 'shoppingcart_product_category',
		'type'       => 'select',
		'choices'    => array(
		'off' => __('Hide Background Color','shoppingcart'),
		'on' => __('Show Background Color','shoppingcart'),
		),
	));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_categories_features_title]', array(
	'default' => $shoppingcart_settings['shoppingcart_categories_features_title'],
	'sanitize_callback' => 'sanitize_text_field',
	'type' => 'option',
	'capability' => 'manage_options'
	)
);

$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_categories_features_title]', array(
	'priority' => 30,
	'label' => __( 'Title', 'shoppingcart' ),
	'section' => 'shoppingcart_product_category',
	'type' => 'text',
	)
);

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_categories_features_description]', array(
	'default' => $shoppingcart_settings['shoppingcart_categories_features_description'],
	'sanitize_callback' => 'sanitize_text_field',
	'type' => 'option',
	'capability' => 'manage_options'
	)
);
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_categories_features_description]', array(
	'priority' => 40,
	'label' => __( 'Description', 'shoppingcart' ),
	'section' => 'shoppingcart_product_category',
	'type' => 'text',
	)
);

for ( $i=1; $i <= $shoppingcart_settings['shoppingcart_total_features'] ; $i++ ) {
	$wp_customize->add_setting(
		'shoppingcart_theme_options[shoppingcart_featured_category_'. $i .']', array(
			'default'				=>'',
			'capability'			=> 'manage_options',
			'sanitize_callback'	=> 'shoppingcart_sanitize_category_select',
			'type'				=> 'option'
		)
	);
	$wp_customize->add_control(
		'shoppingcart_theme_options[shoppingcart_featured_category_'. $i .']',
		array(
			'priority' => 50 . absint($i),
			'label'       => __( 'Featured Products category #', 'shoppingcart' ) . $i ,
			'section'     => 'shoppingcart_product_category',
			'choices'     => $shoppingcart_prod_categories_lists,
			'type'        => 'select',
		)
	);
}