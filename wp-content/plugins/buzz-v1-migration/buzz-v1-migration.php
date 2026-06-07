<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.fireflyinteractive.net
 * @since             1.0.0
 * @package           Buzz_V1_Migration
 *
 * @wordpress-plugin
 * Plugin Name:       Buzz v1 Migration
 * Plugin URI:        http://www.thebuzz.net.au/add-ons/#migration
 * Description:       DO NOT NETWORK ACTIVATE. Migrates Newsletter v1 posts and taxonomies to Buzz Newsletter v2. DO NOT NETWORK ACTIVATE.
 * Version:           1.1.2
 * Author:            Firefly Interactive
 * Author URI:        http://www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buzz-addon-v1-migration
 * Domain Path:       /languages
 * Network: false
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Set up constant (for checking in theme)
define( 'BUZZ_V1_MIGRATION', TRUE );

class Buzz_V1_Migration {

	public function plugin_init() {
		// add options page
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'buzz-v1-migration/options-page.php';
	}

	/**
	 * The code that runs during plugin activation.
	 */
	public function activate($networkwide) {
		if (is_multisite() && $networkwide) {
       		die('This plugin can\'t be activated networkwide');
       	}
	}

	/**
	 * The code that runs during plugin deactivation.
	 */
	public function deactivate() {

	}

}

/**
 * Init Buzz_Addon_V1_Migration Class
 */
$buzz_addon = new Buzz_V1_Migration();

/**
 * Register Activation and Deactivation functions
 */
register_activation_hook( __FILE__, array( $buzz_addon, 'activate' ) );
register_deactivation_hook( __FILE__, array( $buzz_addon, 'deactivate' ) );

/**
 * Add plugin actions if Buzz Newsletter plugin is active
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if( is_plugin_active( 'buzz-newsletter/buzz-newsletter.php' ) ) {

	// Initilaise the plugin
	$buzz_addon->plugin_init();

}
