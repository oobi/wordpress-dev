<?php

namespace FF\Midgard\Instagram;

use FF\Midgard\Midgard_Options_Page;

class Midgard_Instagram_Options_Page {

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
	private $tab_slug = 'instagram';
	private $tab_title = 'Instagram';

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'midgard-instagram-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_instagram_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard Instagram Settings',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard Instagram',					//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_instagram_settings'			//e.g. 'mailchimp_settings
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
			settings_fields( 'midgard_instagram_oauth_group' );		// Option group
			do_settings_sections( $this->settings_page );			// Page
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
			'midgard_instagram_oauth_group', 				// ID
			'Instagram - Authentication Settings', 			// Title
			array($this, 'oauth_section_callback'), 		// Callback
			$this->settings_page 							// Page
		);

		add_settings_section(
			'midgard_instagram_token_test', 				// ID
			'Token Test', 								// Title
			array($this, 'token_test_callback'), 		// Callback
			$this->settings_page 						// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_instagram_oauth_group', 				// Option group
			$this->settings_key, 							// Option name
			array( $this, 'sanitize' ) 						// Sanitize
		);

		add_settings_field(
			'access_token',		                   			// ID
			'Access Token', 								// Title
			array( $this, 'access_token_callback' ),  		// Callback
			$this->settings_page, 				            // Page
			'midgard_instagram_oauth_group'					// Section ID
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

		foreach( $input as $key=>$item ) {
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
		// nothing
	}

	/**
	 * OAuth test section callback
	 */
	public function token_test_callback() {
		$settings = get_option('midgard_instagram_settings');
		if(!$settings) $settings = array();

		// OAuth settings - ensure all keys are present
		$token = $settings['access_token'] ?? null;
		$user_id = $settings['user_id'] ?? null;

		if( !empty( $token ) ) {
			$token = $settings['access_token'];
			$response = InstagramAPI::get_my_id($token);

			if( isset($response->error) ) {
				echo '<b>Auth Failed: </b>' . $response->error->message;
			} else if( isset($response->id )) {
				echo '<b>Success!</b> Your Instagram User ID is ' . $response->id;
				$settings['user_id'] = $response->id;
				update_option('midgard_instagram_settings', $settings);
			} else {
				echo 'Unknown error during authentictation.';
			}
		} else {
			printf( '<p>%s</p>',
				__('Please generate an Access Token and save above.', 'midgard-instagram'));
		}
	}

	/**
	 * Callback to access_token field
	 * THIS IS FOR DEVELOPMENT USE ONLY - should not be made active and visible
	 */
	public function access_token_callback() {

		// get access_token from DB
		$access_token = isset( $this->options['access_token'] ) ? esc_html( $this->options['access_token']) : '';

		printf('<p>You will need to create a new Facebook App with Instagram access in order to connect. <a href="%s" target="_blank">Click here</a> for details. Ignore the OAuth authentication - steps 1..3 cover creating the required app and tester account.</p>',
				'https://developers.facebook.com/docs/instagram-basic-display-api/overview#user-token-generator');

		$this->textfield( 'access_token', false );

		printf('<p>Use the <a href="%s" target="_blank">Facebook User Token Generator</a> to create an authentication token.</p>',
				'https://developers.facebook.com/docs/instagram-basic-display-api/overview#user-token-generator');

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
	$options_page = new Midgard_Instagram_Options_Page();
}
