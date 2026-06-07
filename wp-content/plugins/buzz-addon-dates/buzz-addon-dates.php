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
 * @since             1.0.0
 * @package           Buzz_Addon_Dates
 *
 * @wordpress-plugin
 * Plugin Name:       Buzz Add-On - Dates
 * Plugin URI:        http://www.thebuzz.net.au/add-ons/#dates
 * Description:       Adds Dates to the Buzz Newsletter
 * Version:           2.3
 * Author:            Firefly Interactive
 * Author URI:        http://www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buzz-dates
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Set up constant (for checking in theme)
define('BUZZ_ADDON_DATES', TRUE);
define('BUZZ_ADDON_DATES_PATH', plugin_dir_path(__FILE__));

class Buzz_Addon_Dates
{

	public function plugin_init()
	{
		// dependencies
		require_once BUZZ_ADDON_DATES_PATH . 'admin/widget.php';
		require_once BUZZ_ADDON_DATES_PATH . 'admin/data.php';
		require_once BUZZ_ADDON_DATES_PATH . 'admin/customizer.php';
		require_once BUZZ_ADDON_DATES_PATH . 'admin/ui.php';

		// enqueue scripts
		add_action('admin_enqueue_scripts',			array($this, 'enqueue'));

		// Add widgets
		add_action('widgets_init', function () {
			register_widget('Buzz_Addon_Dates_Widget');
		});

		// Customizer setup
		if (class_exists('Kirki')) {
			$customizer = new Buzz_Addon_Dates_Customizer();
			add_action('buzz_customizer_addons_options', 	array($customizer, 'set_options'));
			add_action('buzz_customizer_addons_colors', 	array($customizer, 'set_colors'));
		}

		// Metaboxes setup
		$ui = new Buzz_Addon_Dates_UI();
		add_action('save_post_newsletter', 			array($ui, 'save_dates_meta'), 10, 2);
		add_action('buzz_after_custom_meta_box', 		array($ui, 'manage_custom_metaboxes'), 10, 2);
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue()
	{
		$screen = get_current_screen();

		// scripts
		if ($screen->post_type == 'newsletter') {
			wp_enqueue_script('jquery-ui-sortable', array('jquery'));
			wp_enqueue_script('buzz-dates-js', 	plugin_dir_url(__FILE__) . 'public/js/admin.js', 	array('jquery', 'jquery-ui-sortable'));

			// styles
			wp_enqueue_style('buzz-dates-css', plugin_dir_url(__FILE__) . 'public/css/admin.css');
		}
	}

	/**
	 * The code that runs during plugin activation.
	 */
	public function activate()
	{
	}

	/**
	 * The code that runs during plugin deactivation.
	 */
	public function deactivate()
	{
	}
}



/**
 * Init Buzz_Addon_Dates Class
 */
$buzz_addon = new Buzz_Addon_Dates();

/**
 * Register Activation and Deactivation functions
 */
register_activation_hook(__FILE__, array($buzz_addon, 'activate'));
register_deactivation_hook(__FILE__, array($buzz_addon, 'deactivate'));

// stub utility function for translation
if( ! function_exists('ff__')) {
	function ff__($text) {
		return $text;
	}
}

/**
 * Add plugin actions if Buzz Newsletter plugin is active
 */
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (is_plugin_active('buzz-newsletter/buzz-newsletter.php')) {

	// Initialise the plugin
	$buzz_addon->plugin_init();
}
