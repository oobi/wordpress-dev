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
 * @package           Buzz_Addon_Print_View
 *
 * @wordpress-plugin
 * Plugin Name:       Buzz Add-On - Print View
 * Plugin URI:        http://www.thebuzz.net.au/add-ons/#print-view
 * Description:       Adds a Print View to Buzz Newsletter
 * Version:           1.4.1
 * Author:            Firefly Interactive
 * Author URI:        http://www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buzz-addon-print-view
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

	// Set up constant (for checking in theme)
	define( 'BUZZ_ADDON_PRINT_VIEW', TRUE );

	include_once( dirname(  __FILE__ ) . '/class-buzz-addon-print-view.php');

	/**
	 * Init Buzz_Addon_Print_View Class
	 */
	$buzz_addon = new Buzz_Addon_Print_View();

	/**
	 * Register Activation and Deactivation functions
	 */
	register_activation_hook( __FILE__, array( $buzz_addon, 'activate' ) );
	register_deactivation_hook( __FILE__, array( $buzz_addon, 'deactivate' ) );


	// get the newsletter print URL
	if ( ! function_exists( 'ff_get_print_url' ) ) :
	function ff_get_print_url( $newsletter_id ) {
		return Buzz_Addon_Print_View::get_print_url( $newsletter_id );
	}
	endif;


	// Initilaise the plugin
	$buzz_addon->plugin_init();

}
