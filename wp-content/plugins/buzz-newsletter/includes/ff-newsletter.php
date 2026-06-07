<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://www.fireflyinteractive.net
 * @since      3.0.0
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      3.0.0
 * @package    ff_newsletter
 * @subpackage ff_newsletter/includes
 * @author     Firefly Interactive
 */
class FF_Newsletter
{

	/**
	 * The path to the plugins folder
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $plugin_base    The string holding the directory path to the plugins folder
	 */
	protected $plugin_base;

	/**
	 * The path to the currently activated theme folder
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $theme_base    The string holding the directory path to the currently activated theme folder
	 */
	protected $theme_base;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      FF_Newsletter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/** TODO: tag
	 */
	protected $plugin_options = FALSE;

	/** TODO: tag
	 */
	protected $options_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/** Refers to a single instance of this class. */
	private static $instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return  FF_Newsletter : A single instance of this class.
	 */
	public static function get_instance()
	{
		if (null == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    3.0.0
	 */
	public function __construct()
	{

		$this->plugin_name 		= 'ff-newsletter';
		$this->version 			= '3.0.0';
		$this->options_name 	= 'newsletter_settings';
		$this->plugin_options 	= FALSE;

		// file paths
		$this->plugin_base = plugin_dir_path(dirname(__FILE__));
		$this->theme_base  = get_stylesheet_directory() . '/ff-newsletter/';

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
	 * - FF_Newsletter_Loader. Orchestrates the hooks of the plugin.
	 * - FF_Newsletter_i18n. Defines internationalization functionality.
	 * - FF_Newsletter_Admin. Defines all hooks for the dashboard.
	 * - FF_Newsletter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * A utility class of static methods common to other classes
		 */
		require_once $this->plugin_base . 'includes/ff-newsletter-common.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $this->plugin_base . 'includes/ff-newsletter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $this->plugin_base . 'includes/ff-newsletter-i18n.php';

		/**
		 * The class responsible for setting up the custom taxonomies and post types
		 */
		require_once $this->plugin_base . 'includes/ff-newsletter-custom-types.php';

		/**
		 * The class responsible for inlining css styles for email templates
		 */
		require_once $this->plugin_base . 'includes/ff-newsletter-inline-css.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once $this->plugin_base . 'admin/ff-newsletter-admin.php';

		/**
		 * The class responsible for creating an options page for the plugin
		 */
		//require_once $this->plugin_base . 'admin/ff-newsletter-options-page.php';

		/**
		 * The class responsible for customising the admin area UI
		 */
		$this->custom_require('admin/ff-newsletter-ui.php');

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $this->plugin_base . 'public/ff-newsletter-public.php';

		/**
		 * The public-facing template tags
		 */
		require_once $this->plugin_base . 'public/ff-newsletter-api.php';

		$this->loader = new FF_Newsletter_Loader();
	}

	/**
	 * Custom require function.
	 *
	 * Allows the required file to be overidden by a theme.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function custom_require($path, $once = TRUE)
	{
		// this is the default path to the file we want to include
		$include_path = $this->plugin_base . $path;

		// if the same file exists in the active theme then this can override the default
		if (file_exists($this->theme_base . $path)) {
			$include_path = $this->theme_base . $path;
		}

		// use require or require_once
		if ($once) {
			require_once $include_path;
		} else {
			require $include_path;
		}
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the FF_Newsletter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new FF_Newsletter_i18n();
		$plugin_i18n->set_domain($this->get_plugin_name());

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		// set up plugin
		$plugin_admin = new FF_Newsletter_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		// customise UI
		$customise_ui = new FF_Newsletter_UI();
		$this->loader->add_action('manage_edit-newsletter_columns',			$customise_ui, 'register_custom_columns_newsletter');
		$this->loader->add_action('manage_edit-article_columns',			$customise_ui, 'register_custom_columns_article');
		$this->loader->add_action('restrict_manage_posts',					$customise_ui, 'custom_article_filter_ui');
		$this->loader->add_action('parse_query',							$customise_ui, 'custom_article_filter_query');
		$this->loader->add_action('manage_newsletter_posts_custom_column',	$customise_ui, 'fill_custom_columns_newsletter', 10, 2);
		$this->loader->add_action('manage_article_posts_custom_column',		$customise_ui, 'fill_custom_columns_article', 10, 2);
		$this->loader->add_action('load-post.php',							$customise_ui, 'manage_custom_metaboxes');
		$this->loader->add_action('load-post-new.php',						$customise_ui, 'manage_custom_metaboxes');
		$this->loader->add_action('save_post',								$customise_ui, 'save_article_meta', 10, 2);

		// ajax actions
		$this->loader->add_action('wp_ajax_update-article-order', 			$customise_ui, 'update_article_order');
		$this->loader->add_action('wp_ajax_update-article-attribute', 		$customise_ui, 'update_article_attribute');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		// set up plugin
		$plugin_public = new FF_Newsletter_Public($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', 	$plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', 	$plugin_public, 'enqueue_scripts');
		$this->loader->add_action('init', 					$plugin_public, 'register_menus');

		// set up rewrites
		$this->loader->add_action('query_vars', 			$plugin_public, 'add_url_vars');
		$this->loader->add_action('init', 					$plugin_public, 'add_rewrite_rules');
		$this->loader->add_action('pre_get_posts', 		$plugin_public, 'redirect_to_latest');

		// flag unpublished articles
		$this->loader->add_action('init', 					$plugin_public, 'flag_unpublished_articles');

		// set up newsletter
		$types = new FF_Newsletter_Custom_Types();
		$this->loader->add_action('init', 					$types, 'add_custom_types');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    3.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     3.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The name of the plugin options variable in the WordPress database
	 *
	 * @since     3.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_options_name()
	{
		return $this->options_name;
	}

	/** TODO: tag
	 */
	public function get_option($option, $default = NULL)
	{

		// get options
		if ($this->plugin_options === FALSE) {
			$this->plugin_options = get_option($this->options_name, array());
		}

		// return the option, else the default
		return array_key_exists($option, $this->plugin_options) ? $this->plugin_options[$option] : $default;
	}

	/** TODO: tag
	 */
	public function set_option($option, $value)
	{

		// get options
		if ($this->plugin_options === FALSE) {
			$this->plugin_options = array();
			//$this->plugin_options = update_option( $this->options_name , $value );
		}

		// set the new option value
		$this->plugin_options[$option] = $value;

		// write back to WordPress database
		update_option($this->options_name, $this->plugin_options);
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     3.0.0
	 * @return    FF_Newsletter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     3.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
