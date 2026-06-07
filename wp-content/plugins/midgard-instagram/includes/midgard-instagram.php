<?php

namespace FF\Midgard\Instagram;

use FF\Midgard\Midgard_Plugin_Base;
use FF\Midgard\Midgard_Common;

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
 * @package    Midgard_Instagram
 * @subpackage Midgard_Instagram/includes
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Midgard_Instagram extends Midgard_Plugin_Base {

	/**
	 * The API endpoints
	 */
	private $api_endpoint;
	private $api_endpoint_version;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Midgard_Instagram_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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

		// call parent constructor
		parent::__construct($plugin_name, $version);

		// plugin type strings
		$this->feed_type_label = __('Instagram', 'midgard-instagram');
		$this->feed_type = 'instagram';

		// init
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->set_locale();

		// register feed type
		Midgard_Common::register_feed_type($this->feed_type);
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Midgard_Instagram_Loader. Orchestrates the hooks of the plugin.
	 * - Midgard_Instagram_i18n. Defines internationalization functionality.
	 * - Midgard_Instagram_Admin. Defines all hooks for the admin area.
	 * - Midgard_Instagram_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for interfacing with remote API
		 */
		require_once $this->plugin_base . 'includes/instagram-api.php';

		/**
		 * The class responsible for creating an options page for the plugin
		 */
		require_once $this->plugin_base . 'admin/options-page.php';

		/**
		 * The class responsible for customising the admin area UI
		 */
		require_once $this->plugin_base . 'plugins/plugin-instagram-ui.php';

		/**
		 * The class responsible for retrieving and handling the remote data
		 */
		require_once $this->plugin_base . 'plugins/plugin-instagram-data.php';

		/**
		 * The class responsible for customising the admin area UI
		 */
		require_once $this->plugin_base . 'plugins/plugin-instagram-ui.php';
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		//////////////////////////////////////////////////////////////
		// DATA HOOKS
		// NB - all these hooks need to be registered
		//////////////////////////////////////////////////////////////

		// data handler
		$data = new Midgard_Instagram_Data();

		// data hooks
		$this->loader->add_filter('midgard_data_get_' . $this->feed_type, $data, 'get_data', 10, 2);

		//////////////////////////////////////////////////////////////
		// customise UI
		//////////////////////////////////////////////////////////////

		// UI handler
		$customise_ui = new Midgard_Instagram_UI($this->feed_type, $this->feed_type_label);

		// add value to the feed type selector
		$this->loader->add_action( 'midgard_feed_type_option', $customise_ui, 'add_feed_type_options', 10, 2 );

		// add fields to feed meta box
		$this->loader->add_action( 'midgard_after_feed_meta_box', $customise_ui, 'add_feed_options', 10, 2 );

		// save custom meta fields
		$this->loader->add_action( 'midgard_after_save_feed_meta', $customise_ui, 'save_feed_options', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

	}

}
