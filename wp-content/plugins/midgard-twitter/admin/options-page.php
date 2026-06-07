<?php

namespace FF\Midgard\Twitter;

use FF\Midgard\Midgard_Options_Page;

class Midgard_Twitter_Options_Page {

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
	private $tab_slug = 'twitter';
	private $tab_title = 'Twitter';

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'midgard-twitter-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_twitter_settings',			//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard Twitter Settings',			//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard Twitter',					//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_twitter_settings'			//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// get current settings
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name

		// Actions
		add_action('midgard_settings_tab', array($this, 'settings_tab'));
		add_action('midgard_settings_tab_content', array($this, 'settings_tab_content'));
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Render form output if this is the current tab
	 */
	public function settings_tab_content() {
		if( Midgard_Options_Page::$current_tab == $this->tab_slug) {
			settings_fields( 'midgard_twitter_oauth_group' );		// Option group
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
	// OVERRIDE METHOD
	public function page_init() {

		/* ADD SECTIONS */
		add_settings_section(
			'midgard_twitter_oauth_group', 					// ID
			'OAuth Settings', 								// Title
			array($this, 'oauth_section_callback'), 		// Callback
			$this->settings_page 							// Page
		);

		add_settings_section(
			'midgard_twitter_oauth_test', 					// ID
			'OAuth Test', 									// Title
			array($this, 'oauth_test_callback'), 			// Callback
			$this->settings_page 							// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_twitter_oauth_group', 					// Option group
			$this->settings_key, 							// Option name
			array( $this, 'sanitize' ) 						// Sanitize
		);

		add_settings_field(
			'consumer_key', 			                    // ID
			'Consumer Key', 							    // Title
			array( $this, 'consumer_key_callback' ), 	    // Callback
			$this->settings_page, 				            // Page
			'midgard_twitter_oauth_group'					// Section ID
		);

		add_settings_field(
			'consumer_secret',		                    	// ID
			'Consumer Secret', 						    	// Title
			array( $this, 'consumer_secret_callback' ),  	// Callback
			$this->settings_page, 				            // Page
			'midgard_twitter_oauth_group'					// Section ID
		);

		add_settings_field(
			'access_token', 			                    // ID
			'Access Token', 							    // Title
			array( $this, 'access_token_callback' ), 	    // Callback
			$this->settings_page, 				            // Page
			'midgard_twitter_oauth_group'					// Section ID
		);

		add_settings_field(
			'access_token_secret',		                    // ID
			'Access Token Secret', 						    // Title
			array( $this, 'access_token_secret_callback' ), // Callback
			$this->settings_page, 				            // Page
			'midgard_twitter_oauth_group'					// Section ID
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
				__('Login to your Twitter account at <a href="https://apps.twitter.com/">apps.twitter.com</a> and create a new app to generate the keys below.', 'midgard-twitter'));
	}

	/**
	 * OAuth test section callback
	 */
	public function oauth_test_callback() {
		$settings = get_option('midgard_twitter_settings');
		if(!$settings) $settings = array();

		// OAuth settings - ensure all keys are present
		$auth = array_merge( array(
			'consumer_key' 		=> '',
			'consumer_secret' 	=> '',
			'access_token' 		=> '',
			'access_token_secret' => ''
		), $settings ) ;

		if(!empty( $auth['consumer_key'] )) {
			$connection = new TwitterOAuth($auth['consumer_key'], $auth['consumer_secret'], $auth['access_token'], $auth['access_token_secret']);
			$test = $connection->get("account/verify_credentials");

			if( isset($test->errors) ) {
				printf( '<p style="color:red">%s</p>',
				__('Bad authentication data. Please check your keys.', 'midgard-twitter'));

				foreach($test->errors as $e) {
					echo '<code>' . $e->message . '</code><br>';
				}
			} else {
				printf( '<p style="color:green">%s</p>',
				__('Authentication succeeded!', 'midgard-twitter'));
			}

		} else {
			printf( '<p>%s</p>',
				__('Please complete the details above.', 'midgard-twitter'));
		}

	}

	/**
	 * Callback to consumer_key field
	 */
	public function consumer_key_callback() {
		$this->textfield( 'consumer_key' );
	}

	/**
	 * Callback to consumer_secret field
	 */
	public function consumer_secret_callback() {
		$this->textfield( 'consumer_secret' );
	}

	/**
	 * Callback to access_token field
	 */
	public function access_token_callback() {
		$this->textfield( 'access_token' );
	}

	/**
	 * Callback to access_token_secret field
	 */
	public function access_token_secret_callback() {
		$this->textfield( 'access_token_secret' );
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

}

// init the options page
if( is_admin() ) {
	$options_page = new Midgard_Twitter_Options_Page();
}
