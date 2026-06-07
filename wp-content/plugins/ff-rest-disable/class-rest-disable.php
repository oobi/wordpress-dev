<?php

namespace FF\REST_Disable;

/**
 * The core plugin class.
 */
class REST_Disable {

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
	}

	/**
	 * Register all of the hooks of the plugin.
	 */
	private function define_hooks() {
		// REST require auth
		add_filter( 'rest_authentication_errors', array($this, 'limit_logged_in_rest_access'), 10 );
	}


	/**
	 * Returning an authentication error if a user who is not logged in tries to query the REST API
	 * This has two modes for letting things through
	 * 		DENY ALL (except defined URLs)
	 * 		ALLOW ALL (except defined URLs)
	 * @param $access
	 * @return WP_Error
	 */
	public function limit_logged_in_rest_access( $access ) {

		// if the user is logged in just go through with the usual auth - no further checks required
		if( is_user_logged_in() ) {
			return $access;
		}

		// current REST request (strip GET parameters)
		$current_request = strtolower( preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']) );

		// get options
		$opt = get_option('ff-rest-security-settings');

		// selected access mode (allow, deny)
		$mode = $opt['auth_mode'];

		// exemptions for allow/deny
		$exemptions = $opt['exempt_uri'];

		// test exemptions
		$exempt = false;

		if(is_array($exemptions)) {
			foreach($exemptions as $e) {
				// wildcard match
				if(substr($e, -1) == '*') {
					$exempt = strpos( $current_request, site_url(rtrim($e, '*'), 'relative') ) === 0;
				}
				// otherwise exact match
				else {
					$exempt = ( $current_request == strtolower( site_url($e, 'relative') ) );
				}

				if($exempt) break;
			}
		}

		// generate WP error if access conditions are not met
		$error = new \WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access the REST API.', 'midgard' ), array( 'status' => rest_authorization_required_code() ) );


		// if mode is allowed but the current request is exempted
		// then it is NOT allowed
		if( $mode == 'allow' && $exempt) {
			return $error;
		}
		// otherwise if the mode is 'deny' and the current request is NOT excepted
		// then it is NOT allowed
		else if( $mode == 'deny' && !$exempt) {
			return $error;
		}

		// otherwise get out of the way
		return $access;
	}
}
