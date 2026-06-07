<?php

namespace FF\LogicalDoc;

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
 * @package    Midgard
 * @subpackage Midgard/includes
 * @author     Firefly Interactive <info@fi.net.au>
 */
class LogicalDoc {

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
		$this->disabled = ( empty(LOGICAL_HOST) || empty(LOGICAL_USER) || empty(LOGICAL_PASS) );

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
		 * A utility classto throw exceptions
		 */
		require_once $this->plugin_base . 'includes/exception-thrower.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $this->plugin_base . 'includes/loader.php';

		/**
		 * LogicalDOC API
		 */
		require_once $this->plugin_base . 'includes/logical-client.php';

		/**
		 * Custom Taxonomy
		 */
		require_once $this->plugin_base . 'includes/custom-types.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $this->plugin_base . 'admin/admin.php';

		/**
		 * The class responsible for adding functionality to the edit screen
		 */
		require_once $this->plugin_base . 'admin/metabox-ui.php';

		/**
		 * The class responsible for syncing tags with Logical
		 */
		require_once $this->plugin_base . 'admin/tag_sync.php';

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

		// Metabox UI
		if( ! $this->disabled ) {
			$metabox_ui = new Metabox_UI();
			$tag_sync = new Tag_Sync();
			$this->loader->add_action( 'save_post',								$metabox_ui, 'save_post_meta', 10, 2 );
			$this->loader->add_action( 'post_submitbox_misc_actions',			$metabox_ui, 'add_to_publish_meta_box' );
			$this->loader->add_action( 'admin_enqueue_scripts',					$metabox_ui, 'enqueue_admin_scripts' );
			$this->loader->add_action( 'admin_init',							$metabox_ui, 'manage_custom_metaboxes' );
			$this->loader->add_action( 'admin_menu',							$tag_sync, 'add_management_page', 10);
		}

		// admin reminder that config is incomplete
		if( $this->disabled ) {
			$admin = new Admin( $this->plugin_name, $this->version );
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
		$types = new Custom_Types();
		$this->loader->add_action( 'init', $types, 'add_custom_types', 10 );

		// prevent new term sin custom type
		add_filter('pre_insert_term', function ( $term, $taxonomy ) {
			return ( 'logical-tag' === $taxonomy )
				? new \WP_Error( 'term_addition_blocked', __( 'You cannot add terms to this taxonomy' ) )
				: $term;
		}, 0, 2 );
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
