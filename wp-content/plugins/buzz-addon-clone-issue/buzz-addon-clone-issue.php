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
 * @package           Buzz_Addon_Taxonomies
 *
 * @wordpress-plugin
 * Plugin Name:       Buzz Add-On - Clone Issue
 * Plugin URI:        http://www.thebuzz.net.au/add-ons/#clone
 * Description:       Adds the ability to clone an entire issue and associated articles to the Buzz Newsletter
 * Version:           1.0
 * Author:            Firefly Interactive
 * Author URI:        http://www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buzz-clone
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

	require_once( plugin_dir_path(__FILE__) . 'class-buzz-addon-clone-issue.php');

	// Set up constant (for checking in theme)
	define( 'BUZZ_ADDON_CLONE', TRUE );


	// create a new instance
	$buzz_addon = new Buzz_Addon_Clone_Issue();

	// Initialise the plugin
	$buzz_addon->plugin_init();
}
