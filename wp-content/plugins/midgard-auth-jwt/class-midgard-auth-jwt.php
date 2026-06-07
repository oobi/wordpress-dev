<?php

namespace FF\Midgard;

/** Require the JWT library from the jwt-authentication-for-wp-rest-api class. */
use \FF_WP_REST_JWT\JWT;

// e.g. http://schoolapp.fi.net.au/?midgard-auth-jwt

class Midgard_Auth_JWT {

	// query var to look for in order to execute
	protected $query_var;

	// token expires in (seconds)
	protected $expiry;

	function __construct() {
		// get the options page settings
		$options = get_option( 'midgard_auth_jwt_settings' );

		// Set expiry
		$this->expiry = isset( $options['token_expiry'] ) ? (int)$options['token_expiry'] : Midgard_Auth_JWT::get_default_expiry();

		// set query var
		$this->query_var = isset( $options['jwt_key'] ) ? $options['jwt_key'] : 'midgard-auth-jwt';
	}

	public static function get_default_expiry() {
		return defined('JWT_AUTH_EXPIRE') ? JWT_AUTH_EXPIRE : (DAY_IN_SECONDS * 7);
	}

	function run() {
		$this->add_hooks();

		/** Set up options page */
		require_once(dirname(__FILE__) . '/options-page.php');
	}

	function add_hooks() {
		add_action('parse_request', array($this, 'intercept_request'));
	}

	function add_new_query_vars($vars) {
		$vars[] = $this->query_var;
		return $vars;
	}

	function intercept_request( $query ) {

		// if code has already been generated, do nothing
		// prevents endless loop of code generating
		if( isset( $_GET[ $this->query_var] ) && !empty($_GET[ $this->query_var]) ) {
			// display thanks page
			include(dirname(__FILE__) . '/result.php');
			exit;
		}

		// if query var in request then generate me a token
		if( isset( $_GET[ $this->query_var ]) ) {

			// if user is logged in then generate a JWT
			if( is_user_logged_in() ) {

				// get secret key from wp-config definition
				$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

				// get the current logged in user
				$user = wp_get_current_user();

				// BELOW CODE SNIPPED FROM class-jwt-auth-pubilc.php in the jwt-authentication-for-wp-rest-api plugin
				// we need to replicate this much of the functionality but not the actual authentication flow

				/** Valid credentials, the user exists create the according Token */
				$issuedAt = time();
				$notBefore = apply_filters('jwt_auth_not_before', $issuedAt, $issuedAt);
				$expire = apply_filters('jwt_auth_expire', $issuedAt + $this->expiry, $issuedAt);

				$token = array(
					'iss' => get_bloginfo('url'),
					'iat' => $issuedAt,
					'nbf' => $notBefore,
					'exp' => $expire,
					'data' => array(
						'user' => array(
							'id' => $user->data->ID,
						),
					),
				);

				/** Let the user modify the token data before the sign. */
				$token = JWT::encode(apply_filters('jwt_auth_token_before_sign', $token, $user), $secret_key);

				/** The token is signed, now create the object with no sensible user data to the client*/
				$data = array(
					'token' => $token,
					'user_email' => $user->data->user_email,
					'user_nicename' => $user->data->user_nicename,
					'user_display_name' => $user->data->display_name,
				);

				/** Let the user modify the data before send it back */
				$data = apply_filters('jwt_auth_token_before_dispatch', $data, $user);

				// add code to URL
				$params = array(
					$this->query_var 	=> $data['token']
				);

				header( 'Location: ' . get_site_url() . '?' . http_build_query( $params ) );
				exit();
			}
			// otherwise go through login flow first
			else {
				auth_redirect();
			}
		}
	}

}