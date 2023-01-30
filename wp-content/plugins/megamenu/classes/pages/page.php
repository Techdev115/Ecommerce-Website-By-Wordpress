<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_Page' ) ) :

	/**
	 * Handles all admin related functionality.
	 */
	class Mega_Menu_Page {

		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'megamenu_settings_page' ) );
			add_action( 'megamenu_admin_scripts', array( $this, 'enqueue_scripts' ) );
		}


		/**
		 * Adds the "Menu Themes" menu item and page.
		 *
		 * @since 1.0
		 */
		public function megamenu_settings_page() {

			$svg = 'PHN2ZyB2ZXJzaW9uPSIxLjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEyNy4wMDAwMDBwdCIgaGVpZ2h0PSIxMjcuMDAwMDAwcHQiIHZpZXdCb3g9IjAgMCAxMjcuMDAwMDAwIDEyNy4wMDAwMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgogICAgICAgICAgICAgICAgICAgIDxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLDEyNy4wMDAwMDApIHNjYWxlKDAuMTAwMDAwLC0wLjEwMDAwMCkiIGZpbGw9IiMwMDAwMDAiIHN0cm9rZT0ibm9uZSI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik0zMzAgMTEyNyBsLTI0NSAtMTQzIC03IC0xODAgYy01IC05OCAtNyAtMjUzIC01IC0zNDQgbDIgLTE2NSAxMzAKICAgICAgICAgICAgICAgICAgICAgICAgLTc2IGMyOTUgLTE3MyAzNDUgLTIwNCAzNDUgLTIxMSAwIC00IDI0IC04IDU0IC04IDQ4IDAgNjUgNyAxNjcgNjYgMjIzIDEyOQogICAgICAgICAgICAgICAgICAgICAgICAzNzYgMjI0IDM5MCAyNDAgMTggMjEgMjYgNTkzIDEwIDYzNyAtMTIgMzAgLTczIDcyIC0yNzYgMTkwIC03MSA0MiAtMTUyIDkwCiAgICAgICAgICAgICAgICAgICAgICAgIC0xNzkgMTA2IC0zNiAyMyAtNjAgMzEgLTk1IDMxIC00MSAwIC03MiAtMTYgLTI5MSAtMTQzeiBtNDEwIC03NyBjMTMxIC03NgogICAgICAgICAgICAgICAgICAgICAgICAxNDEgLTg1IDExNSAtMTA1IC00MyAtMzEgLTIyMSAtMTI1IC0yMzkgLTEyNSAtMjEgMCAtMjE3IDExMiAtMjM1IDEzNCAtOCAxMAogICAgICAgICAgICAgICAgICAgICAgICAtNiAxNyA3IDI4IDM3IDMyIDIwNyAxMjggMjI2IDEyOCAxMiAwIDY4IC0yNyAxMjYgLTYweiBtLTM2MSAtMjc5IGM4OCAtNTAKICAgICAgICAgICAgICAgICAgICAgICAgMTgxIC05OSAyMDcgLTExMCBsNDcgLTIxIDEyMSA2OSBjMTY4IDk2IDI1NSAxNDEgMjcyIDE0MSAxMiAwIDE0IC0zOCAxNCAtMjI4CiAgICAgICAgICAgICAgICAgICAgICAgIGwwIC0yMjggLTc3IC00NyAtNzggLTQ3IC03IDE0NiBjLTMgODAgLTggMTQ3IC0xMCAxNDkgLTIgMiAtNTMgLTI1IC0xMTMgLTYwCiAgICAgICAgICAgICAgICAgICAgICAgIC02MSAtMzUgLTExOSAtNjQgLTEyOSAtNjUgLTExIDAgLTcwIDI3IC0xMzIgNjAgLTYyIDMzIC0xMTUgNjAgLTExNyA2MCAtMyAwCiAgICAgICAgICAgICAgICAgICAgICAgIC04IC02MyAtMTIgLTE0MCAtNCAtNzcgLTExIC0xNDAgLTE2IC0xNDAgLTQgMCAtMzkgMTkgLTc4IDQyIGwtNzAgNDIgLTMgMTI2CiAgICAgICAgICAgICAgICAgICAgICAgIGMtNCAxODIgMSAzNDAgMTIgMzQwIDUgMCA4MSAtNDAgMTY5IC04OXogbTE5NSAtNDU4IGw1NSAtMjcgNDEgMjggYzIzIDE1IDQ4CiAgICAgICAgICAgICAgICAgICAgICAgIDI1IDU2IDIyIDE1IC02IDIwIC03OSA4IC0xMTEgLTcgLTE4IC05NCAtNjUgLTEyMSAtNjUgLTIyIDAgLTgzIDM1IC0xMDAgNTgKICAgICAgICAgICAgICAgICAgICAgICAgLTE4IDIyIC0xNSAxMjIgMyAxMjIgMiAwIDI4IC0xMiA1OCAtMjd6Ii8+CiAgICAgICAgICAgICAgICAgICAgPC9nPgogICAgICAgICAgICAgICAgPC9zdmc+';

			$icon = 'data:image/svg+xml;base64,' . $svg;

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			$page = add_menu_page( __( 'Max Mega Menu', 'megamenu' ), __( 'Mega Menu', 'megamenu' ), $capability, 'maxmegamenu', array( $this, 'page' ), $icon );

			$tabs = apply_filters( 'megamenu_menu_tabs', array() );

			foreach ( $tabs as $key => $title ) {
				if ( $key == 'menu_locations' ) {
					add_submenu_page( 'maxmegamenu', __( 'Max Mega Menu', 'megamenu' ) . ' - ' . $title, $title, $capability, 'maxmegamenu', array( $this, 'page' ) );
				} else {
					add_submenu_page( 'maxmegamenu', __( 'Max Mega Menu', 'megamenu' ) . ' - ' . $title, $title, $capability, 'maxmegamenu_' . $key, array( $this, 'page' ) );
				}
			}

		}



		/**
		 * Main settings page wrapper.
		 *
		 * @since 1.4
		 */
		public function page() {

			$tab = isset( $_GET['page'] ) ? substr( $_GET['page'], 12 ) : false;

			// backwards compatibility
			if ( isset( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			}

			if ( ! $tab ) {
				$tab = 'menu_locations';
			}

			$header_links = apply_filters(
				'megamenu_header_links',
				array(
					'homepage'        => array(
						'url'    => 'https://www.megamenu.com/?utm_source=free&amp;utm_medium=settings&amp;utm_campaign=pro',
						'target' => '_mmmpro',
						'text'   => __( 'Homepage', 'megamenu' ),
						'class'  => '',
					),
					'documentation'   => array(
						'url'    => 'https://www.megamenu.com/documentation/installation/?utm_source=free&amp;utm_medium=settings&amp;utm_campaign=pro',
						'text'   => __( 'Documentation', 'megamenu' ),
						'target' => '_mmmpro',
						'class'  => '',
					),
					'troubleshooting' => array(
						'url'    => 'https://www.megamenu.com/articles/troubleshooting/?utm_source=free&amp;utm_medium=settings&amp;utm_campaign=pro',
						'text'   => __( 'Troubleshooting', 'megamenu' ),
						'target' => '_mmmpro',
						'class'  => '',
					),
				)
			);

			if ( ! is_plugin_active( 'megamenu-pro/megamenu-pro.php' ) ) {
				$header_links['pro'] = array(
					'url'    => 'https://www.megamenu.com/upgrade/?utm_source=free&amp;utm_medium=settings&amp;utm_campaign=pro',
					'target' => '_mmmpro',
					'text'   => __( 'Upgrade to Pro', 'megamenu' ),
					'class'  => 'mega-highlight',
				);
			}

			$versions = apply_filters(
				'megamenu_versions',
				array(
					'core' => array(
						'version' => MEGAMENU_VERSION,
						'text'    => __( 'Core version', 'megamenu' ),
					),
					'pro'  => array(
						'version' => "<a href='https://www.megamenu.com/upgrade/?utm_source=free&amp;utm_medium=settings&amp;utm_campaign=pro' target='_mmmpro'>not installed</a>",
						'text'    => __( 'Pro extension', 'megamenu' ),
					),
				)
			);

			?>

		<div class='megamenu_outer_wrap'>
			<div class='megamenu_header_top'>
				<ul>
					<?php
					foreach ( $header_links as $id => $data ) {
						echo "<li class='{$data['class']}'><a href='{$data['url']}' target='{$data['target']}'>{$data['text']}";
						echo '</a>';
						echo '</li>';
					}
					?>
				</ul>
			</div>
			<div class='megamenu_header'>
				<div class='megamenu_header_left'>
					<h2><?php _e( 'Max Mega Menu', 'megamenu' ); ?></h2>
					<div class='version'>
						<?php

							$total     = count( $versions );
							$count     = 0;
							$separator = ' - ';

						foreach ( $versions as $id => $data ) {
							echo $data['text'] . ': <b>' . $data['version'] . '</b>';

							$count = $count + 1;

							if ( $total > 0 && $count != $total ) {
								echo $separator;
							}
						}
						?>
					</div>
				</div>

				<?php 
					if ( isset( $_GET['debug'] ) ) {
						echo "<textarea style='width: 100%; height: 400px;'>";
						var_dump( get_option("megamenu_settings") );
						echo "</textarea>";
					}
				?>
			</div>

			<div class='megamenu_wrap'>
					<div class='megamenu_left'>
						<ul class='mega-page-navigation'>
							<?php
								$tabs = apply_filters( 'megamenu_menu_tabs', array() );

							foreach ( $tabs as $key => $title ) {
								$class = $tab == $key ? 'active' : '';

								if ( $key == 'menu_locations' ) {
									$args = array( 'page' => 'maxmegamenu' );
								} else {
									$args = array( 'page' => 'maxmegamenu_' . $key );
								}

								$url = esc_url( add_query_arg( $args, admin_url( 'admin.php' ) ) );

								echo "<li class='{$key}'><a class='{$class}' href='{$url}'>{$title}</a></li>";
							}
							?>
						</ul>
					</div>
					<div class='megamenu_right'>
							<?php $this->print_messages(); ?>

							<?php

							$saved_settings = get_option( 'megamenu_settings' );

							if ( has_action( "megamenu_page_{$tab}" ) ) {
								do_action( "megamenu_page_{$tab}", $saved_settings );
							} else {
								do_action( 'megamenu_page_menu_locations', $saved_settings );
							}

							?>
					</div>
			</div>



		</div>

			<?php
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
		 * Display messages to the user
		 *
		 * @since 1.0
		 */
		public function print_messages() {

			if ( is_plugin_active( 'clearfy/clearfy.php' ) ) {
				if ( $clearfy_options = get_option( 'wbcr_clearfy_cache_options' ) ) {
					if ( $clearfy_options['disable_dashicons'] == true ) {
						echo "<p class='fail'>" . __( 'Please enable Dashicons in the Clearfy plugin options. Max Mega Menu requires Dashicons.', 'megamenu' ) . '</p>';
					}
				}
			}

			do_action( 'megamenu_print_messages' );

		}


		/**
		 * Enqueue admin scripts
		 *
		 * @since 1.8.3
		 */
		public function enqueue_scripts( $hook ) {

			wp_deregister_style( 'select2' );
			wp_deregister_script( 'select2' );

			wp_enqueue_style( 'select2', MEGAMENU_BASE_URL . 'js/select2/select2.css', false, MEGAMENU_VERSION );
			wp_enqueue_script( 'mega-menu-select2', MEGAMENU_BASE_URL . 'js/select2/select2.min.js', array(), MEGAMENU_VERSION );

			wp_enqueue_style( 'mega-menu-settings', MEGAMENU_BASE_URL . 'css/admin/admin.css', false, MEGAMENU_VERSION );

			wp_enqueue_style( 'spectrum', MEGAMENU_BASE_URL . 'js/spectrum/spectrum.css', false, "1.8.1" );
			wp_enqueue_script( 'spectrum', MEGAMENU_BASE_URL . 'js/spectrum/spectrum.js', array( 'jquery' ), "1.8.1" );

			wp_localize_script(
				'spectrum',
				'megamenu_spectrum_settings',
				apply_filters( 'megamenu_spectrum_localisation', array() )
			);

			wp_enqueue_script( 'mega-menu-theme-editor', MEGAMENU_BASE_URL . 'js/settings.js', array( 'jquery', 'spectrum', 'code-editor' ), MEGAMENU_VERSION );

			wp_localize_script(
				'mega-menu-theme-editor',
				'megamenu_settings',
				array(
					'saving'                            => __( 'Saving', 'megamenu' ),
					'confirm'                           => __( 'Are you sure?', 'megamenu' ),
					'theme_save_error'                  => __( 'Error saving theme.', 'megamenu' ),
					'theme_save_error_refresh'          => __( 'Please try refreshing the page.', 'megamenu' ),
					'theme_save_error_exhausted'        => __( 'The server ran out of memory whilst trying to regenerate the menu CSS.', 'megamenu' ),
					'theme_save_error_memory_limit'     => __( 'Try disabling unusued plugins to increase the available memory. Alternatively, for details on how to increase your server memory limit see:', 'megamenu' ),
					'theme_save_error_500'              => __( 'The server returned a 500 error. The server did not provide an error message (you should find details of the error in your server error log), but this is usually due to your server memory limit being reached.', 'megamenu' ),
					'increase_memory_limit_url'         => 'http://www.wpbeginner.com/wp-tutorials/fix-wordpress-memory-exhausted-error-increase-php-memory/',
					'increase_memory_limit_anchor_text' => 'How to increase the WordPress memory limit',
				)
			);

			if ( function_exists( 'wp_enqueue_code_editor' ) ) {
				wp_deregister_style( 'codemirror' );
				wp_deregister_script( 'codemirror' );

				$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/x-scss' ) );
				wp_localize_script( 'mega-menu-theme-editor', 'cm_settings', $cm_settings );
				wp_enqueue_style( 'wp-codemirror' );
			}
		}

	}

endif;
