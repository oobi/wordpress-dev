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
 * @package           Midgard_Auth_JWT
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly Midgard - Auth JWT
 * Plugin URI:        www.fi.net.au
 * Description:       Generate JWT for logged in user. Depends on ff-jwt-authentication-for-wp-rest-api - must be active.
 * Version:           1.5.2
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       midgard-auth-jwt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define version and name for speed (rather than read metadata which is slow)
define('MIDGARD_PLUGIN_AUTH_JWT_NAME', 'midgard-auth-jwt');
define('MIDGARD_PLUGIN_AUTH_JWT_VERSION', '1.5.2');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_midgard_auth_jwt() {
	// $plugin = new Midgard_Auth_JWT();
	// $plugin->run();
	if( class_exists('\\FF_WP_REST_JWT\\Jwt_Auth') ) {
		require_once(dirname(__FILE__) . '/class-midgard-auth-jwt.php');
		$plugin = new \FF\Midgard\Midgard_Auth_JWT();
		$plugin->run();
	} else {
		// show a warning
		printf('<div class="notice notice-warning"><p>%s</p></div>',
				__('The Midgard Auth JWT plugin relies on "Firefly JWT Authentication for WP-API". This plugin must be installed and activated for it to work.', 'midgard-auth-jwt'));
	}
}

// run only when Midgard master plugin is ready (classes loaded)
add_action('init', 'run_midgard_auth_jwt');