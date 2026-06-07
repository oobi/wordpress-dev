<?php

namespace FF\Calendar;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;


/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.fi.net.au
 * @since      1.0.0
 *
 * @package    FF_Calendar
 * @subpackage FF_Calendar/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    FF_Calendar
 * @subpackage FF_Calendar/includes
 * @author     Firefly Interactive <info@fi.net.au>
 */
class FF_Calendar {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      FF_Calendar_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - FF_Calendar_Loader. Orchestrates the hooks of the plugin.
	 * - FF_Calendar_i18n. Defines internationalization functionality.
	 * - FF_Calendar_Admin. Defines all hooks for the admin area.
	 * - FF_Calendar_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * Vendor Libs
		 * https://github.com/PHPSocialNetwork/phpfastcache
		 * https://github.com/iCalcreator/iCalcreator
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'vendor/autoload.php';

		/**
		 * A utility classto throw exceptions
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'includes/exception-thrower.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'includes/loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'includes/i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'admin/admin_init.php';

		/**
		 * The class responsible for creating REST endpoints
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'admin/rest_routes.php';

		/**
		 * The class responsible for defining the options page.
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'admin/options-page.php';

		/**
		 * The class responsible for retrieving and handling the remote data
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'admin/data.php';

		/**
		 * The class responsible for creating the widgets.
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'admin/widget.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'public/public_init.php';

		/**
		 * The class responsible for creating the shortcode interface
		 */
		require_once FF_CALENDAR_PLUGIN_PATH . 'public/shortcodes.php';



		// setup cache object
		CacheManager::setDefaultConfig(new ConfigurationOption([
			"path" => FF_CALENDAR_CACHE_DIR
		]));

		$this->loader = new Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the FF_Calendar_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Admin_Init( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// add rest routes
		$plugin_rest = new REST_Controller( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes' );

		// add widget
		add_action( 'widgets_init', function(){
			register_widget( new FF_Calendar_Widget() );
		});

		// plugin options
		$plugin_options = new Options_Page( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $plugin_options, 'add_plugin_page' );
		$this->loader->add_action( 'admin_init', $plugin_options, 'page_init' );
		$this->loader->add_action( 'admin_head', $plugin_options, 'help_menu' );
		$this->loader->add_action( 'update_option_'. FF_CALENDAR_SETTINGS_KEY, $plugin_options, 'on_options_save', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Public_Init( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// set up calendar output shortcodes and widgets
		$shortcodes = new Shortcodes();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    FF_Calendar_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**********************************************************************************
	 * CACHE
	 **********************************************************************************/

	/**
	 * retrieve and instance to the cache object
	 */
	public static function get_cache_instance() {
		return CacheManager::getInstance('files');
	}

	/**
	 * is caching turned on? Return true if so.
	 * @return boolean
	 */
	public static function is_cache_active() {
		$opt = get_option('ff_calendar_settings', array());
		return array_key_exists('cache_active', $opt) && $opt['cache_active'];
	}

	/**
	 * get the time to hold cache
	 * @return int
	 */
	public static function get_cache_time() {
		$opt = get_option('ff_calendar_settings', array());
		if( array_key_exists('cache_time', $opt) ){
			return $opt['cache_time'];
		} else {
			return 0;
		}
	}

}
