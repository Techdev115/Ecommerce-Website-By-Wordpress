<?php
/**
 * Theme Customizer Functions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
$shoppingcart_settings = shoppingcart_get_theme_options();
/********************** SHOPPINGCART DEFAULT PANEL ***********************************/
$wp_customize->add_section('header_image', array(
'title' => __('Header Media', 'shoppingcart'),
'priority' => 20,
'panel' => 'shoppingcart_wordpress_default_panel'
));
$wp_customize->add_section('colors', array(
'title' => __('Colors', 'shoppingcart'),
'priority' => 30,
'panel' => 'shoppingcart_wordpress_default_panel'
));
$wp_customize->add_section('background_image', array(
'title' => __('Background Image', 'shoppingcart'),
'priority' => 40,
'panel' => 'shoppingcart_wordpress_default_panel'
));
$wp_customize->add_section('nav', array(
'title' => __('Navigation', 'shoppingcart'),
'priority' => 50,
'panel' => 'shoppingcart_wordpress_default_panel'
));
$wp_customize->add_section('static_front_page', array(
'title' => __('Static Front Page', 'shoppingcart'),
'priority' => 60,
'panel' => 'shoppingcart_wordpress_default_panel'
));
$wp_customize->add_section('title_tagline', array(
	'title' => __('Site Title & Logo Options', 'shoppingcart'),
	'priority' => 10,
	'panel' => 'shoppingcart_wordpress_default_panel'
));

$wp_customize->add_section('shoppingcart_custom_header', array(
	'title' => __('Options', 'shoppingcart'),
	'priority' => 503,
	'panel' => 'shoppingcart_options_panel'
));

/********************  SHOPPINGCART THEME OPTIONS *****************************************/

$wp_customize->add_setting('shoppingcart_theme_options[shoppingcart_header_display]', array(
	'capability' => 'edit_theme_options',
	'default' => $shoppingcart_settings['shoppingcart_header_display'],
	'sanitize_callback' => 'shoppingcart_sanitize_select',
	'type' => 'option',
));
$wp_customize->add_control('shoppingcart_theme_options[shoppingcart_header_display]', array(
	'label' => __('Site Logo/ Text Options', 'shoppingcart'),
	'priority' => 105,
	'section' => 'title_tagline',
	'type' => 'select',
		'choices' => array(
		'header_text' => __('Display Site Title Only','shoppingcart'),
		'header_logo' => __('Display Site Logo Only','shoppingcart'),
		'show_both' => __('Show Both','shoppingcart'),
		'disable_both' => __('Disable Both','shoppingcart'),
	),
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_disable_top_bar]', array(
	'default' => $shoppingcart_settings['shoppingcart_disable_top_bar'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_disable_top_bar]', array(
	'priority'=>5,
	'label' => __('Disable Top Bar', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_search_custom_header]', array(
	'default' => $shoppingcart_settings['shoppingcart_search_custom_header'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_search_custom_header]', array(
	'priority'=>20,
	'label' => __('Disable Search Form', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_stick_menu]', array(
	'default' => $shoppingcart_settings['shoppingcart_stick_menu'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_stick_menu]', array(
	'priority'=>30,
	'label' => __('Disable Stick Menu', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_scroll]', array(
	'default' => $shoppingcart_settings['shoppingcart_scroll'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_scroll]', array(
	'priority'=>40,
	'label' => __('Disable Goto Top', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_top_social_icons]', array(
	'default' => $shoppingcart_settings['shoppingcart_top_social_icons'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_top_social_icons]', array(
	'priority'=>50,
	'label' => __('Disable Top Social Icons', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_buttom_social_icons]', array(
	'default' => $shoppingcart_settings['shoppingcart_buttom_social_icons'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_buttom_social_icons]', array(
	'priority'=>70,
	'label' => __('Disable Bottom Social Icons', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_display_page_single_featured_image]', array(
	'default' => $shoppingcart_settings['shoppingcart_display_page_single_featured_image'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_display_page_single_featured_image]', array(
	'priority'=>100,
	'label' => __('Disable Page/Single Featured Image', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_disable_main_menu]', array(
	'default' => $shoppingcart_settings['shoppingcart_disable_main_menu'],
	'sanitize_callback' => 'shoppingcart_checkbox_integer',
	'type' => 'option',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_disable_main_menu]', array(
	'priority'=>120,
	'label' => __('Disable Main Menu', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));

$wp_customize->add_setting( 'shoppingcart_theme_options[shoppingcart_reset_all]', array(
	'default' => $shoppingcart_settings['shoppingcart_reset_all'],
	'capability' => 'edit_theme_options',
	'sanitize_callback' => 'shoppingcart_reset_alls',
	'transport' => 'postMessage',
));
$wp_customize->add_control( 'shoppingcart_theme_options[shoppingcart_reset_all]', array(
	'priority'=>130,
	'label' => __('Reset all default settings. (Refresh it to view the effect)', 'shoppingcart'),
	'section' => 'shoppingcart_custom_header',
	'type' => 'checkbox',
));