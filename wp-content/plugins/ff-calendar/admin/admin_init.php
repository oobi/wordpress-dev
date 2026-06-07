<?php

namespace FF\Calendar;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.fi.net.au
 * @since      1.0.0
 *
 * @package    FF_Calendar
 * @subpackage FF_Calendar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    FF_Calendar
 * @subpackage FF_Calendar/admin
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Admin_Init {

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

		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// General admin css
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$current_screen = get_current_screen();

		// shortcode generator JS
		if( $current_screen->id == 'settings_page_ff_calendar_settings' ) {
			wp_enqueue_script( $this->plugin_name . '-shortcode', plugin_dir_url( __FILE__ ) . 'js/shortcode-generator.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-options', plugin_dir_url( __FILE__ ) . 'js/options-page.js', array( 'jquery' ), $this->version, false );
		}

	}

	/**
	 * Grab all of the saved calendar feeds
	 * or a specific feed if ID is passed
	 *
	 * @param 		Array 			$request 		Options for the function.
	 * @return 		String|null 	 				The calendar feeds, or null if none.
	 */
	 public function feeds_rest_endpoint( $request ) {
		$feed_id = $request['id'];

		// get calendar settings
		$options 	= get_option( 'ff_calendar_settings' );

		// do the feeds exist?
		if( array_key_exists( 'calendar_feeds', $options ) && isset( $options['calendar_feeds'] ) ) {

			// if an ID was passed, return the feed with that ID (if it exists)
			if( !empty( $feed_id ) ) {
				$index = array_search( $feed_id, array_column( $options['calendar_feeds'], 'id' ) );
				if( $index ) {
					return $options['calendar_feeds'][$index];
				}
			}
			// else return all feeds
			else {
				return $options['calendar_feeds'];
			}

		}

		// otherwise return nothing
		return null;
	}

}
