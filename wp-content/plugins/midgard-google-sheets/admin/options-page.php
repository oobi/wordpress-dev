<?php

namespace FF\Midgard\Sheets;

use FF\Midgard\Midgard_Options_Page;

class Midgard_Google_Sheets_Options_Page
{

	/**
	 * key which stores settings in the database (e.g. email_view_swettings)
	 */
	protected $settings_key;

	/**
	 * Constructor configuration (overridden by child classes)
	 */
	protected $config;

	/**
	 * Slug for settings page
	 */
	private $settings_page;

	/**
	 * Tab settings
	 */
	private $tab_slug = 'google-sheets';
	private $tab_title = 'Google Sheets';

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'midgard-google-sheets-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_google_sheets_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard Google Sheets Settings',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard Sheets',						//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_google_sheets_settings'			//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// get current settings
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name

		// Actions
		add_action('midgard_settings_tab', array($this, 'settings_tab'));
		add_action('midgard_settings_tab_content', array($this, 'settings_tab_content'));
		add_action('admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Render form output if this is the current tab
	 */
	public function settings_tab_content() {
		if( Midgard_Options_Page::$current_tab == $this->tab_slug) {
			settings_fields( 'midgard_sheets_oauth_group' );		// Option group
			do_settings_sections( $this->settings_page );	// Page
			submit_button();
		}
	}

	/**
	 * Render tab for this options page
	 */
	public function settings_tab() {
		echo Midgard_Options_Page::tab_output($this->tab_slug, $this->tab_title);
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT FIELD DEFINITIONS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Register and add settings
	 */
	public function page_init() {

		/* ADD SECTIONS */
		add_settings_section(
			'midgard_sheets_oauth_group', 				// ID
			'Google OAuth Settings', 					// Title
			array($this, 'oauth_section_callback'), 	// Callback
			$this->settings_page 						// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_sheets_oauth_group', 				// Option group
			$this->settings_key, 						// Option name
			array( $this, 'sanitize' ) 					// Sanitize
		);

		add_settings_field(
			'app_name', 			                    	// ID
			'Application Name', 							// Title
			array( $this, 'app_name_callback' ), 	    	// Callback
			$this->settings_page, 				            // Page
			'midgard_sheets_oauth_group'					// Section ID
		);

		add_settings_field(
			'client_secret', 			                    // ID
			'Client Secret', 							    // Title
			array( $this, 'client_secret_callback' ), 	    // Callback
			$this->settings_page, 				            // Page
			'midgard_sheets_oauth_group'					// Section ID
		);

		add_settings_field(
			'client_secret_path',	                    	// ID
			'Client Secret Path', 						    // Title
			array( $this, 'client_secret_path_callback' ), 	// Callback
			$this->settings_page, 				            // Page
			'midgard_sheets_oauth_group'					// Section ID
		);

		add_settings_field(
			'auth_code',		      		              	// ID
			'Authorization Code',							// Title
			array( $this, 'auth_code_callback' ),		  	// Callback
			$this->settings_page, 				            // Page
			'midgard_sheets_oauth_group'					// Section ID
		);

		add_settings_field(
			'credentials',	                    			// ID
			'Credentials', 						    		// Title
			array( $this, 'credentials_callback' ), 		// Callback
			$this->settings_page, 				            // Page
			'midgard_sheets_oauth_group'					// Section ID
		);

		add_settings_field(
			'reset_auth',		      		              	// ID
			'Reset Authorisation',							// Title
			array( $this, 'reset_auth_callback' ),		  	// Callback
			$this->settings_page, 				            // Page
			'midgard_sheets_oauth_group'					// Section ID
		);
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT VALIDATION ON SAVE
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {

		// get inputs
		$app_name 				= sanitize_text_field($input['app_name']);
		$client_secret 			= json_encode(json_decode($input['client_secret']));  // force JSON format
		$client_secret_path 	= sanitize_text_field($input['client_secret_path']);
		$credentials 			= json_encode(json_decode($input['credentials']));
		$auth_code 				= sanitize_text_field($input['auth_code']);

		// blitz saved auth if this is set
		if(isset($input['reset_auth'])) {
			$auth_code = '';
			$credentials = '';
		}

		// delete any old secret files
		Midgard_Google_Utils::delete_cache();

		// only recreate if the file path is empty OR if the file path didn't contain a valid extension
		// this prevents new filenames being created every time we deploy locally and save the setting
		// (which messes everyone up using the same database)
		$filename = pathinfo($client_secret_path, PATHINFO_EXTENSION) == 'json' ? $client_secret_path : null;
		$client_secret_path = Midgard_Google_Utils::set_client_secret($client_secret, $filename);

		// Make a call to Google API to set the access token
		if( ! empty( $auth_code ) ) {
			$credentials = Midgard_Google_Client::get_access_token($auth_code, $credentials, $client_secret_path );
		}

		// return new values
		$new_input['app_name']				= $app_name;
		$new_input['client_secret'] 		= $client_secret;
		$new_input['client_secret_path'] 	= $client_secret_path;
		$new_input['credentials'] 			= $credentials;
		$new_input['auth_code'] 			= $auth_code;

		return $new_input;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT FIELD DISLPAY CALLBACKS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * OAuth settings section callback
	 */
	public function oauth_section_callback() { ?>
		<ol>
			<li>Use <a href="https://console.developers.google.com/start/api?id=sheets.googleapis.com" target="_blank">this wizard</a>
				to create or select a project in the Google Developers Console and automatically turn on the API.
				Click Continue, then go to credentials.</li>
			<li>On the Add credentials to your project page, click the Cancel button.</li>
			<li>At the top of the page, select the OAuth consent screen tab. Select an Email address, enter a Product name if not already set, and click the Save button.</li>
			<li>Select the Credentials tab, click the Create credentials button and select OAuth client ID.</li>
			<li>Select the application type Desktop, enter the name of your application - e.g. "Buzz Midgard", and click the Create button.</li>
			<li>Click OK to dismiss the resulting dialog.</li>
			<li>Click the Download JSON (down arrow) button to the right of the client ID.</li>
			<li>Open the file in a text editor and copy the contents into the Client Secret field below</li>
		</ol>
		<?php
	}

	/**
	 * Callback to client secret field
	 */
	public function app_name_callback() {
		$app_name = isset( $this->options['app_name'] ) ? esc_html( $this->options['app_name']) : '';
		printf(
			'<input type="text" class="large-text" id="app_name" name="%s[app_name]" value="%s" required>',
			$this->settings_key,
			$app_name
		);
		_e('<p class="description">Enter your application name.</p>', 'midgard-google-sheets');
	}

	/**
	 * Callback to client secret field
	 */
	public function client_secret_callback() {
		$secret = isset( $this->options['client_secret'] ) ? esc_html( $this->options['client_secret']) : '';

		if( $secret === 'null' ) {
			$secret = '';
		}

		printf(
			'<textarea rows="5" class="large-text" id="client_secret" name="%s[client_secret]" required>%s</textarea>',
			$this->settings_key,
			$secret
		);
		_e('<p class="description">Paste the contents of the Client Secret JSON file.</p>', 'midgard-google-sheets');
	}

	/**
	 * Callback to client secret field path
	 */
	public function client_secret_path_callback() {

		$secret_path = isset( $this->options['client_secret_path'] ) ? esc_html( $this->options['client_secret_path']) : '';
		$secret_full_path =  Midgard_Google_Utils::get_json_path($secret_path);

		printf(
			'<input type="hidden" name="%s[client_secret_path]" value="%s">',
			$this->settings_key,
			$secret_path
		);

		echo '<p>';

		if( !file_exists($secret_full_path) ) {
			echo '<p><code style="color:red">FILE NOT FOUND</code></p>';
			return;
		} else {
			echo '<p><code style="color:green">FILE FOUND</code></p>';

			// get / check google authorisation
			try {
				$client = Midgard_Google_Client::get_client($secret_path);

				// Request authorization from the user.
				if( empty( $this->options['auth_code'] ) ) {
					$authUrl = $client->createAuthUrl();
					_e('<p>Click the link below to retrieve an authorization code from Google which you will need for the next step.');
					printf('<p><a href="%1$s" target="_blank">%2$s</a></p>',
						$authUrl,
						__('Authorise with Google', 'midgard-google-sheets'));
				}

			} catch( \Exception $ex) {
				echo '<p><code style="color:red">';
				_e('Something went horribly wrong', 'midgard-google-sheets');
				echo ' - ';
				echo $ex->getMessage();
				echo '</code></p>';
			}
		}

		echo '</p>';

	}

	/**
	 * Callback to CREDENTIALS field path
	 */
	public function credentials_callback() {
		//D:\vhosts\fi.net.au_schoolapp\httpdocs/wp-content/midgard-cache/sites/1/google-sheets/client_secret_58d06a70291dc.json
		$client_secret_path = isset( $this->options['client_secret_path'] ) ? esc_html( $this->options['client_secret_path']) : '';
		$auth_code 			= isset( $this->options['auth_code'] ) ? esc_html( $this->options['auth_code']) : '';
		$credentials 		= isset( $this->options['credentials'] ) ? $this->options['credentials'] : '';

		printf(
			'<textarea rows="5" class="hidden" id="credentials" name="%s[credentials]">%s</textarea>',
			$this->settings_key,
			is_string( $credentials ) ? $credentials : json_encode( $credentials )
		);

		echo '<p>';

		if( empty($credentials) ) {
			echo '<code>CREDENTIALS NOT YET SAVED</code>';
		} else {
			echo '<p><code style="color:green">CREDENTIALS SAVED</code></p>';
		}
		echo '</p>';
	}

	/**
	 * Callback to auth code field
	 */
	public function auth_code_callback() {
		$secret = isset( $this->options['auth_code'] ) ? esc_html( $this->options['auth_code']) : '';
		$field_type = empty( $this->options['auth_code'] ) ? 'text' : 'hidden';

		// retrieve code from URL is google auth comes back
		$code = $_GET['code'] ?? false;

		if( $field_type == 'text' && $code ) {
			$secret = urldecode($_GET['code']);
		}

		printf(
			'<input type="' . $field_type . '" class="large-text" id="auth_code" name="%s[auth_code]" value="%s">',
			$this->settings_key,
			$secret
		);

		// message to show if auth code is set OR not set
		if( $field_type === 'text' ) {
			_e('<p class="description">Paste the authorization code here</p>', 'midgard-google-sheets');
		} else {
			echo '<p><code style="color:green">AUTHORISATION CODE REDEEMED</code></p>';
		}

	}

	/**
	 * Callback to reset auth
	 */
	public function reset_auth_callback() {
		printf(
			'<input type="checkbox"  id="reset_auth" name="%s[reset_auth]" value="1">',
			$this->settings_key
		);
		_e('<p class="description">Tick the box to reset stored authorisation keys. Your application will need to be re-authorised.</p>', 'midgard-google-sheets');
	}

}

// init the options page
if( is_admin() ) {
	$options_page = new Midgard_Google_Sheets_Options_Page();
}
