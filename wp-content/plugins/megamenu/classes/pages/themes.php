<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_Themes' ) ) :

	/**
	 * Handles all admin related functionality.
	 */
	class Mega_Menu_Themes {


		/**
		 * All themes (default and custom)
		 */
		var $themes = array();


		/**
		 * Active theme
		 */
		var $active_theme = array();


		/**
		 * Active theme ID
		 */
		var $id = '';


		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'wp_ajax_megamenu_save_theme', array( $this, 'ajax_save_theme' ) );
			add_action( 'admin_post_megamenu_save_theme', array( $this, 'save_theme' ) );
			add_action( 'admin_post_megamenu_add_theme', array( $this, 'create_theme' ) );
			add_action( 'admin_post_megamenu_delete_theme', array( $this, 'delete_theme' ) );
			add_action( 'admin_post_megamenu_revert_theme', array( $this, 'revert_theme' ) );
			add_action( 'admin_post_megamenu_duplicate_theme', array( $this, 'duplicate_theme' ) );
			add_action( 'admin_post_megamenu_import_theme', array( $this, 'import_theme' ) );

			add_filter( 'megamenu_menu_tabs', array( $this, 'add_themes_tab' ), 2 );
			add_action( 'megamenu_page_theme_editor', array( $this, 'theme_editor_page' ) );

			add_filter( 'wp_code_editor_settings', array( $this, 'codemirror_disable_lint' ), 99 );
		}

		/**
		 * Divi turns on code linting. This turns it off.
		 *
		 * @since 2.8
		 */
		public function codemirror_disable_lint( $settings ) {
			if ( isset( $_GET['page'] ) && 'maxmegamenu_theme_editor' === $_GET['page'] ) { // @codingStandardsIgnoreLine
				$settings['codemirror']['lint']    = false;
				$settings['codemirror']['gutters'] = array();
			}

			return $settings;
		}

		/**
		 * Add the Menu Locations tab to our available tabs
		 *
		 * @param array $tabs
		 * @since 2.8
		 */
		public function add_themes_tab( $tabs ) {
			$tabs['theme_editor'] = __( 'Menu Themes', 'megamenu' );
			return $tabs;
		}

		/**
		 *
		 * @since 1.4
		 */
		public function init() {
			if ( class_exists( 'Mega_Menu_Style_Manager' ) ) {
				$style_manager = new Mega_Menu_Style_Manager();
				$this->themes  = $style_manager->get_themes();

				$last_updated      = max_mega_menu_get_last_updated_theme();
				$preselected_theme = isset( $this->themes[ $last_updated ] ) ? $last_updated : 'default';
				$theme_id          = isset( $_GET['theme'] ) ? sanitize_text_field( $_GET['theme'] ) : $preselected_theme; // @codingStandardsIgnoreLine

				if ( isset( $this->themes[ $theme_id ] ) ) {
					$this->id = $theme_id;
				} else {
					$this->id = $preselected_theme;
				}

				$this->active_theme = $this->themes[ $this->id ];

			}
		}

		/**
		 * Returns the next available custom theme ID
		 *
		 * @since 1.0
		 */
		public function get_next_theme_id() {
			$last_id = 0;

			if ( $saved_themes = max_mega_menu_get_themes() ) {
				foreach ( $saved_themes as $key => $value ) {
					if ( strpos( $key, 'custom_theme' ) !== false ) {
						$parts    = explode( '_', $key );
						$theme_id = end( $parts );

						if ( $theme_id > $last_id ) {
							$last_id = $theme_id;
						}
					}
				}
			}

			$next_id = $last_id + 1;

			return $next_id;
		}


		/**
		 *
		 * @since 2.4.1
		 */
		public function ajax_save_theme() {
			check_ajax_referer( 'megamenu_save_theme' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$style_manager = new Mega_Menu_Style_Manager();

			$test = $style_manager->test_theme_compilation( $this->get_prepared_theme_for_saving() );

			if ( is_wp_error( $test ) ) {
				wp_send_json_error( $test->get_error_message() );
			} else {
				$this->save_theme( true );
				wp_send_json_success( 'Saved' );
			}

			wp_die();
		}


		/**
		 *
		 * @since 2.4.1
		 */
		public function get_prepared_theme_for_saving() {

			$submitted_settings = $_POST['settings'];

			if ( isset( $_POST['checkboxes'] ) ) {
				foreach ( $_POST['checkboxes'] as $checkbox => $value ) {
					if ( isset( $submitted_settings[ $checkbox ] ) ) {
						$submitted_settings[ $checkbox ] = 'on';
					} else {
						$submitted_settings[ $checkbox ] = 'off';
					}
				}
			}

			if ( is_numeric( $submitted_settings['responsive_breakpoint'] ) ) {
				$submitted_settings['responsive_breakpoint'] = $submitted_settings['responsive_breakpoint'] . 'px';
			}

			if ( isset( $submitted_settings['toggle_blocks'] ) ) {
				unset( $submitted_settings['toggle_blocks'] );
			}

			if ( isset( $submitted_settings['panel_width'] ) ) {
				$submitted_settings['panel_width'] = trim( $submitted_settings['panel_width'] );
			}

			if ( isset( $submitted_settings['panel_inner_width'] ) ) {
				$submitted_settings['panel_inner_width'] = trim( $submitted_settings['panel_inner_width'] );
			}

			$theme = array_map( 'esc_attr', $submitted_settings );

			return $theme;
		}


		/**
		 * Save changes to an exiting theme.
		 *
		 * @since 1.0
		 */
		public function save_theme( $is_ajax = false ) {

			check_admin_referer( 'megamenu_save_theme' );

			$theme = esc_attr( $_POST['theme_id'] );

			$saved_themes = max_mega_menu_get_themes();

			if ( isset( $saved_themes[ $theme ] ) ) {
				unset( $saved_themes[ $theme ] );
			}

			$prepared_theme = $this->get_prepared_theme_for_saving();

			$saved_themes[ $theme ] = $prepared_theme;

			max_mega_menu_save_themes( $saved_themes );
			max_mega_menu_save_last_updated_theme( $theme );

			do_action( 'megamenu_after_theme_save' );
			do_action( 'megamenu_delete_cache' );

			if ( ! $is_ajax ) {
				$this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$theme}&saved=true" ) );
				return;
			}

			return $prepared_theme;
		}


		/**
		 * Duplicate an existing theme.
		 *
		 * @since 1.0
		 */
		public function duplicate_theme() {

			check_admin_referer( 'megamenu_duplicate_theme' );

			$this->init();

			$theme = esc_attr( $_GET['theme_id'] );

			$copy = $this->themes[ $theme ];

			$saved_themes = max_mega_menu_get_themes();

			$next_id = $this->get_next_theme_id();

			$copy['title'] = $copy['title'] . ' ' . __( 'Copy', 'megamenu' );

			$new_theme_id = 'custom_theme_' . $next_id;

			$saved_themes[ $new_theme_id ] = $copy;

			max_mega_menu_save_themes( $saved_themes );

			do_action( 'megamenu_after_theme_duplicate' );

			$this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$new_theme_id}&duplicated=true" ) );

		}


		/**
		 * Delete a theme
		 *
		 * @since 1.0
		 */
		public function delete_theme() {

			check_admin_referer( 'megamenu_delete_theme' );

			$theme = esc_attr( $_GET['theme_id'] );

			if ( $this->theme_is_being_used_by_location( $theme ) ) {

				$this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$theme}&deleted=false" ) );
				return;
			}

			$saved_themes = max_mega_menu_get_themes();

			if ( isset( $saved_themes[ $theme ] ) ) {
				unset( $saved_themes[ $theme ] );
			}

			max_mega_menu_save_themes( $saved_themes );

			do_action( 'megamenu_after_theme_delete' );

			do_action( 'megamenu_delete_cache' );

			$this->redirect( admin_url( 'admin.php?page=maxmegamenu_theme_editor&theme=default&deleted=true' ) );

		}


		/**
		 * Revert a theme (only available for default themes, you can't revert a custom theme)
		 *
		 * @since 1.0
		 */
		public function revert_theme() {

			check_admin_referer( 'megamenu_revert_theme' );

			$theme = esc_attr( $_GET['theme_id'] );

			$saved_themes = max_mega_menu_get_themes();

			if ( isset( $saved_themes[ $theme ] ) ) {
				unset( $saved_themes[ $theme ] );
			}

			max_mega_menu_save_themes( $saved_themes );

			do_action( 'megamenu_after_theme_revert' );

			do_action( 'megamenu_delete_cache' );

			$this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$theme}&reverted=true" ) );

		}


		/**
		 * Create a new custom theme
		 *
		 * @since 1.0
		 */
		public function create_theme() {

			check_admin_referer( 'megamenu_create_theme' );

			$this->init();

			$saved_themes = max_mega_menu_get_themes();

			$next_id = $this->get_next_theme_id();

			$new_theme_id = 'custom_theme_' . $next_id;

			$style_manager = new Mega_Menu_Style_Manager();
			$new_theme     = $style_manager->get_default_theme();

			$new_theme['title'] = "Custom {$next_id}";

			$saved_themes[ $new_theme_id ] = $new_theme;

			max_mega_menu_save_themes( $saved_themes );

			do_action( 'megamenu_after_theme_create' );

			$this->redirect( admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$new_theme_id}&created=true" ) );

		}

		/**
		* Duplicate an existing theme.
		*
		* @since 1.8
		*/
		public function import_theme() {
			check_admin_referer( 'megamenu_import_theme' );

			$import = json_decode( stripslashes( $_POST['data'] ), true );

			$sanitized = array();

			foreach ( $import as $key => $value ) {
				if ( $key == 'custom_css' ) {
					$sanitized[ $key ] = sanitize_textarea_field( $value );
				} else {
					$sanitized[ $key ] = sanitize_text_field( $value );
				}
			}

			$import = $sanitized;

			if ( is_array( $import ) ) {
				$saved_themes                  = max_mega_menu_get_themes();
				$next_id                       = $this->get_next_theme_id();
				$import['title']               = $import['title'] . ' ' . __( ' - Imported', 'megamenu' );
				$new_theme_id                  = 'custom_theme_' . $next_id;
				$saved_themes[ $new_theme_id ] = $import;
				max_mega_menu_save_themes( $saved_themes );
				do_action( 'megamenu_after_theme_import' );

				$url = add_query_arg(
					array(
						'page'     => 'maxmegamenu_theme_editor',
						'theme'    => $new_theme_id,
						'imported' => 'true',
					),
					admin_url( 'admin.php' )
				);

			} else {
				$url = add_query_arg(
					array(
						'page'     => 'maxmegamenu_theme_editor',
						'imported' => 'false',
					),
					admin_url( 'admin.php' )
				);
			}

			$this->redirect( $url );
		}


		/**
		 * Redirect and exit
		 *
		 * @since 1.8
		 */
		public function redirect( $url ) {
			wp_redirect( $url );
			exit;
		}


		/**
		 * Checks to see if a certain theme is in use.
		 *
		 * @since 1.0
		 * @param string $theme
		 */
		public function theme_is_being_used_by_location( $theme ) {
			$settings = get_option( 'megamenu_settings' );

			if ( ! $settings ) {
				return false;
			}

			$locations = get_nav_menu_locations();

			$menus = get_registered_nav_menus();

			$theme_in_use_locations = array();

			if ( count( $locations ) ) {

				foreach ( $locations as $location => $menu_id ) {

					if ( has_nav_menu( $location ) && max_mega_menu_is_enabled( $location ) && isset( $settings[ $location ]['theme'] ) && $settings[ $location ]['theme'] == $theme ) {
						$theme_in_use_locations[] = isset( $menus[ $location ] ) ? $menus[ $location ] : $location;
					}
				}

				if ( count( $theme_in_use_locations ) ) {
					return $theme_in_use_locations;
				}
			}

			return false;
		}


		/**
		 * Display messages to the user
		 *
		 * @since 1.0
		 */
		public function print_messages() {

			$this->init();

			$style_manager = new Mega_Menu_Style_Manager();

			$test = $style_manager->test_theme_compilation( $this->active_theme );

			if ( is_wp_error( $test ) ) {
				?>
				<div class="notice notice-error is-dismissible"> 
					<p><?php echo $test->get_error_message(); ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 'false' ) {
				?>
				<div class="notice notice-error is-dismissible"> 
					<p><?php _e( 'Failed to delete theme. The theme is in use by a menu.', 'megamenu' ) ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 'true' ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php _e( 'Theme Deleted', 'megamenu' ) ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['duplicated'] ) ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php _e( 'Theme Duplicated', 'megamenu' ) ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['reverted'] ) ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php _e( 'Theme Reverted', 'megamenu' ) ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['created'] ) ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php _e( "New Theme Created. To apply this theme to a menu location, go to <i>Mega Menu > Menu Locations</i> and select this theme from the 'Theme' dropdown.", 'megamenu' ) ?></p>
				</div>
				<?php
			}

			do_action( 'megamenu_print_messages' );

		}


		/**
		 * Lists the available themes
		 *
		 * @since 1.0
		 */
		public function theme_selector() {

			$list_items = "<select id='theme_selector'>";

			foreach ( $this->themes as $id => $theme ) {

				$locations = $this->theme_is_being_used_by_location( $id );

				$selected = $id == $this->id ? 'selected=selected' : '';

				$list_items .= "<option {$selected} value='" . admin_url( "admin.php?page=maxmegamenu_theme_editor&theme={$id}" ) . "'>";

				$title = $theme['title'];

				if ( is_array( $locations ) ) {
					$title .= ' (' . implode( ', ', $locations ) . ')';
				}

				$list_items .= esc_html( $title );

				$list_items .= '</option>';
			}

			return $list_items .= '</select>';

		}

		/**
		 * Checks to see if a given string contains any of the provided search terms
		 *
		 * @param srgin $key
		 * @param array $needles
		 * @since 1.0
		 */
		private function string_contains( $key, $needles ) {

			foreach ( $needles as $needle ) {

				if ( strpos( $key, $needle ) !== false ) {
					return true;
				}
			}

			return false;

		}


		/**
		 *
		 * @since 2.9
		 */
		public function export_theme() {
			$style_manager = new Mega_Menu_Style_Manager();
			$default_theme = $style_manager->get_default_theme();

			$theme_to_export = $this->active_theme;

			$diff = array();

			foreach ( $default_theme as $key => $value ) {
				if ( isset( $theme_to_export[ $key ] ) && $theme_to_export[ $key ] != $value || $key == 'title' ) {
					$diff[ $key ] = $theme_to_export[ $key ];
				}
			}

			?>

		<div class='menu_settings menu_settings_menu_themes'>
			<h3 class='first'><?php _e( 'Export Theme', 'megamenu' ); ?></h3>
			<table>
				<tr>
					<td class='mega-name'>
						<?php _e( 'JSON Format', 'megamenu' ); ?>
						<div class='mega-description'><?php _e( "Log into the site you wish to import the theme to. Go to Mega Menu > Tools and paste this into the 'Import Theme' text area:", 'megamenu' ); ?></div>
					</td>
					<td class='mega-value'>
						<?php echo "<textarea class='mega-export'>" . sanitize_textarea_field( htmlentities( json_encode( $diff ) ) ) . '</textarea>'; ?>
					</td>
				</tr>
				<tr>
					<td class='mega-name'>
						<?php _e( 'PHP Format', 'megamenu' ); ?>
						<div class='mega-description'><?php _e( 'Paste this code into your themes functions.php file:', 'megamenu' ); ?></div>
					</td>
					<td class='mega-value'>
					   <?php
							$key  = strtolower( str_replace( ' ', '_', $theme_to_export['title'] ) );
							$key .= '_' . time();
							echo "<textarea class='mega-export'>";
							echo 'function megamenu_add_theme_' . $key . '($themes) {';
							echo "\n" . '    $themes["' . $key . '"] = array(';

						foreach ( $diff as $theme_key => $value ) {
							echo "\n        '" . $theme_key . "' => '" . $value . "',";
						}

							echo "\n" . '    );';
							echo "\n" . '    return $themes;';
							echo "\n" . '}';
							echo "\n" . 'add_filter("megamenu_themes", "megamenu_add_theme_' . $key . '");';
							echo '</textarea>';
						?>
					</td>
				</tr>
			</table>
		</div>

			<?php
		}

		public function import_theme_page() {

			?>

		<div class='menu_settings menu_settings_menu_themes'>
			<h3 class='first'><?php _e( 'Import Theme', 'megamenu' ); ?></h3>
			<table>
				<tr>
					<td class='mega-name'>
						<?php _e( 'Import Theme', 'megamenu' ); ?>
						<div class='mega-description'><?php _e( 'Import a menu theme in JSON format', 'megamenu' ); ?></div>
					</td>
					<td class='mega-value'>
					   <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
							<?php wp_nonce_field( 'megamenu_import_theme' ); ?>
							<input type="hidden" name="action" value="megamenu_import_theme" />
							<textarea name='data'></textarea>
							<input type='submit' class='button button-primary' value='<?php _e( 'Import Theme', 'megamenu' ); ?>' />
						</form>
					</td>
				</tr>
			</table>
		</div>

			<?php
		}


		/**
		 * Displays the theme editor form.
		 *
		 * @since 1.0
		 */
		public function theme_editor_page( $saved_settings ) {

			$this->init();

			if ( isset( $_GET['export'] ) ) {
				$this->export_theme();
				return;
			}

			if ( isset( $_GET['import'] ) ) {
				$this->import_theme_page();
				return;
			}

			$create_url = esc_url(
				add_query_arg(
					array(
						'action' => 'megamenu_add_theme',
					),
					wp_nonce_url( admin_url( 'admin-post.php' ), 'megamenu_create_theme' )
				)
			);

			$duplicate_url = esc_url(
				add_query_arg(
					array(
						'action'   => 'megamenu_duplicate_theme',
						'theme_id' => $this->id,
					),
					wp_nonce_url( admin_url( 'admin-post.php' ), 'megamenu_duplicate_theme' )
				)
			);

			$delete_url = esc_url(
				add_query_arg(
					array(
						'action'   => 'megamenu_delete_theme',
						'theme_id' => $this->id,
					),
					wp_nonce_url( admin_url( 'admin-post.php' ), 'megamenu_delete_theme' )
				)
			);

			$revert_url = esc_url(
				add_query_arg(
					array(
						'action'   => 'megamenu_revert_theme',
						'theme_id' => $this->id,
					),
					wp_nonce_url( admin_url( 'admin-post.php' ), 'megamenu_revert_theme' )
				)
			);

			$export_url = esc_url(
				add_query_arg(
					array(
						'page'   => 'maxmegamenu_theme_editor',
						'theme'  => $this->id,
						'export' => 'true',
					),
					admin_url( 'admin.php' )
				)
			);

			$import_url = esc_url(
				add_query_arg(
					array(
						'page'   => 'maxmegamenu_theme_editor',
						'import' => 'true',
					),
					admin_url( 'admin.php' )
				)
			);

			?>

			<?php $this->print_messages(); ?>

		<div class='menu_settings menu_settings_menu_themes'>

			<div class='theme_selector'>
				<?php _e( 'Select theme to edit', 'megamenu' ); ?> <?php echo $this->theme_selector(); ?>
			</div>

			<div class='mega-ellipsis'>
				<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
					<path d="M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"></path>
				</svg>
				<ul class='mega-ellipsis-content'>
					<li class='mega-create-theme'><a href='<?php echo $create_url; ?>'><span class='dashicons dashicons-welcome-add-page'></span><?php _e( 'Add new theme', 'megamenu' ); ?></a></li>
					<li class='mega-duplicate-theme'><a href='<?php echo $duplicate_url; ?>'><span class='dashicons dashicons-images-alt2'></span><?php _e( 'Duplicate theme', 'megamenu' ); ?></a></li>
					<li class='mega-export-theme'><a href='<?php echo $export_url; ?>'><span class='dashicons dashicons-upload'></span><?php _e( 'Export theme', 'megamenu' ); ?></a></li>
					<li class='mega-import-theme'><a href='<?php echo $import_url; ?>'><span class='dashicons dashicons-download'></span><?php _e( 'Import a theme', 'megamenu' ); ?></a></li>
					<?php if ( $this->string_contains( $this->id, array( 'custom' ) ) ) : ?>
						<li class='mega-delete-theme'><a class='delete confirm' href='<?php echo $delete_url; ?>'><span class='dashicons dashicons-trash'></span><?php _e( 'Delete theme', 'megamenu' ); ?></a></li>
					<?php else : ?>
						<li class='mega-revert-theme'><a class='confirm' href='<?php echo $revert_url; ?>'><span class='dashicons dashicons-update-alt'></span><?php _e( 'Revert theme', 'megamenu' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>

			<h3 class='editing_theme'><?php echo __( 'Editing theme', 'megamenu' ) . ': ' . esc_html( $this->active_theme['title'] ); ?></h3>



			<?php

			$saved_settings = get_option( 'megamenu_settings' );

			if ( isset( $saved_settings['css'] ) && $saved_settings['css'] == 'disabled' ) {
				?>
					<div class='fail'><?php _e( 'CSS Output (under Mega Menu > General Settings) has been disabled. Therefore, changes made within the theme editor will not be applied to your menu.', 'megamenu' ); ?></div>
				<?php
			}

			$locations = $this->theme_is_being_used_by_location( $this->id );

			if ( ! $locations && ! isset( $_GET['created'] ) ) {
				?>
					<div class='warning'><?php _e( "This menu theme is not currently active as it has not been applied to any menu locations. You may wish to check you are editing the correct menu theme - you can choose a different theme to edit using the 'Select theme to edit' selector above. Alternatively, to apply this theme to a menu go to <i>Appearance > Menus > Max Mega Menu Settings</i> and select this theme from the 'Theme' dropdown.", 'megamenu' ); ?></div>
				<?php
			}

			?>

			<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" class="theme_editor">
				<input type="hidden" name="theme_id" value="<?php echo esc_attr( $this->id ); ?>" />
				<input type="hidden" name="action" value="megamenu_save_theme" />
				<?php wp_nonce_field( 'megamenu_save_theme' ); ?>

				<?php

					$settings = apply_filters(
						'megamenu_theme_editor_settings',
						array(

							'general'        => array(
								'title'    => __( 'General Settings', 'megamenu' ),
								'settings' => array(
									'title'       => array(
										'priority'    => 10,
										'title'       => __( 'Theme Title', 'megamenu' ),
										'description' => '',
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'title',
											),
										),
									),
									'arrow'       => array(
										'priority'    => 20,
										'title'       => __( 'Arrow', 'megamenu' ),
										'description' => __( 'Select the arrow styles.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Up', 'megamenu' ),
												'type'  => 'arrow',
												'key'   => 'arrow_up',
											),
											array(
												'title' => __( 'Down', 'megamenu' ),
												'type'  => 'arrow',
												'key'   => 'arrow_down',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'arrow',
												'key'   => 'arrow_left',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'arrow',
												'key'   => 'arrow_right',
											),
										),
									),
									'line_height' => array(
										'priority'    => 30,
										'title'       => __( 'Line Height', 'megamenu' ),
										'description' => __( 'Set the general line height to use in the sub menu contents.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'line_height',
											),
										),
									),
									'z_index'     => array(
										'priority'    => 40,
										'title'       => __( 'Z Index', 'megamenu' ),
										'description' => __( 'Set the z-index to ensure the sub menus appear ontop of other content.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'z_index',
												'validation' => 'int',
											),
										),
									),
									'shadow'      => array(
										'priority'    => 50,
										'title'       => __( 'Shadow', 'megamenu' ),
										'description' => __( 'Apply a shadow to mega and flyout menus.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Enabled', 'megamenu' ),
												'type'  => 'checkbox',
												'key'   => 'shadow',
											),
											array(
												'title' => __( 'Horizontal', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'shadow_horizontal',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Vertical', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'shadow_vertical',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Blur', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'shadow_blur',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Spread', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'shadow_spread',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'shadow_color',
											),
										),
									),
									'transitions' => array(
										'priority'    => 60,
										'title'       => __( 'Hover Transitions', 'megamenu' ),
										'description' => __( 'Apply hover transitions to menu items. Note: Transitions will not apply to gradient backgrounds.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Enabled', 'megamenu' ),
												'type'  => 'checkbox',
												'key'   => 'transitions',
											),
										),
									),
									'resets'      => array(
										'priority'    => 70,
										'title'       => __( 'Reset Widget Styling', 'megamenu' ),
										'description' => __( 'Caution: Reset the styling of widgets within the mega menu? This may break the styling of widgets that you have added to your sub menus. Default: Disabled.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Enabled', 'megamenu' ),
												'type'  => 'checkbox',
												'key'   => 'resets',
											),
										),
									),
								),
							),
							'menu_bar'       => array(
								'title'    => __( 'Menu Bar', 'megamenu' ),
								'settings' => array(
									'menu_item_height'     => array(
										'priority'    => 05,
										'title'       => __( 'Menu Height', 'megamenu' ),
										'description' => __( 'Define the height of each top level menu item link. This value plus the Menu Padding (top and bottom) settings define the overall height of the menu bar.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'menu_item_link_height',
												'validation' => 'px',
											),
										),
									),
									'menu_background'      => array(
										'priority'    => 10,
										'title'       => __( 'Menu Background', 'megamenu' ),
										'description' => __( "The background color for the main menu bar. Set each value to transparent for a 'button' style menu.", 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'container_background_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'container_background_to',
											),
										),
									),
									'menu_padding'         => array(
										'priority'    => 20,
										'title'       => __( 'Menu Padding', 'megamenu' ),
										'description' => __( 'Padding for the main menu bar.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'container_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'container_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'container_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'container_padding_left',
												'validation' => 'px',
											),
										),
									),
									'menu_border_radius'   => array(
										'priority'    => 30,
										'title'       => __( 'Menu Border Radius', 'megamenu' ),
										'description' => __( 'Set a border radius on the main menu bar.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'container_border_radius_top_left',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Top Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'container_border_radius_top_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'container_border_radius_bottom_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'container_border_radius_bottom_left',
												'validation' => 'px',
											),
										),
									),
									'top_level_menu_items' => array(
										'priority'    => 50,
										'title'       => __( 'Top Level Menu Items', 'megamenu' ),
										'description' => '',
									),
									'menu_item_align'      => array(
										'priority'    => 55,
										'title'       => __( 'Menu Items Align', 'megamenu' ),
										'description' => __( 'Align <i>all</i> menu items to the left (default), centrally or to the right.', 'megamenu' ),
										'info'        => array( __( "This option will apply to all menu items. To align an individual menu item to the right, edit the menu item itself and set 'Menu Item Align' to 'Right'.", 'megamenu' ) ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'align',
												'key'   => 'menu_item_align',
											),
										),
									),
									'menu_item_font'       => array(
										'priority'    => 60,
										'title'       => __( 'Item Font', 'megamenu' ),
										'description' => __( 'The font to use for each top level menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_link_color',
											),
											array(
												'title' => __( 'Size', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_font_size',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Family', 'megamenu' ),
												'type'  => 'font',
												'key'   => 'menu_item_link_font',
											),
											array(
												'title' => __( 'Transform', 'megamenu' ),
												'type'  => 'transform',
												'key'   => 'menu_item_link_text_transform',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'menu_item_link_weight',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'menu_item_link_text_decoration',
											),
											array(
												'title' => __( 'Align', 'megamenu' ),
												'type'  => 'align',
												'key'   => 'menu_item_link_text_align',
											),
										),
									),
									'menu_item_font_hover' => array(
										'priority'    => 65,
										'title'       => __( 'Item Font (Hover)', 'megamenu' ),
										'description' => __( 'Set the font to use for each top level menu item (on hover).', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_link_color_hover',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'menu_item_link_weight_hover',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'menu_item_link_text_decoration_hover',
											),
										),
									),
									'menu_item_background' => array(
										'priority'    => 70,
										'title'       => __( 'Item Background', 'megamenu' ),
										'description' => __( "The background color for each top level menu item. Tip: Set these values to transparent if you've already set a background color on the menu bar.", 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_background_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_background_to',
											),
										),
									),
									'menu_item_background_hover' => array(
										'priority'    => 75,
										'title'       => __( 'Item Background (Hover)', 'megamenu' ),
										'description' => __( 'The background color for a top level menu item (on hover).', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_background_hover_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_background_hover_to',
											),
										),
									),
									'menu_item_spacing'    => array(
										'priority'    => 80,
										'title'       => __( 'Item Spacing', 'megamenu' ),
										'description' => __( 'Define the size of the gap between each top level menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'menu_item_spacing',
												'validation' => 'px',
											),
										),
									),

									'menu_item_padding'    => array(
										'priority'    => 85,
										'title'       => __( 'Item Padding', 'megamenu' ),
										'description' => __( 'Set the padding for each top level menu item.', 'megamenu' ),
										'info'        => array( __( "Generally we advise against using the Top and Bottom options here. Use the 'Menu Height' setting to determine the height of your top level menu items.", 'megamenu' ) ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_padding_left',
												'validation' => 'px',
											),
										),
									),
									'menu_item_border'     => array(
										'priority'    => 90,
										'title'       => __( 'Item Border', 'megamenu' ),
										'description' => __( 'Set the border to display on each top level menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_border_color',
											),
											array(
												'title' => __( 'Color (Hover)', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_border_color_hover',
											),
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_border_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_border_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_border_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_border_left',
												'validation' => 'px',
											),
										),
									),
									'menu_item_border_radius' => array(
										'priority'    => 95,
										'title'       => __( 'Item Border Radius', 'megamenu' ),
										'description' => __( 'Set rounded corners for each top level menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_border_radius_top_left',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Top Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_border_radius_top_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_border_radius_bottom_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_link_border_radius_bottom_left',
												'validation' => 'px',
											),
										),
									),
									'menu_item_divider'    => array(
										'priority'    => 160,
										'title'       => __( 'Item Divider', 'megamenu' ),
										'description' => __( 'Show a small divider bar between each menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Enabled', 'megamenu' ),
												'type'  => 'checkbox',
												'key'   => 'menu_item_divider',
											),
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'menu_item_divider_color',
											),
											array(
												'title' => __( 'Glow Opacity', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'menu_item_divider_glow_opacity',
												'validation' => 'float',
											),
										),
									),
									'menu_item_highlight'  => array(
										'priority'    => 170,
										'title'       => __( 'Highlight Current Item', 'megamenu' ),
										'description' => __( "Apply the 'hover' styling to current menu items. Applies to top level menu items only.", 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Enabled', 'megamenu' ),
												'type'  => 'checkbox',
												'key'   => 'menu_item_highlight_current',
											),
										),
										'info'        => array(
											"<a href='https://www.megamenu.com/documentation/highlight-active-menu-items/' target='blank'>" . __( 'Documentation: Highlighting Menu Items', 'megamenu' ) . '</a>',
										),
									),
								),
							),
							'mega_panels'    => array(
								'title'    => __( 'Mega Menus', 'megamenu' ),
								'settings' => array(
									'panel_background'     => array(
										'priority'    => 10,
										'title'       => __( 'Panel Background', 'megamenu' ),
										'description' => __( 'Set a background color for a whole sub menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_background_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_background_to',
											),
										),
									),
									'panel_width'          => array(
										'priority'    => 20,
										'title'       => __( 'Panel Width', 'megamenu' ),
										'description' => __( 'Mega Panel width.', 'megamenu' ),
										'info'        => array(
											__( 'A 100% wide panel will only ever be as wide as the menu itself. For a fixed sub menu width set this to a pixel value.', 'megamenu' ),
											__( 'Advanced: Enter a jQuery selector to synchronize the width and position of the sub menu with existing page element (e.g. body, #container, .page).', 'megamenu' ),
											"<a href='https://www.megamenu.com/documentation/adjust-sub-menu-width/' target='blank'>" . __( 'Documentation: Configuring the sub menu width', 'megamenu' ) . '</a>',
										),
										'settings'    => array(
											array(
												'title' => __( 'Outer Width', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_width',
											),
											array(
												'title' => __( 'Inner Width', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_inner_width',
											),
										),
									),
									'panel_padding'        => array(
										'priority'    => 30,
										'title'       => __( 'Panel Padding', 'megamenu' ),
										'description' => __( 'Set the padding for the whole sub menu. Set these values 0px if you wish your sub menu content to go edge-to-edge.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_padding_left',
												'validation' => 'px',
											),
										),
									),
									'panel_border'         => array(
										'priority'    => 40,
										'title'       => __( 'Panel Border', 'megamenu' ),
										'description' => __( 'Set the border to display on the sub menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_border_color',
											),
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_border_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_border_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_border_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_border_left',
												'validation' => 'px',
											),
										),
									),
									'panel_border_radius'  => array(
										'priority'    => 50,
										'title'       => __( 'Panel Border Radius', 'megamenu' ),
										'description' => __( 'Set rounded corners for the sub menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_border_radius_top_left',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Top Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_border_radius_top_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_border_radius_bottom_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_border_radius_bottom_left',
												'validation' => 'px',
											),
										),
									),
									'widget_padding'       => array(
										'priority'    => 60,
										'title'       => __( 'Column Padding', 'megamenu' ),
										'description' => __( 'Use this to define the amount of space around each widget / set of menu items within the sub menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_widget_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_widget_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_widget_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_widget_padding_left',
												'validation' => 'px',
											),
										),
									),
									'mega_menu_widgets'    => array(
										'priority'    => 65,
										'title'       => __( 'Widgets', 'megamenu' ),
										'description' => '',
									),
									'widget_heading_font'  => array(
										'priority'    => 70,
										'title'       => __( 'Title Font', 'megamenu' ),
										'description' => __( 'Set the font to use Widget headers in the mega menu. Tip: set this to the same style as the Second Level Menu Item Font to keep your styling consistent.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_header_color',
											),
											array(
												'title' => __( 'Size', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_font_size',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Family', 'megamenu' ),
												'type'  => 'font',
												'key'   => 'panel_header_font',
											),
											array(
												'title' => __( 'Transform', 'megamenu' ),
												'type'  => 'transform',
												'key'   => 'panel_header_text_transform',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'panel_header_font_weight',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'panel_header_text_decoration',
											),
											array(
												'title' => __( 'Align', 'megamenu' ),
												'type'  => 'align',
												'key'   => 'panel_header_text_align',
											),
										),
									),
									'widget_heading_padding' => array(
										'priority'    => 90,
										'title'       => __( 'Title Padding', 'megamenu' ),
										'description' => __( 'Set the padding for the widget headings.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_padding_left',
												'validation' => 'px',
											),
										),
									),
									'widget_heading_margin' => array(
										'priority'    => 100,
										'title'       => __( 'Title Margin', 'megamenu' ),
										'description' => __( 'Set the margin for the widget headings.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_margin_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_margin_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_margin_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_margin_left',
												'validation' => 'px',
											),
										),
									),
									'widget_header_border' => array(
										'priority'    => 110,
										'title'       => __( 'Title Border', 'megamenu' ),
										'description' => __( 'Set the border for the widget headings.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_header_border_color',
											),
											array(
												'title' => __( 'Color (Hover)', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_header_border_color_hover',
											),
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_border_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_border_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_border_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_header_border_left',
												'validation' => 'px',
											),
										),
									),
									'widget_content_font'  => array(
										'priority'    => 115,
										'title'       => __( 'Content Font', 'megamenu' ),
										'description' => __( 'Set the font to use for panel contents.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_font_color',
											),
											array(
												'title' => __( 'Size', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_font_size',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Family', 'megamenu' ),
												'type'  => 'font',
												'key'   => 'panel_font_family',
											),
										),
									),
									'second_level_menu_items' => array(
										'priority'    => 120,
										'title'       => __( 'Second Level Menu Items', 'megamenu' ),
										'description' => '',
									),
									'second_level_font'    => array(
										'priority'    => 130,
										'title'       => __( 'Item Font', 'megamenu' ),
										'description' => __( "Set the font for second level menu items when they're displayed in a Mega Menu.", 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_second_level_font_color',
											),
											array(
												'title' => __( 'Size', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_font_size',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Family', 'megamenu' ),
												'type'  => 'font',
												'key'   => 'panel_second_level_font',
											),
											array(
												'title' => __( 'Transform', 'megamenu' ),
												'type'  => 'transform',
												'key'   => 'panel_second_level_text_transform',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'panel_second_level_font_weight',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'panel_second_level_text_decoration',
											),
											array(
												'title' => __( 'Align', 'megamenu' ),
												'type'  => 'align',
												'key'   => 'panel_second_level_text_align',
											),
										),
									),
									'second_level_font_hover' => array(
										'priority'    => 140,
										'title'       => __( 'Item Font (Hover)', 'megamenu' ),
										'description' => __( 'Set the font style on hover.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_second_level_font_color_hover',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'panel_second_level_font_weight_hover',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'panel_second_level_text_decoration_hover',
											),
										),
									),
									'second_level_background_hover' => array(
										'priority'    => 150,
										'title'       => __( 'Item Background (Hover)', 'megamenu' ),
										'description' => __( 'Set the background hover color for second level menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_second_level_background_hover_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_second_level_background_hover_to',
											),
										),
									),
									'second_level_padding' => array(
										'priority'    => 160,
										'title'       => __( 'Item Padding', 'megamenu' ),
										'description' => __( 'Set the padding for the second level menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_padding_left',
												'validation' => 'px',
											),
										),
									),
									'second_level_margin'  => array(
										'priority'    => 170,
										'title'       => __( 'Item Margin', 'megamenu' ),
										'description' => __( 'Set the margin for the second level menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_margin_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_margin_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_margin_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_margin_left',
												'validation' => 'px',
											),
										),
									),
									'second_level_border'  => array(
										'priority'    => 180,
										'title'       => __( 'Item Border', 'megamenu' ),
										'description' => __( 'Set the border for the second level menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_second_level_border_color',
											),
											array(
												'title' => __( 'Color (Hover)', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_second_level_border_color_hover',
											),
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_border_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_border_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_border_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_second_level_border_left',
												'validation' => 'px',
											),
										),
									),
									'third_level_menu_items' => array(
										'priority'    => 190,
										'title'       => __( 'Third Level Menu Items', 'megamenu' ),
										'description' => '',
									),
									'third_level_font'     => array(
										'priority'    => 200,
										'title'       => __( 'Item Font', 'megamenu' ),
										'description' => __( "Set the font for third level menu items when they're displayed in a Mega Menu.", 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_third_level_font_color',
											),
											array(
												'title' => __( 'Size', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_font_size',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Family', 'megamenu' ),
												'type'  => 'font',
												'key'   => 'panel_third_level_font',
											),
											array(
												'title' => __( 'Transform', 'megamenu' ),
												'type'  => 'transform',
												'key'   => 'panel_third_level_text_transform',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'panel_third_level_font_weight',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'panel_third_level_text_decoration',
											),
											array(
												'title' => __( 'Align', 'megamenu' ),
												'type'  => 'align',
												'key'   => 'panel_third_level_text_align',
											),
										),
									),
									'third_level_font_hover' => array(
										'priority'    => 210,
										'title'       => __( 'Item Font (Hover)', 'megamenu' ),
										'description' => __( 'Set the font style on hover.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_third_level_font_color_hover',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'panel_third_level_font_weight_hover',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'panel_third_level_text_decoration_hover',
											),
										),
									),
									'third_level_background_hover' => array(
										'priority'    => 220,
										'title'       => __( 'Item Background (Hover)', 'megamenu' ),
										'description' => __( 'Set the background hover color for third level menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_third_level_background_hover_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_third_level_background_hover_to',
											),
										),
									),
									'third_level_padding'  => array(
										'priority'    => 230,
										'title'       => __( 'Item Padding', 'megamenu' ),
										'description' => __( 'Set the padding for the third level menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_padding_left',
												'validation' => 'px',
											),
										),
									),

									'third_level_margin'   => array(
										'priority'    => 235,
										'title'       => __( 'Item Margin', 'megamenu' ),
										'description' => __( 'Set the margin for the third level menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_margin_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_margin_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_margin_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_margin_left',
												'validation' => 'px',
											),
										),
									),
									'third_level_border'   => array(
										'priority'    => 237,
										'title'       => __( 'Item Border', 'megamenu' ),
										'description' => __( 'Set the border for the third level menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_third_level_border_color',
											),
											array(
												'title' => __( 'Color (Hover)', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'panel_third_level_border_color_hover',
											),
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_border_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_border_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_border_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'panel_third_level_border_left',
												'validation' => 'px',
											),
										),
									),
								),
							),
							'flyout_menus'   => array(
								'title'    => __( 'Flyout Menus', 'megamenu' ),
								'settings' => array(
									'flyout_menu_background' => array(
										'priority'    => 10,
										'title'       => __( 'Sub Menu Background', 'megamenu' ),
										'description' => __( 'Set the background color for the flyout menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_menu_background_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_menu_background_to',
											),
										),
									),
									'flyout_menu_width'   => array(
										'priority'    => 20,
										'title'       => __( 'Sub Menu Width', 'megamenu' ),
										'description' => __( 'The width of each flyout menu. This must be a fixed pixel value.', 'megamenu' ),
										'info'        => array(
											__( 'Set this value to the width of your longest menu item title to stop menu items wrapping onto 2 lines.', 'megamenu' ),
											__( "Experimental: Set this value to 'auto' to use a flexible width.", 'megamenu' ),
										),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'flyout_width',
												'validation' => 'px',
											),
										),
									),
									'flyout_menu_padding' => array(
										'priority'    => 30,
										'title'       => __( 'Sub Menu Padding', 'megamenu' ),
										'description' => __( 'Set the padding for the whole flyout menu.', 'megamenu' ),
										'info'        => array( __( "Only suitable for single level flyout menus. If you're using multi level flyout menus set these values to 0px.", 'megamenu' ) ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_padding_left',
												'validation' => 'px',
											),
										),
									),
									'flyout_menu_border'  => array(
										'priority'    => 40,
										'title'       => __( 'Sub Menu Border', 'megamenu' ),
										'description' => __( 'Set the border for the flyout menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_border_color',
											),
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_border_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_border_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_border_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_border_left',
												'validation' => 'px',
											),
										),
									),
									'flyout_menu_border_radius' => array(
										'priority'    => 50,
										'title'       => __( 'Sub Menu Border Radius', 'megamenu' ),
										'description' => __( 'Set rounded corners for flyout menus. Rounded corners will be applied to all flyout menu levels.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_border_radius_top_left',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Top Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_border_radius_top_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_border_radius_bottom_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_border_radius_bottom_left',
												'validation' => 'px',
											),
										),
									),
									'flyout_menu_item_background' => array(
										'priority'    => 60,
										'title'       => __( 'Menu Item Background', 'megamenu' ),
										'description' => __( 'Set the background color for a flyout menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_background_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_background_to',
											),
										),
									),
									'flyout_menu_item_background_hover' => array(
										'priority'    => 70,
										'title'       => __( 'Menu Item Background (Hover)', 'megamenu' ),
										'description' => __( 'Set the background color for a flyout menu item (on hover).', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_background_hover_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_background_hover_to',
											),
										),
									),
									'flyout_menu_item_height' => array(
										'priority'    => 80,
										'title'       => __( 'Menu Item Height', 'megamenu' ),
										'description' => __( 'The height of each flyout menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'flyout_link_height',
												'validation' => 'px',
											),
										),
									),
									'flyout_menu_item_padding' => array(
										'priority'    => 90,
										'title'       => __( 'Menu Item Padding', 'megamenu' ),
										'description' => __( 'Set the padding for each flyout menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_link_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_link_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_link_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_link_padding_left',
												'validation' => 'px',
											),
										),
									),
									'flyout_menu_item_font' => array(
										'priority'    => 100,
										'title'       => __( 'Menu Item Font', 'megamenu' ),
										'description' => __( 'Set the font for the flyout menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_link_color',
											),
											array(
												'title' => __( 'Size', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'flyout_link_size',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Family', 'megamenu' ),
												'type'  => 'font',
												'key'   => 'flyout_link_family',
											),
											array(
												'title' => __( 'Transform', 'megamenu' ),
												'type'  => 'transform',
												'key'   => 'flyout_link_text_transform',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'flyout_link_weight',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'flyout_link_text_decoration',
											),
										),
									),
									'flyout_menu_item_font_hover' => array(
										'priority'    => 110,
										'title'       => __( 'Menu Item Font (Hover)', 'megamenu' ),
										'description' => __( 'Set the font for the flyout menu items.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_link_color_hover',
											),
											array(
												'title' => __( 'Weight', 'megamenu' ),
												'type'  => 'weight',
												'key'   => 'flyout_link_weight_hover',
											),
											array(
												'title' => __( 'Decoration', 'megamenu' ),
												'type'  => 'decoration',
												'key'   => 'flyout_link_text_decoration_hover',
											),
										),
									),
									'flyout_menu_item_divider' => array(
										'priority'    => 120,
										'title'       => __( 'Menu Item Divider', 'megamenu' ),
										'description' => __( 'Show a line divider below each menu item.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Enabled', 'megamenu' ),
												'type'  => 'checkbox',
												'key'   => 'flyout_menu_item_divider',
											),
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'flyout_menu_item_divider_color',
											),
										),
									),
								),
							),
							'mobile_menu'    => array(
								'title'    => __( 'Mobile Menu', 'megamenu' ),
								'settings' => array(
									'mobile_toggle_bar'   => array(
										'priority'    => 5,
										'title'       => __( 'Mobile Toggle Bar', 'megamenu' ),
										'description' => '',
									),
									'toggle_bar_background' => array(
										'priority'    => 20,
										'title'       => __( 'Toggle Bar Background', 'megamenu' ),
										'description' => __( 'Set the background color for the mobile menu toggle bar.', 'megamenu' ),
										'info'        => array(
											__( "Don't forget to update the Menu toggle block text and icon color in the Toggle Bar Designer above!", 'megamenu' ),
										),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'toggle_background_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'toggle_background_to',
											),
										),
									),
									'toggle_bar_height'   => array(
										'priority'    => 25,
										'title'       => __( 'Toggle Bar Height', 'megamenu' ),
										'description' => __( 'Set the height of the mobile menu toggle bar.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'toggle_bar_height',
											),
										),
									),
									'toggle_bar_border_radius' => array(
										'priority'    => 26,
										'title'       => __( 'Toggle Bar Border Radius', 'megamenu' ),
										'description' => __( 'Set a border radius on the mobile toggle bar.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'toggle_bar_border_radius_top_left',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Top Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'toggle_bar_border_radius_top_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'toggle_bar_border_radius_bottom_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'toggle_bar_border_radius_bottom_left',
												'validation' => 'px',
											),
										),
									),
									'disable_mobile_toggle' => array(
										'priority'    => 28,
										'title'       => __( 'Disable Mobile Toggle Bar', 'megamenu' ),
										'description' => __( "Hide the toggle bar and display the menu in it's open state by default.", 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'checkbox',
												'key'   => 'disable_mobile_toggle',
											),
										),
									),
									'responsive_breakpoint' => array(
										'priority'    => 3,
										'title'       => __( 'Responsive Breakpoint', 'megamenu' ),
										'description' => __( 'The menu will be converted to a mobile menu when the browser width is below this value.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'responsive_breakpoint',
												'validation' => 'px',
											),
										),
									),
									'responsive_breakpoint_disabled' => array(
										'priority'    => 4,
										'title'       => __( "The 'Responsive Breakpoint' option has been set to 0px. The desktop version of the menu will be displayed for all browsers (regardless of the browser width), so the following options are disabled.", 'megamenu' ),
										'description' => '',
									),
									'mobile_toggle_disabled' => array(
										'priority'    => 5,
										'title'       => __( "The 'Disable Mobile Toggle Bar' option has been enabled. The following options are disabled as the mobile toggle bar will not be displayed.", 'megamenu' ),
										'description' => '',
									),
									'mobile_top_level_menu_items' => array(
										'priority'    => 33,
										'title'       => __( 'Mobile Sub Menu', 'megamenu' ),
										'description' => '',
									),
									'mobile_menu_overlay' => array(
										'priority'    => 34,
										'title'       => __( 'Overlay Content', 'megamenu' ),
										'description' => __( 'If enabled, the mobile sub menu will overlay the page content (instead of pushing the page content down)', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'checkbox',
												'key'   => 'mobile_menu_overlay',
											),
										),
									),
									'mobile_menu_force_width' => array(
										'priority'    => 35,
										'title'       => __( 'Force Full Width', 'megamenu' ),
										'description' => __( "If enabled, the mobile sub menu will match the width and position on the given page element (rather than being limited to the width of the toggle bar). For a full width sub menu, leave the 'Selector' value set to 'body'.", 'megamenu' ),
										'settings'    => array(
											array(
												'title' => 'Enabled',
												'type'  => 'checkbox',
												'key'   => 'mobile_menu_force_width',
											),
											array(
												'title' => __( 'Selector', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'mobile_menu_force_width_selector',
											),
										),
									),
									'mobile_menu_off_canvas_width' => array(
										'priority'    => 36,
										'title'       => __( 'Off Canvas Width', 'megamenu' ),
										'description' => __( "The width of the sub menu if the Mobile Effect is set to 'Slide Left' or 'Slide Right'. Must be specified in px.", 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'mobile_menu_off_canvas_width',
												'validation' => 'px',
											),
										),
									),
									'mobile_menu_item_height' => array(
										'priority'    => 38,
										'title'       => __( 'Menu Item Height', 'megamenu' ),
										'description' => __( 'Height of each top level item in the mobile menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'freetext',
												'key'   => 'mobile_menu_item_height',
											),
										),
									),
									'mobile_menu_padding' => array(
										'priority'    => 39,
										'title'       => __( 'Menu Padding', 'megamenu' ),
										'description' => __( 'Padding for the mobile sub menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Top', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'mobile_menu_padding_top',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Right', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'mobile_menu_padding_right',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Bottom', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'mobile_menu_padding_bottom',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Left', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'mobile_menu_padding_left',
												'validation' => 'px',
											),
										),
									),
									'mobile_background'   => array(
										'priority'    => 40,
										'title'       => __( 'Menu Background', 'megamenu' ),
										'description' => __( 'The background color for the mobile menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'mobile_background_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'mobile_background_to',
											),
										),
									),
									'mobile_background_hover' => array(
										'priority'    => 45,
										'title'       => __( 'Menu Item Background (Active)', 'megamenu' ),
										'description' => __( 'The background color for each top level menu item in the mobile menu when the sub menu is open.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'From', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'mobile_menu_item_background_hover_from',
											),
											array(
												'title' => __( 'Copy', 'megamenu' ),
												'type'  => 'copy_color',
												'key'   => 'copy_color',
											),
											array(
												'title' => __( 'To', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'mobile_menu_item_background_hover_to',
											),
										),
									),
									'mobile_menu_item_font' => array(
										'priority'    => 50,
										'title'       => __( 'Font', 'megamenu' ),
										'description' => __( 'The font to use for each top level menu item in the mobile menu.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'mobile_menu_item_link_color',
											),
											array(
												'title' => __( 'Size', 'megamenu' ),
												'type'  => 'freetext',
												'key'   => 'mobile_menu_item_link_font_size',
												'validation' => 'px',
											),
											array(
												'title' => __( 'Align', 'megamenu' ),
												'type'  => 'align',
												'key'   => 'mobile_menu_item_link_text_align',
											),
										),
									),
									'mobile_menu_item_font_hover' => array(
										'priority'    => 55,
										'title'       => __( 'Font (Active)', 'megamenu' ),
										'description' => __( 'The font color for each top level menu item in the mobile menu when the sub menu is open.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => __( 'Color', 'megamenu' ),
												'type'  => 'color',
												'key'   => 'mobile_menu_item_link_color_hover',
											),
										),
									),
									'mobile_mega_menus'   => array(
										'priority'    => 60,
										'title'       => __( 'Mega Menus', 'megamenu' ),
										'description' => '',
									),
									'mobile_columns'      => array(
										'priority'    => 65,
										'title'       => __( 'Mega Menu Columns', 'megamenu' ),
										'description' => __( 'Collapse mega menu content into this many columns on mobile.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'mobile_columns',
												'key'   => 'mobile_columns',
											),
										),
									),
								),
							),
							'custom_styling' => array(
								'title'    => __( 'Custom Styling', 'megamenu' ),
								'settings' => array(
									'custom_styling' => array(
										'priority'    => 40,
										'title'       => __( 'CSS Editor', 'megamenu' ),
										'description' => __( 'Define any custom CSS you wish to add to menus using this theme. You can use standard CSS or SCSS.', 'megamenu' ),
										'settings'    => array(
											array(
												'title' => '',
												'type'  => 'textarea',
												'key'   => 'custom_css',
											),
										),
									),
								),
							),
						)
					);

					echo "<h2 class='nav-tab-wrapper'>";

					$is_first = true;

				foreach ( $settings as $section_id => $section ) {

					if ( $is_first ) {
						$active   = 'nav-tab-active ';
						$is_first = false;
					} else {
						$active = '';
					}

					echo "<a class='mega-tab nav-tab {$active}' data-tab='mega-tab-content-{$section_id}'>" . $section['title'] . '</a>';

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

						echo "        <div class='mega-tab-content mega-tab-content-{$section_id}' style='display: {$display}'>";
						echo "            <table class='{$section_id}'>";

						// order the fields by priority
						uasort( $section['settings'], array( $this, 'compare_elems' ) );

					foreach ( $section['settings'] as $group_id => $group ) {

						echo "<tr class='mega-{$group_id}'>";

						if ( isset( $group['settings'] ) ) {

							echo "<td class='mega-name'>" . $group['title'] . "<div class='mega-description'>" . $group['description'] . '</div></td>';
							echo "<td class='mega-value'>";

							foreach ( $group['settings'] as $setting_id => $setting ) {

								if ( isset( $setting['validation'] ) ) {
									echo "<label class='mega-{$setting['key']}' data-validation='{$setting['validation']}'>";
								} else {
									echo "<label class='mega-{$setting['key']}'>";
								}
								echo "<span class='mega-short-desc'>{$setting['title']}</span>";

								switch ( $setting['type'] ) {
									case 'freetext':
										$this->print_theme_freetext_option( $setting['key'] );
										break;
									case 'textarea':
										$this->print_theme_textarea_option( $setting['key'] );
										break;
									case 'align':
										$this->print_theme_align_option( $setting['key'] );
										break;
									case 'checkbox':
										$this->print_theme_checkbox_option( $setting['key'] );
										break;
									case 'arrow':
										$this->print_theme_arrow_option( $setting['key'] );
										break;
									case 'color':
										$this->print_theme_color_option( $setting['key'] );
										break;
									case 'weight':
										$this->print_theme_weight_option( $setting['key'] );
										break;
									case 'font':
										$this->print_theme_font_option( $setting['key'] );
										break;
									case 'transform':
										$this->print_theme_transform_option( $setting['key'] );
										break;
									case 'decoration':
										$this->print_theme_text_decoration_option( $setting['key'] );
										break;
									case 'mobile_columns':
										$this->print_theme_mobile_columns_option( $setting['key'] );
										break;
									case 'copy_color':
										$this->print_theme_copy_color_option( $setting['key'] );
										break;
									default:
										do_action( "megamenu_print_theme_option_{$setting['type']}", $setting['key'], $this->id );
										break;
								}

								echo '</label>';

							}

							if ( isset( $group['info'] ) ) {
								foreach ( $group['info'] as $paragraph ) {
									echo "<div class='mega-info'>{$paragraph}</div>";
								}
							}

							foreach ( $group['settings'] as $setting_id => $setting ) {
								if ( isset( $setting['validation'] ) ) {

									echo "<div class='mega-validation-message mega-validation-message-mega-{$setting['key']}'>";

									if ( $setting['validation'] == 'int' ) {
										$message = __( 'Enter a whole number (e.g. 1, 5, 100, 999)' );
									}

									if ( $setting['validation'] == 'px' ) {
										$message = __( 'Enter a value including a unit (e.g. 10px, 10rem, 10%)' );
									}

									if ( $setting['validation'] == 'float' ) {
										$message = __( 'Enter a valid number (e.g. 0.1, 1, 10, 999)' );
									}

									if ( strlen( $setting['title'] ) ) {
										echo $setting['title'] . ': ' . $message;
									} else {
										echo $message;
									}

									echo '</div>';
								}
							}

							echo '</td>';
						} else {
							echo "<td colspan='2'><h5>{$group['title']}</h5></td>";
						}

						echo '</tr>';

					}

						echo '</table>';
						echo '</div>';
				}

				?>


				<div class='megamenu_submit'>
					<div class='mega_left'>
						<?php submit_button(); ?>
					</div>
					<div class='mega_right'>
					</div>
				</div>

				<?php $this->show_cache_warning(); ?>
			</form>
		</div>

			<?php

		}


		/**
		 * Check for installed caching/minification/CDN plugins and output a warning if one is found to be
		 * installed and activated
		 */
		public function show_cache_warning() {

			$active_plugins = max_mega_menu_get_active_caching_plugins();

			if ( count( $active_plugins ) ) :

				?>

		<div>

			<h3><?php _e( 'Changes not showing up?', 'megamenu' ); ?></h3>

			<p><?php echo _n( 'We have detected the following plugin that may prevent changes made within the theme editor from being applied to the menu.', 'We have detected the following plugins that may prevent changes made within the theme editor from being applied to the menu.', count( $active_plugins ), 'megamenu' ); ?></p>

			<ul class='ul-disc'>
				<?php
				foreach ( $active_plugins as $name ) {
					echo '<li>' . $name . '</li>';
				}
				?>
			</ul>

			<p><?php echo _n( 'Try clearing the cache of the above plugin if your changes are not being applied to the menu.', 'Try clearing the caches of the above plugins if your changes are not being applied to the menu.', count( $active_plugins ), 'megamenu' ); ?></p>

		</div>

				<?php

			endif;
		}


		/**
		 * Compare array values
		 *
		 * @param array $elem1
		 * @param array $elem2
		 * @return bool
		 * @since 2.1
		 */
		private function compare_elems( $elem1, $elem2 ) {

			return $elem1['priority'] > $elem2['priority'];

		}


		/**
		 * Print a select dropdown with left, center and right options
		 *
		 * @since 1.6.1
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_align_option( $key ) {

			$value = $this->active_theme[ $key ];

			?>

			<select name='settings[<?php echo $key; ?>]'>
				<option value='left' <?php selected( $value, 'left' ); ?>><?php _e( 'Left', 'megamenu' ); ?></option>
				<option value='center' <?php selected( $value, 'center' ); ?>><?php _e( 'Center', 'megamenu' ); ?></option>
				<option value='right' <?php selected( $value, 'right' ); ?>><?php _e( 'Right', 'megamenu' ); ?></option>
			</select>

			<?php
		}


		/**
		 * Print a copy icon
		 *
		 * @since 2.2.3
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_copy_color_option( $key ) {

			?>

			<span class='dashicons dashicons-arrow-right-alt'></span>

			<?php
		}


		/**
		 * Print a select dropdown with 1 and 2 options
		 *
		 * @since 1.2.0
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_mobile_columns_option( $key ) {

			$value = $this->active_theme[ $key ];

			?>

			<select name='settings[<?php echo $key; ?>]'>
				<option value='1' <?php selected( $value, '1' ); ?>><?php _e( '1 Column', 'megamenu' ); ?></option>
				<option value='2' <?php selected( $value, '2' ); ?>><?php _e( '2 Columns', 'megamenu' ); ?></option>
			</select>

			<?php
		}


		/**
		 * Print a select dropdown with text decoration options
		 *
		 * @since 1.6.1
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_text_decoration_option( $key ) {

			$value = $this->active_theme[ $key ];

			?>

			<select name='settings[<?php echo $key; ?>]'>
				<option value='none' <?php selected( $value, 'none' ); ?>><?php _e( 'None', 'megamenu' ); ?></option>
				<option value='underline' <?php selected( $value, 'underline' ); ?>><?php _e( 'Underline', 'megamenu' ); ?></option>
			</select>

			<?php
		}


		/**
		 * Print a checkbox option
		 *
		 * @since 1.6.1
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_checkbox_option( $key ) {

			$value = $this->active_theme[ $key ];

			?>

			<input type='hidden' name='checkboxes[<?php echo $key; ?>]' />
			<input type='checkbox' name='settings[<?php echo $key; ?>]' <?php checked( $value, 'on' ); ?> />

			<?php
		}


		/**
		 * Print an arrow dropdown selection box
		 *
		 * @since 1.0
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_arrow_option( $key ) {

			$value = $this->active_theme[ $key ];

			$arrow_icons = $this->arrow_icons();

			?>
			<select class='icon_dropdown' name='settings[<?php echo $key; ?>]'>
				<?php

					echo "<option value='disabled'>" . __( 'Disabled', 'megamenu' ) . '</option>';

				foreach ( $arrow_icons as $code => $class ) {
					$name = str_replace( 'dashicons-', '', $class );
					$name = ucwords( str_replace( array( '-', 'arrow' ), ' ', $name ) );
					echo "<option data-class='{$class}' value='{$code}' " . selected( $value, $code, false ) . '>' . esc_html( $name ) . '</option>';
				}

				?>
			</select>

			<?php
		}



		/**
		 * Print a colorpicker
		 *
		 * @since 1.0
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_color_option( $key ) {

			$value = $this->active_theme[ $key ];

			if ( $value == 'transparent' ) {
				$value = 'rgba(0,0,0,0)';
			}

			if ( $value == 'rgba(0,0,0,0)' ) {
				$value_text = 'transparent';
			} else {
				$value_text = $value;
			}

			echo "<div class='mm-picker-container'>";
			echo "    <input type='text' class='mm_colorpicker' name='settings[$key]' value='" . esc_attr( $value ) . "' />";
			echo "    <div class='chosen-color'>" . esc_html( $value_text ) . '</div>';
			echo '</div>';

		}


		/**
		 * Print a font weight selector
		 *
		 * @since 1.0
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_weight_option( $key ) {

			$value = $this->active_theme[ $key ];

			$options = apply_filters(
				'megamenu_font_weights',
				array(
					'inherit' => __( 'Theme Default', 'megamenu' ),
					'300'     => __( 'Light (300)', 'megamenu' ),
					'normal'  => __( 'Normal (400)', 'megamenu' ),
					'bold'    => __( 'Bold (700)', 'megamenu' ),
				)
			);

			/**
			 *   '100' => __("Thin (100)", "megamenu"),
			 *   '200' => __("Extra Light (200)", "megamenu"),
			 *   '300' => __("Light (300)", "megamenu"),
			 *   'normal' => __("Normal (400)", "megamenu"),
			 *   '500' => __("Medium (500)", "megamenu"),
			 *   '600' => __("Semi Bold (600)", "megamenu"),
			 *   'bold' => __("Bold (700)", "megamenu"),
			 *   '800' => __("Extra Bold (800)", "megamenu"),
			 *   '900' => __("Black (900)", "megamenu")
			*/

			echo "<select name='settings[$key]'>";

			foreach ( $options as $weight => $name ) {
				echo "<option value='" . esc_attr( $weight ) . "' " . selected( $value, $weight, false ) . '>' . esc_html( $name ) . '</option>';
			}

			echo '</select>';

		}


		/**
		 * Print a font transform selector
		 *
		 * @since 1.0
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_transform_option( $key ) {

			$value = $this->active_theme[ $key ];

			echo "<select name='settings[$key]'>";
			echo "    <option value='none' " . selected( $value, 'none', false ) . '>' . __( 'Normal', 'megamenu' ) . '</option>';
			echo "    <option value='capitalize'" . selected( $value, 'capitalize', false ) . '>' . __( 'Capitalize', 'megamenu' ) . '</option>';
			echo "    <option value='uppercase'" . selected( $value, 'uppercase', false ) . '>' . __( 'UPPERCASE', 'megamenu' ) . '</option>';
			echo "    <option value='lowercase'" . selected( $value, 'lowercase', false ) . '>' . __( 'lowercase', 'megamenu' ) . '</option>';
			echo '</select>';

		}


		/**
		 * Print a textarea
		 *
		 * @since 1.0
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_textarea_option( $key ) {

			$value = sanitize_textarea_field( $this->active_theme[ $key ] );

			?>

		<textarea id='codemirror' name='settings[<?php echo $key; ?>]'><?php echo stripslashes( $value ); ?></textarea>

		<p><b><?php _e( 'Custom Styling Tips', 'megamenu' ); ?></b></p>
		<p><?php _e( "You can enter standard CSS or <a href='https://sass-lang.com/guide' target='_blank'>SCSS</a> into the custom styling area. If using SCSS there are some variables and mixins you can use:" ); ?></p>
		<ul class='custom_styling_tips'>
			<li><code>#{$wrap}</code> <?php _e( 'converts to the ID selector of the menu wrapper, e.g. div#mega-menu-wrap-primary', 'megamenu' ); ?></li>
			<li><code>#{$menu}</code> <?php _e( 'converts to the ID selector of the menu, e.g. ul#mega-menu-primary', 'megamenu' ); ?></li>
			<li><code>@include mobile|desktop { .. }</code> <?php _e( 'wraps the CSS within a media query based on the configured Responsive Breakpoint (see example CSS)', 'megamenu' ); ?></li>
			<?php
				$string = __( 'Using the %wrap% and %menu% variables makes your theme portable (allowing you to apply the same theme to multiple menu locations)', 'megamenu' );
				$string = str_replace( '%wrap%', '<code>#{$wrap}</code>', $string );
				$string = str_replace( '%menu%', '<code>#{$menu}</code>', $string );
			?>
			<li><?php echo $string; ?></li>
			<li>Example CSS:</li>
			<code>/** Add text shadow to top level menu items on desktop AND mobile **/
				<br />#{$wrap} #{$menu} > li.mega-menu-item > a.mega-menu-link {
				<br />&nbsp;&nbsp;&nbsp;&nbsp;text-shadow: 1px 1px #000000;
				<br />}
			</code>
			<br /><br />
			<code>/** Add text shadow to top level menu items on desktop only **/
				<br />@include desktop {
				<br />&nbsp;&nbsp;&nbsp;&nbsp;#{$wrap} #{$menu} > li.mega-menu-item > a.mega-menu-link {
				<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;text-shadow: 1px 1px #000000;
				<br />&nbsp;&nbsp;&nbsp;&nbsp;}
				<br />}
			</code></li>
		</ul>

			<?php

		}


		/**
		 * Print a font selector
		 *
		 * @since 1.0
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_font_option( $key ) {

			$value = $this->active_theme[ $key ];

			echo "<select name='settings[$key]'>";

			echo "<option value='inherit'>" . __( 'Theme Default', 'megamenu' ) . '</option>';

			foreach ( $this->fonts() as $font ) {
				$orig_font = $font;
				$font      = esc_attr( stripslashes( $font ) );
				$parts     = explode( ',', $font );
				$font_name = trim( $parts[0] );
				echo "<option value=\"{$font}\" " . selected( $orig_font, htmlspecialchars_decode( $value ) ) . '>' . esc_html( $font_name ) . '</option>';
			}

			echo '</select>';
		}


		/**
		 * Print a text input
		 *
		 * @since 1.0
		 * @param string $key
		 * @param string $value
		 */
		public function print_theme_freetext_option( $key ) {

			$value = $this->active_theme[ $key ];

			echo "<input class='mega-setting-{$key}' type='text' name='settings[$key]' value='" . esc_attr( $value ) . "' />";

		}


		/**
		 * Returns a list of available fonts.
		 *
		 * @since 1.0
		 */
		public function fonts() {

			$fonts = array(
				'Georgia, serif',
				'Palatino Linotype, Book Antiqua, Palatino, serif',
				'Times New Roman, Times, serif',
				'Arial, Helvetica, sans-serif',
				'Arial Black, Gadget, sans-serif',
				'Comic Sans MS, cursive, sans-serif',
				'Impact, Charcoal, sans-serif',
				'Lucida Sans Unicode, Lucida Grande, sans-serif',
				'Tahoma, Geneva, sans-serif',
				'Trebuchet MS, Helvetica, sans-serif',
				'Verdana, Geneva, sans-serif',
				'Courier New, Courier, monospace',
				'Lucida Console, Monaco, monospace',
			);

			$fonts = apply_filters( 'megamenu_fonts', $fonts );

			return $fonts;

		}


		/**
		 * List of all available arrow DashIcon classes.
		 *
		 * @since 1.0
		 * @return array - Sorted list of icon classes
		 */
		private function arrow_icons() {

			$icons = array(
				'dash-f142' => 'dashicons-arrow-up',
				'dash-f140' => 'dashicons-arrow-down',
				'dash-f141' => 'dashicons-arrow-left',
				'dash-f139' => 'dashicons-arrow-right',
				'dash-f342' => 'dashicons-arrow-up-alt',
				'dash-f346' => 'dashicons-arrow-down-alt',
				'dash-f340' => 'dashicons-arrow-left-alt',
				'dash-f344' => 'dashicons-arrow-right-alt',
				'dash-f343' => 'dashicons-arrow-up-alt2',
				'dash-f347' => 'dashicons-arrow-down-alt2',
				'dash-f341' => 'dashicons-arrow-left-alt2',
				'dash-f345' => 'dashicons-arrow-right-alt2',
				'dash-f132' => 'dashicons-plus',
				'dash-f460' => 'dashicons-minus',
				'dash-f158' => 'dashicons-no',
				'dash-f335' => 'dashicons-no-alt',

			);

			$icons = apply_filters( 'megamenu_arrow_icons', $icons );

			return $icons;

		}

	}

endif;
