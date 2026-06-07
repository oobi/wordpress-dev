<?php

namespace FF\DocRaptor;

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
 * @package    DocRaptor
 * @subpackage DocRaptor/includes
 * @author     Firefly Interactive <info@fi.net.au>
 */
class DocRaptorPlugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
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
	protected $plugin_base;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Flag for disabled - true if config is incomplete
	 */
	protected $disabled;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version) {

		// this is used for CSS / JS identification
		$this->plugin_name 		= $plugin_name;
		$this->version 			= $version;

		// file paths
		$this->plugin_base = plugin_dir_path( dirname( __FILE__ ) );

		// check for complete config
		$this->disabled = ( empty(DOCRAPTOR_API_KEY) );

		// setup
		$this->load_dependencies();

		$this->define_public_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Loader. Orchestrates the hooks of the plugin.
	 * - i18n. Defines internationalization functionality.
	 * - Admin. Defines all hooks for the admin area.
	 * - Public. Defines all hooks for the public side of the site.
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
		 * The class responsible for defining all actions that occur in the public area.
		 */
		require_once $this->plugin_base . 'public/public.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $this->plugin_base . 'admin/admin.php';

		// setup loader
		$this->loader = new Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		if( ! is_admin() ) return;

		$admin = new DocRaptorAdmin( $this->plugin_name, $this->version );

		// admin reminder that config is incomplete
		if( $this->disabled ) {
			$this->loader->add_action( 'admin_notices', $admin, 'setup_incomplete_admin_notice', 10 );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		if( is_admin() ) return;
		$public = new DocRaptorPublic( $this->plugin_name, $this->version );

		// add rewrites if not disabled
		if( ! $this->disabled ) {
			// query vars
			$this->loader->add_filter('query_vars', $public, 'add_query_vars', 10);
			$this->loader->add_filter('init', $public, 'add_rewrite_rules', 10);
		}
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
	 * @return    Loader    Orchestrates the hooks of the plugin.
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
