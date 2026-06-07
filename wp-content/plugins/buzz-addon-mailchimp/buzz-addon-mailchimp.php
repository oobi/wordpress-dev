<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.fireflyinteractive.net
 * @since             1.4.0
 * @package           Buzz_Mailchimp
 *
 * @wordpress-plugin
 * Plugin Name:       Buzz Add-On - Mailchimp Integration
 * Plugin URI:        http://www.thebuzz.net.au/add-ons/#mailchimp
 * Description:       Integrates the Buzz Newsletter plugin with MailChimp send functionality.
 * Version:           1.6.4
 * Author:            Firefly Interactive
 * Author URI:        http://www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buzz-mailchimp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/activator.php
 */
function activate_buzz_addon_mailchimp() {

	// Require parent Buzz Newsletter plugin and Email View plugin
	if ( ( ! is_plugin_active( 'buzz-newsletter/buzz-newsletter.php' ) || ! is_plugin_active( 'buzz-addon-email-view/buzz-addon-email-view.php' ) )
		 && current_user_can( 'activate_plugins' ) ) {
        // Stop activation redirect and show error
        wp_die(
        	'Sorry, this plugin requires both the "Buzz Newsletter" and "Buzz Addon Email View" plugins to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>',
			'Plugin cannot be activated');
    }

	require_once plugin_dir_path( __FILE__ ) . 'includes/activator.php';
	Buzz_Mailchimp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/deactivator.php
 */
function deactivate_buzz_addon_mailchimp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.php';
	Buzz_Mailchimp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buzz_addon_mailchimp' );
register_deactivation_hook( __FILE__, 'deactivate_buzz_addon_mailchimp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/buzz-mailchimp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {

	$plugin = new Buzz_Mailchimp();
	$plugin->run();

}
run_plugin_name();
