<?php

namespace FF\Midgard;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.fi.net.au
 * @since             1.3
 * @package           Midgard
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly Midgard
 * Plugin URI:        www.fi.net.au
 * Description:       Ingest and manage feeds and field mappings from remote sources
 * Version:           2.20
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       midgard
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define version and name for speed (rather than read metadata which is slow)
define('MIDGARD_PLUGIN_NAME', 'midgard');
define('MIDGARD_PLUGIN_VERSION', '2.20');

// define master plugin path
define('MIDGARD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// define path to root cache folder
if(! defined('MIDGARD_CACHE_ROOT_DIR')) {
	define('MIDGARD_CACHE_ROOT_DIR', WP_CONTENT_DIR . '/midgard-cache');
}

// define site-specific cache path (used by any plugin which requires caching)
if( ! defined('MIDGARD_CACHE_DIR') ) {
	$midgard_cache_dir = (is_multisite() ? '/sites/' . get_current_blog_id() : '');
	define('MIDGARD_CACHE_DIR', MIDGARD_CACHE_ROOT_DIR . $midgard_cache_dir);
}

// define feed cache dir
if( ! defined('MIDGARD_FEED_CACHE_DIR') ) {
	define('MIDGARD_FEED_CACHE_DIR', MIDGARD_CACHE_DIR . '/feeds');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/activator.php
 */
function activate_midgard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/activator.php';
	Midgard_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/deactivator.php
 */
function deactivate_midgard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.php';
	Midgard_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'FF\Midgard\activate_midgard' );
register_deactivation_hook( __FILE__, 'FF\Midgard\deactivate_midgard' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/midgard.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_midgard() {
	$plugin = new Midgard(MIDGARD_PLUGIN_NAME, MIDGARD_PLUGIN_VERSION);
	$plugin->run();
}

add_filter('https_ssl_verify', '__return_false');

run_midgard();
