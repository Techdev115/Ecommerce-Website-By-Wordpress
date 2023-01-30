<?php
/**
 * Plugin Name: Max Mega Menu
 * Plugin URI:  https://www.megamenu.com
 * Description: An easy to use mega menu plugin. Written the WordPress way.
 * Version:     2.9.3
 * Author:      megamenu.com
 * Author URI:  https://www.megamenu.com
 * License:     GPL-2.0+
 * Copyright:   2020 Tom Hemsley (https://www.megamenu.com)
 *
 * Max Mega Menu is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Max Mega Menu is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access.
}

if ( ! class_exists( 'Mega_Menu' ) ) :

	/**
	 * Main plugin class
	 */
	final class Mega_Menu {
		/**
		 * Current plugin version
		 *
		 * @var string
		 */
		public $version = '2.9.3';


		/**
		 * Identify the last version where a change was made to the CSS that requires the menu CSS to be rebuilt
		 *
		 * @var string
		 */
		public $scss_last_updated = '2.4.1.3';


		/**
		 * Init
		 *
		 * @since 1.0
		 */
		public static function init() {
			$plugin = new self();
		}


		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();

			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'init', array( $this, 'register_sidebar' ) );
			add_action( 'admin_init', array( $this, 'install_upgrade_check' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'widgets_init', array( $this, 'register_widget' ) );
			add_filter( 'in_widget_form', array( $this, 'add_notice_to_nav_menu_widget' ), 10, 3 );

			add_action( 'after_setup_theme', array( $this, 'register_nav_menus' ) );

			add_filter( 'wp_nav_menu_args', array( $this, 'modify_nav_menu_args' ), 99999 );
			add_filter( 'wp_nav_menu', array( $this, 'add_responsive_toggle' ), 10, 2 );

			add_filter( 'wp_nav_menu_objects', array( $this, 'add_widgets_to_menu' ), apply_filters( 'megamenu_wp_nav_menu_objects_priority', 10 ), 2 );
			add_filter( 'megamenu_nav_menu_objects_before', array( $this, 'apply_depth_to_menu_items' ), 5, 2 );
			add_filter( 'megamenu_nav_menu_objects_before', array( $this, 'setup_menu_items' ), 5, 2 );
			add_filter( 'megamenu_nav_menu_objects_after', array( $this, 'reorder_menu_items_within_megamenus' ), 6, 2 );
			add_filter( 'megamenu_nav_menu_objects_after', array( $this, 'apply_classes_to_menu_items' ), 7, 2 );
			add_filter( 'megamenu_nav_menu_objects_after', array( $this, 'set_descriptions_if_enabled' ), 8, 2 );
			add_filter( 'body_class', array( $this, 'add_megamenu_body_classes' ), 10, 1 );

			add_filter( 'megamenu_nav_menu_css_class', array( $this, 'prefix_menu_classes' ), 10, 3 );
			add_filter( 'megamenu_nav_menu_css_class', array( $this, 'css_classes_never_highlight' ), 10, 3 );

			// plugin compatibility.
			add_filter( 'conditional_menus_theme_location', array( $this, 'conditional_menus_restore_theme_location' ), 10, 3 );
			add_filter( 'black_studio_tinymce_enable_pages', array( $this, 'megamenu_blackstudio_tinymce' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 11 );

			add_action( 'admin_print_footer_scripts-nav-menus.php', array( $this, 'admin_print_footer_scripts' ) );
			add_action( 'admin_print_scripts-nav-menus.php', array( $this, 'admin_print_scripts' ) );
			add_action( 'admin_print_styles-nav-menus.php', array( $this, 'admin_print_styles' ) );

			add_shortcode( 'maxmenu', array( $this, 'register_shortcode' ) );
			add_shortcode( 'maxmegamenu', array( $this, 'register_shortcode' ) );

			if ( is_admin() ) {
				$admin_classes = array(
					'Mega_Menu_Nav_Menus',
					'Mega_Menu_Widget_Manager',
					'Mega_Menu_Menu_Item_Manager',
					'Mega_Menu_Page',
					'Mega_Menu_General',
					'Mega_Menu_Locations',
					'Mega_Menu_Themes',
					'Mega_Menu_Tools',
				);

				foreach ( $admin_classes as $class ) {
					if ( class_exists( $class ) ) {
						new $class();
					}
				}
			}

			if ( class_exists( 'Mega_Menu_Toggle_Blocks' ) ) {
				new Mega_Menu_Toggle_Blocks();
			}

			if ( class_exists( 'Mega_Menu_Style_Manager' ) ) {
				$mega_menu_style_manager = new Mega_Menu_Style_Manager();
				$mega_menu_style_manager->setup_actions();
			}

		}


		/**
		 * Add a body class for each active mega menu location.
		 *
		 * @since  2.3
		 * @param  array $classes current body classes.
		 * @return array
		 */
		public function add_megamenu_body_classes( $classes ) {
			$locations = get_nav_menu_locations();

			if ( count( $locations ) ) {
				foreach ( $locations as $location => $id ) {
					if ( has_nav_menu( $location ) && max_mega_menu_is_enabled( $location ) ) {
						$classes[] = 'mega-menu-' . str_replace( '_', '-', $location );
					}
				}
			}

			return $classes;
		}


		/**
		 * Add custom actions to allow enqueuing scripts on specific pages
		 *
		 * @since 1.8.3
		 * @param string $hook page ID.
		 */
		public function admin_enqueue_scripts( $hook ) {
			if ( ! wp_script_is( 'maxmegamenu' ) ) {

				if ( in_array( $hook, array( 'nav-menus.php', 'gutenberg_page_gutenberg-navigation' ), true ) ) {
					// load widget scripts and styles first to allow us to dequeue conflicting colorbox scripts from other plugins.
					do_action( 'sidebar_admin_setup' );
					do_action( 'admin_enqueue_scripts', 'widgets.php' );
					do_action( 'megamenu_nav_menus_scripts', $hook );
				}

				if ( strpos( $hook, 'maxmegamenu' ) !== false ) {
					do_action( 'megamenu_admin_scripts', $hook );
				}
			}
		}


		/**
		 * Print the widgets.php scripts on the nav-menus.php page. Required for 4.8 Core Media Widgets.
		 *
		 * @since 2.3.7
		 * @param string $hook page ID.
		 */
		public function admin_print_footer_scripts( $hook ) {
			do_action( 'admin_footer-widgets.php' );
		}


		/**
		 * Print the widgets.php scripts on the nav-menus.php page. Required for 4.8 Core Media Widgets.
		 *
		 * @since 2.3.7
		 * @param string $hook page ID.
		 */
		public function admin_print_scripts( $hook ) {
			do_action( 'admin_print_scripts-widgets.php' );
		}


		/**
		 * Print the widgets.php scripts on the nav-menus.php page. Required for 4.8 Core Media Widgets.
		 *
		 * @since 2.3.7
		 * @param string $hook page ID.
		 */
		public function admin_print_styles( $hook ) {
			do_action( 'admin_print_styles-widgets.php' );
		}


		/**
		 * Register menu locations created within Max Mega Menu.
		 *
		 * @since 1.8
		 */
		public function register_nav_menus() {

			$locations = get_option( 'megamenu_locations' );

			if ( is_array( $locations ) && count( $locations ) ) {
				foreach ( $locations as $key => $val ) {
					register_nav_menu( $key, $val );
				}
			}
		}


		/**
		 * Black Studio TinyMCE Compatibility.
		 * Load TinyMCE assets on nav-menus.php page.
		 *
		 * @since  1.8
.		 * @param  array $pages Pages to load tinymce scripts on
		 * @return array $pages
		 */
		public function megamenu_blackstudio_tinymce( $pages ) {
			$pages[] = 'nav-menus.php';
			return $pages;
		}


		/**
		 * Detect new or updated installations and run actions accordingly.
		 *
		 * @since 1.3
		 */
		public function install_upgrade_check() {
			$version = get_option( 'megamenu_version' );

			if ( $version ) {

				if ( version_compare( $this->version, $version, '!=' ) ) {

					update_option( 'megamenu_version', $this->version );

					do_action( 'megamenu_after_update' );

				}
			} else {

				add_option( 'megamenu_version', $this->version );
				add_option( 'megamenu_initial_version', $this->version );
				add_option( 'megamenu_multisite_share_themes', 'false' );

				do_action( 'megamenu_after_install' );

				$settings = get_option( 'megamenu_settings' );

				// set defaults.
				if ( ! $settings ) {
					$settings['prefix']       = 'disabled';
					$settings['descriptions'] = 'enabled';
					$settings['second_click'] = 'go';

					add_option( 'megamenu_settings', $settings );
				}
			}
		}


		/**
		 * Register widget
		 *
		 * @since 1.7.4
		 */
		public function register_widget() {
			if ( class_exists( 'Mega_Menu_Widget' ) ) {
				register_widget( 'Mega_Menu_Widget' );
			}
			//register_widget( 'Mega_Menu_Widget_Reusable_Block' );

			// Check if Elementor installed and activated
			//if ( did_action( 'elementor/loaded' ) ) {
			//    register_widget( 'Mega_Menu_Widget_Elementor_Template' );
			//}
		}


		/**
		 * Create our own widget area to store all mega menu widgets.
		 * All widgets from all menus are stored here, they are filtered later
		 * to ensure the correct widgets show under the correct menu item.
		 *
		 * @since 1.0
		 */
		public function register_sidebar() {

			register_sidebar(
				array(
					'id'          => 'mega-menu',
					'name'        => __( 'Max Mega Menu Widgets', 'megamenu' ),
					'description' => __( 'This is where Max Mega Menu stores widgets that you have added to sub menus using the mega menu builder. You can edit existing widgets here, but new widgets must be added through the Mega Menu interface (under Appearance > Menus).', 'megamenu' ),
				)
			);
		}


		/**
		 * Shortcode used to display a menu.
		 *
		 * @since  1.3
		 * @param array $atts shortcode attributes.
		 * @return string
		 */
		public function register_shortcode( $atts ) {
			if ( ! isset( $atts['location'] ) ) {
				return false;
			}

			if ( has_nav_menu( $atts['location'] ) ) {
				return wp_nav_menu(
					array(
						'theme_location' => $atts['location'],
						'echo'           => false,
					)
				);
			}

			return '<!-- menu not found [maxmegamenu] -->';

		}


		/**
		 * Initialise translations
		 *
		 * @since 1.0
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'megamenu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Define Mega Menu constants
		 *
		 * @since 1.0
		 */
		private function define_constants() {
			define( 'MEGAMENU_VERSION', $this->version );
			define( 'MEGAMENU_BASE_URL', trailingslashit( plugins_url( 'megamenu' ) ) );
			define( 'MEGAMENU_PATH', plugin_dir_path( __FILE__ ) );
		}


		/**
		 * All Mega Menu classes
		 *
		 * @since 1.0
		 */
		private function plugin_classes() {
			$classes = array(
				'Mega_Menu_Walker'                    => MEGAMENU_PATH . 'classes/walker.class.php',
				'Mega_Menu_Widget_Manager'            => MEGAMENU_PATH . 'classes/widget-manager.class.php',
				'Mega_Menu_Menu_item_Manager'         => MEGAMENU_PATH . 'classes/menu-item-manager.class.php',
				'Mega_Menu_Nav_Menus'                 => MEGAMENU_PATH . 'classes/nav-menus.class.php',
				'Mega_Menu_Style_Manager'             => MEGAMENU_PATH . 'classes/style-manager.class.php',
				'Mega_Menu_Page'                      => MEGAMENU_PATH . 'classes/pages/page.php',
				'Mega_Menu_General'                   => MEGAMENU_PATH . 'classes/pages/general.php',
				'Mega_Menu_Locations'                 => MEGAMENU_PATH . 'classes/pages/locations.php',
				'Mega_Menu_Themes'                    => MEGAMENU_PATH . 'classes/pages/themes.php',
				'Mega_Menu_Tools'                     => MEGAMENU_PATH . 'classes/pages/tools.php',
				'Mega_Menu_Widget'                    => MEGAMENU_PATH . 'classes/widgets/widget.class.php',
				'Mega_Menu_Widget_Reusable_Block'     => MEGAMENU_PATH . 'classes/widgets/widget-reusable-block.class.php',
				'Mega_Menu_Widget_Elementor_Template' => MEGAMENU_PATH . 'classes/widgets/widget-elementor-template.class.php',
				'Mega_Menu_toggle_Blocks'             => MEGAMENU_PATH . 'classes/toggle-blocks.class.php',
			);

			return $classes;
		}


		/**
		 * Load required classes
		 *
		 * @since 1.0
		 */
		private function includes() {
			foreach ( $this->plugin_classes() as $id => $path ) {
				if ( is_readable( $path ) && ! class_exists( $id ) ) {
					include_once $path;
				}
			}

			$template = get_template();

			if ( 'zerif-pro' === $template ) {
				$template = 'zerif';
			}

			switch ( $template ) {
				case 'twentyseventeen':
				case 'zerif':
					if ( is_readable( MEGAMENU_PATH . "integration/{$template}/functions.php" ) ) {
						include_once MEGAMENU_PATH . "integration/{$template}/functions.php";
					}
				default:
					break;
			}

			// gutenberg block
			//include_once MEGAMENU_PATH . "integration/location/location.php";
		}


		/**
		 * Appends "mega-" to all menu classes.
		 * This is to help avoid theme CSS conflicts.
		 *
		 * @since 1.0
		 * @param array  $classes classes for this menu item.
		 * @param object $item The menu item object.
		 * @param object $args Menu arguments.
		 * @return array
		 */
		public function prefix_menu_classes( $classes, $item, $args ) {
			$return = array();

			foreach ( $classes as $class ) {
				$return[] = 'mega-' . $class;
			}

			$location = $args->theme_location;
			$settings = get_option( 'megamenu_settings' );

			$prefix = isset( $settings['prefix'] ) ? $settings['prefix'] : 'enabled';

			if ( isset( $settings[ $location ]['prefix'] ) ) {
				$prefix = $settings[ $location ]['prefix'];
			}

			if ( 'disabled' === $prefix ) {
				// add in custom classes, sans 'mega-' prefix.
				foreach ( $classes as $class ) {

					// custom classes are added before the 'menu-item' class.
					if ( 'menu-item' === $class ) {
						break;
					}

					if ( in_array( $class, array( 'menu-column', 'menu-row', 'hide-on-mobile', 'hide-on-desktop' ), true ) ) { // these are always prefixed.
						continue;
					}

					if ( strpos( $class, 'menu-columns-' ) !== false ) { // mega-menu-columns-X-of-Y are always prefixed.
						continue;
					}

					$return[] = $class;
				}
			}

			return $return;
		}

		/**
		 * Remove the current menu item classes when a custom class of 'never-highlight' has been added to the menu item
		 *
		 * @param array $classes
		 * @param ovject $item
		 * @param array $args
		 * @return array
		 */
		public function css_classes_never_highlight( $classes, $item, $args ) {
			if ( in_array( 'mega-never-highlight', $classes ) ) {
				if ( in_array( 'mega-current-menu-ancestor', $classes ) ) {
					$classes = array_diff( $classes, array( 'mega-current-menu-ancestor' ) );
				}

				if ( in_array( 'mega-current-menu-item', $classes ) ) {
					$classes = array_diff( $classes, array( 'mega-current-menu-item' ) );
				}

				if ( in_array( 'mega-current-page-ancestor', $classes ) ) {
					$classes = array_diff( $classes, array( 'mega-current-page-ancestor' ) );
				}
			}

			return $classes;
		}


		/**
		 * Add the html for the responsive toggle box to the menu
		 *
		 * @param  string $nav_menu HTML markup of menu.
		 * @param  object $args wp_nav_menu arguments.
		 * @return string
		 * @since  1.3
		 */
		public function add_responsive_toggle( $nav_menu, $args ) {
			$args = (object) $args;

			// make sure we're working with a Mega Menu.
			if ( ! $args->walker || ! is_a( $args->walker, 'Mega_Menu_Walker' ) ) {
				return $nav_menu;
			}

			$find = 'class="' . $args->container_class . '">';

			$theme_id = mmm_get_theme_id_for_location( $args->theme_location );

			$content = '';

			$content = apply_filters( 'megamenu_toggle_bar_content', $content, $nav_menu, $args, $theme_id );

			$replace = $find . '<div class="mega-menu-toggle">' . $content . '</div>';

			return str_replace( $find, $replace, $nav_menu );
		}


		/**
		 * Append the widget objects to the menu array before the
		 * menu is processed by the walker.
		 *
		 * @since  1.0
		 * @param  array  $items - All menu item objects.
		 * @param  object $args wp_nav_menu arguments.
		 * @return array - Menu objects including widgets
		 */
		public function add_widgets_to_menu( $items, $args ) {

			$args = (object) $args;

			// make sure we're working with a Mega Menu.
			if ( ! $args->walker || ! is_a( $args->walker, 'Mega_Menu_Walker' ) ) {
				return $items;
			}

			$items = apply_filters( 'megamenu_nav_menu_objects_before', $items, $args );

			$widget_manager = new Mega_Menu_Widget_Manager();

			$rolling_dummy_id = 999999999;

			$items_to_move = array();

			foreach ( $items as $item ) {

				// populate standard (non-grid) sub menus.
				if ( property_exists( $item, 'depth' ) && 0 === $item->depth && 'megamenu' === $item->megamenu_settings['type'] || ( property_exists( $item, 'depth' ) && 1 === $item->depth && 'tabbed' === $item->parent_submenu_type && 'grid' !== $item->megamenu_settings['type'] ) ) {

					$panel_widgets = $widget_manager->get_widgets_for_menu_id( $item->ID, $args->menu );

					if ( ! in_array( 'menu-megamenu', $item->classes, true ) ) {
						$item->classes[] = 'menu-megamenu';
					}

					if ( count( $panel_widgets ) ) {

						$widget_position       = 0;
						$total_widgets_in_menu = count( $panel_widgets );
						$next_order            = $this->menu_order_of_next_sibling( $item->ID, $item->menu_item_parent, $items );

						if ( ! in_array( 'menu-item-has-children', $item->classes, true ) ) {
							$item->classes[] = 'menu-item-has-children';
						}

						foreach ( $panel_widgets as $widget ) {
							$widget_settings = array_merge(
								Mega_Menu_Nav_Menus::get_menu_item_defaults(),
								array(
									'mega_menu_columns' => absint( $widget['columns'] ),
								)
							);

							$menu_item = array(
								'type'                => 'widget',
								'parent_submenu_type' => 'megamenu',
								'title'               => $widget['id'],
								'content'             => $widget_manager->show_widget( $widget['id'] ),
								'menu_item_parent'    => $item->ID,
								'db_id'               => 0,
								'ID'                  => $widget['id'],
								'menu_order'          => $next_order - $total_widgets_in_menu + $widget_position,
								'megamenu_order'      => $widget['order'],
								'megamenu_settings'   => $widget_settings,
								'depth'               => 1,
								'classes'             => array(
									'menu-item',
									'menu-item-type-widget',
									'menu-widget-class-' . $widget_manager->get_widget_class( $widget['id'] ),
								),
							);

							$items[] = (object) $menu_item;

							$widget_position++;
						}
					}
				}

				// populate grid sub menus.
				if ( property_exists( $item, 'depth' ) && 0 === $item->depth && 'grid' === $item->megamenu_settings['type'] || ( property_exists( $item, 'depth' ) && 1 === $item->depth && 'tabbed' === $item->parent_submenu_type && 'grid' === $item->megamenu_settings['type'] ) ) {

					$saved_grid = $widget_manager->get_grid_widgets_and_menu_items_for_menu_id( $item->ID, $args->menu->term_id, $items );

					$next_order = $this->menu_order_of_next_sibling( $item->ID, $item->menu_item_parent, $items ) - 999;

					foreach ( $saved_grid as $row => $row_data ) {

						$rolling_dummy_id++;
						$next_order++;

						if ( isset( $row_data['columns'] ) ) {

							if ( ! in_array( 'menu-item-has-children', $item->classes, true ) ) {
								$item->classes[] = 'menu-item-has-children';
							}

							if ( ! in_array( 'menu-megamenu', $item->classes, true ) ) {
								$item->classes[] = 'menu-megamenu';
							}

							$classes = array( 'menu-row' );

							if ( isset( $row_data['meta']['class'] ) ) {
								$classes = array_merge( $classes, array_unique( explode( ' ', $row_data['meta']['class'] ) ) );
							}

							$row_item = array(
								'menu_item_parent'    => $item->ID,
								'type'                => 'mega_row',
								'title'               => 'Custom Row',
								'parent_submenu_type' => '',
								'menu_order'          => $next_order,
								'depth'               => 0,
								'ID'                  => "{$item->ID}-{$row}",
								'megamenu_settings'   => Mega_Menu_Nav_Menus::get_menu_item_defaults(),
								'db_id'               => $rolling_dummy_id,
								'classes'             => $classes,
							);

							$items[] = (object) $row_item;

							$row_dummy_id = $rolling_dummy_id;

							foreach ( $row_data['columns'] as $col => $col_data ) {

								$rolling_dummy_id++;
								$next_order++;

								$classes = array( 'menu-column' );

								if ( isset( $col_data['meta']['class'] ) ) {
									$classes = array_merge( $classes, array_unique( explode( ' ', $col_data['meta']['class'] ) ) );
								}

								if ( isset( $row_data['meta']['columns'] ) ) {
									$row_columns = $row_data['meta']['columns'];
								} else {
									$row_columns = 12;
								}

								if ( isset( $col_data['meta']['span'] ) ) {
									$classes[] = "menu-columns-{$col_data['meta']['span']}-of-{$row_columns}";
								}

								if ( isset( $col_data['meta']['hide-on-mobile'] ) && 'true' === $col_data['meta']['hide-on-mobile'] ) {
									$classes[] = 'hide-on-mobile';
								}

								if ( isset( $col_data['meta']['hide-on-mobile'] ) && 'true' === $col_data['meta']['hide-on-desktop'] ) {
									$classes[] = 'hide-on-desktop';
								}

								$col_item = array(
									'menu_item_parent'    => $row_dummy_id,
									'type'                => 'mega_column',
									'title'               => 'Custom Column',
									'parent_submenu_type' => '',
									'menu_order'          => $next_order,
									'depth'               => 0,
									'ID'                  => "{$item->ID}-{$row}-{$col}",
									'megamenu_settings'   => Mega_Menu_Nav_Menus::get_menu_item_defaults(),
									'db_id'               => $rolling_dummy_id,
									'classes'             => $classes,
								);

								$items[] = (object) $col_item;

								if ( isset( $col_data['items'] ) ) {

									foreach ( $col_data['items'] as $key => $block ) {

										$next_order++;

										if ( 'widget' === $block['type'] ) {

											$widget_settings = array_merge( Mega_Menu_Nav_Menus::get_menu_item_defaults() );

											$menu_item = array(
												'type'    => 'widget',
												'parent_submenu_type' => '',
												'title'   => '',
												'content' => $widget_manager->show_widget( $block['id'] ),
												'menu_item_parent' => $rolling_dummy_id,
												'db_id'   => 0,
												'ID'      => $block['id'],
												'menu_order' => $next_order,
												'megamenu_order' => 0,
												'megamenu_settings' => $widget_settings,
												'depth'   => 1,
												'classes' => array(
													'menu-item',
													'menu-item-type-widget',
													'menu-widget-class-' . $widget_manager->get_widget_class( $block['id'] ),
												),
											);

											$items[] = (object) $menu_item;

										} else {
											// mark this menu item to be moved into a new position.
											$items_to_move[ $block['id'] ] = array(
												'new_parent' => $rolling_dummy_id,
												'new_order'  => $next_order,
											);
										}
									}
								}
							}
						}
					}
				}
			}

			if ( count( $items_to_move ) ) {
				foreach ( $items_to_move as $id => $new_parent ) {
					$items_to_find[] = $id;
				}

				foreach ( $items as $item ) {
					if ( in_array( $item->ID, $items_to_find, true ) ) {
						$item->menu_item_parent = $items_to_move[ $item->ID ]['new_parent'];
						$item->menu_order       = $items_to_move[ $item->ID ]['new_order'];
					}
				}
			}

			$items = apply_filters( 'megamenu_nav_menu_objects_after', $items, $args );

			return $items;
		}


		/**
		 * Return the menu order of the next sibling menu item.
		 * Eg, given A as the $item_id, the menu order of D will be returned
		 * Eg, given B as the $item_id, the menu order of C will be returned
		 * Eg, given D as the $item_id, the menu order of D + 1000 will be returned
		 *
		 * - A
		 * --- B
		 * --- C
		 * - D
		 *
		 * @since  2.0
		 * @param  int   $item_id ID of the menu item.
		 * @param  array $menu_item_parent ID of the parent menu item.
		 * @param  array $items array of all items.
		 * @return int
		 */
		private function menu_order_of_next_sibling( $item_id, $menu_item_parent, $items ) {

			$get_order_of_next_item = false;

			foreach ( $items as $key => $item ) {

				if ( $menu_item_parent !== $item->menu_item_parent ) {
					continue;
				}

				if ( 'widget' === $item->type ) {
					continue;
				}

				if ( $get_order_of_next_item ) {
					return $item->menu_order;
				}

				if ( $item->ID === $item_id ) {
					$get_order_of_next_item = true;
				}

				if ( isset( $item->menu_order ) ) {
					$rolling_last_menu_order = $item->menu_order;
				}
			}

			// there isn't a next sibling.
			return $rolling_last_menu_order + 1000;

		}


		/**
		 * Determine if menu item is a top level item or a second level item
		 *
		 * @since  2.7.7
		 * @param  array  $items - All menu item objects.
		 * @param  object $args wp_nav_menu arguments.
		 * @return array
		 */
		public function apply_depth_to_menu_items( $items, $args ) {
			$parents = array();

			foreach ( $items as $key => $item ) {
				if ( $item->menu_item_parent == 0 ) {
					$parents[]   = $item->ID;
					$item->depth = 0;
				}
			}

			if ( count( $parents ) ) {
				foreach ( $items as $key => $item ) {
					if ( in_array( $item->menu_item_parent, $parents ) ) {
						$item->depth = 1;
					}
				}
			}

			return $items;
		}


		/**
		 * Setup the mega menu settings for each menu item
		 *
		 * @since  2.0
		 * @param  array  $items - All menu item objects.
		 * @param  object $args wp_nav_menu arguments.
		 * @return array
		 */
		public function setup_menu_items( $items, $args ) {

			// apply saved metadata to each menu item.
			foreach ( $items as $item ) {
				$saved_settings = array_filter( (array) get_post_meta( $item->ID, '_megamenu', true ) );

				$item->megamenu_settings   = array_merge( Mega_Menu_Nav_Menus::get_menu_item_defaults(), $saved_settings );
				$item->megamenu_order      = isset( $item->megamenu_settings['mega_menu_order'][ $item->menu_item_parent ] ) ? $item->megamenu_settings['mega_menu_order'][ $item->menu_item_parent ] : 0;
				$item->parent_submenu_type = 'flyout';

				if ( isset( $item->menu_order ) ) {
					$item->menu_order = $item->menu_order * 1000;
				}

				// add parent mega menu type.
				if ( property_exists( $item, 'depth' ) && absint( $item->depth ) === 1 ) {
					$parent_settings = array_filter( (array) get_post_meta( $item->menu_item_parent, '_megamenu', true ) );

					if ( isset( $parent_settings['type'] ) ) {
						$item->parent_submenu_type = $parent_settings['type'];
					}
				}
			}

			return $items;
		}


		/**
		 * Reorder items within the mega menu.
		 *
		 * @since  2.0
		 * @param  array  $items - All menu item objects.
		 * @param  object $args wp_nav_menu arguments.
		 * @return array
		 */
		public function reorder_menu_items_within_megamenus( $items, $args ) {
			$new_items = array();

			// reorder menu items within mega menus based on internal ordering.
			foreach ( $items as $item ) {
				// items ordered with 'forced' ordering.
				if ( property_exists( $item, 'parent_submenu_type' ) && 'megamenu' === $item->parent_submenu_type && property_exists( $item, 'megamenu_order' ) && 0 !== $item->megamenu_order ) {
					if ( $parent_menu_item = get_post( $item->menu_item_parent ) ) {
						$item->menu_order = $parent_menu_item->menu_order * 1000 + $item->megamenu_order;
					}
				}
			}

			foreach ( $items as $item ) {
				if ( in_array( 'wpml-ls-item', $item->classes, true ) ) {
					$item->classes[] = 'menu-flyout';
				}

				$new_items[ $item->menu_order ] = $item;
			}

			ksort( $new_items );

			return $new_items;
		}


		/**
		 * If descriptions are enabled, create a new 'mega_description' property.
		 * This is for backwards compatibility for users who have used filters
		 * to display descriptions
		 *
		 * @since  2.3
		 * @param  array  $items All menu item objects.
		 * @param  object $args wp_nav_menu arguments.
		 * @return array
		 */
		public function set_descriptions_if_enabled( $items, $args ) {

			$location = $args->theme_location;
			$settings = get_option( 'megamenu_settings' );

			$descriptions = isset( $settings['descriptions'] ) ? $settings['descriptions'] : 'disabled';

			if ( isset( $settings[ $location ]['descriptions'] ) ) {
				$descriptions = $settings[ $location ]['descriptions'];
			}

			if ( 'enabled' === $descriptions ) {
				foreach ( $items as $item ) {
					if ( property_exists( $item, 'description' ) && strlen( $item->description ) ) {
						$item->mega_description = $item->description;
						$item->classes[]        = 'has-description';
					}
				}
			}

			return $items;
		}


		/**
		 * Apply column and clear classes to menu items (inc. widgets)
		 *
		 * @since  2.0
		 * @param  array  $items All menu item objects.
		 * @param  object $args wp_nav_menu arguments.
		 * @return array
		 */
		public function apply_classes_to_menu_items( $items, $args ) {
			$parents = array();

			foreach ( $items as $item ) {

				if ( ! in_array( 'menu-row', $item->classes, true ) && ! in_array( 'menu-column', $item->classes, true ) ) {
					if ( property_exists( $item, 'depth' ) && 0 === $item->depth ) {
						$item->classes[] = 'align-' . $item->megamenu_settings['align'];
						$item->classes[] = 'menu-' . $item->megamenu_settings['type'];
					}

					if ( 'true' === $item->megamenu_settings['hide_arrow'] ) {
						$item->classes[] = 'hide-arrow';
					}

					if ( 'disabled' !== $item->megamenu_settings['icon'] ) {
						$item->classes[] = 'has-icon';
					}

					if ( 'disabled' !== $item->megamenu_settings['icon'] && isset( $item->megamenu_settings['icon_position'] ) ) {
						$item->classes[] = 'icon-' . $item->megamenu_settings['icon_position'];
					}

					if ( 'true' === $item->megamenu_settings['hide_text'] && 0 === $item->depth ) {
						$item->classes[] = 'hide-text';
					}

					if ( 'left' !== $item->megamenu_settings['item_align'] && 0 === $item->depth ) {
						$item->classes[] = 'item-align-' . $item->megamenu_settings['item_align'];
					}

					if ( 'true' === $item->megamenu_settings['hide_on_desktop'] ) {
						$item->classes[] = 'hide-on-desktop';
					}

					if ( 'true' === $item->megamenu_settings['hide_on_mobile'] ) {
						$item->classes[] = 'hide-on-mobile';
					}

					if ( 'true' === $item->megamenu_settings['hide_sub_menu_on_mobile'] ) {
						$item->classes[] = 'hide-sub-menu-on-mobile';
					}

					if ( 'true' === $item->megamenu_settings['disable_link'] ) {
						$item->classes[] = 'disable-link';
					}

					if ( 'true' === $item->megamenu_settings['collapse_children'] ) {
						$item->classes[] = 'collapse-children';
					}

					if ( absint( $item->megamenu_settings['submenu_columns'] ) > 1 ) {
						$item->classes[] = absint( $item->megamenu_settings['submenu_columns'] ) . '-columns';
					}
				}

				// add column classes for second level menu items displayed in mega menus.
				if ( 'megamenu' === $item->parent_submenu_type ) {

					$parent_settings = array_filter( (array) get_post_meta( $item->menu_item_parent, '_megamenu', true ) );
					$parent_settings = array_merge( Mega_Menu_Nav_Menus::get_menu_item_defaults(), $parent_settings );

					$span = $item->megamenu_settings['mega_menu_columns'];

					$total_columns = $parent_settings['panel_columns'];

					if ( $total_columns >= $span ) {
						$item->classes[] = "menu-columns-{$span}-of-{$total_columns}";
						$column_count    = $span;
					} else {
						$item->classes[] = "menu-columns-{$total_columns}-of-{$total_columns}";
						$column_count    = $total_columns;
					}

					if ( ! isset( $parents[ $item->menu_item_parent ] ) ) {
						$parents[ $item->menu_item_parent ] = $column_count;
					} else {
						$parents[ $item->menu_item_parent ] = $parents[ $item->menu_item_parent ] + $column_count;

						if ( $parents[ $item->menu_item_parent ] > $total_columns ) {
							$parents[ $item->menu_item_parent ] = $column_count;
							$item->classes[]                    = 'menu-clear';
						}
					}
				}
			}

			return $items;
		}


		/**
		 * Use the Mega Menu walker to output the menu
		 * Resets all parameters used in the wp_nav_menu call
		 * Wraps the menu in mega-menu IDs and classes
		 *
		 * @since  1.0
		 * @param  object $args wp_nav_menu arguments.
		 * @return array
		 */
		public function modify_nav_menu_args( $args ) {

			if ( ! isset( $args['theme_location'] ) ) {
				return $args;
			}

			// internal action to use as a counter.
			do_action( 'megamenu_instance_counter_' . $args['theme_location'] );

			$num_times_called = did_action( 'megamenu_instance_counter_' . $args['theme_location'] );

			$settings               = get_option( 'megamenu_settings' );
			$current_theme_location = $args['theme_location'];

			$active_instance = 0;

			if ( isset( $settings[ $current_theme_location ]['active_instance'] ) ) {
				$active_instance = $settings[ $current_theme_location ]['active_instance'];
			} elseif ( isset( $settings['instances'][ $current_theme_location ] ) ) {
				$active_instance = $settings['instances'][ $current_theme_location ];
			}

			if ( $active_instance != '0' && strlen( $active_instance ) ) {
				if ( strpos( $active_instance, ',' ) || is_numeric( $active_instance ) ) {
					$active_instances = explode( ',', $active_instance );

					if ( ! in_array( $num_times_called, $active_instances ) ) {
						return $args;
					}
				} else if ( isset( $args['container_id'] ) && $active_instance != $args['container_id'] ) {
					return $args;
				}
			}

			$locations = get_nav_menu_locations();

			if ( isset( $settings[ $current_theme_location ]['enabled'] ) && true === boolval( $settings[ $current_theme_location ]['enabled'] ) ) {

				if ( ! isset( $locations[ $current_theme_location ] ) ) {
					return $args;
				}

				$menu_id = $locations[ $current_theme_location ];

				if ( ! $menu_id ) {
					return $args;
				}

				$menu_theme    = mmm_get_theme_for_location( $current_theme_location );
				$menu_settings = $settings[ $current_theme_location ];

				$effect = isset( $menu_settings['effect'] ) ? $menu_settings['effect'] : 'disabled';

				// as set on the main settings page.
				$vertical_behaviour = isset( $settings['mobile_behaviour'] ) ? $settings['mobile_behaviour'] : 'standard';

				if ( isset( $menu_settings['mobile_behaviour'] ) ) {
					$vertical_behaviour = $menu_settings['mobile_behaviour'];
				}

				// as set on the main settings page.
				$second_click = isset( $settings['second_click'] ) ? $settings['second_click'] : 'go';

				if ( isset( $menu_settings['second_click'] ) ) {
					$second_click = $menu_settings['second_click'];
				}

				$unbind = isset( $settings['unbind'] ) ? $settings['unbind'] : 'enabled';

				if ( isset( $menu_settings['unbind'] ) ) {
					$unbind = $menu_settings['unbind'];
				}

				$event = 'hover_intent';

				if ( isset( $menu_settings['event'] ) ) {
					if ( 'hover' === $menu_settings['event'] ) {
						$event = 'hover_intent';
					} elseif ( 'hover_' === $menu_settings['event'] ) {
						$event = 'hover';
					} else {
						$event = $menu_settings['event'];
					}
				}

				$mobile_force_width = 'false';

				if ( isset( $menu_theme['mobile_menu_force_width'] ) && 'on' === $menu_theme['mobile_menu_force_width'] ) {
					if ( isset( $menu_theme['mobile_menu_force_width_selector'] ) ) {
						$mobile_force_width = $menu_theme['mobile_menu_force_width_selector'];
					} else {
						$mobile_force_width = 'body';
					}
				}

				$effect_mobile = 'disabled';

				if ( isset( $menu_settings['effect_mobile'] ) ) {
					$effect_mobile = $menu_settings['effect_mobile'];
				}

				$effect_speed_mobile = 200;

				if ( isset( $menu_settings['effect_speed_mobile'] ) ) {
					$effect_speed_mobile = $menu_settings['effect_speed_mobile'];
				}

				$container = 'div';

				if ( isset( $menu_settings['container'] ) ) {
					$container = $menu_settings['container'];
				}

				if ( 'disabled' === $effect_mobile ) {
					$effect_speed_mobile = 0;
				}

				$mobile_state = 'collapse_all';

				if ( isset( $menu_settings['mobile_state'] ) ) {
					$mobile_state = $menu_settings['mobile_state'];
				}

				$hover_intent_params = apply_filters(
					'megamenu_javascript_localisation', // backwards compatiblity.
					array(
						'timeout'  => 300,
						'interval' => 100,
					)
				);

				$wrap_attributes = apply_filters(
					'megamenu_wrap_attributes',
					array(
						'id'                         => '%1$s',
						'class'                      => '%2$s mega-no-js',
						'data-event'                 => $event,
						'data-effect'                => $effect,
						'data-effect-speed'          => isset( $menu_settings['effect_speed'] ) ? $menu_settings['effect_speed'] : '200',
						'data-effect-mobile'         => $effect_mobile,
						'data-effect-speed-mobile'   => $effect_speed_mobile,
						'data-panel-width'           => preg_match( '/^\d/', $menu_theme['panel_width'] ) !== 1 ? $menu_theme['panel_width'] : '',
						'data-panel-inner-width'     => substr( trim( $menu_theme['panel_inner_width'] ), -1 ) !== '%' ? $menu_theme['panel_inner_width'] : '',
						'data-mobile-force-width'    => $mobile_force_width,
						'data-second-click'          => $second_click,
						'data-document-click'        => 'collapse',
						'data-vertical-behaviour'    => $vertical_behaviour,
						'data-breakpoint'            => absint( $menu_theme['responsive_breakpoint'] ),
						'data-unbind'                => 'disabled' === $unbind ? 'false' : 'true',
						'data-mobile-state'          => $mobile_state,
						'data-hover-intent-timeout'  => absint( $hover_intent_params['timeout'] ),
						'data-hover-intent-interval' => absint( $hover_intent_params['interval'] ),
					),
					$menu_id,
					$menu_settings,
					$settings,
					$current_theme_location
				);

				$attributes = '';

				foreach ( $wrap_attributes as $attribute => $value ) {
					if ( strlen( $value ) ) {
						$attributes .= ' ' . $attribute . '="' . esc_attr( $value ) . '"';
					}
				}

				$sanitized_location = str_replace( apply_filters( 'megamenu_location_replacements', array( '-', ' ' ) ), '-', $current_theme_location );

				$defaults = array(
					'menu'            => wp_get_nav_menu_object( $menu_id ),
					'container'       => $container,
					'container_class' => 'mega-menu-wrap',
					'container_id'    => 'mega-menu-wrap-' . $sanitized_location,
					'menu_class'      => 'mega-menu max-mega-menu mega-menu-horizontal',
					'menu_id'         => 'mega-menu-' . $sanitized_location,
					'fallback_cb'     => 'wp_page_menu',
					'before'          => '',
					'after'           => '',
					'link_before'     => '',
					'link_after'      => '',
					'items_wrap'      => '<ul' . $attributes . '>%3$s</ul>',
					'depth'           => 0,
					'walker'          => new Mega_Menu_Walker(),
				);

				$args = array_merge( $args, apply_filters( 'megamenu_nav_menu_args', $defaults, $menu_id, $current_theme_location ) );
			}

			return $args;
		}


		/**
		 * Display admin notices.
		 */
		public function admin_notices() {

			if ( ! $this->is_compatible_wordpress_version() ) :

				?>
			<div class="error">
				<p><?php esc_html_e( 'Max Mega Menu is not compatible with your version of WordPress. Please upgrade WordPress to the latest version or disable Max Mega Menu.', 'megamenu' ); ?></p>
			</div>
				<?php

			endif;

			if ( did_action( 'megamenu_after_install' ) === 1 ) :

				?>

				<?php

			endif;

			if ( defined( 'MEGAMENU_HIDE_CSS_NAG' ) && MEGAMENU_HIDE_CSS_NAG === true ) {
				return;
			}

			$css_version = get_transient( 'megamenu_css_version' );
			$css         = get_transient( 'megamenu_css' );

			if ( $css && version_compare( $this->scss_last_updated, $css_version, '>' ) ) :

				?>
			<div class="updated">
				<?php

				$clear_cache_url = esc_url(
					add_query_arg(
						array(
							'action' => 'megamenu_clear_css_cache',
						),
						wp_nonce_url( admin_url( 'admin-post.php' ), 'megamenu_clear_css_cache' )
					)
				);

				$link = "<a href='{$clear_cache_url}'>" . __( 'clear the CSS cache', 'megamenu' ) . '</a>';

				$allowed_html = array(
					'a' => array(
						'href'  => array(),
						'title' => array(),
					),
				);

				?>

				<p>
					<?php
						printf(
							/* translators: %s is the link to clear the menu CSS cache */
							esc_html__( 'Max Mega Menu has been updated. Please %s to ensure maximum compatibility with the latest version.', 'megamenu' ),
							wp_kses( $link, $allowed_html )
						);
					?>
				</p>
			</div>
				<?php

			endif;
		}


		/**
		 * Checks this WordPress installation is v3.8 or above.
		 * 3.8 is needed for dashicons.
		 */
		public function is_compatible_wordpress_version() {
			global $wp_version;

			return $wp_version >= 3.8;
		}

		/**
		 * Add compatibility for conditional menus plugin
		 *
		 * @since 2.2
		 */
		public function conditional_menus_restore_theme_location( $location, $new_args, $old_args ) {
			return $old_args['theme_location'];
		}

		/**
		 * Add a note to the Navigation Widget to explain that Max Mega Menu will not work with it.
		 *
		 * @since 2.5.1
		 */
		public function add_notice_to_nav_menu_widget( $widget, $return, $instance ) {
			if ( 'nav_menu' === $widget->id_base ) {
				?>
					<p style="font-size: 11px; font-style: italic;">
						<?php esc_html_e( "If you are trying to display Max Mega Menu here, use the 'Max Mega Menu' widget instead.", 'megamenu' ); ?>
						<a href="https://www.megamenu.com/documentation/widget/" target="_blank"><?php esc_html_e( 'More information', 'megamenu' ); ?></a>
					</p>
				<?php
			}
		}

	}

	add_action( 'plugins_loaded', array( 'Mega_Menu', 'init' ), 10 );

endif;


if ( ! function_exists( 'mmm_get_theme_id_for_location' ) ) {
	/**
	 * Get the menu theme for a specific location
	 *
	 * @since 2.1
	 * @param string $location - theme location identifier.
	 * @return array Menu Theme.
	 */
	function mmm_get_theme_id_for_location( $location = false ) {

		if ( ! $location ) {
			return false;
		}

		if ( ! has_nav_menu( $location ) ) {
			return false;
		}

		// if a location has been passed, check to see if MMM has been enabled for the location.
		$settings = get_option( 'megamenu_settings' );

		if ( is_array( $settings ) && isset( $settings[ $location ]['enabled'] ) && isset( $settings[ $location ]['theme'] ) ) {
			return $settings[ $location ]['theme'];
		}

		return false;
	}
}

if ( ! function_exists( 'mmm_get_theme_for_location' ) ) {
	/**
	 * Get the theme assigned to a specified location
	 *
	 * @since 2.0.2
	 * @param string $location - theme location identifier.
	 */
	function mmm_get_theme_for_location( $location = false ) {

		if ( ! $location ) {
			return false;
		}

		if ( ! has_nav_menu( $location ) ) {
			return false;
		}

		// if a location has been passed, check to see if MMM has been enabled for the location.
		$settings = get_option( 'megamenu_settings' );

		$style_manager = new Mega_Menu_Style_Manager();

		$themes = $style_manager->get_themes();

		if ( is_array( $settings ) && isset( $settings[ $location ]['enabled'] ) && isset( $settings[ $location ]['theme'] ) ) {
			$theme = $settings[ $location ]['theme'];

			$menu_theme = isset( $themes[ $theme ] ) ? $themes[ $theme ] : $themes['default'];

			return $menu_theme;
		}

		return $themes['default'];
	}
}


if ( ! function_exists( 'max_mega_menu_is_enabled' ) ) {
	/**
	 * Determines if Max Mega Menu has been enabled for a given menu location.
	 *
	 * Usage:
	 *
	 * Max Mega Menu is enabled:
	 * function_exists( 'max_mega_menu_is_enabled' )
	 *
	 * Max Mega Menu has been enabled for a theme location:
	 * function_exists( 'max_mega_menu_is_enabled' ) && max_mega_menu_is_enabled( $location )
	 *
	 * @since 1.8
	 * @param string $location - theme location identifier.
	 */
	function max_mega_menu_is_enabled( $location = false ) {

		if ( ! $location ) {
			return true; // the plugin is enabled.
		}

		if ( ! has_nav_menu( $location ) ) {
			return false;
		}

		// if a location has been passed, check to see if MMM has been enabled for the location.
		$settings = get_option( 'megamenu_settings' );

		return is_array( $settings ) && isset( $settings[ $location ]['enabled'] ) && $settings[ $location ]['enabled'] == true;
	}
}

if ( ! function_exists( 'max_mega_menu_share_themes_across_multisite' ) ) {
	/**
	 * In the first version of MMM, themes were (incorrectly) shared between all sites in a multi site network.
	 * Themes will not be shared across sites for new users installing v2.4.3 onwards, but they will be shared for existing (older) users.
	 *
	 * @since 2.3.7
	 */
	function max_mega_menu_share_themes_across_multisite() {

		if ( defined( 'MEGAMENU_SHARE_THEMES_MULTISITE' ) && MEGAMENU_SHARE_THEMES_MULTISITE === false ) {
			return false;
		}

		if ( defined( 'MEGAMENU_SHARE_THEMES_MULTISITE' ) && MEGAMENU_SHARE_THEMES_MULTISITE === true ) {
			return true;
		}

		if ( get_option( 'megamenu_multisite_share_themes' ) === 'false' ) { // only exists if initially installed version is 2.4.3+.
			return false;
		}

		return apply_filters( 'megamenu_share_themes_across_multisite', true );

	}
}

if ( ! function_exists( 'max_mega_menu_get_themes' ) ) {
	/**
	 * Return saved themes
	 *
	 * @since 2.3.7
	 */
	function max_mega_menu_get_themes() {

		if ( ! max_mega_menu_share_themes_across_multisite() ) {
			return get_option( 'megamenu_themes' );
		}

		return get_site_option( 'megamenu_themes' );

	}
}

if ( ! function_exists( 'max_mega_menu_save_themes' ) ) {
	/**
	 * Save menu theme
	 *
	 * @since 2.3.7
	 * @param array $themes menu themes.
	 */
	function max_mega_menu_save_themes( $themes ) {

		if ( ! max_mega_menu_share_themes_across_multisite() ) {
			return update_option( 'megamenu_themes', $themes );
		}

		return update_site_option( 'megamenu_themes', $themes );

	}
}

if ( ! function_exists( 'max_mega_menu_save_last_updated_theme' ) ) {
	/**
	 * Save last updated theme
	 *
	 * @since 2.3.7
	 * @param string $theme The ID of the theme.
	 */
	function max_mega_menu_save_last_updated_theme( $theme ) {

		if ( ! max_mega_menu_share_themes_across_multisite() ) {
			return update_option( 'megamenu_themes_last_updated', $theme );
		}

		return update_site_option( 'megamenu_themes_last_updated', $theme );

	}
}

if ( ! function_exists( 'max_mega_menu_get_last_updated_theme' ) ) {
	/**
	 * Return last updated theme
	 *
	 * @since 2.3.7
	 */
	function max_mega_menu_get_last_updated_theme() {

		if ( ! max_mega_menu_share_themes_across_multisite() ) {
			return get_option( 'megamenu_themes_last_updated' );
		}

		return get_site_option( 'megamenu_themes_last_updated' );

	}
}

if ( ! function_exists( 'max_mega_menu_get_toggle_blocks' ) ) {
	/**
	 * Return saved toggle blocks
	 *
	 * @since 2.3.7
	 */
	function max_mega_menu_get_toggle_blocks() {

		if ( ! max_mega_menu_share_themes_across_multisite() ) {
			return get_option( 'megamenu_toggle_blocks' );
		}

		return get_site_option( 'megamenu_toggle_blocks' );

	}
}

if ( ! function_exists( 'max_mega_menu_save_toggle_blocks' ) ) {
	/**
	 * Save toggle blocks
	 *
	 * @since 2.3.7
	 */
	function max_mega_menu_save_toggle_blocks( $saved_blocks ) {

		if ( ! max_mega_menu_share_themes_across_multisite() ) {
			return update_option( 'megamenu_toggle_blocks', $saved_blocks );
		}

		return update_site_option( 'megamenu_toggle_blocks', $saved_blocks );

	}
}

if ( ! function_exists( 'max_mega_menu_delete_themes' ) ) {
	/**
	 * Delete saved themes
	 *
	 * @since 2.3.7
	 */
	function max_mega_menu_delete_themes() {

		if ( ! max_mega_menu_share_themes_across_multisite() ) {
			return delete_option( 'megamenu_themes' );
		}

		return delete_site_option( 'megamenu_themes' );

	}
}

if ( ! function_exists( 'max_mega_menu_get_active_caching_plugins' ) ) {
	/**
	 * Return list of active caching/CDN/minification plugins
	 *
	 * @since  2.4
	 * @return array
	 */
	function max_mega_menu_get_active_caching_plugins() {

		$caching_plugins = apply_filters(
			'megamenu_caching_plugins',
			array(
				'litespeed-cache/litespeed-cache.php',
				'js-css-script-optimizer/js-css-script-optimizer.php',
				'merge-minify-refresh/merge-minify-refresh.php',
				'minify-html-markup/minify-html.php',
				'simple-cache/simple-cache.php',
				'w3-total-cache/w3-total-cache.php',
				'wp-fastest-cache/wpFastestCache.php',
				'wp-speed-of-light/wp-speed-of-light.php',
				'wp-super-cache/wp-cache.php',
				'wp-super-minify/wp-super-minify.php',
				'autoptimize/autoptimize.php',
				'bwp-minify/bwp-minify.php',
				'cache-enabler/cache-enabler.php',
				'cloudflare/cloudflare.php',
				'comet-cache/comet-cache.php',
				'css-optimizer/bpminifycss.php',
				'fast-velocity-minify/fvm.php',
				'hyper-cache/plugin.php',
				'remove-query-strings-littlebizzy/remove-query-strings.php',
				'remove-query-strings-from-static-resources/remove-query-strings.php',
				'query-strings-remover/query-strings-remover.php',
				'wp-rocket/wp-rocket.php',
				'hummingbird-performance/wp-hummingbird.php',
				'breeze/breeze.php',
			)
		);

		$active_plugins = array();

		foreach ( $caching_plugins as $plugin_path ) {
			if ( is_plugin_active( $plugin_path ) ) {
				$plugin_data      = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );
				$active_plugins[] = $plugin_data['Name'];
			}
		}

		return $active_plugins;
	}
}
