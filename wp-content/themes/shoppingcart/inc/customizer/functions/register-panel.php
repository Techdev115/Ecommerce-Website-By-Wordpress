<?php
/**
 * Theme Customizer Functions
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */
/******************** SHOPPINGCART CUSTOMIZE REGISTER *********************************************/
add_action( 'customize_register', 'shoppingcart_customize_register_wordpress_default' );
function shoppingcart_customize_register_wordpress_default( $wp_customize ) {
	$wp_customize->add_panel( 'shoppingcart_wordpress_default_panel', array(
		'priority' => 5,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'WordPress Settings', 'shoppingcart' ),
		'description' => '',
	) );
}

add_action( 'customize_register', 'shoppingcart_customize_register_options');
function shoppingcart_customize_register_options( $wp_customize ) {
	$wp_customize->add_panel( 'shoppingcart_options_panel', array(
		'priority' => 6,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Theme Options', 'shoppingcart' ),
		'description' => '',
	) );
}

add_action( 'customize_register', 'shoppingcart_customize_register_featuredcontent' );
function shoppingcart_customize_register_featuredcontent( $wp_customize ) {
	$wp_customize->add_panel( 'shoppingcart_featuredcontent_panel', array(
		'priority' => 8,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Slider Options', 'shoppingcart' ),
		'description' => '',
	) );
}

add_action( 'customize_register', 'shoppingcart_customize_register_frontpage_options');
function shoppingcart_customize_register_frontpage_options( $wp_customize ) {
	$wp_customize->add_panel( 'shoppingcart_frontpage_panel', array(
		'priority' => 7,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Frontpage Template', 'shoppingcart' ),
		'description' => '',
	) );
}

add_action( 'customize_register', 'shoppingcart_customize_register_colors' );
function shoppingcart_customize_register_colors( $wp_customize ) {
	$wp_customize->add_panel( 'colors', array(
		'priority' => 9,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Colors Section', 'shoppingcart' ),
		'description' => '',
	) );
}