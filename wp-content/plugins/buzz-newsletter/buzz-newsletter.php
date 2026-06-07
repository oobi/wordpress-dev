<?php

/**
 * @link            http://www.fireflyinteractive.net
 * @since           3.0.0
 * @package         ff_newsletter
 *
 * @wordpress-plugin
 * Plugin Name: 	Buzz Newsletter
 * Plugin URI: 		http://www.thebuzz.net.au/
 * Description: 	Buzz newsletter system
 * Version: 		3.7.2
 * Author: 			Firefly Interactive
 * Author URI: 		http://www.fi.net.au
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     ff_newsletter
 * Domain Path:     /languages
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_ff_newsletter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/ff-newsletter-activator.php';
	FF_Newsletter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_ff_newsletter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/ff-newsletter-deactivator.php';
	FF_Newsletter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ff_newsletter' );
register_deactivation_hook( __FILE__, 'deactivate_ff_newsletter' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/ff-newsletter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    3.0.0
 */
function run_ff_newsletter() {

	$plugin = FF_Newsletter::get_instance();
	$plugin->run();

}
run_ff_newsletter();
