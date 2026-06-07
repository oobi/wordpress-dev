<?php

namespace FF\Midgard;

/**
 * The core plugin class.
 *
 * This is the basis of a child plugin class - it is designed to be extended by child plugins
 * It contains things common to all child plugins
 *
 * @since      1.0.0
 * @package    Midgard
 * @subpackage Midgard/base
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Midgard_Plugin_Base {

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
	 * String given to the feed type described by this plugin
	 */
	protected $feed_type;
	protected $feed_type_label;

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

		// loader object
		$this->loader = new Midgard_Loader();

		// file paths
		$this->set_plugin_base();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Midgard_WP_Rest_V2_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function set_locale() {

		$plugin_i18n = new Midgard_i18n( $this->plugin_name );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Set the plugin base path.
	 * This works with extended (child) subclasses where __FILE__ does not
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function set_plugin_base() {
		$c = new \ReflectionClass($this);
		$this->plugin_base = plugin_dir_path( dirname( $c->getFileName() ) );
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
	 * @return    Midgard_WP_Rest_V2_Loader    Orchestrates the hooks of the plugin.
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
