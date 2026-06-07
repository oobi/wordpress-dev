<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    Buzz_Addon_Campaign_Monitor
 * @subpackage Buzz_Addon_Campaign_Monitor/includes
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
 * @package    Buzz_Addon_Campaign_Monitor
 * @subpackage Buzz_Addon_Campaign_Monitor/includes
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Buzz_Addon_Campaign_Monitor {

	/**
	 * The path to the plugins folder
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $plugin_base    The string holding the directory path to the plugins folder
	 */
	protected $plugin_base;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buzz_Addon_Campaign_Monitor_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct() {

		$this->plugin_name = 'buzz-addon-campaign-monitor';
		$this->version = '1.0.0';

		// file paths
		$this->plugin_base = plugin_dir_path( dirname( __FILE__ ) );
		// setup
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
	 * - Buzz_Addon_Campaign_Monitor_Loader. Orchestrates the hooks of the plugin.
	 * - Buzz_Addon_Campaign_Monitor_i18n. Defines internationalization functionality.
	 * - Buzz_Addon_Campaign_Monitor_Admin. Defines all hooks for the admin area.
	 * - Buzz_Addon_Campaign_Monitor_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $this->plugin_base . 'includes/loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $this->plugin_base . 'includes/i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $this->plugin_base . 'admin/admin.php';

		/**
		 * Campaign monitor classes
		 */
		require_once $this->plugin_base . 'vendor/autoload.php';

		/**
		 * The class responsible for abstracting Campaign Monitor calls
		 */
		require_once $this->plugin_base . 'admin/mailer-api.php';

		/**
		 * The class responsible for allowing the use of custom WP List Tables
		 */
		if( ! class_exists( 'WP_List_Table' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		require_once $this->plugin_base . 'admin/list-table.php';

		/**
		 * The file responsible for creating the Send Newsletters page and functionality
		 */
		require_once $this->plugin_base . 'admin/campaign-page.php';

		/**
		 * The file responsible for creating the Newsletter Options page and functionality
		 */
		require_once $this->plugin_base . 'admin/options-page.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/public.php';

		$this->loader = new Buzz_Addon_Campaign_Monitor_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buzz_Addon_Campaign_Monitor_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buzz_Addon_Campaign_Monitor_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

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
		// admin styles and scripts
		$plugin_admin = new Buzz_Addon_Campaign_Monitor_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// campaign management page
		$plugin_campaign_page = new Buzz_Newsletter_Campaign_Monitor_Campaign_Page();
		if(isset( $_GET['page']) && $_GET['page'] == 'buzz-campaign-monitor' ) {
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_campaign_page, 'enqueue_custom_scripts' );
		}

		$this->loader->add_action( 'admin_menu', $plugin_campaign_page, 'add_admin_pages' );
		// $this->loader->add_action( 'wp_ajax_get_campaign_monitor_segments', $plugin_campaign_page, 'create_get_campaign_monitor_segments_callback' );

		// plugin options
		$plugin_options = new Buzz_Addon_Campaign_Monitor_Options_Page();
		$this->loader->add_action( 'admin_menu', $plugin_options, 'add_plugin_page' );
		$this->loader->add_action( 'admin_init', $plugin_options, 'page_init' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Buzz_Addon_Campaign_Monitor_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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
	 * @return    Buzz_Addon_Campaign_Monitor_Loader    Orchestrates the hooks of the plugin.
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

}
