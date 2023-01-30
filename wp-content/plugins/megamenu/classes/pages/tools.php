<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_Tools' ) ) :

	/**
	 * Handles all admin related functionality.
	 */
	class Mega_Menu_Tools {


		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'admin_post_megamenu_clear_css_cache', array( $this, 'tools_clear_css_cache' ) );
			add_action( 'admin_post_megamenu_delete_data', array( $this, 'delete_data' ) );

			add_filter( 'megamenu_menu_tabs', array( $this, 'add_tools_tab' ), 4 );
			add_action( 'megamenu_page_tools', array( $this, 'tools_page' ) );
		}

		/**
		 * Add the Menu Locations tab to our available tabs
		 *
		 * @param array $tabs
		 * @since 2.8
		 */
		public function add_tools_tab( $tabs ) {
			$tabs['tools'] = __( 'Tools', 'megamenu' );
			return $tabs;
		}


		/**
		 * Clear the CSS cache.
		 *
		 * @since 1.5
		 */
		public function tools_clear_css_cache() {
			check_admin_referer( 'megamenu_clear_css_cache' );
			do_action( 'megamenu_delete_cache' );
			$this->redirect( admin_url( 'admin.php?page=maxmegamenu_tools&clear_css_cache=true' ) );
		}


		/**
		 * Deletes all Max Mega Menu data from the database
		 *
		 * @since 1.5
		 */
		public function delete_data() {

			check_admin_referer( 'megamenu_delete_data' );

			do_action( 'megamenu_delete_cache' );

			// delete options
			delete_option( 'megamenu_settings' );
			delete_option( 'megamenu_locations' );
			delete_option( 'megamenu_toggle_blocks' );
			delete_option( 'megamenu_version' );
			delete_option( 'megamenu_initial_version' );
			delete_option( 'megamenu_themes_last_updated' );
			delete_option( 'megamenu_multisite_share_themes' );

			// delete all widgets assigned to menus
			$widget_manager = new Mega_Menu_Widget_Manager();

			if ( $mega_menu_widgets = $widget_manager->get_mega_menu_sidebar_widgets() ) {
				foreach ( $mega_menu_widgets as $widget_id ) {
					$widget_manager->delete_widget( $widget_id );
				}
			}

			// delete all mega menu metadata stored against menu items
			delete_metadata( 'post', 0, '_megamenu', '', true );

			// clear cache
			delete_transient( 'megamenu_css' );

			// delete custom themes
			max_mega_menu_delete_themes();

			$this->redirect( admin_url( 'admin.php?page=maxmegamenu_tools&delete_data=true' ) );
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
		 * Content for 'Tools' tab
		 *
		 * @since 1.4
		 */
		public function tools_page( $saved_settings ) {
			$this->print_messages();

			?>

		<div class='menu_settings menu_settings_tools'>
			<h3 class='first'><?php _e( 'Tools', 'megamenu' ); ?></h3>
			<table>
				<tr>
					<td class='mega-name'>
						<?php _e( 'Cache', 'megamenu' ); ?>
						<div class='mega-description'><?php _e( 'The CSS for your menu is updated each time a menu or a menu theme is changed. You can force the menu CSS to be updated using this tool.', 'megamenu' ); ?></div>
					</td>
					<td class='mega-value'>
						<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
							<?php wp_nonce_field( 'megamenu_clear_css_cache' ); ?>
							<input type="hidden" name="action" value="megamenu_clear_css_cache" />

							<input type='submit' class='button button-primary' value='<?php _e( 'Clear CSS Cache', 'megamenu' ); ?>' />

							<?php if ( get_transient( 'megamenu_css_last_updated' ) ) : ?>
								<p><em><small><?php echo sprintf( __( 'The menu CSS was last updated on %s', 'megamenu' ), date( 'l jS F Y H:i:s', get_transient( 'megamenu_css_last_updated' ) ) ); ?><small><em></p>
							<?php endif; ?>
						</form>
					</td>
				</tr>
				<tr>
					<td class='mega-name'>
						<?php _e( 'Plugin Data', 'megamenu' ); ?>
						<div class='mega-description'><?php _e( 'Delete all saved Max Mega Menu plugin data from the database. Use with caution!', 'megamenu' ); ?></div>
					</td>
					<td class='mega-value'>
						<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
							<?php wp_nonce_field( 'megamenu_delete_data' ); ?>
							<input type="hidden" name="action" value="megamenu_delete_data" />

							<input type='submit' class='button button-secondary confirm' value='<?php _e( 'Delete Data', 'megamenu' ); ?>' />
						</form>
					</td>
				</tr>
			</table>
		</div>

			<?php
		}


		/**
		 * Display messages to the user
		 *
		 * @since 1.0
		 */
		public function print_messages() {
			if ( isset( $_GET['clear_css_cache'] ) && $_GET['clear_css_cache'] == 'true' ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php esc_html_e( 'The cache has been cleared and the menu CSS has been regenerated.', 'megamenu' ) ?></p>

					<?php
						$theme_class = new Mega_Menu_Themes();

						$theme_class->show_cache_warning();
					?>
				</div>
				<?php
			}

			if ( isset( $_GET['delete_data'] ) && $_GET['delete_data'] == 'true' ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><?php _e( 'All plugin data removed', 'megamenu' ) ?></p>
				</div>
				<?php
			}
		}
	}

endif;
