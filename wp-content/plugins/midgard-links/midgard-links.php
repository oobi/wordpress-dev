<?php

use FF\Midgard\Links\Midgard_Links;

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
 * @package           Midgard_Links
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly Midgard - Links
 * Plugin URI:        www.fi.net.au
 * Description:       Add Links support to Midgard
 * Version:           1.1
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       midgard-links
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define version and name for speed (rather than read metadata which is slow)
define('MIDGARD_PLUGIN_LINKS_NAME', 'midgard-links');
define('MIDGARD_PLUGIN_LINKS_VERSION', '1.1');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_midgard_links() {

	// The core plugin class that is used to define internationalization,
	// admin-specific hooks, and public-facing site hooks.
	require_once(MIDGARD_PLUGIN_PATH . 'base/plugin-base.php');
	require plugin_dir_path( __FILE__ ) . 'includes/midgard-links.php';

	$plugin = new Midgard_Links(MIDGARD_PLUGIN_LINKS_NAME, MIDGARD_PLUGIN_LINKS_VERSION);
	$plugin->run();

}

// run only when Midgard master plugin is ready (classes loaded)
add_action('midgard_ready', 'run_midgard_links');

