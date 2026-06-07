<?php

namespace FF\Calendar;

/**
 * The plugin bootstrap file
 *
 * @link              http://www.fi.net.au
 * @since             1.0.0
 * @package           FF_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly Calendar
 * Plugin URI:        http://www.fi.net.au
 * Description:       Plugin to consume ICS feeds and display events as a calendar
 * Version:           2.7
 * Author:            Firefly Interactive
 * Author URI:        http://www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ff-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define version and name for speed (rather than read metadata which is slow)
define('FF_CALENDAR_PLUGIN_NAME', 'ff-calendar');
define('FF_CALENDAR_PLUGIN_VERSION', '2.7');
define('FF_CALENDAR_API_VERSION', 'v1'); 						// Used for REST routes. Must be prefixed with "v"
define('FF_CALENDAR_SHORTCODE_VERSION', 'v2'); 					// Used for shortcodes. Must be prefixed with "v"
define('FF_CALENDAR_SETTINGS_KEY', 'ff_calendar_settings'); 	// Settings key

// Define the plugin path
define('FF_CALENDAR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('FF_CALENDAR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// define cache path
if( ! defined('FF_CALENDAR_CACHE_DIR') ) {
	define('FF_CALENDAR_CACHE_DIR', WP_CONTENT_DIR . '/cache/ff-calendar');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ff-calendar-activator.php
 */
function activate() {
	require_once FF_CALENDAR_PLUGIN_PATH . 'includes/activator.php';
	Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ff-calendar-deactivator.php
 */
function deactivate() {
	require_once FF_CALENDAR_PLUGIN_PATH . 'includes/deactivator.php';
	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'FF\Calendar\activate' );
register_deactivation_hook( __FILE__, 'FF\Calendar\deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require FF_CALENDAR_PLUGIN_PATH . 'includes/ff-calendar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run() {
	$plugin = new FF_Calendar(FF_CALENDAR_PLUGIN_NAME, FF_CALENDAR_PLUGIN_VERSION);
	$plugin->run();

}
run();
