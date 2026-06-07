<?php

namespace FF\Midgard\Facebook;

use Facebook\Facebook;
use FF\Midgard\Midgard_Options_Page;

class Midgard_Facebook_Options_Page {

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
	private $tab_slug = 'facebook';
	private $tab_title = 'Facebook';

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'midgard-facebook-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_facebook_settings',			//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard Facebook Settings',			//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard Facebook',					//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_facebook_settings'			//e.g. 'mailchimp_settings
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
			settings_fields( 'midgard_facebook_oauth_group' );		// Option group
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
			'midgard_facebook_oauth_group', 				// ID
			'Facebook - Authentication Settings', 			// Title
			array($this, 'oauth_section_callback'), 		// Callback
			$this->settings_page 							// Page
		);

		add_settings_section(
			'midgard_facebook_oauth_test', 				// ID
			'Login', 									// Title
			array($this, 'oauth_test_callback'), 		// Callback
			$this->settings_page 						// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_facebook_oauth_group', 				// Option group
			$this->settings_key, 							// Option name
			array( $this, 'sanitize' ) 						// Sanitize
		);

		add_settings_field(
			'app_id', 			               			    // ID
			'App ID', 									    // Title
			array( $this, 'app_id_callback' ), 	    		// Callback
			$this->settings_page, 				            // Page
			'midgard_facebook_oauth_group'					// Section ID
		);

		add_settings_field(
			'app_secret',		                   	 		// ID
			'App Secret', 						    		// Title
			array( $this, 'app_secret_callback' ), 	 		// Callback
			$this->settings_page, 				            // Page
			'midgard_facebook_oauth_group'					// Section ID
		);

		// HIDDEN IN PRODUCTION MODE
		// THIS IS HERE TO SHOW THE TOKEN TO DEVELOPERS
		if( defined('WP_DEBUG') && WP_DEBUG )  {
			add_settings_field(
				'access_token',		                   			// ID
				'Access Token', 								// Title
				array( $this, 'access_token_callback' ),  		// Callback
				$this->settings_page, 				            // Page
				'midgard_facebook_oauth_group'					// Section ID
			);
		}

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
		$new_input = array();

		foreach($input as $key=>$item) {
			$new_input[$key] = sanitize_text_field($item);
		}

		return $new_input;
	}


	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT FIELD DISLPAY CALLBACKS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * OAuth settings section callback
	 */
	public function oauth_section_callback() {
		printf( '<p>%s</p>',
				__('Login to your Facebook account at <a href="https://developers.facebook.com/apps">developers.facebook.com/apps</a>. Create a new app or edit an existing one to get the required tokens.', 'midgard-facebook'));
		printf( '<p>%s</p>',
				__('The account used to create the App ID/Secret must be the same account used to login below.', 'midgard-facebook'));
	}

	/**
	 * OAuth test section callback
	 */
	public function oauth_test_callback() {
		printf( '<p>%s</p><ol><li>%s</li><li>%s</li></ol>',
				__('To complete setup, log in using the link below to authenticate your account. The account you use to log in must be:', 'midgard-facebook'),
				__('The same account used to set up the App ID and App Secret', 'midgard-facebook'),
				__('The account must have admin access to the page specified in the Page ID field', 'midgard-facebook'));


		$settings = get_option('midgard_facebook_settings');
		if(!$settings) $settings = array();

		// OAuth settings - ensure all keys are present
		$auth = array_merge( array(
			'app_id' 		=> '',
			'app_secret' 	=> '',
			'access_token'	=> ''
		), $settings ) ;


		// if no fields have been filled out yet, ask for them to be
		if( empty( $auth['app_id'] ) || empty( $auth['app_secret'] ) ) {
			printf( '<p style="color:red;">%s</p>',
				__('You must complete the details above and save the page before logging in.', 'midgard-facebook'));
		}

		// otherwise if we have app ID/secret but no access token, get one
		if( !empty( $auth['app_id'] ) && !empty( $auth['app_secret'] ) && empty( $auth['access_token'] )) {
			$connection = new Facebook([
				'app_id' 				=> $auth['app_id'],
				'app_secret' 			=> $auth['app_secret'],
			]);

			// now generate the access token and save to database if valid
			$auth['access_token'] = $this->get_access_token( $connection, $auth['app_id'], $auth['app_secret'] );
			if( $auth['access_token'] ) {
				update_option( $this->config['settings_key'], $auth );
			}

		}

		// if there is an access token, show message
		if( !empty( $auth['access_token'] ) ) {
			printf( '<p style="color:green">%s</p>',
					__('Authentication successfull!', 'midgard-facebook'));
		}

	}

	/**
	 * Callback to app_id field
	 */
	public function app_id_callback() {
		$this->textfield( 'app_id' );
	}

	/**
	 * Callback to app_secret field
	 */
	public function app_secret_callback() {
		$this->textfield( 'app_secret' );
	}

	/**
	 * Callback to access_token field
	 * THIS IS FOR DEVELOPMENT USE ONLY - should not be made active and visible
	 */
	public function access_token_callback() {
		$this->textfield( 'access_token' );
		printf('<p>%s</p>', __('This is generated automatically when the authentication is verified. You may need to refresh the page to see a value in this field initially.', 'midgard-facebook') );
	}

	/**
	 * Generate a settings text field based on the key supplied
	 */
	protected function textfield( $key, $disabled=false, $new_value=null ) {
		$value = isset( $this->options[$key] ) ? esc_attr( $this->options[$key]) : '';
		printf(
			'<input type="text" class="large-text" id="%1$s" name="%2$s[%1$s]" value="%3$s" %4$s>',
			$key,
			$this->settings_key,
			$new_value !== null ? $new_value : $value,
			$disabled ? 'readonly' : ''
		);
	}

	/**
	 * Generate the access token with the app ID and app secret
	 * @param 	{object} 	connection 	- The connection object to the Facebook API
	 * @param 	{string} 	app_id 		- The app ID
	 * @param 	{string} 	app_secret	- The app secret
	 * @return 	{string} 				- The access token
	 */
	private function get_access_token( $connection, $app_id, $app_secret ) {
		// set up the login helper
		$helper = $connection->getRedirectLoginHelper();

		// authentication redirect URL
		$auth_redirect_url =  admin_url('options-general.php?page=midgard_settings&tab=facebook', true);

		// Must be added manually (undocumented as of 2018/06/19) to aid getAccessToken() method later
		if ( isset( $_GET['state'] ) ) {
			$helper->getPersistentDataHandler()->set('state', $_GET['state']);
		}

		// if code is not present in the URL, attempt to login to get it
		if( ! isset( $_GET['code'] ) ) {

			// Output the login link
			$permissions = ['pages_read_user_content']; // necessary to access pages your account is an admin of

			$loginUrl = $helper->getLoginUrl( $auth_redirect_url, $permissions );
			echo '<a class="button" style="height:auto;line-height:normal;padding:10px 20px;background:#3B5998;color:#FFF;border-color:#3B5998;"
				href="' . htmlspecialchars($loginUrl) . '"><span class="dashicons dashicons-facebook"></span> Log in with Facebook!</a>';

		} else {

			// Get the access token
			try {
				$accessToken = $helper->getAccessToken( $auth_redirect_url );
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				echo 'Graph returned an error: ' . $e->getMessage();
				exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
				exit;
			}

			// if not found, throw an error
			if (! isset($accessToken)) {
				if ($helper->getError()) {
					header('HTTP/1.0 401 Unauthorized');
					echo "Error: " . $helper->getError() . "\n";
					echo "Error Code: " . $helper->getErrorCode() . "\n";
					echo "Error Reason: " . $helper->getErrorReason() . "\n";
					echo "Error Description: " . $helper->getErrorDescription() . "\n";
				} else {
					header('HTTP/1.0 400 Bad Request');
					echo 'Bad request';
				}
				exit;
			}

			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $connection->getOAuth2Client();

			// Get the access token metadata from /debug_token
			$tokenMetadata = $oAuth2Client->debugToken( $accessToken );

			// Validation
			$tokenMetadata->validateAppId($app_id);
			$tokenMetadata->validateExpiration();

			// if the access token isn't long-lived, make it so
			if (! $accessToken->isLongLived()) {
				// Exchanges a short-lived access token for a long-lived one
				try {
				  $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
				} catch (Facebook\Exceptions\FacebookSDKException $e) {
				  echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
				  exit;
				}
			}

			// return the access token
			return $accessToken->getValue();
		}

		// if code isn't set, return false until it is
		return false;
	}

}

// init the options page
if( is_admin() ) {
	$options_page = new Midgard_Facebook_Options_Page();
}
