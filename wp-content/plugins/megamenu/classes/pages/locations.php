<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access.
}

if ( ! class_exists( 'Mega_Menu_Locations' ) ) :

	/**
	 * Handles the Mega Menu > Menu Settings page
	 */
	class Mega_Menu_Locations {

		/**
		 * Constructor
		 *
		 * @since 2.8
		 */
		public function __construct() {
			add_action( 'admin_post_megamenu_add_menu_location', array( $this, 'add_menu_location' ) );
			add_action( 'admin_post_megamenu_delete_menu_location', array( $this, 'delete_menu_location' ) );
			add_action( 'admin_post_megamenu_save_menu_location', array( $this, 'save_menu_location' ) );

			add_action( 'admin_post_megamenu_sandbox', array( $this, 'sandbox' ) );
			add_action( 'wp_print_scripts', array( $this, 'sandbox_remove_unnecessary_scripts' ) );
			add_action( 'wp_print_styles', array( $this, 'sandbox_remove_unnecessary_styles' ) );

			add_filter( 'megamenu_menu_tabs', array( $this, 'add_locations_tab' ), 1 );
			add_action( 'megamenu_page_menu_locations', array( $this, 'menu_locations_page' ) );
		}


		/**
		 * Add the Menu Locations tab to our available tabs
		 *
		 * @param array $tabs array of available tabs.
		 * @since 2.8
		 */
		public function add_locations_tab( $tabs ) {
			$tabs['menu_locations'] = __( 'Menu Locations', 'megamenu' );
			return $tabs;
		}


		/**
		 * Add a new menu location.
		 *
		 * @since 2.8
		 */
		public function add_menu_location() {
			check_admin_referer( 'megamenu_add_menu_location' );

			$locations            = get_option( 'megamenu_locations' );
			$next_id              = $this->get_next_menu_location_id();
			$new_menu_location_id = 'max_mega_menu_' . $next_id;

			$title = 'Max Mega Menu Location ' . $next_id;

			if ( isset( $_POST['title'] ) ) {
				$title = esc_attr( wp_unslash( $_POST['title'] ) );
			}

			$locations[ $new_menu_location_id ] = esc_attr( $title );

			update_option( 'megamenu_locations', $locations );

			$menu_id = 0;

			if ( isset( $_POST['menu_id'] ) ) {
				$menu_id = absint( $_POST['menu_id'] );
			}

			if ( $menu_id > 0 ) {
				$locations = get_theme_mod( 'nav_menu_locations' );

				$locations[ $new_menu_location_id ] = $menu_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}

			do_action( 'megamenu_after_add_menu_location' );

			$redirect_url = add_query_arg(
				array(
					'page'           => 'maxmegamenu',
					'location_added' => 'true',
					'location'       => $new_menu_location_id,
				),
				admin_url( 'admin.php' )
			);

			$this->redirect( $redirect_url );

		}


		/**
		 * Delete a menu location.
		 *
		 * @since 2.8
		 */
		public function delete_menu_location() {
			check_admin_referer( 'megamenu_delete_menu_location' );

			$locations          = get_option( 'megamenu_locations' );
			$location_to_delete = esc_attr( $_GET['location'] );

			if ( isset( $locations[ $location_to_delete ] ) ) {
				unset( $locations[ $location_to_delete ] );
				update_option( 'megamenu_locations', $locations );
			}

			do_action( 'megamenu_after_delete_menu_location' );
			do_action( 'megamenu_delete_cache' );

			$redirect_url = add_query_arg(
				array(
					'page'            => 'maxmegamenu',
					'delete_location' => 'true',
				),
				admin_url( 'admin.php' )
			);

			$this->redirect( $redirect_url );
		}

		/**
		 * Save a menu location
		 *
		 * @since 2.0
		 */
		public function save_menu_location() {
			check_admin_referer( 'megamenu_save_menu_location' );

			$location = false;

			if ( isset( $_POST['location'] ) ) {
				$location = esc_attr( $_POST['location'] );
			}

			if ( $location ) {
				$submitted_settings = apply_filters( 'megamenu_submitted_settings_meta', $_POST['megamenu_meta'] );

				if ( isset( $submitted_settings[ $location ]['enabled'] ) ) {
					$submitted_settings[ $location ]['enabled'] = '1';
				}

				if ( ! isset( $submitted_settings[ $location ]['unbind'] ) ) {
					$submitted_settings[ $location ]['unbind'] = 'disabled';
				}

				if ( ! isset( $submitted_settings[ $location ]['descriptions'] ) ) {
					$submitted_settings[ $location ]['descriptions'] = 'disabled';
				}

				if ( ! isset( $submitted_settings[ $location ]['prefix'] ) ) {
					$submitted_settings[ $location ]['prefix'] = 'disabled';
				}

				if ( ! get_option( 'megamenu_settings' ) ) {
					update_option( 'megamenu_settings', $submitted_settings );
				} else {
					$existing_settings = get_option( 'megamenu_settings' );
					$new_settings      = array_merge( $existing_settings, $submitted_settings );

					update_option( 'megamenu_settings', $new_settings );
				}

				do_action( 'megamenu_after_save_settings' );
				do_action( 'megamenu_delete_cache' );
			}

			/* Save custom location description **/
			if ( isset( $_POST['custom_location'] ) && is_array( $_POST['custom_location'] ) ) {
				$custom_location = array_map( 'sanitize_text_field', $_POST['custom_location'] );
				$locations       = get_option( 'megamenu_locations' );
				$new_locations   = array_merge( (array) $locations, $custom_location );

				update_option( 'megamenu_locations', $new_locations );
			}

			$args =	array(
				'page'          => 'maxmegamenu',
				'location'      => urlencode( $location ),
				'save_location' => 'true',
			);

			if ( ! isset( $submitted_settings[ $location ]['enabled'] ) ) {
				unset( $args['location'] );
			}

			$redirect_url = add_query_arg(
				$args,
				admin_url( 'admin.php' )
			);

			$this->redirect( $redirect_url );
		}

		/**
		 * Redirect and exit
		 *
		 * @since 2.8
		 */
		public function redirect( $url ) {
			wp_redirect( $url );
			exit;
		}


		/**
		 * Returns the next available menu location ID
		 *
		 * @since 2.8
		 */
		public function get_next_menu_location_id() {
			$last_id = 0;

			if ( $locations = get_option( 'megamenu_locations' ) ) {
				foreach ( $locations as $key => $value ) {
					if ( strpos( $key, 'max_mega_menu_' ) !== false ) {
						$parts   = explode( '_', $key );
						$menu_id = end( $parts );

						if ( $menu_id > $last_id ) {
							$last_id = $menu_id;
						}
					}
				}
			}

			$next_id = $last_id + 1;

			return $next_id;
		}


		/**
		 * Content for Menu Locations page
		 *
		 * @since 2.8
		 */
		public function menu_locations_page( $saved_settings ) {
			if ( isset( $_GET['add_location'] ) ) {
				$this->add_location_page();
				return;
			}

			$all_locations = $this->get_registered_locations();
			$enabled_locations = array();
			$disabled_locations = array();

			foreach ( $all_locations as $id => $description ) {
				if ( max_mega_menu_is_enabled( $id ) ) {
					$enabled_locations[ $id ] = $description;
				} else {
					$disabled_locations[ $id ] = $description;
				}
			}
			?>

			<div class='menu_settings menu_settings_menu_locations'>

				<?php $this->print_messages(); ?>

				<h3 class='first'><?php esc_html_e( 'Menu Locations', 'megamenu' ); ?></h3>

				<table>
					<tr>
						<td class='mega-name'>
							<?php esc_html_e( 'Registered Menu Locations', 'megamenu' ); ?>
							<div class='mega-description'>
								<p><?php esc_html_e( 'This is an overview of the menu locations supported by your theme.', 'megamenu' ); ?></p>
								<p><?php esc_html_e( 'Use these options to enable Max Mega Menu and define the behaviour of each menu location.', 'megamenu' ); ?></p>
							</div>
						</td>
						<td class='mega-value mega-vartical-align-top'>
							<?php

							if ( ! count( $enabled_locations + $disabled_locations ) ) {
								echo '<p>';
								esc_html_e( 'Your theme does not natively support menus, but you can add a new menu location using Max Mega Menu and display the menu using the Max Mega Menu widget or shortcode.', 'megamenu' );
								echo '</p>';
							}

							echo "<div class='mega-enabled-locations'>";
							if ( count( $enabled_locations ) ) {
								foreach ( $enabled_locations as $location => $description ) {
									$this->show_location_accordion_header( $all_locations, $location, $description, $saved_settings );
								}
							}
							echo "</div>";

							echo "<div class='mega-disabled-locations'>";
							if ( count( $disabled_locations ) ) {
								foreach ( $disabled_locations as $location => $description ) {
									$this->show_location_accordion_header( $all_locations, $location, $description, $saved_settings );
								}
							}
							echo "</div>";

							$add_location_url = esc_url(
								add_query_arg(
									array(
										'page'         => 'maxmegamenu',
										'add_location' => 'true',
									),
									admin_url( 'admin.php' )
								)
							);

							echo "<p><a class='button button-secondary mega-add-location' href='{$add_location_url}'>" . esc_html__( 'Add another menu location', 'megamenu' ) . '</a></p>';

							?>

						</td>
					</tr>
				</table>

				<?php do_action( 'megamenu_menu_locations', $saved_settings ); ?>

			</div>

			<?php
		}

		/**
		 * Output the HTML for a location accordion header
		 *
		 * @param array $locations All available locations
		 * @param array $location the current location
		 * @param string $description the location description
		 */
		private function show_location_accordion_header( $locations, $location, $description, $saved_settings ) {
			$open_class       = ( isset( $_GET['location'] ) && $_GET['location'] === $location ) ? ' mega-accordion-open' : '';
			$is_enabled_class = 'mega-location-disabled';
			$tooltip          = '';

			if ( max_mega_menu_is_enabled( $location ) ) {
				$is_enabled_class = 'mega-location-enabled';
			} elseif ( ! has_nav_menu( $location ) ) {
				$is_enabled_class .= ' mega-location-disabled-assign-menu';
			}

			$has_active_location_class = '';

			$active_instance = 0;

			if ( isset( $saved_settings[ $location ]['active_instance'] ) ) {
				$active_instance = $saved_settings[ $location ]['active_instance'];
			} elseif ( isset( $saved_settings['instances'][ $location ] ) ) {
				$active_instance = $saved_settings['instances'][ $location ];
			}

			if ( $active_instance > 0 ) {
				$has_active_location_class = ' mega-has-active-location';
				$tooltip                   = __( 'Active for Instance' ) . ' ' . esc_attr( $active_instance );
			}

			?>

			<div class='mega-location <?php echo esc_attr( $is_enabled_class ); ?><?php echo esc_attr( $has_active_location_class ); ?>'>
				<div class='mega-accordion-title<?php echo esc_attr( $open_class ); ?>'>
					<span class='dashicons dashicons-location'></span>
					<h4><?php echo esc_html( $description ); ?></h4>
					<?php

					$tooltip_attr = '';

					if ( strlen( $tooltip ) > 0 ) {
						$tooltip_attr = " data-tooltip='{$tooltip}'";
					}
					?>
					<span class='mega-tooltip'<?php echo $tooltip_attr; ?>>
						<span class='dashicons dashicons-yes'></span>
					</span>

					<div class='mega-ellipsis'>
						<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
							<path d="M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"></path>
						</svg>
						<ul class='mega-ellipsis-content'>
							<li><?php echo $this->assigned_menu_link( $location ); ?></li>
							<li><?php echo $this->sandbox_link( $location ); ?></li>
							<?php
							if ( strpos( $location, 'max_mega_menu_' ) !== false ) {
								echo '<li>' . $this->delete_location_link( $location ) . '</li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div class='mega-accordion-content'>
					<?php
						// if no menu has been assigned to the location
					if ( ! has_nav_menu( $location ) ) {
						echo "<p class='mega-warning'><span class='dashicons dashicons-warning'></span>";
						echo " <a href='" . admin_url( 'nav-menus.php?action=locations' ) . "'>" . esc_html__( 'Assign a menu', 'megamenu' ) . '</a> ';
						echo __( 'to this location to enable these options.', 'megamenu' );
						echo '</p>';
					} else {
						$this->show_menu_locations_options( $locations, $location, $description );
					}
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Content for Menu Locations page
		 *
		 * @since 2.8
		 */
		public function add_location_page() {
			?>

			<div class='menu_settings menu_settings_add_location'>

				<h3 class='first'><?php esc_html_e( 'Add Menu Location', 'megamenu' ); ?></h3>

				<form action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="megamenu_add_menu_location" />
					<?php wp_nonce_field( 'megamenu_add_menu_location' ); ?>

					<table>
						<tr>
							<td class='mega-name'>
								<?php esc_html_e( 'Location Name', 'megamenu' ); ?>
								<div class='mega-description'>
									<p><?php esc_html_e( 'Give the location a name that describes where the menu will be displayed on your site.', 'megamenu' ); ?></p>
								</div>
							</td>
							<td class='mega-value mega-vartical-align-top'>
								<input class='wide' type='text' name='title' required='required' placeholder='<?php esc_attr_e( 'E.g. Footer, Blog Sidebar, Header', 'megamenu' ); ?>' />
							</td>
						</tr>
						<tr>
							<td class='mega-name'>
								<?php esc_html_e( 'Assign a menu', 'megamenu' ); ?>
								<div class='mega-description'>
									<p><?php esc_html_e( 'Select a menu to be assigned to this location. This can be changed later using the Appearance > Menus > Manage Location page.', 'megamenu' ); ?></p>
								</div>
							</td>
							<td class='mega-value mega-vartical-align-top'>
								<?php

								$menus = wp_get_nav_menus();

								if ( count( $menus ) ) {
									foreach ( $menus as $menu ) {
										echo '<div class="mega-radio-row"><input type="radio" id="' . esc_attr( $menu->slug ) . '" name="menu_id" value="' . esc_attr( $menu->term_id ) . '" /><label for="' . esc_attr( $menu->slug ) . '">' . esc_attr( $menu->name ) . '</label></div>';
									}
								}

								echo '<div class="mega-radio-row"><input checked="checked" type="radio" id="0" name="menu_id" value="0" /><label for="0">' . esc_html__( "Skip - I'll assign a menu later", 'megamenu' ) . '</label></div>';
								?>
							</td>
						</tr>
					</table>
					<?php submit_button( __( 'Add menu location', 'megamenu' ) ); ?>
				</form>
			</div>

			<?php
		}


		/**
		 * Display a link showing the menu assigned to the specified location
		 *
		 * @param string $location
		 * @since 2.8
		 */
		public function assigned_menu_link( $location ) {
			$menu_id = $this->get_menu_id_for_location( $location );

			if ( $menu_id ) {
				return "<a href='" . admin_url( "nav-menus.php?action=edit&menu={$menu_id}" ) . "'><span class='dashicons dashicons-menu-alt2'></span>" . esc_html( $this->get_menu_name_for_location( $location ) ) . '</a>';
			} else {
				return "<a href='" . admin_url( 'nav-menus.php?action=locations' ) . "'><span class='dashicons dashicons-menu-alt2'></span>" . esc_html__( 'Assign a menu', 'megamenu' ) . '</a>';
			}
		}

		/**
		 * Display a link showing the menu assigned to the specified location
		 *
		 * @param string $location
		 * @since 2.8
		 */
		public function sandbox_link( $location ) {
			return "<a target='megamenu_sandbox' href='" . admin_url( "admin-post.php?action=megamenu_sandbox&location={$location}" ) . "'><span class='dashicons dashicons-external'></span>" . esc_html__( 'View in Sandbox', 'megamenu' ) . '</a>';
		}


		/**
		 * Display a link showing the menu assigned to the specified location
		 *
		 * @param string $location
		 * @since 2.8
		 */
		public function delete_location_link( $location ) {
			$delete_location_url = esc_url(
				add_query_arg(
					array(
						'action'   => 'megamenu_delete_menu_location',
						'location' => $location,
					),
					wp_nonce_url( admin_url( 'admin-post.php' ), 'megamenu_delete_menu_location' )
				)
			);

			return "<a class='confirm' href='{$delete_location_url}'><span class='dashicons dashicons-trash'></span>" . esc_html__( 'Delete location', 'megamenu' ) . '</a>';
		}

		/**
		 * Remove unnecessary scripts from the sandbox page
		 *
		 * @since 2.9
		 */
		public function sandbox_remove_unnecessary_scripts() {
			if ( isset( $_GET['action'] ) && $_GET['action'] === 'megamenu_sandbox' ) {
				global $wp_scripts;

				$queue_items       = $wp_scripts->queue;
				$wp_scripts->queue = array();

				do_action( 'megamenu_enqueue_scripts' );
			}
		}

		/**
		 * Remove unnecessary styles from the sandbox page
		 *
		 * @since 2.9
		 */
		public function sandbox_remove_unnecessary_styles() {
			if ( isset( $_GET['action'] ) && $_GET['action'] === 'megamenu_sandbox' ) {
				global $wp_styles;

				$queue_items      = $wp_styles->queue;
				$wp_styles->queue = array();

				do_action( 'megamenu_enqueue_styles' );
			}
		}


		/**
		 * Content for Sandbox page
		 *
		 * @since 2.9
		 */
		public function sandbox() {
			remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
			remove_action( 'wp_head', '_admin_bar_bump_cb' );

			if ( isset( $_GET['location'] ) ) {
				$location = esc_attr( $_GET['location'] );

				?>
				<!DOCTYPE html>
				<html>
					<head>
						<title>Sandbox: <?php echo $location; ?></title>
						<style type='text/css'>
							body, html {
								margin: 0;
								padding: 0;
								min-height: 200vh;
							}
							body {
								font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
								background-image:
									linear-gradient(45deg, #eee 25%, transparent 25%), 
									linear-gradient(135deg, #eee 25%, transparent 25%),
									linear-gradient(45deg, transparent 75%, #eee 75%),
									linear-gradient(135deg, transparent 75%, #eee 75%);
								background-size:25px 25px;
								background-position:0 0, 12.5px 0, 12.5px -12.5px, 0px 12.5px;
							}
							#query-monitor-main {
								display: none;
							}
							.menu_wrapper {
								max-width: 1280px; 
								margin: 0 auto;
								margin-top: 20px;
							}
						</style>
						<?php wp_head(); ?>
					</head>
					<body>
						<div class='menu_wrapper'>
							<?php echo do_shortcode( "[maxmegamenu location={$location}]" ); ?>
						</div>
						<?php wp_footer(); ?>
					</body>
				</html>
				<?php
			}

			die();
		}

		/**
		 * Content for Menu Location options
		 *
		 * @since 2.8
		 */
		public function show_menu_locations_options( $all_locations, $location, $description ) {

			$menu_id            = $this->get_menu_id_for_location( $location );
			$is_custom_location = strpos( $location, 'max_mega_menu_' ) !== false;
			$plugin_settings    = get_option( 'megamenu_settings' );
			$location_settings  = isset( $plugin_settings[ $location ] ) ? $plugin_settings[ $location ] : array();

			?>

			<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
				<input type="hidden" name="action" value="megamenu_save_menu_location" />
				<input type="hidden" name="location" value="<?php echo esc_attr( $location ); ?>" />
				<?php wp_nonce_field( 'megamenu_save_menu_location' ); ?>
				<?php

					$settings = apply_filters(
						'megamenu_location_settings',
						array(

							'general'        => array(
								'priority' => 10,
								'title'    => __( 'General Settings', 'megamenu' ),
								'settings' => array(
									'enabled'       => array(
										'priority'    => 10,
										'title'       => __( 'Enabled', 'megamenu' ),
										'description' => __( 'Enable Max Mega Menu for this menu location?', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'checkbox_enabled',
												'key'   => 'enabled',
												'value' => isset( $location_settings['enabled'] ) ? $location_settings['enabled'] : 0,
											),
										),
									),
									'event'         => array(
										'priority'    => 20,
										'title'       => __( 'Event', 'megamenu' ),
										'description' => __( 'Select the event to trigger sub menus', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'event',
												'key'   => 'event',
												'value' => isset( $location_settings['event'] ) ? $location_settings['event'] : 'hover',
											),
										),
									),
									'effect'        => array(
										'priority'    => 30,
										'title'       => __( 'Effect', 'megamenu' ),
										'description' => __( 'Select the sub menu animation type', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'effect',
												'key'   => 'effect',
												'value' => isset( $location_settings['effect'] ) ? $location_settings['effect'] : 'fade_up',
												'title' => __( 'Animation' ),
											),
											array(
												'type'  => 'effect_speed',
												'key'   => 'effect_speed',
												'value' => isset( $location_settings['effect_speed'] ) ? $location_settings['effect_speed'] : '200',
												'title' => __( 'Speed' ),
											),
										),
									),
									'effect_mobile' => array(
										'priority'    => 40,
										'title'       => __( 'Effect (Mobile)', 'megamenu' ),
										'description' => __( 'Choose a style for your mobile menu', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'effect_mobile',
												'key'   => 'effect_mobile',
												'value' => isset( $location_settings['effect_mobile'] ) ? $location_settings['effect_mobile'] : 'none',
												'title' => __( 'Style' ),
											),
											array(
												'type'  => 'effect_speed_mobile',
												'key'   => 'effect_speed_mobile',
												'value' => isset( $location_settings['effect_speed_mobile'] ) ? $location_settings['effect_speed_mobile'] : '200',
												'title' => __( 'Speed' ),
											),
										),
									),
									'theme'         => array(
										'priority'    => 50,
										'title'       => __( 'Theme', 'megamenu' ),
										'description' => __( 'Select a theme to be applied to the menu', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'theme_selector',
												'key'   => 'theme',
												'value' => isset( $location_settings['theme'] ) ? $location_settings['theme'] : 'default',
											),
										),
									),
								),
							),
							'advanced'       => array(
								'priority' => 25,
								'title'    => __( 'Advanced', 'megamenu' ),
								'settings' => array(
									'click_behaviour'  => array(
										'priority'    => 10,
										'title'       => __( 'Click Event Behaviour', 'megamenu' ),
										'description' => __( "Define what should happen when the event is set to 'click'. This also applies to mobiles.", 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'click_behaviour',
												'key'   => 'click_behaviour',
												'value' => $plugin_settings,
											),
										),
									),
									'mobile_behaviour' => array(
										'priority'    => 20,
										'title'       => __( 'Mobile Sub Menu Behaviour', 'megamenu' ),
										'description' => __( 'Define the sub menu toggle behaviour for the mobile menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'mobile_behaviour',
												'key'   => 'mobile_behaviour',
												'value' => $plugin_settings,
											),
										),
									),
									'mobile_state' => array(
										'priority'    => 20,
										'title'       => __( 'Mobile Sub Menu Default State', 'megamenu' ),
										'description' => __( 'Define the default state of the sub menus when the mobile menu is visible.', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'mobile_state',
												'key'   => 'mobile_state',
												'value' => $plugin_settings,
											),
										),
									),
									'descriptions'     => array(
										'priority'    => 20,
										'title'       => __( 'Menu Item Descriptions', 'megamenu' ),
										'description' => __( 'Enable output of menu item descriptions.', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'descriptions',
												'key'   => 'descriptions',
												'value' => $plugin_settings,
											),
										),
									),
									'unbind'           => array(
										'priority'    => 20,
										'title'       => __( 'Unbind JavaScript Events', 'megamenu' ),
										'description' => __( 'To avoid conflicts with theme menu systems, JavaScript events which have been added to menu items will be removed by default.', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'unbind',
												'key'   => 'unbind',
												'value' => $plugin_settings,
											),
										),
									),
									'prefix'           => array(
										'priority'    => 20,
										'title'       => __( 'Prefix Menu Item Classes', 'megamenu' ),
										'description' => __( "Prefix custom menu item classes with 'mega-'?", 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'prefix',
												'key'   => 'prefix',
												'value' => $plugin_settings,
											),
										),
									),
									'container'        => array(
										'priority'    => 20,
										'title'       => __( 'Container', 'megamenu' ),
										'description' => __( 'Use nav or div element for menu wrapper?', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'container',
												'key'   => 'container',
												'value' => $plugin_settings,
											),
										),
									),
									'active_instance'  => array(
										'priority'    => 30,
										'title'       => __( 'Active Menu Instance', 'megamenu' ),
										'info'        => array( __( '0: Apply to all instances. 1: Apply to first instance. 2: Apply to second instance', 'megamenu' ) . '…' ),
										'description' => __( 'Some themes will output this menu location multiple times on the same page. For example, it may be output once for the main menu, then again for the mobile menu. This setting can be used to make sure Max Mega Menu is only applied to one of those instances.', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'active_instance',
												'key'   => 'active_instance',
												'value' => $plugin_settings,
											),
										),
									),
								),
							),
							'output_options' => array(
								'priority' => 30,
								'title'    => __( 'Display Options', 'megamenu' ),
								'settings' => array(
									'location_php_function' => array(
										'priority'    => 10,
										'title'       => __( 'PHP Function', 'megamenu' ),
										'description' => __( 'For use in a theme template (usually header.php)', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'location_php_function',
												'key'   => 'location_php_function',
												'value' => $location,
											),
										),
									),
									'location_shortcode' => array(
										'priority'    => 20,
										'title'       => __( 'Shortcode', 'megamenu' ),
										'description' => __( 'For use in a post or page.', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'location_shortcode',
												'key'   => 'location_shortcode',
												'value' => $location,
											),
										),
									),
									'location_widget'    => array(
										'priority'    => 30,
										'title'       => __( 'Widget', 'megamenu' ),
										'description' => __( 'For use in a widget area.', 'megamenu' ),
										'settings'    => array(
											array(
												'type'  => 'location_widget',
												'key'   => 'location_widget',
												'value' => $location,
											),
										),
									),
								),
							),
						),
						$location,
						$plugin_settings
					);

				if ( $is_custom_location ) {

					$settings['general']['settings']['location_description'] = array(
						'priority'    => 15,
						'title'       => __( 'Location Description', 'megamenu' ),
						'description' => __( 'Update the custom location description', 'megamenu' ),
						'settings'    => array(
							array(
								'type'  => 'location_description',
								'key'   => 'location_description',
								'value' => $description,
							),
						),
					);
				}

				$initial_version = get_option( 'megamenu_initial_version' );

				if ( $initial_version && version_compare( $initial_version, '2.8', '>' ) ) {
					//unset( $settings['advanced']['settings']['prefix'] ); // users who started out with 2.8.1+ will not see this option.
				}

				echo "<div class='mega-accordion-content-wrapper'>";

				echo "<h2 class='nav-tab-wrapper'>";

				$is_first = true;

				uasort( $settings, array( $this, 'compare_elems' ) );

				foreach ( $settings as $section_id => $section ) {

					if ( $is_first ) {
						$active   = 'nav-tab-active';
						$is_first = false;
					} else {
						$active = '';
					}

					echo "<a class='mega-tab nav-tab {$active}' data-tab='mega-tab-content-{$section_id}'>" . esc_html( $section['title'] ) . '</a>';

				}

				echo '</h2>';

				$is_first = true;

				foreach ( $settings as $section_id => $section ) {

					if ( $is_first ) {
							$display  = 'block';
							$is_first = false;
					} else {
						$display = 'none';
					}

						echo "<div class='mega-tab-content mega-tab-content-{$section_id}' style='display: {$display}'>";

					if ( $section_id == 'output_options' && ! $is_custom_location ) {
						 echo "<p class='mega-warning '><span class='dashicons dashicons-warning'></span>" . __( 'This menu location is registered by your theme. Your theme should already include the code required to display this menu location on your site.', 'megamenu' ) . '</p>';
					}

						echo "<table class='{$section_id}'>";

						// order the fields by priority
						uasort( $section['settings'], array( $this, 'compare_elems' ) );

					foreach ( $section['settings'] as $group_id => $group ) {

						echo "<tr class='" . esc_attr( 'mega-' . $group_id ) . "'>";

						if ( isset( $group['settings'] ) ) {

							echo "<td class='mega-name'>";
							if ( isset( $group['icon'] ) ) {
								echo "<span class='dashicons dashicons-" . esc_html( $group['icon'] ) . "'></span>";
							}
							echo esc_html( $group['title'] );
							echo "<div class='mega-description'>" . esc_html( $group['description'] ) . '</div>';
							echo '</td>';
							echo "<td class='mega-value'>";

							foreach ( $group['settings'] as $setting_id => $setting ) {

								echo "<label class='" . esc_attr( 'mega-' . $setting['key'] ) . "'>";

								if ( isset( $setting['title'] ) ) {
									echo "<span class='mega-short-desc'>" . esc_html( $setting['title'] ) . '</span>';
								}

								switch ( $setting['type'] ) {
									case 'freetext':
										$this->print_location_freetext_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'textarea':
										$this->print_location_textarea_option( $location, $setting['key'] );
										break;
									case 'checkbox_enabled':
										$this->print_location_enabled_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'event':
										$this->print_location_event_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'effect':
										$this->print_location_effect_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'effect_speed':
										$this->print_location_effect_speed_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'effect_mobile':
										$this->print_location_effect_mobile_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'effect_speed_mobile':
										$this->print_location_effect_speed_mobile_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'theme_selector':
										$this->print_location_theme_selector_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'location_description':
										$this->print_location_description_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'checkbox':
										$this->print_location_checkbox_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'location_php_function':
										$this->print_location_php_function_option( $location, $setting['value'] );
										break;
									case 'location_shortcode':
										$this->print_location_shortcode_option( $location, $setting['value'] );
										break;
									case 'location_widget':
										$this->print_location_widget_option( $location, $setting['key'], $setting['value'] );
										break;
									case 'active_instance':
										$this->print_active_instance_option( $location, $setting['value'] );
										break;
									case 'click_behaviour':
										$this->print_click_behaviour_option( $location, $setting['value'] );
										break;
									case 'mobile_behaviour':
										$this->print_mobile_behaviour_option( $location, $setting['value'] );
										break;
									case 'mobile_state':
										$this->print_mobile_state_option( $location, $setting['value'] );
										break;
									case 'container':
										$this->print_container_option( $location, $setting['value'] );
										break;
									case 'descriptions':
										$this->print_descriptions_option( $location, $setting['value'] );
										break;
									case 'unbind':
										$this->print_unbind_option( $location, $setting['value'] );
										break;
									case 'prefix':
										$this->print_prefix_option( $location, $setting['value'] );
										break;
									default:
										do_action( "megamenu_print_location_option_{$setting['type']}", $setting['key'], $this->id );
										break;
								}

								echo '</label>';

							}

							if ( isset( $group['info'] ) ) {
								foreach ( $group['info'] as $paragraph ) {
									echo "<div class='mega-info'>{$paragraph}</div>";
								}
							}

							echo '</td>';
						} else {
							echo "<td colspan='2'><h5>{$group['title']}</h5></td>";
						}
						echo '</tr>';

					}

					if ( $section_id == 'general' ) {
						do_action( 'megamenu_settings_table', $location, $plugin_settings );
					}

						echo '</table>';
						echo '</div>';
				}

				?>
				
				</div>
				<div class='megamenu_submit'>
					<?php submit_button( $text = null ); ?>
				</div>
			</form>

			<?php
		}


		/**
		 * Return a list of all registed menu locations
		 *
		 * @since 2.8
		 * @return array
		 */
		public function get_registered_locations() {
			$all_locations = get_registered_nav_menus();

			// PolyLang - remove auto created/translated menu locations
			if ( function_exists( 'pll_default_language' ) ) {
				$default_lang = pll_default_language( 'name' );

				foreach ( $all_locations as $loc => $description ) {
					if ( false !== strpos( $loc, '___' ) ) {
						// Remove locations created by Polylang
						unregister_nav_menu( $loc );
					} else {
						// Remove the language name appended to the original locations
						register_nav_menu( $loc, str_replace( ' ' . $default_lang, '', $description ) );
					}
				}

				$all_locations = get_registered_nav_menus();
			}

			$locations        = array();
			$custom_locations = get_option( 'megamenu_locations' );

			if ( is_array( $custom_locations ) ) {
				$all_locations = array_merge( $custom_locations, $all_locations );
			}

			if ( count( $all_locations ) ) {
				$megamenu_locations = array();

				// reorder locations so custom MMM locations are listed at the bottom
				foreach ( $all_locations as $location => $val ) {
					if ( strpos( $location, 'max_mega_menu_' ) === false ) {
						$locations[ $location ] = $val;
					} else {
						$megamenu_locations[ $location ] = $val;
					}
				}

				$locations = array_merge( $locations, $megamenu_locations );
			}

			return $locations;
		}


		/**
		 * Returns the menu ID for a specified menu location, defaults to 0
		 *
		 * @since 2.8
		 * @param string $location
		 */
		private function get_menu_id_for_location( $location ) {
			$locations = get_nav_menu_locations();
			$id        = isset( $locations[ $location ] ) ? $locations[ $location ] : 0;
			return $id;
		}


		/**
		 * Returns the menu name for a specified menu location
		 *
		 * @since 2.8
		 * @param string $location
		 */
		private function get_menu_name_for_location( $location ) {
			$id = $this->get_menu_id_for_location( $location );

			$menus = wp_get_nav_menus();

			foreach ( $menus as $menu ) {
				if ( $menu->term_id == $id ) {
					return $menu->name;
				}
			}

			return false;
		}


		/**
		 * Display messages to the user
		 *
		 * @since 2.0
		 */
		public function print_messages() {
			if ( isset( $_GET['location_added'] ) ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php _e( 'New Menu Location Created', 'megamenu' ) ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['delete_location'] ) ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php _e( 'Menu Location Deleted', 'megamenu' ) ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['save_location'] ) ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php _e( 'Menu Location Saved', 'megamenu' ) ?></p>
				</div>
				<?php
			}
		}


		/**
		 * Print a checkbox option for enabling/disabling MMM for a specific location
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_enabled_option( $location, $key, $value ) {
			?>
				<input type='checkbox' name='megamenu_meta[<?php echo esc_attr( $location ); ?>][<?php echo esc_attr( $key ); ?>]' <?php checked( $value, '1' ); ?> />
			<?php
		}


		/**
		 * Print a generic checkbox option
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_checkbox_option( $location, $key, $value ) {
			?>
				<input type='checkbox' value='true' name='megamenu_meta[<?php echo esc_attr( $location ); ?>][<?php echo esc_attr( $key ); ?>]' <?php checked( $value, 'true' ); ?> />
			<?php
		}


		/**
		 * Print the active instance option
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_active_instance_option( $location, $plugin_settings ) {
			$active_instance = 0;

			if ( isset( $plugin_settings[ $location ]['active_instance'] ) ) {
				$active_instance = $plugin_settings[ $location ]['active_instance'];
			} elseif ( isset( $plugin_settings['instances'][ $location ] ) ) {
				$active_instance = $plugin_settings['instances'][ $location ];
			}

			?>
				<input type='text' name='megamenu_meta[<?php echo esc_attr( $location ); ?>][active_instance]' value='<?php echo esc_attr( $active_instance ); ?>' />
			<?php
		}

		/**
		 * Print the click behaviour option
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_click_behaviour_option( $location, $plugin_settings ) {
			$second_click = 'go';

			if ( isset( $plugin_settings[ $location ]['second_click'] ) ) {
				$second_click = $plugin_settings[ $location ]['second_click'];
			} elseif ( isset( $plugin_settings['second_click'] ) ) {
				$second_click = $plugin_settings['second_click'];
			}

			?>
				<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][second_click]'>
					<option value='close' <?php echo selected( $second_click == 'close' ); ?>><?php _e( 'First click will open a sub menu, second click will close the sub menu.', 'megamenu' ); ?></option>
					<option value='go' <?php echo selected( $second_click == 'go' ); ?>><?php _e( 'First click will open a sub menu, second click will follow the link.', 'megamenu' ); ?></option>
				<select>
			<?php
		}


		/**
		 * Print the mobile menu behaviour option
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_mobile_behaviour_option( $location, $plugin_settings ) {
			$mobile_behaviour = 'standard';

			if ( isset( $plugin_settings[ $location ]['mobile_behaviour'] ) ) {
				$mobile_behaviour = $plugin_settings[ $location ]['mobile_behaviour'];
			} elseif ( isset( $plugin_settings['mobile_behaviour'] ) ) {
				$mobile_behaviour = $plugin_settings['mobile_behaviour'];
			}

			?>
				<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][mobile_behaviour]'>
					<option value='standard' <?php echo selected( $mobile_behaviour == 'standard' ); ?>><?php _e( 'Standard - Open sub menus will remain open until closed by the user.', 'megamenu' ); ?></option>
					<option value='accordion' <?php echo selected( $mobile_behaviour == 'accordion' ); ?>><?php _e( 'Accordion - Open sub menus will automatically close when another one is opened.', 'megamenu' ); ?></option>
				<select>
			<?php
		}

		/**
		 * Print the mobile menu behaviour option
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_mobile_state_option( $location, $plugin_settings ) {
			$mobile_state = 'collapse_all';

			if ( isset( $plugin_settings[ $location ]['mobile_state'] ) ) {
				$mobile_state = $plugin_settings[ $location ]['mobile_state'];
			}

			?>
				<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][mobile_state]'>
					<option value='collapse_all' <?php echo selected( $mobile_state == 'collapse_all' ); ?>><?php _e( 'Collapse all', 'megamenu' ); ?></option>
					<option value='expand_all' <?php echo selected( $mobile_state == 'expand_all' ); ?>><?php _e( 'Expand all', 'megamenu' ); ?></option>
					<option value='expand_active' <?php echo selected( $mobile_state == 'expand_active' ); ?>><?php _e( 'Expand active parents', 'megamenu' ); ?></option>
				<select>
			<?php
		}


		/**
		 * Print the container option select box
		 *
		 * @since 2.9
		 * @param string $key
		 * @param string $value
		 */
		public function print_container_option( $location, $plugin_settings ) {
			$container = 'div';

			if ( isset( $plugin_settings[ $location ]['container'] ) ) {
				$container = $plugin_settings[ $location ]['container'];
			}

			?>
				<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][container]'>
					<option value='div' <?php echo selected( $container == 'div' ); ?>>&lt;div&gt;</option>
					<option value='nav' <?php echo selected( $container == 'nav' ); ?>>&lt;nav&gt;</option>
				<select>
			<?php
		}


		/**
		 * Print the checkbox option for enabling menu item descriptions
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_descriptions_option( $location, $plugin_settings ) {
			$descriptions = 'disabled';

			if ( isset( $plugin_settings[ $location ]['descriptions'] ) ) {
				$descriptions = $plugin_settings[ $location ]['descriptions'];
			} elseif ( isset( $plugin_settings['descriptions'] ) ) {
				$descriptions = $plugin_settings['descriptions'];
			}

			?>

				<input type='checkbox' value='enabled' name='megamenu_meta[<?php echo esc_attr( $location ); ?>][descriptions]' <?php checked( $descriptions, 'enabled' ); ?> />

			<?php
		}


		/**
		 * Print the checkbox option for prefixing menu items with 'mega-'
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_prefix_option( $location, $plugin_settings ) {
			$prefix = 'disabled';

			if ( isset( $plugin_settings[ $location ]['prefix'] ) ) {
				$prefix = $plugin_settings[ $location ]['prefix'];
			} elseif ( isset( $plugin_settings['prefix'] ) ) {
				$prefix = $plugin_settings['prefix'];
			}

			?>

				<input type='checkbox' value='enabled' name='megamenu_meta[<?php echo esc_attr( $location ); ?>][prefix]' <?php checked( $prefix, 'enabled' ); ?> />

			<?php
		}


		/**
		 * Print the checkbox option for the Unbind JavaScript Events option
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_unbind_option( $location, $plugin_settings ) {

			$unbind = 'enabled';

			if ( isset( $plugin_settings[ $location ]['unbind'] ) ) {
				$unbind = $plugin_settings[ $location ]['unbind'];
			} elseif ( isset( $plugin_settings['unbind'] ) ) {
				$unbind = $plugin_settings['unbind'];
			}

			?>
				
				<input type='checkbox' value='enabled' name='megamenu_meta[<?php echo esc_attr( $location ); ?>][unbind]' <?php checked( $unbind, 'enabled' ); ?> />

			<?php
		}


		/**
		 * Print a select box containing all available sub menu trigger events
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_event_option( $location, $key, $value ) {

			$options = apply_filters(
				'megamenu_event_options',
				array(
					'hover'  => __( 'Hover Intent', 'megamenu' ),
					'hover_' => __( 'Hover', 'megamenu' ),
					'click'  => __( 'Click', 'megamenu' ),
				)
			);

			?>
			<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][<?php echo esc_attr( $key ); ?>]'>
			<?php

			foreach ( $options as $type => $name ) {
				echo "<option value='" . esc_attr( $type ) . "' " . selected( $value, $type, false ) . '>' . esc_html( $name ) . '</option>';
			}

			echo '</select>';

		}

		/**
		 * Print a select box containing all available sub menu animation options
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_effect_option( $location, $key, $value ) {
			?>
			<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][<?php echo esc_attr( $key ); ?>]'>
			<?php

			$selected = strlen( $value ) ? $value : 'fade_up';

			$options = apply_filters(
				'megamenu_transition_effects',
				array(
					'disabled' => array(
						'label'    => __( 'None', 'megamenu' ),
						'selected' => $selected == 'disabled',
					),
					'fade'     => array(
						'label'    => __( 'Fade', 'megamenu' ),
						'selected' => $selected == 'fade',
					),
					'fade_up'  => array(
						'label'    => __( 'Fade Up', 'megamenu' ),
						'selected' => $selected == 'fade_up' || $selected == 'fadeUp',
					),
					'slide'    => array(
						'label'    => __( 'Slide', 'megamenu' ),
						'selected' => $selected == 'slide',
					),
					'slide_up' => array(
						'label'    => __( 'Slide Up', 'megamenu' ),
						'selected' => $selected == 'slide_up',
					),
				),
				$selected
			);

			foreach ( $options as $key => $value ) {
				echo "<option value='" . esc_attr( $key ) . "' " . selected( $value['selected'] ) . '>' . esc_html( $value['label'] ) . '</option>';
			}

			echo '</select>';

		}


		/**
		 * Print a select box containing all available effect speeds (desktop)
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_effect_speed_option( $location, $key, $value ) {
			?>
			<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][<?php echo esc_attr( $key ); ?>]'>
			<?php

			$selected = strlen( $value ) ? $value : '200';

			$options = apply_filters(
				'megamenu_effect_speed',
				array(
					'600' => __( 'Slow', 'megamenu' ),
					'400' => __( 'Med', 'megamenu' ),
					'200' => __( 'Fast', 'megamenu' ),
				),
				$selected
			);

			ksort( $options );

			foreach ( $options as $key => $value ) {
				echo "<option value='" . esc_attr( $key ) . "' " . selected( $selected, $key ) . '>' . esc_html( $value ) . '</option>';
			}

			echo '</select>';

		}


		/**
		 * Print the textbox containing the various mobile menu options
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_effect_mobile_option( $location, $key, $value ) {
			?>
			<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][<?php echo esc_attr( $key ); ?>]'>
			<?php

			$selected = strlen( $value ) ? $value : 'disabled';

			$options = apply_filters(
				'megamenu_transition_effects_mobile',
				array(
					'disabled'    => array(
						'label'    => __( 'None', 'megamenu' ),
						'selected' => $selected == 'disabled',
					),
					'slide'       => array(
						'label'    => __( 'Slide Down', 'megamenu' ),
						'selected' => $selected == 'slide',
					),
					'slide_left'  => array(
						'label'    => __( 'Slide Left (Off Canvas)', 'megamenu' ),
						'selected' => $selected == 'slide_left',
					),
					'slide_right' => array(
						'label'    => __( 'Slide Right (Off Canvas)', 'megamenu' ),
						'selected' => $selected == 'slide_right',
					),
				),
				$selected
			);

			foreach ( $options as $key => $value ) {
				echo "<option value='" . esc_attr( $key ) . "' " . selected( $value['selected'] ) . '>' . esc_html( $value['label'] ) . '</option>';
			}

			echo '</select>';

		}


		/**
		 * Print a select box containing all available effect speeds (mobile)
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_effect_speed_mobile_option( $location, $key, $value ) {
			?>
			<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][<?php echo esc_attr( $key ); ?>]'>
			<?php

			$selected = strlen( $value ) ? $value : '200';

			$options = apply_filters(
				'megamenu_effect_speed_mobile',
				array(
					'600' => __( 'Slow', 'megamenu' ),
					'400' => __( 'Med', 'megamenu' ),
					'200' => __( 'Fast', 'megamenu' ),
				),
				$selected
			);

			ksort( $options );

			foreach ( $options as $key => $value ) {
				echo "<option value='" . esc_attr( $key ) . "' " . selected( $selected, $key ) . '>' . esc_html( $value ) . '</option>';
			}

			echo '</select>';

		}


		/**
		 * Print a select box containing all available menu themes
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_theme_selector_option( $location, $key, $value ) {
			?>
			<select name='megamenu_meta[<?php echo esc_attr( $location ); ?>][<?php echo esc_attr( $key ); ?>]'>
			<?php

			$style_manager  = new Mega_Menu_Style_Manager();
			$themes         = $style_manager->get_themes();
			$selected_theme = strlen( $value ) ? $value : 'default';

			foreach ( $themes as $key => $theme ) {
				$edit_theme_url = esc_url(
					add_query_arg(
						array(
							'page'  => 'maxmegamenu_theme_editor',
							'theme' => $key,
						),
						admin_url( 'admin.php' )
					)
				);

				echo "<option data-url='" . esc_attr($edit_theme_url) . "' value='" . esc_attr( $key ) . "' " . selected( $selected_theme, $key ) . '>' . esc_html( $theme['title'] ) . '</option>';
			}

			echo '</select>';
			echo "<span class='dashicons dashicons-edit megamenu-edit-theme'>";

		}


		/**
		 * Print the textbox containing the sample PHP code to output a menu location
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_php_function_option( $location, $value ) {
			?>
			<textarea readonly="readonly">&lt;?php wp_nav_menu( array( 'theme_location' => '<?php echo esc_attr( $value ); ?>' ) ); ?&gt;</textarea>
			<?php
		}


		/**
		 * Print the textbox containing the sample shortcode to output a menu location
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_shortcode_option( $location, $value ) {
			?>
			<textarea readonly="readonly">[maxmegamenu location=<?php echo esc_attr( $value ); ?>]</textarea>
			<?php
		}


		/**
		 * Print the textbox containing instructions on how to display this menu location using a widget
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_widget_option( $location, $value ) {
			?>
			<textarea readonly="readonly"><?php _e( "Add the 'Max Mega Menu' widget to a widget area.", 'megamenu' ); ?></textarea>
			<?php
		}


		/**
		 * Print a standard text input box
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_freetext_option( $location, $key, $value ) {
			echo "<input class='" . esc_attr( 'mega-setting-' . $key ) . "' type='text' name='megamenu_meta[$location][$key]' value='" . esc_attr( $value ) . "' />";
		}


		/**
		 * Print a text input box allowing the user to change the name of a custom menu location
		 *
		 * @since 2.8
		 * @param string $key
		 * @param string $value
		 */
		public function print_location_description_option( $location, $key, $value ) {
			echo "<input class='" . esc_attr( 'mega-setting-' . $key ) . " wide' type='text' name='custom_location[$location]' value='" . esc_attr( $value ) . "' />";
		}


		/**
		 * Compare array values
		 *
		 * @since 2.8
		 * @param array $elem1
		 * @param array $elem2
		 * @return bool
		 */
		private function compare_elems( $elem1, $elem2 ) {
			return $elem1['priority'] > $elem2['priority'];
		}
	}

endif;
