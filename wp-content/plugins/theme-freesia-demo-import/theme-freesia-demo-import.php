<?php

/*
Plugin Name: Theme Freesia Demo Import
Plugin URI: https://themefreesia.com/plugins/theme-freesia-demo-import
Description: Import your content, widgets and theme settings with one click. While activating Theme Freesia Demo Import Plugin you must deactivate One Click demo import plugins. You can't activate both plugin at the same time. After Theme Freesia demo importer plugin done its job. We recommended you to deactivate the plugins but it has done its job already.
Version: 1.0.3
Author: Theme Freesia
Author URI: https://themefreesia.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Text Domain: theme-freesia-demo-import
*/

// Block direct access to the main plugin file.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$arise_plus = class_exists('Arise_License_Menu');
$freesiaempire_plus = class_exists('Freesia_Empire_License_Menu');
$edge_plus = class_exists('Edge_License_Menu');
$pixgraphy_plus = class_exists('Pixgraphy_License_Menu');
$event_plus = class_exists('Event_License_Menu');
$excellent_plus = class_exists('Excellent_License_Menu');
$idyllic_plus = class_exists('Idyllic_License_Menu');
$storexmas_plus = class_exists('StoreXmas_License_Menu');
$magbook_plus = class_exists('Magbook_License_Menu');
$photograph_plus = class_exists('Photograph_License_Menu');
$extension_plus = class_exists('Extension_License_Menu');
$shoppingcart_plus = class_exists('ShoppingCart_License_Menu');
$cocktail_plus = class_exists('Cocktail_License_Menu');
$eventsia_plus = class_exists('Eventsia_License_Menu');
$supermarket_plus = class_exists('Supermarket_License_Menu');

$theme = wp_get_theme();

if ($arise_plus || $freesiaempire_plus || $edge_plus || $pixgraphy_plus || $event_plus || $excellent_plus || $idyllic_plus || $storexmas_plus || $magbook_plus || $photograph_plus || $extension_plus || $shoppingcart_plus || $cocktail_plus || $eventsia_plus || $supermarket_plus ) {

	if ($theme->Name == 'Photograph'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/photograph-plus.php';
	

	} elseif ($theme->Name == 'Wedding Photos'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/photograph-plus.php';
	

	} elseif ($theme->Name == 'Webart'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/photograph-plus.php';
	

	} elseif ($theme->Name == 'Idyllic'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/idyllic-plus.php';

	} elseif ($theme->Name == 'Arise'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/arise-plus.php';

	}  elseif ($theme->Name == 'Freesia Empire'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/freesia-empire-plus.php';

	}   elseif ($theme->Name == 'Freesia Business'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/freesia-empire-plus.php';

	}   elseif ($theme->Name == 'Freesia Corporate'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/freesia-empire-plus.php';

	}  elseif ($theme->Name == 'Edge'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/edge-plus.php';

	}  elseif ($theme->Name == 'Alternative'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/edge-plus.php';
	

	}   elseif ($theme->Name == 'Pixgraphy'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/pixgraphy-plus.php';

	}   elseif ($theme->Name == 'Event'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/event-plus.php';

	}   elseif ($theme->Name == 'Excellent'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/excellent-plus.php';

	}   elseif ($theme->Name == 'StoreXmas'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/storexmas-plus.php';

	}   elseif ($theme->Name == 'Magbook'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/magbook-plus.php';

	}   elseif ($theme->Name == 'Extension'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/extension-plus.php';

	}   elseif ($theme->Name == 'ShoppingCart'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/shoppingcart-plus.php';

	}   elseif ($theme->Name == 'Cocktail'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/cocktail-plus.php';

	}   elseif ($theme->Name == 'Cappuccino'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/cocktail-plus.php';

	}   elseif ($theme->Name == 'Mocktail'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/cocktail-plus.php';

	}   elseif ($theme->Name == 'Eventsia'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/eventsia-plus.php';

	}   elseif ($theme->Name == 'Supermarket'){

		require_once plugin_dir_path( __FILE__ ) .'plus-files/supermarket-plus.php';

	}

} else {

	if ($theme->Name == 'Photograph'){

	require_once plugin_dir_path( __FILE__ ) .'files/photograph.php';
	

	} elseif ($theme->Name == 'Idyllic'){

		require_once plugin_dir_path( __FILE__ ) .'files/idyllic.php';

	}  elseif ($theme->Name == 'Alternative'){

		require_once plugin_dir_path( __FILE__ ) .'files/alternative.php';

	}  elseif ($theme->Name == 'Arise'){

		require_once plugin_dir_path( __FILE__ ) .'files/arise.php';

	}  elseif ($theme->Name == 'Freesia Empire'){

		require_once plugin_dir_path( __FILE__ ) .'files/freesia-empire.php';

	}  elseif ($theme->Name == 'Edge'){

		require_once plugin_dir_path( __FILE__ ) .'files/edge.php';

	}  elseif ($theme->Name == 'Pixgraphy'){

		require_once plugin_dir_path( __FILE__ ) .'files/pixgraphy.php';

	}   elseif ($theme->Name == 'Event'){

		require_once plugin_dir_path( __FILE__ ) .'files/event.php';

	}   elseif ($theme->Name == 'Excellent'){

		require_once plugin_dir_path( __FILE__ ) .'files/excellent.php';

	}   elseif ($theme->Name == 'StoreXmas'){

		require_once plugin_dir_path( __FILE__ ) .'files/storexmas.php';

	}   elseif ($theme->Name == 'Magbook'){

		require_once plugin_dir_path( __FILE__ ) .'files/magbook.php';

	}   elseif ($theme->Name == 'Webart'){

		require_once plugin_dir_path( __FILE__ ) .'files/webart.php';

	}   elseif ($theme->Name == 'Wedding Photos'){

		require_once plugin_dir_path( __FILE__ ) .'files/wedding-photos.php';

	}   elseif ($theme->Name == 'Freesia Business'){

		require_once plugin_dir_path( __FILE__ ) .'files/freesia-business.php';

	}   elseif ($theme->Name == 'Freesia Corporate'){

		require_once plugin_dir_path( __FILE__ ) .'files/freesia-corporate.php';

	}   elseif ($theme->Name == 'Extension'){

		require_once plugin_dir_path( __FILE__ ) .'files/extension.php';

	}   elseif ($theme->Name == 'ShoppingCart'){

		require_once plugin_dir_path( __FILE__ ) .'files/shoppingcart.php';

	}   elseif ($theme->Name == 'Cocktail'){

		require_once plugin_dir_path( __FILE__ ) .'files/cocktail.php';

	}   elseif ($theme->Name == 'Mocktail'){

		require_once plugin_dir_path( __FILE__ ) .'files/mocktail.php';

	}   elseif ($theme->Name == 'Cappuccino'){

		require_once plugin_dir_path( __FILE__ ) .'files/cappuccino.php';

	}   elseif ($theme->Name == 'Eventsia'){

		require_once plugin_dir_path( __FILE__ ) .'files/eventsia.php';

	}   elseif ($theme->Name == 'Supermarket'){

		require_once plugin_dir_path( __FILE__ ) .'files/supermarket.php';

	}

}

add_filter( 'tf-tfdi/regenerate_thumbnails_in_content_import', '__return_false' );

add_filter( 'tf-tfdi/disable_pt_branding', '__return_true' );

/**
 * Main plugin class with initialization tasks.
 */
class TFDI_Plugin {
	/**
	 * Constructor for this class.
	 */
	public function __construct() {
		/**
		 * Display admin error message if PHP version is older than 5.3.2.
		 * Otherwise execute the main plugin class.
		 */
		if ( version_compare( phpversion(), '5.3.2', '<' ) ) { 
			add_action( 'admin_notices', array( $this, 'old_php_admin_error_notice' ) );
		}
		else {
			// Set plugin constants.
			$this->set_plugin_constants();

			// Composer autoloader.
			require_once TF_DI_PATH . 'vendor/autoload.php';

			// Instantiate the main plugin class *Singleton*.
			$tf_one_click_demo_import = OCDI\ThemeFreesiaDemoImport::get_instance();

			// Register WP CLI commands
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				WP_CLI::add_command( 'tfdi list', array( 'OCDI\WPCLICommands', 'list_predefined' ) );
				WP_CLI::add_command( 'tfdi import', array( 'OCDI\WPCLICommands', 'import' ) );
			}
		}
	}


	/**
	 * Display an admin error notice when PHP is older the version 5.3.2.
	 * Hook it to the 'admin_notices' action.
	 */
	public function old_php_admin_error_notice() {
		$message = sprintf( esc_html__( 'The %2$sOne Click Demo Import%3$s plugin requires %2$sPHP 5.3.2+%3$s to run properly. Please contact your hosting company and ask them to update the PHP version of your site to at least PHP 5.3.2.%4$s Your current version of PHP: %2$s%1$s%3$s', 'theme-freesia-demo-import' ), phpversion(), '<strong>', '</strong>', '<br>' );

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}


	/**
	 * Set plugin constants.
	 *
	 * Path/URL to root of this plugin, with trailing slash and plugin version.
	 */
	private function set_plugin_constants() {
		// Path/URL to root of this plugin, with trailing slash.
		if ( ! defined( 'TF_DI_PATH' ) ) {
			define( 'TF_DI_PATH', plugin_dir_path( __FILE__ ) );
		}
		if ( ! defined( 'TF_DI_URL' ) ) {
			define( 'TF_DI_URL', plugin_dir_url( __FILE__ ) );
		}

		// Action hook to set the plugin version constant.
		add_action( 'admin_init', array( $this, 'set_plugin_version_constant' ) );
	}


	/**
	 * Set plugin version constant -> TF_DI_VERSION.
	 */
	public function set_plugin_version_constant() {
		if ( ! defined( 'TF_DI_VERSION' ) ) {
			$plugin_data = get_plugin_data( __FILE__ );
			define( 'TF_DI_VERSION', $plugin_data['Version'] );
		}
	}
}

// Instantiate the plugin class.
$tfdi_plugin = new TFDI_Plugin();
