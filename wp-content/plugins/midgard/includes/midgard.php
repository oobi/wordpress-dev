<?php

namespace FF\Midgard;

use phpFastCache\CacheManager;

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
class Midgard {

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
	 * @var      Midgard_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct($plugin_name, $version) {

		// this is used for CSS / JS identification
		$this->plugin_name 		= $plugin_name;
		$this->version 			= $version;

		// file paths
		$this->plugin_base = plugin_dir_path( dirname( __FILE__ ) );

		// setup
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// register feed type (bundled plugin)
		Midgard_Common::register_feed_type('json');
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Midgard_Loader. Orchestrates the hooks of the plugin.
	 * - Midgard_i18n. Defines internationalization functionality.
	 * - Midgard_Admin. Defines all hooks for the admin area.
	 * - Midgard_Public. Defines all hooks for the public side of the site.
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
		 * A utility class of static methods common to other classes
		 */
		require_once $this->plugin_base . 'includes/common.php';

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
		 * The class responsible for defining custom post types
		 */
		require_once $this->plugin_base . 'includes/custom-types.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $this->plugin_base . 'admin/admin.php';

		/**
		 * The class responsible for creating an options page for the plugin
		 */
		require_once $this->plugin_base . 'admin/options-page.php';

		/**
		 * The class responsible for creating an options page for the plugin
		 */
		require_once $this->plugin_base . 'admin/security-page.php';

		/**
		 * The class responsible for creating a menu page for the plugin
		 */
		require_once $this->plugin_base . 'admin/menu-page.php';

		/**
		 * The class responsible for creating a cache page for the plugin
		 */
		require_once $this->plugin_base . 'admin/cache-page.php';

		/**
		 * The class responsible for customising the admin area UI
		 */
		require_once $this->plugin_base . 'admin/feed-edit-ui.php';

		/**
		 * Base classes for extension plugins
		 */
		require_once $this->plugin_base . 'base/plugin-ui-base.php';
		require_once $this->plugin_base . 'base/plugin-data-base.php';

		/**
		 * bundled JSON plugin
		 */
		require_once $this->plugin_base . 'plugins/plugin-json-ui.php';
		require_once $this->plugin_base . 'plugins/plugin-json-data.php';

		/**
		 * REST controller
		 */
		require_once $this->plugin_base . 'public/rest-controller.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $this->plugin_base . 'public/public.php';


		/**
		 * Check existence of cache folder and recreate if not present
		 */
		$this->check_cache_folder();

		/**
		 * composer vendor libraries
		 * https://github.com/PHPSocialNetwork/phpfastcache
		 * https://github.com/Skyscanner/JsonPath-PHP
		 * https://twig.sensiolabs.org
		 */
		require_once( $this->plugin_base . '/vendor/autoload.php');

		// setup cache object
		CacheManager::setDefaultConfig(array(
			"path" => MIDGARD_FEED_CACHE_DIR
		));

		// setup loader
		$this->loader = new Midgard_Loader();


		// trigger action for 'all classes ready'
		do_action('midgard_ready');
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Midgard_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Midgard_i18n( $this->plugin_name );

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

		$plugin_admin = new Midgard_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// data handler
		$jsonData = new Midgard_JSON_Data();

		// data hooks
		$this->loader->add_filter('midgard_data_get_json', $jsonData, 'get_data', 10, 2);

		// Feed UI
		$feed_ui = new Midgard_Feed_Edit_UI();
		$this->loader->add_action( 'load-post.php',							$feed_ui, 'manage_custom_metaboxes' );
		$this->loader->add_action( 'load-post-new.php',						$feed_ui, 'manage_custom_metaboxes' );
		$this->loader->add_action( 'save_post',								$feed_ui, 'save_feed_meta', 10, 2 );

		// activate WordPress Link Manager
		// add_filter( 'pre_option_link_manager_enabled', '__return_true' );

		////////////////////////////////////////////////////////////
		// JSON plugin (bundled)
		$feed_type = 'json';
		$feed_type_label = _x('JSON', 'midgard');
		$json_ui = new Midgard_JSON_UI($feed_type, $feed_type_label);

		// add value to the feed type selector
		$this->loader->add_action( 'midgard_feed_type_option', 				$json_ui, 'add_feed_type_options', 10, 2 );

		// add fields to feed meta box
		$this->loader->add_action( 'midgard_after_feed_meta_box', 			$json_ui, 'add_feed_options', 10, 2 );

		// save custom meta fields
		$this->loader->add_action( 'midgard_after_save_feed_meta', 			$json_ui, 'save_feed_options', 10, 2 );

		////////////////////////////////////////////////////////////
		// ACTIVATION ERROR - CACHE FOLDER NOT CREATED
		// hook for showing warning if the activation hook wasn't able to create the cache folder
		$this->loader->add_action( 'admin_notices', $this, 'admin_notice_failure' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Midgard_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// set up custom post types
		$types = new Midgard_Custom_Types();
		$this->loader->add_action( 'init', 				$types, 'add_custom_types' );

		// data output (render) template
		$this->loader->add_filter( 'template_include',  $plugin_public, 'set_data_output_template', 10 );
		$this->loader->add_filter( 'the_content', 		$plugin_public, 'set_data_output_content', 10 );
		$this->loader->add_filter( 'pre_get_posts', 	$plugin_public, 'limit_preview_access', 10 );

		// add our own CORS implementation
		$this->loader->add_action( 'rest_pre_serve_request',  $plugin_public, 'set_cors_headers', 10 );

		// REST controller
		$rest_controller = new Midgard_REST_Controller();
		$this->loader->add_action('rest_api_init', $rest_controller, 'register_routes');

		// REST require auth
		$this->loader->add_filter( 'rest_authentication_errors', $plugin_public, 'limit_logged_in_rest_access', 10 );
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
	 * @return    Midgard_Loader    Orchestrates the hooks of the plugin.
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
		$opt = get_option('midgard_settings', array());
		return array_key_exists('cache_active', $opt) && $opt['cache_active'];
	}

	/**
	 * is caching turned on? Return true if so.
	 * @return boolean
	 */
	public static function get_default_cache_time() {
		$opt = get_option('midgard_settings', array());
		if( array_key_exists('default_cache_time', $opt) ){
			return $opt['default_cache_time'];
		} else {
			return 0;
		}
	}

	/**
	 * Check presence of cache folder and create if not present
	 */
	public static function check_cache_folder() {
		// create and secure cache dir if it doesn't already exist
		if(!file_exists(MIDGARD_FEED_CACHE_DIR)) {

			if(!mkdir(MIDGARD_FEED_CACHE_DIR, 0755, true)) {
				// set a transient to warn if feed cache folder couldn't be created
				set_transient( 'midgard-warn-cache-folder-create', true, 5 );
			}

		}

		// add a htaccess to cache folder to prevent any direct access
		$htaccess_path = MIDGARD_CACHE_ROOT_DIR . '/.htaccess';
		if( !file_exists( $htaccess_path) ) {
			$bytes = file_put_contents( $htaccess_path, 'Deny from all');
		}

		// add index.php to prevent directory browsing
		$index_path = MIDGARD_CACHE_ROOT_DIR . '/index.php';
		if( !file_exists( $index_path ) ) {
			$bytes = file_put_contents( $index_path, '<?php // silence is golden');
		}
	}

	/**
	 * Admin notice if cache folder wasn't created at startup'
	 */
	public static function admin_notice_failure() {
		//  Check transient, if available display notice
		if( get_transient( 'midgard-warn-cache-folder-create' ) ){	?>

			<div class="updated notice is-dismissible">
				<p><?php _e( 'Unable to create Midgard cache folder at <code>' . MIDGARD_CACHE_DIR . '</code>. Check folder permissions and re-activate the plugin.' , 'midgard' ); ?></p>
				<p><?php _e('Some features may not function correctly until this error is corrected.', 'midgard'); ?>
			</div>

			<?php // Delete transient, only display this notice once.
			delete_transient( 'midgard-warn-cache-folder-create' );
		}
	}

}
