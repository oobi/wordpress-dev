<?php

namespace FF\LogicalDoc;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.fi.net.au
 * @since             1.0
 * @package           Firefly
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly LogicalDoc (actuaries.asn.au)
 * Plugin URI:        www.fi.net.au
 * Description:       Sync posts to LogicalDOC as documents. Custom fields setup for actuaries institute.
 * Version:           2.7.1
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ff-logicaldoc
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define version and name for speed (rather than read metadata which is slow)
define('FF_LOGICALDOC_PLUGIN_NAME', 'ff-logicaldoc');
define('FF_LOGICALDOC_PLUGIN_VERSION', '2.7.1');

// Logical definitions - override in wp-config.php
if(!defined('LOGICAL_HOST')) {
	define('LOGICAL_HOST', '');
}
if(!defined('LOGICAL_USER')) {
	define('LOGICAL_USER', '');
}
if(!defined('LOGICAL_PASS')) {
	define('LOGICAL_PASS', '');
}
if(!defined('LOGICAL_ROOT_FOLDER')) {
	define('LOGICAL_ROOT_FOLDER', 4);
}
if(!defined('LOGICAL_TEMPLATE_ID')) {
	define('LOGICAL_TEMPLATE_ID', 0);
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/ff-logicaldoc.php';


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
	$plugin = new LogicalDoc(FF_LOGICALDOC_PLUGIN_NAME, FF_LOGICALDOC_PLUGIN_VERSION);
	$plugin->run();
}
run_midgard();
