<?php

namespace FF\Calendar;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.fi.net.au
 * @since      1.0.0
 *
 * @package    FF_Calendar
 * @subpackage FF_Calendar/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    FF_Calendar
 * @subpackage FF_Calendar/public
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Public_Init {

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
	 * The version of this API.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_version    The current version of this API.
	 */
	 private $api_version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		$this->api_version 	= FF_CALENDAR_API_VERSION;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// Full Calendar CSS library
		wp_enqueue_style( 'fc_css', FF_CALENDAR_PLUGIN_URL . 'lib/fullcalendar/fullcalendar.css', array(), $this->version, false );

		// Font Awesome
		// wp_enqueue_style( 'fa_css', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css', array(), $this->version, false );

		// Plugin
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Full Calendar JS library
		wp_enqueue_script( 'fc_moment', FF_CALENDAR_PLUGIN_URL . 'lib/fullcalendar/lib/moment.min.js', array(), $this->version, false );
		wp_enqueue_script( 'fc_js', FF_CALENDAR_PLUGIN_URL . 'lib/fullcalendar/fullcalendar.js', array( 'fc_moment', 'jquery' ), $this->version, false );

		// custom FullCalendar extensions
		wp_enqueue_script( 'fc_upcoming_view_js', FF_CALENDAR_PLUGIN_URL . 'public/js/view-upcoming.js', array( 'fc_js', 'jquery' ), $this->version, false );

		// cookies
		wp_enqueue_script( 'js_cookie', FF_CALENDAR_PLUGIN_URL . 'lib/js.cookie.js', array(), $this->version, false );

		// plugin
		wp_register_script( 'fc_calendar_render', FF_CALENDAR_PLUGIN_URL . 'public/js/calendar-render.js', array('jquery'), $this->version, false );
		$translation = array(
			// all REST routes need a trailing slash
			'all_data_route' 	=> get_rest_url( null, '/' . $this->plugin_name . '/' . $this->api_version . '/all/' ),
			'events_route' 		=> get_rest_url( null, '/' . $this->plugin_name . '/' . $this->api_version . '/events/' ),
			'categories_route' 	=> get_rest_url( null, '/' . $this->plugin_name . '/' . $this->api_version . '/categories/' ),
			'config_route' 		=> get_rest_url( null, '/' . $this->plugin_name . '/' . $this->api_version . '/feeds/' ),
			// following appended to the above routes if limiting data
			'limit_days' 		=> 'days/',
			'limit_events' 		=> 'limit/'
		);
		wp_localize_script( 'fc_calendar_render', 'FF_CALENDAR', $translation );
		wp_enqueue_script( 'fc_calendar_render' );
	}

}
