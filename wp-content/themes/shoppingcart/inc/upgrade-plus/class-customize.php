<?php
/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0
 * @access public
 */
final class shoppingcart_Customize {

	/**
	 * Returns the instance.
	 *
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 */
	public function sections( $manager ) {

		// Load custom sections.
		require get_template_directory() . '/inc/upgrade-plus/section-pro.php';

		// Register custom section types.
		$manager->register_section_type( 'ShoppingCart_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new ShoppingCart_Customize_Section_Pro(
				$manager,
				'shoppingcart',
				array(
					'title'    => esc_html__( 'ShoppingCart', 'shoppingcart' ),
					'pro_text' => esc_html__( 'Upgrade To Plus',         'shoppingcart' ),
					'pro_url'  => 'https://themefreesia.com/plugins/shoppingcart-plus/',
					'priority' => 1
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'shoppingcart-customize-controls', trailingslashit( get_template_directory_uri() ) . 'inc/js/customizer-custom-scripts.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'shoppingcart-customize-controls', trailingslashit( get_template_directory_uri() ) . 'inc/js/shoppingcart-customizer.css' );
	}
}

// Doing this customizer thang!
shoppingcart_Customize::get_instance();
