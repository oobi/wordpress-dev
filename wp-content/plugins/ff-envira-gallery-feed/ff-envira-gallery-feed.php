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
 * @package           Midgard_Feed2
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly - Envira Gallery Feed
 * Plugin URI:        www.fi.net.au
 * Description:       Add REST feed support to Envira Galleries (requires Envira plugin)
 * Version:           1.0
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ff-envira-gallery-feed
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ff_envira_gallery_feed() {

	require plugin_dir_path( __FILE__ ) . 'includes/ff-envira-feed.php';

	$plugin = new FF\Envira\FF_Envira_Feed();
	$plugin->run();

}

run_ff_envira_gallery_feed();
