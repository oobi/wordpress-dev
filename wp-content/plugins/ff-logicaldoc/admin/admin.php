<?php

namespace FF\LogicalDoc;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.fi.net.au
 * @since      1.0.0
 *
 * @package    Midgard
 * @subpackage Midgard/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Midgard
 * @subpackage Midgard/admin
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Show an admin message that config is incomplete
	 */
	public function setup_incomplete_admin_notice() {
		$class 		= "update-nag";
		$message 	= sprintf( 'LogicalDOC sync is disabled. You must complete the setup and add host, username, password details to wp-config.php.');
		echo 		"<div class=\"$class\"> <p>$message</p></div>";
	}

}
