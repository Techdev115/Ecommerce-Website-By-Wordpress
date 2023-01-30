<?php
/**
 * Theme Customizer Functions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
$shoppingcart_settings = shoppingcart_get_theme_options();

$wp_customize->add_section('shoppingcart_layout_options', array(
	'title' => __('Layout Options', 'shoppingcart'),
	'priority' => 102,
	'panel' => 'shoppingcart_options_panel'
));

$wp_customize->add_setting('shoppingcart_theme_options[shoppingcart_responsive]', array(
	'default' => $shoppingcart_settings['shoppingcart_responsive'],
	'sanitize_callback' => 'shoppingcart_sanitize_select',
	'type' => 'option',
));
$wp_customize->add_control('shoppingcart_theme_options[shoppingcart_responsive]', array(
	'priority' =>20,
	'label' => __('Responsive Layout', 'shoppingcart'),
	'section' => 'shoppingcart_layout_options',
	'type' => 'select',
	'choices' => array(
		'on' => __('ON ','shoppingcart'),
		'off' => __('OFF','shoppingcart'),
	),
));


$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_post_category]', array(
	'default' => $shoppingcart_settings['shoppingcart_post_category'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_post_category]', array(
	'priority'=>30,
	'label' => __('Disable Category', 'shoppingcart'),
	'section' => 'shoppingcart_layout_options',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_post_author]', array(
	'default' => $shoppingcart_settings['shoppingcart_post_author'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_post_author]', array(
	'priority'=>40,
	'label' => __('Disable Author', 'shoppingcart'),
	'section' => 'shoppingcart_layout_options',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_post_date]', array(
	'default' => $shoppingcart_settings['shoppingcart_post_date'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_post_date]', array(
	'priority'=>50,
	'label' => __('Disable Date', 'shoppingcart'),
	'section' => 'shoppingcart_layout_options',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_post_comments]', array(
	'default' => $shoppingcart_settings['shoppingcart_post_comments'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_post_comments]', array(
	'priority'=>60,
	'label' => __('Disable Comments', 'shoppingcart'),
	'section' => 'shoppingcart_layout_options',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_entry_meta_single]', array(
	'default' => $shoppingcart_settings['shoppingcart_entry_meta_single'],
	'sanitize_callback' => 'shoppingcart_sanitize_select',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_entry_meta_single]', array(
	'priority'=>70,
	'label' => __('Disable Entry Meta from Single Page', 'shoppingcart'),
	'section' => 'shoppingcart_layout_options',
	'type' => 'select',
	'choices' => array(
		'show' => __('Display Entry Format','shoppingcart'),
		'hide' => __('Hide Entry Format','shoppingcart'),
	),
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_entry_meta_blog]', array(
	'default' => $shoppingcart_settings['shoppingcart_entry_meta_blog'],
	'sanitize_callback' => 'shoppingcart_sanitize_select',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_entry_meta_blog]', array(
	'priority'=>80,
	'label' => __('Disable Entry Meta from Blog Page', 'shoppingcart'),
	'section' => 'shoppingcart_layout_options',
	'type'	=> 'select',
	'choices' => array(
		'show-meta' => __('Display Entry Meta','shoppingcart'),
		'hide-meta' => __('Hide Entry Meta','shoppingcart'),
	),
));

$wp_customize->add_setting('shoppingcart_theme_options[shoppingcart_blog_content_layout]', array(
   'default'        => $shoppingcart_settings['shoppingcart_blog_content_layout'],
   'sanitize_callback' => 'shoppingcart_sanitize_select',
   'type'                  => 'option',
   'capability'            => 'manage_options'
));
$wp_customize->add_control('shoppingcart_theme_options[shoppingcart_blog_content_layout]', array(
   'priority'  =>90,
   'label'      => __('Blog Content Display', 'shoppingcart'),
   'section'    => 'shoppingcart_layout_options',
   'type'       => 'select',
   'choices'    => array(
       'fullcontent_display' => __('Blog Full Content Display','shoppingcart'),
       'excerptblog_display' => __(' Excerpt  Display','shoppingcart'),
   ),
));

$wp_customize->add_setting('shoppingcart_theme_options[shoppingcart_design_layout]', array(
	'default'        => $shoppingcart_settings['shoppingcart_design_layout'],
	'sanitize_callback' => 'shoppingcart_sanitize_select',
	'type'                  => 'option',
));
$wp_customize->add_control('shoppingcart_theme_options[shoppingcart_design_layout]', array(
	'priority'  =>100,
	'label'      => __('Design Layout', 'shoppingcart'),
	'section'    => 'shoppingcart_layout_options',
	'type'       => 'select',
	'choices'    => array(
		'full-width-layout' => __('Full Width Layout','shoppingcart'),
		'boxed-layout' => __('Boxed Layout','shoppingcart'),
		'small-boxed-layout' => __('Small Boxed Layout','shoppingcart'),
	),
));