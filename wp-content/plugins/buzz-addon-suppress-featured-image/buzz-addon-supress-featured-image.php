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
 * @package           Buzz_Addon_Suppress_Featured_Image
 *
 * @wordpress-plugin
 * Plugin Name:       Buzz Add-On - Suppress Featured Image
 * Plugin URI:        http://www.thebuzz.net.au/add-ons/#email-view
 * Description:       Adds a switch to suppress the featured image in the article view.
 * Version:           1.0
 * Author:            Firefly Interactive
 * Author URI:        http://www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buzz-email
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add plugin actions if Buzz Newsletter plugin is active
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if( is_plugin_active( 'buzz-newsletter/buzz-newsletter.php' ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'class-buzz-addon-suppress-featured-image.php' );

	// Set up constant (for checking in theme)
	define( 'BUZZ_ADDON_SUPPRESS_FEATURED_IMAGE', TRUE );

	/**
	 * Init Buzz_Addon_Email_View Class
	 */
	$buzz_addon = new Buzz_Addon_Suppress_Featured_Image();

	/**
	 * Register Activation and Deactivation functions
	 */
	register_activation_hook( __FILE__, array( $buzz_addon, 'activate' ) );
	register_deactivation_hook( __FILE__, array( $buzz_addon, 'deactivate' ) );

	// Initilaise the plugin
	$buzz_addon->plugin_init();

}
