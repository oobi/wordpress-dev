<?php

namespace FF\DocRaptor;

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
 * Plugin Name:       Firefly DocRaptor (actuaries.asn.au)
 * Plugin URI:        www.fi.net.au
 * Description:       Add hooks for PDF generation via DocRaptor
 * Version:           1.2
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ff-docraptor
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define version and name for speed (rather than read metadata which is slow)
define('FF_DOCRAPTOR_PLUGIN_NAME', 'ff-docraptor');
define('FF_DOCRAPTOR_PLUGIN_VERSION', '1.2');

// DocRaptor definitions - override in wp-config.php
if(!defined('DOCRAPTOR_API_KEY')) {
	define('DOCRAPTOR_API_KEY', '');
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/docraptor-plugin.php';


$plugin = new DocRaptorPlugin(FF_DOCRAPTOR_PLUGIN_NAME, FF_DOCRAPTOR_PLUGIN_VERSION);
$plugin->run();