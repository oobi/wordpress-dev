<?php

namespace FF\Midgard;

use FF\Midgard\Midgard_Common;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Midgard
 * @subpackage Midgard/public
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Midgard_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Midgard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Midgard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/midgard-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Midgard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Midgard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/midgard-public.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Intercept the output template for the 'data-feed' type and change to our own so we can use
	 * data transforms and grab external data etc
	 */
	public function set_data_output_template($template) {
		global $post;

		if(is_single() && $post->post_type == 'data_feed') {
			$new_template = plugin_dir_path( __FILE__ ) . 'data-output-template.php';
			return $new_template;
		}

		// not found - return original template
		return $template;
	}

	/**
	 * Output a JSON preview for the current feed (post).
	 * This allows us to bypass any additional security and use simply WordPress roles rather
	 * than messing with tokens and REST for previewing the JSON format.
	 */
	public function set_data_output_content($content) {
		global $post;

		// do this only for feed single posts
		if(is_single() && $post->post_type == 'data_feed') {

			$preview = array_key_exists('preview', $_GET) && $_GET['preview'];
			$nomap = array_key_exists('nomap', $_GET) && $_GET['nomap'];

			if($preview) {
				$content = json_encode( Midgard_Common::get_data($post->ID, $nomap), JSON_PRETTY_PRINT);
			} else {
				$content = json_encode( Midgard_Common::get_data($post->ID, $nomap));
			}

		}

		return $content;
	}

	/**
	 * Limit the preview content to administrative users only
	 * This is called by pre_get_posts hook
	 */
	public function limit_preview_access( $query ) {
		// do this only for feed single posts
		if($query->is_single() && $query->query_vars['post_type'] == 'data_feed') {

			// is user logged in?
			if( is_user_logged_in() ) {

				// if user is in admin role then good to go
				if( current_user_can('manage_options') ) {
					return $query;
				}
				// otherwise show 404
				else {
					$query->set_404();
				}
			}
			// otherwise redirect to login page
			else {
				auth_redirect(  );
			}
		}
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

		// allow preflights without auth header
		if( strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
			return $access;
		}

		// if the user is logged in just go through with the usual auth - no further checks required
		if( is_user_logged_in() ) {
			return $access;
		}

		// current REST request (strip GET parameters)
		$current_request = strtolower( preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']) );

		// get options
		$opt = get_option('midgard_security_settings');

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

	/**
	 * set allowed HTTP orgins for CORS requests
	 */
	public function set_cors_headers() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null;


		// if URI matches our remote API then allow a cross origin request
		if( $uri && preg_match( '/\/(midgard|jwt-auth)\//', $uri)) {

			// WordPress already sets most of this up
			// we just need to authorise the headers we want to add
			header( 'Access-Control-Allow-Headers: Authorization, Content-Type, Cache-Control, pragma, x-prototype-version, x-requested-with,', false);

			// park the full implementation
			/*
			$origin = get_http_origin();
			if ( $origin ) {
				// Requests from file:// and data: URLs send "Origin: null".
				if ( 'null' !== $origin ) {
					$origin = esc_url_raw( $origin );
				}
				header( 'Access-Control-Allow-Origin: ' . $origin );
				header( 'Access-Control-Allow-Methods: OPTIONS, GET, POST' );
				header( 'Access-Control-Allow-Credentials: true' );
				false parameter means we can add more than one of these
				header( 'Access-Control-Allow-Headers: Authorization, Content-Type, Cache-Control, pragma, x-prototype-version, x-requested-with,', false);
				header( 'Vary: Origin', false );
			} elseif ( ! headers_sent() && 'GET' === $_SERVER['REQUEST_METHOD'] && ! is_user_logged_in() ) {
				header( 'Vary: Origin', false );
			}
			*/
		}
	}
}
