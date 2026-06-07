<?php

namespace FF\Twitter;

/**
 * The core plugin class.
 */
class Twitter {

	/**
	 * The path to the plugins folder
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $plugin_base    The string holding the directory path to the plugins folder
	 */
	protected $plugin_base;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// file paths
		$this->plugin_base = plugin_dir_path( __FILE__ );

		// setup
		$this->load_dependencies();
		$this->define_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		// options page
		require_once $this->plugin_base . 'class-options.php';
		// rest class
		require_once $this->plugin_base . 'class-rest.php';
		// twitter oauth
		require_once $this->plugin_base . 'vendor/autoload.php';
	}

	/**
	 * Register all of the hooks of the plugin.
	 */
	private function define_hooks() {

		// Admin hooks
		if( is_admin() ) {

			// options page
			$options = new TwitterOptions();
			add_action( 'admin_init', array( $options, 'page_init' ) );
			add_action( 'admin_menu', array( $options, 'add_plugin_page' ) );
		}

		// REST
		$rest = new TwitterRest();
		add_action( 'rest_api_init', array($rest, 'register_routes') );
	}


	public function get_tweets($numTweets) {
		$rest = new TwitterRest();
		return $rest->list_tweets( ['num' => $numTweets] );
	}

}
