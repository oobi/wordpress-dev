<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://www.fireflyinteractive.net
 * @since      3.0.0
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/admin
 * @author     Firefly Interactive
 */
class FF_Newsletter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $ff_newsletter    The ID of this plugin.
	 */
	private $ff_newsletter;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 * @param    string    $ff_newsletter		The name of this plugin.
	 * @param    string    $version    			The version of this plugin.
	 */
	public function __construct( $ff_newsletter, $version ) {

		$this->ff_newsletter = $ff_newsletter;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    3.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in FF_Newsletter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The FF_Newsletter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->ff_newsletter, plugin_dir_url( __FILE__ ) . 'css/ff-newsletter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    3.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in FF_Newsletter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The FF_Newsletter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Only use this JS on Edit Article pages
		$screen = get_current_screen();

		// newsletter screen needs jquery-ui for drag/drop
		if($screen && preg_match('/newsletter|article/', $screen->post_type)) {
			$prerequisites = array('jquery', 'jquery-ui-sortable');
			wp_enqueue_script( $this->ff_newsletter, plugin_dir_url( __FILE__ ) . 'js/ff-newsletter-admin.js', $prerequisites, $this->version, true );
		}


	}

}
