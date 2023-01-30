<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access.
}

if ( ! class_exists( 'Mega_Menu_General' ) ) :

	/**
	 * Handles the Mega Menu > Menu Settings page
	 */
	class Mega_Menu_General {

		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'admin_post_megamenu_save_settings', array( $this, 'save_settings' ) );
			add_action( 'admin_post_megamenu_delete_data', array( $this, 'delete_data' ) );

			add_filter( 'megamenu_menu_tabs', array( $this, 'add_general_tab' ), 4 );
			add_action( 'megamenu_page_general_settings', array( $this, 'general_settings_page' ) );
		}


		/**
		 * Add the Menu Locations tab to our available tabs
		 *
		 * @param array $tabs
		 * @since 2.8
		 */
		public function add_general_tab( $tabs ) {
			$tabs['general_settings'] = __( 'General Settings', 'megamenu' );
			return $tabs;
		}

		/**
		 * Sanitize multidimensional array
		 *
		 * @since 2.7.5
		 */
		public function sanitize_array( &$array ) {
			foreach ( $array as &$value ) {
				if ( ! is_array( $value ) ) {
					$value = sanitize_textarea_field( $value );
				} else {
					$this->sanitize_array( $value );
				}
			}
			return $array;
		}


		/**
		 * Save menu general settings.
		 *
		 * @since 1.0
		 */
		public function save_settings() {
			check_admin_referer( 'megamenu_save_settings' );

			if ( isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
				$settings           = $this->sanitize_array( $_POST['settings'] );
				$submitted_settings = apply_filters( 'megamenu_submitted_settings', $settings );
				$existing_settings  = get_option( 'megamenu_settings' );
				$new_settings       = array_merge( (array) $existing_settings, $submitted_settings );

				update_option( 'megamenu_settings', $new_settings );
			}

			delete_transient( 'megamenu_failed_to_write_css_to_filesystem' );

			do_action( 'megamenu_after_save_general_settings' );
			do_action( 'megamenu_delete_cache' );

			$url = isset( $_POST['_wp_http_referer'] ) ? $_POST['_wp_http_referer'] : admin_url( 'admin.php?page=maxmegamenu&saved=true' );

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
		 * Content for 'Settings' tab
		 *
		 * @since 1.4
		 */
		public function general_settings_page( $saved_settings ) {

			$css = isset( $saved_settings['css'] ) ? $saved_settings['css'] : 'fs';
			$js  = isset( $saved_settings['js'] ) ? $saved_settings['js'] : 'footer';

			$locations = get_registered_nav_menus();

			?>

		<div class='menu_settings menu_settings_general_settings'>

			<form action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="megamenu_save_settings" />
				<?php wp_nonce_field( 'megamenu_save_settings' ); ?>

				<h3 class='first'><?php esc_html_e( 'General Settings', 'megamenu' ); ?></h3>

				<table>
					<tr>
						<td class='mega-name'>
							<?php esc_html_e( 'CSS Output', 'megamenu' ); ?>
							<div class='mega-description'>
							</div>
						</td>
						<td class='mega-value'>
							<select name='settings[css]' id='mega_css'>
								<option value='fs' <?php echo selected( 'fs' === $css ); ?>><?php esc_html_e( 'Save to filesystem', 'megamenu' ); ?>
									<?php
									if ( get_transient( 'megamenu_failed_to_write_css_to_filesystem' ) ) {
										echo ' ' . esc_html( '(Action required: Check upload folder permissions)', 'megamenu' );
									}
									?>
								</option>
								<option value='head' <?php echo selected( 'head' === $css ); ?>><?php esc_html_e( 'Output in &lt;head&gt;', 'megamenu' ); ?></option>
								<option value='disabled' <?php echo selected( 'disabled' === $css ); ?>><?php esc_html_e( "Don't output CSS", 'megamenu' ); ?></option>
							<select>
							<div class='mega-description'>
								<div class='fs' style='display: <?php echo 'fs' === $css ? 'block' : 'none'; ?>'><?php esc_html_e( 'CSS will be saved to wp-content/uploads/maxmegamenu/style.css and enqueued from there.', 'megamenu' ); ?></div>
								<div class='head' style='display: <?php echo 'head' === $css ? 'block' : 'none'; ?>'><?php esc_html_e( 'CSS will be loaded from the cache in a &lt;style&gt; tag in the &lt;head&gt; of the page.', 'megamenu' ); ?></div>
								<div class='disabled' style='display: <?php echo 'disabled' === $css ? 'block' : 'none'; ?>'>
									<?php esc_html_e( 'CSS will not be output, you must enqueue the CSS for the menu manually.', 'megamenu' ); ?>
									<div class='fail'><?php esc_html_e( 'Selecting this option will effectively disable the theme editor and many of the features available in Max Mega Menu and Max Mega Menu Pro. Only enable this option if you fully understand the consequences.', 'megamenu' ); ?>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class='mega-name'>
							<?php esc_html_e( 'More options', 'megamenu' ); ?>
							<div class='mega-description'>
							</div>
						</td>
						<td class='mega-value'>

							<?php
								$locations_url = add_query_arg(
									array(
										'page'           => 'maxmegamenu',
									),
									admin_url( 'admin.php' )
								);
							?>

							<p>Looking for the <b>Click Event Behaviour</b>, <b>Mobile Menu Behaviour</b>, <b>Menu Item Descriptions</b>, <b>Unbind JavaScript Events</b>, <b>Prefix Menu Item Classes</b> or <b>Active Menu Instance</b> options?</p>
							<p>These can now be defined <em>per menu location</em> on the <em><a href='<?php echo esc_attr( $locations_url ); ?>'>Mega Menu > Menu Locations</a></em> page (expand a menu location and look within the 'Advanced' tab).</p>
						</td>
					</tr>
					<!--tr>
						<td class='mega-name'>
								<?php esc_html_e( 'JavaScript Output', 'megamenu' ); ?>
							<div class='mega-description'>
							</div>
						</td>
						<td class='mega-value'>
							<select name='settings[js]' id='mega_css'>
								<option value='footer' <?php echo selected( 'footer' === $js ); ?>><?php esc_html_e( 'Footer (default)', 'megamenu' ); ?></option>
								<option value='head' <?php echo selected( 'head' === $js ); ?>><?php esc_html_e( 'Output in &lt;head&gt;', 'megamenu' ); ?></option>
							<select>
						</td>
					</tr-->
				</table>

					<?php do_action( 'megamenu_general_settings', $saved_settings ); ?>

					<?php submit_button(); ?>
			</form>
		</div>

			<?php
		}

	}

endif;
