<?php
/**
 * Plugin Name:   Firefly Group Updater
 * Plugin URI:    https://www.fi.net.au
 * Text Domain:   ff-group-updater
 * Domain Path:   /languages
 * Description:   Allows auto update for Firefly plugins
 * Author:        Firefly Interactive
 * Version:       1.0.0
 * Licence:       GPLv3+
 * Author URI:    http://www.fi.net.au
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// init auto-updater
require_once plugin_dir_path( __FILE__ ) . 'updater.php';
