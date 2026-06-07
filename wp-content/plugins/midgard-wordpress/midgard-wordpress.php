<?php

use FF\Midgard\WordPress\Midgard_WordPress;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.fi.net.au
 * @since             1.0.1
 * @package           Midgard_WordPress
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly Midgard - WordPress
 * Plugin URI:        www.fi.net.au
 * Description:       Add WordPress secure feed support to Midgard
 * Version:           2.1
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       midgard-wordpress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define version and name for speed (rather than read metadata which is slow)
define('MIDGARD_PLUGIN_WORDPRESS_NAME', 'midgard-wordpress');
define('MIDGARD_PLUGIN_WORDPRESS_VERSION', '2.1');

// token URLs for remote WordPress
define('MIDGARD_PLUGIN_WORDPRESS_TOKEN_URL', '/wp-json/jwt-auth/v1/token');
define('MIDGARD_PLUGIN_WORDPRESS_TOKEN_VALIDATE_URL', '/wp-json/jwt-auth/v1/token/validate');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_midgard_wordpress() {

	// The core plugin class that is used to define internationalization,
	// admin-specific hooks, and public-facing site hooks.
	require_once(MIDGARD_PLUGIN_PATH . 'base/plugin-base.php');
	require plugin_dir_path( __FILE__ ) . 'includes/midgard-wordpress.php';

	$plugin = new Midgard_WordPress(MIDGARD_PLUGIN_WORDPRESS_NAME, MIDGARD_PLUGIN_WORDPRESS_VERSION);
	$plugin->run();

}

// run only when Midgard master plugin is ready (classes loaded)
add_action('midgard_ready', 'run_midgard_wordpress');

