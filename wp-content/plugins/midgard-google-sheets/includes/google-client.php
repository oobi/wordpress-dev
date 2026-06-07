<?php

namespace FF\Midgard\Sheets;

/* !! NOTICE FOR LOCAL DEVELOPMENT !!
 * Save https://curl.haxx.se/ca/cacert.pem on your local file system
 * In your php.ini insert or edit the following line: curl.cainfo = "[pathtothisfile]\cacert.pem"
*/

class Midgard_Google_Client
{
	protected static $instance; // instance of the Midgard_Google_client
	protected $client;			// google client belonging to the static instance

	/////////////////////////////////////////////////////////////
	// INSTANCE METHODS
	/////////////////////////////////////////////////////////////

	/**
	 * Setup and return a reference to a Google_Client object
	 */
	public function __construct($client_secret_path, $application_name=false) {
		// If modifying these scopes, delete your previously saved credentials
		// at ~/.credentials/sheets.googleapis.com-php-quickstart.json
		$scopes = implode(' ', array( \Google_Service_Sheets::SPREADSHEETS_READONLY) );
		$settings = get_option( 'midgard_google_sheets_settings' );
		// optionally set application name
		if(!$application_name) {
			$application_name = $settings['app_name'];
		}

		$client = new \Google_Client();
		$client->setApplicationName($application_name);
		$client->setScopes($scopes);
		if(file_exists($client_secret_path)) {
			@$client->setAuthConfig($client_secret_path);
		}
		$client->setAccessType('offline');

		$this->client = $client;
	}

	private function _get_client() {
		return $this->client;
	}

	/////////////////////////////////////////////////////////////
	// STATIC CLASS METHODS
	/////////////////////////////////////////////////////////////

	public static function get_instance($client_secret_path, $application_name=false) {
		if( !self::$instance ) {
			self::$instance = new Midgard_Google_Client( $client_secret_path, $application_name);
		}
		return self::$instance;
	}

	public static function get_client($client_secret_filename, $application_name=false) {
		$secret_path = Midgard_Google_Utils::get_json_path($client_secret_filename);
		$instance = self::get_instance( $secret_path, $application_name );
		return $instance->_get_client();
	}

	/**
	 * Use supplied auth code to call Google API to request an access token
	 * @param {string} $auth_code - a valid auth code
	 * @param {string} $access_token - the credentials data
	 * @param {string} $client_secret_path - the location of the client secret file
	 * @param {*} Google Client
	 */
	public static function get_access_token($auth_code, $access_token, $client_secret_path) {
		// get a regerence to the google client
		$client = self::get_client($client_secret_path);

		if( is_string($access_token)) {
			$access_token = json_decode($access_token);
		}

		// if the credentials path exists use the credentials found there
		if (empty($access_token)) {
			// Exchange authorization code for an access token.
    		$access_token = $client->fetchAccessTokenWithAuthCode($auth_code);
    		$is_error = array_key_exists('error', $access_token);

    		if(!$is_error) {
	    		$client->setAccessToken($access_token);

	    		// Refresh the token if it's expired.
				if ($client->isAccessTokenExpired()) {
					$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
					$access_token = $client->getAccessToken();
				}
		   } else {
		   		echo '<code>' . $access_token['error_description'] . '</code>';
		   }

	  	}

		 return $access_token;

	}
}
