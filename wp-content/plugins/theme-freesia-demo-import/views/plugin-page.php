<?php
/**
 * The plugin page view - the "settings" page of the plugin.
 *
 * @package tfdi
 */

namespace OCDI;

$predefined_themes = $this->import_files;

if ( ! empty( $this->import_files ) && isset( $_GET['import-mode'] ) && 'manual' === $_GET['import-mode'] ) {
	$predefined_themes = array();
}

?>

<div class="tfdi  wrap  about-wrap">

	<?php ob_start(); ?>
		<h1 class="tfdi__title  dashicons-before  dashicons-upload"><?php esc_html_e( 'One Click Demo Import', 'theme-freesia-demo-import' ); ?></h1>
	<?php
	$plugin_title = ob_get_clean();

	// Display the plugin title (can be replaced with custom title text through the filter below).
	echo wp_kses_post( apply_filters( 'tf-tfdi/plugin_page_title', $plugin_title ) );

	// Display warrning if PHP safe mode is enabled, since we wont be able to change the max_execution_time.
	if ( ini_get( 'safe_mode' ) ) {
		printf(
			esc_html__( '%sWarning: your server is using %sPHP safe mode%s. This means that you might experience server timeout errors.%s', 'theme-freesia-demo-import' ),
			'<div class="notice  notice-warning  is-dismissible"><p>',
			'<strong>',
			'</strong>',
			'</p></div>'
		);
	}

	// Start output buffer for displaying the plugin intro text.
	ob_start();
	?>

	<div class="tfdi__intro-notice  notice  notice-warning  is-dismissible">
		<p><?php esc_html_e( 'Before you begin, make sure all the required plugins are activated.', 'theme-freesia-demo-import' ); ?></p>
	</div>

	<div class="tfdi__intro-text">
		<p class="about-description">
			<?php esc_html_e( 'Importing demo data (post, pages, images, theme settings, ...) is the easiest way to setup your theme.', 'theme-freesia-demo-import' ); ?>
			<?php esc_html_e( 'It will allow you to quickly edit everything instead of creating content from scratch.', 'theme-freesia-demo-import' ); ?>
		</p>

		<hr>

		<p><?php esc_html_e( 'When you import the data, the following things might happen:', 'theme-freesia-demo-import' ); ?></p>

		<ul>
			<li><?php esc_html_e( 'No existing posts, pages, categories, images, custom post types or any other data will be deleted or modified.', 'theme-freesia-demo-import' ); ?></li>
			<li><?php esc_html_e( 'Posts, pages, images, widgets, menus and other theme settings will get imported.', 'theme-freesia-demo-import' ); ?></li>
			<li><?php esc_html_e( 'Please click on the Import button only once and wait, it can take a couple of minutes.', 'theme-freesia-demo-import' ); ?></li>
		</ul>

		<?php if ( ! empty( $this->import_files ) ) : ?>
			<?php if ( empty( $_GET['import-mode'] ) || 'manual' !== $_GET['import-mode'] ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => $this->plugin_page_setup['menu_slug'], 'import-mode' => 'manual' ), admin_url( $this->plugin_page_setup['parent_slug'] ) ) ); ?>" class="tfdi__import-mode-switch"><?php esc_html_e( 'Switch to manual import!', 'theme-freesia-demo-import' ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => $this->plugin_page_setup['menu_slug'] ), admin_url( $this->plugin_page_setup['parent_slug'] ) ) ); ?>" class="tfdi__import-mode-switch"><?php esc_html_e( 'Switch back to theme predefined imports!', 'theme-freesia-demo-import' ); ?></a>
			<?php endif; ?>
		<?php endif; ?>

		<hr>
	</div>

	<?php
	$plugin_intro_text = ob_get_clean();

	// Display the plugin intro text (can be replaced with custom text through the filter below).
	echo wp_kses_post( apply_filters( 'tf-tfdi/plugin_intro_text', $plugin_intro_text ) );
	?>

	<?php if ( empty( $this->import_files ) ) : ?>
		<div class="notice  notice-info  is-dismissible">
			<p><?php esc_html_e( 'There are no predefined import files available in this theme. Please upload the import files manually!', 'theme-freesia-demo-import' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( empty( $predefined_themes ) ) : ?>

		<div class="tfdi__file-upload-container">
			<h2><?php esc_html_e( 'Manual demo files upload', 'theme-freesia-demo-import' ); ?></h2>

			<div class="tfdi__file-upload">
				<h3><label for="content-file-upload"><?php esc_html_e( 'Choose a XML file for content import:', 'theme-freesia-demo-import' ); ?></label></h3>
				<input id="tfdi__content-file-upload" type="file" name="content-file-upload">
			</div>

			<div class="tfdi__file-upload">
				<h3><label for="widget-file-upload"><?php esc_html_e( 'Choose a WIE or JSON file for widget import:', 'theme-freesia-demo-import' ); ?></label></h3>
				<input id="tfdi__widget-file-upload" type="file" name="widget-file-upload">
			</div>

			<div class="tfdi__file-upload">
				<h3><label for="customizer-file-upload"><?php esc_html_e( 'Choose a DAT file for customizer import:', 'theme-freesia-demo-import' ); ?></label></h3>
				<input id="tfdi__customizer-file-upload" type="file" name="customizer-file-upload">
			</div>

			<?php if ( class_exists( 'ReduxFramework' ) ) : ?>
			<div class="tfdi__file-upload">
				<h3><label for="redux-file-upload"><?php esc_html_e( 'Choose a JSON file for Redux import:', 'theme-freesia-demo-import' ); ?></label></h3>
				<input id="tfdi__redux-file-upload" type="file" name="redux-file-upload">
				<div>
					<label for="redux-option-name" class="tfdi__redux-option-name-label"><?php esc_html_e( 'Enter the Redux option name:', 'theme-freesia-demo-import' ); ?></label>
					<input id="tfdi__redux-option-name" type="text" name="redux-option-name">
				</div>
			</div>
			<?php endif; ?>
		</div>

		<p class="tfdi__button-container">
			<button class="tfdi__button  button  button-hero  button-primary  js-tfdi-import-data"><?php esc_html_e( 'Import Demo Data', 'theme-freesia-demo-import' ); ?></button>
		</p>

	<?php elseif ( 1 === count( $predefined_themes ) ) : ?>

		<div class="tfdi__demo-import-notice  js-tfdi-demo-import-notice"><?php
			if ( is_array( $predefined_themes ) && ! empty( $predefined_themes[0]['import_notice'] ) ) {
				echo wp_kses_post( $predefined_themes[0]['import_notice'] );
			}
		?></div>

		<p class="tfdi__button-container">
			<button class="tfdi__button  button  button-hero  button-primary  js-tfdi-import-data"><?php esc_html_e( 'Import Demo Data', 'theme-freesia-demo-import' ); ?></button>
		</p>

	<?php else : ?>

		<!-- TFDI grid layout -->
		<div class="tfdi__gl  js-tfdi-gl">
		<?php
			// Prepare navigation data.
			$categories = Helpers::get_all_demo_import_categories( $predefined_themes );
		?>
			<?php if ( ! empty( $categories ) ) : ?>
				<div class="tfdi__gl-header  js-tfdi-gl-header">
					<nav class="tfdi__gl-navigation">
						<ul>
							<li class="active"><a href="#all" class="tfdi__gl-navigation-link  js-tfdi-nav-link"><?php esc_html_e( 'All', 'theme-freesia-demo-import' ); ?></a></li>
							<?php foreach ( $categories as $key => $name ) : ?>
								<li><a href="#<?php echo esc_attr( $key ); ?>" class="tfdi__gl-navigation-link  js-tfdi-nav-link"><?php echo esc_html( $name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
					<div clas="tfdi__gl-search">
						<input type="search" class="tfdi__gl-search-input  js-tfdi-gl-search" name="tfdi-gl-search" value="" placeholder="<?php esc_html_e( 'Search demos...', 'theme-freesia-demo-import' ); ?>">
					</div>
				</div>
			<?php endif; ?>
			<div class="tfdi__gl-item-container  wp-clearfix  js-tfdi-gl-item-container">
				<?php foreach ( $predefined_themes as $index => $import_file ) : ?>
					<?php
						// Prepare import item display data.
						$img_src = isset( $import_file['import_preview_image_url'] ) ? $import_file['import_preview_image_url'] : '';
						$is_plus = isset( $import_file['isPlus'] ) ? $import_file['isPlus'] : '';
						// Default to the theme screenshot, if a custom preview image is not defined.
						if ( empty( $img_src ) ) {
							$theme = wp_get_theme();
							$img_src = $theme->get_screenshot();
						}

					?>
					<div class="tfdi__gl-item js-tfdi-gl-item" data-categories="<?php echo esc_attr( Helpers::get_demo_import_item_categories( $import_file ) ); ?>" data-name="<?php echo esc_attr( strtolower( $import_file['import_file_name'] ) ); ?>">
						<div class="tfdi__gl-item-image-container">
							<?php if ( ! empty( $img_src ) ) : ?>
								<img class="tfdi__gl-item-image" src="<?php echo esc_url( $img_src ) ?>">
							<?php else : ?>
								<div class="tfdi__gl-item-image  tfdi__gl-item-image--no-image"><?php esc_html_e( 'No preview image.', 'theme-freesia-demo-import' ); ?></div>
							<?php endif; ?>
						</div>
						<div class="tfdi__gl-item-footer<?php echo ! empty( $import_file['preview_url'] ) ? '  tfdi__gl-item-footer--with-preview' : ''; ?>">
							<h4 class="tfdi__gl-item-title" title="<?php echo esc_attr( $import_file['import_file_name'] ); ?>"><?php echo esc_html( $import_file['import_file_name'] ); ?></h4>

							<?php if ($is_plus == true){ ?>
								<a class="tfdi__gl-item-button  buy-button button" href="<?php echo esc_url( $import_file['buy_url'] ); ?>" target="_blank"> <?php esc_html_e( 'Buy Now', 'theme-freesia-demo-import' ); ?></a>

							<?php } else { ?>
								<button class="tfdi__gl-item-button  button  button-primary  js-tfdi-gl-import-data" value="<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Import', 'theme-freesia-demo-import' ); ?></button>
							<?php } ?>
							
							<?php if ( ! empty( $import_file['preview_url'] ) ) : ?>
								<a class="tfdi__gl-item-button  button" href="<?php echo esc_url( $import_file['preview_url'] ); ?>" target="_blank"><?php esc_html_e( 'Preview', 'theme-freesia-demo-import' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div id="js-tfdi-modal-content"></div>

	<?php endif; ?>

	<p class="tfdi__ajax-loader  js-tfdi-ajax-loader">
		<span class="spinner"></span> <?php esc_html_e( 'Importing, please wait!', 'theme-freesia-demo-import' ); ?>
	</p>

	<div class="tfdi__response  js-tfdi-ajax-response"></div>
</div>
