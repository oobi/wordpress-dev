<?php

namespace FF\Midgard;

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
class Midgard_Admin {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Midgard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Midgard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$screen = function_exists('get_current_screen') ? get_current_screen() : null;

		// only show in edit screen for data feed post type
		if($screen && $screen->base == 'post' && $screen->post_type == 'data_feed') {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/midgard-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Midgard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Midgard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$screen = function_exists('get_current_screen') ? get_current_screen() : null;

		// only show in edit screen for data feed post type
		if($screen && $screen->base == 'post' && $screen->post_type == 'data_feed') {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/midgard-admin.js', array( 'jquery' ), $this->version, false );
		}

	}

}
