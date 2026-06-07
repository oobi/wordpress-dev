<?php

namespace FF\Twitter;

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
 * @package           FF\Twitter
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly Twitter
 * Plugin URI:        www.fi.net.au
 * Description:       Read a twitter feed into a JSON for display however you like.
 * Version:           1.0
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ff-twitter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// transient key
define('FF_TWITTER_TRANSIENT', 'ff-list-tweets');

/**
 * Includes
 */
require plugin_dir_path( __FILE__ ) . 'class-twitter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
$plugin = new Twitter();