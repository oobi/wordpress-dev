<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.fi.net.au
 * @since             1.0.0
 * @package           Midgard_Auth_Remote
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly Midgard - Auth Remote
 * Plugin URI:        www.fi.net.au
 * Description:       Plugin to allow remote authenticated calls to Midgard JWT Auth
 * Version:           1.0.2
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       midgard-auth-remote
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define version and name for speed (rather than read metadata which is slow)
define('MIDGARD_PLUGIN_AUTH_REMOTE_NAME', 'midgard-auth-remote');
define('MIDGARD_PLUGIN_AUTH_REMOTE_VERSION', '1.0.2');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_midgard_auth_remote() {
	require_once(dirname(__FILE__) . '/class-midgard-auth-remote.php');
	$plugin = new \FF\Midgard\Midgard_Auth_Remote();
	$plugin->run();
}

// run the plugin
run_midgard_auth_remote();