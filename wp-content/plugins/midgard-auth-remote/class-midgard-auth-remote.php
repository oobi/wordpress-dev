<?php

namespace FF\Midgard;

class Midgard_Auth_Remote {

	// query var to look for in order to execute
	protected $query_var = 'midgard-auth-token';

	// the response sent by the validator that indicates accepted token
	protected $valid_response_code = 'jwt_auth_valid_token';

	// user to log in as
	protected $log_in_as;

	// REST entpoint for validation
	protected $validate_url;

	// disable WordPress admin banner and access to dashboard
	protected $prevent_admin;

	/**
	 * Constructor
	 */
	function __construct() {
		// get the options page settings
		$options = get_option( 'midgard_auth_remote_settings' );

		// Get options
		$this->log_in_as 	= isset( $options['log_in_as'] ) ? $options['log_in_as'] : null;
		$this->validate_url = isset( $options['validate_url'] ) ? $options['validate_url'] : null;
		$this->query_var 	= isset( $options['jwt_key'] ) ? $options['jwt_key'] : 'midgard-auth-token';
		$this->prevent_admin = isset( $options['prevent_admin']) ? !!$options['prevent_admin'] : false;
	}

	/**
	 * Initialise the plugin
	 */
	public function run() {
		// add hooks
		add_action('parse_request', array($this, 'intercept_request'));

		if( $this->prevent_admin ) {
			add_action('init', array($this, 'maybe_remove_admin_bar'));
			add_action('init', array($this, 'maybe_restrict_dashboard'));
		}

		/** Set up options page */
		require_once(dirname(__FILE__) . '/options-page.php');

		// init the options page
		if( is_admin() ) {
			$options_page = new Midgard_Auth_Remote_Options_Page();
		}
	}

	/**
	 * Intercept the request query and process the token if supplied
	 * @param $query - WordPress Query params
	 */
	public function intercept_request( $query ) {
		// if not already logged in...
		// AND if the token is supplied in URL
		if( !is_admin() && !is_user_logged_in() && isset( $_GET[$this->query_var] ) ) {
			$token = $_GET[$this->query_var];

			// issue a remote request to validate the supplied token against the defined endpoint
			$args = array(
				'headers' => array( 'Authorization' => 'Bearer ' . $token ),
				'timeout' => 10
			);

			// validate the token with the remote REST service
			$response = @wp_safe_remote_post( $this->validate_url, $args );

			// good response?
			if( $response && ! is_wp_error( $response ) ) {

				$status = wp_remote_retrieve_response_code( $response );

				if( $status == 200 ) {
					// convert JSON to array
					$response_json = json_decode( $response['body'], true  );

					// is the response valid (was the token accepted) ?
					$valid = is_array($response_json) && isset($response_json['code']) && $response_json['code'] == $this->valid_response_code;

					if( $valid ) {
						// create a session for the chosen user
						$user = get_user_by( 'slug', $this->log_in_as );
						wp_clear_auth_cookie();
						wp_set_current_user( $user->ID );
						wp_set_auth_cookie( $user->ID );
						$this->maybe_remove_admin_bar();
					}
				}

			}

		}

	}

	/**
	 * Is this session logged in as the chosen user?
	 * Return true if logged in and the
	 */
	protected function is_user_session() {
		$user = wp_get_current_user();
		return ( $user->user_login == $this->log_in_as );
	}

	/**
	 * Conditionally remove the admin bar
	 * IF the currently logged in user is the target user
	 */
	public function maybe_remove_admin_bar() {
		if( !is_admin() && $this->is_user_session() ) {
			show_admin_bar(false);
		}
	}

	/**
	 * Restrict dashboard access for logged in user
	 */
	public function maybe_restrict_dashboard() {
		if ( $this->is_user_session() ) {
			// if admin section and not doing something AJAX get out
			if ( is_admin()  && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_redirect( home_url() );
				exit;
			}
		}
	}

}