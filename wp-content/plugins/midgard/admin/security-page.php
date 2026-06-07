<?php

namespace FF\Midgard;

class Midgard_Security_Page {

	/**
	 * key which stores settings in the database (e.g. email_view_swettings)
	 */
	protected $settings_key;

	/**
	 * Constructor configuration (overridden by child classes)
	 */
	protected $config;

	/**
	 * Options array
	 */
	protected $options;

	/**
	 * Slug for settings page
	 */
	private $settings_page;

	/**
	 * Tab settings
	 */
	private $tab_slug = 'security';
	private $tab_title = 'Security';

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'midgard-security-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_security_settings',			//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard Security Settings',			//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard Security',					//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_security_settings'			//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// get current settings, default to blank array
		$opt 			= get_option( $this->config['settings_key'] );
		$this->options 	= $opt ? $opt : array();

		// Actions
		add_action('midgard_settings_tab', array($this, 'settings_tab'), 5);
		add_action('midgard_settings_tab_content', array($this, 'settings_tab_content'));
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Render form output if this is the current tab
	 */
	public function settings_tab_content() {
		if( Midgard_Options_Page::$current_tab == $this->tab_slug) {
			settings_fields( 'midgard_security_group' );	// Option group
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
			'midgard_security_group', 						// ID
			'Security Settings', 								// Title
			array($this, 'security_group_callback'), 		// Callback
			$this->settings_page 							// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_security_group', 						// Option group
			$this->settings_key, 							// Option name
			array( $this, 'sanitize' ) 						// Sanitize
		);

		add_settings_field(
			'auth_mode', 			                    	// ID
			'Authentication mode', 							// Title
			array( $this, 'auth_mode_callback' ), 			// Callback
			$this->settings_page, 				            // Page
			'midgard_security_group'						// Section ID
		);

		add_settings_field(
			'auth_exempt', 			                    	// ID
			'Exemptions (URI)',								// Title
			array( $this, 'exempt_uri_callback' ), 			// Callback
			$this->settings_page, 				            // Page
			'midgard_security_group'						// Section ID
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

		if( isset( $input['auth_mode'] ) ) {
			$new_input['auth_mode'] = wp_kses_post( $input['auth_mode'] );
		} else {
			$new_input['auth_mode'] = 'allow';
		}

		if( isset( $input['exempt_uri'] ) ) {
			$new_input['exempt_uri'] = explode("\n", wp_kses_post( $input['exempt_uri'] ) );
		}

		if( ! isset( $new_input['exempt_uri'] ) || ! is_array( $new_input['exempt_uri'] ) ) {
			$new_input['exempt_uri'] = array();
		}

		// trim URIs
		foreach($new_input['exempt_uri'] as $index=>$x) {
			$new_input['exempt_uri'][$index] = trim($x);
		}

		return $new_input;
	}


	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT FIELD DISPLAY CALLBACKS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Settings section callback
	 */
	public function security_group_callback() {
		echo '<p>';
		_e('WordPress list/get endpoints default to being publicly accessible, however you can alter this behaviour (and the behaviour of other <strong>publicly accessible</strong> endpoints).');

		echo '<br><em>';
		_e('Note that this ONLY applies to <strong>publicly</strong> accessible endpoints. This plugin will NOT remove any existing authentication requirement for any endpoint. For example EDIT or DELETE endpoints require authentication regardless.');
		echo '</em><br>';

		echo '<p>'  . __('Examples', 'midgard') . '</p>';
		echo '<ol>';
			printf('<li>%s</li>', __('To make ONLY Midgard feeds private, select "allow all" and enter the exemption: <code>/wp-json/ff/v1/midgard/*</code>', 'midgard') );
			printf('<li>%s</li>', __('To make ALL publicly accessible feeds private, select "deny all" and enter any exemptions requried. <br>If you are using JWT authentication you will need to exempt this: <code>/wp-json/jwt-auth/v1/token/*</code>.', 'midgard') );
			printf('<li>%s</li>', __('To exempt all the native WordPress feeds: <code>/wp-json/wp/v2/*</code>.', 'midgard') );
			printf('<li>%s</li>', __('WordPress posts <code>/wp-json/wp/v2/posts</code>', 'midgard') );
			printf('<li>%s</li>', __('WordPress pages <code>/wp-json/wp/v2/pages</code>', 'midgard') );
		echo '</ol>';

		echo '</p>';
	}

	/**
	 * Callback to auth_mode field
	 */
	public function auth_mode_callback() {
		$auth_mode = array_key_exists('auth_mode', $this->options) ? $this->options['auth_mode'] : 'allow';

		printf('<select id="%1$s[auth_mode]" name="%1$s[auth_mode]">',
				$this->settings_key );

		printf('<option value="allow" %s>%s</option>',
				selected($auth_mode, 'allow', false),
				__('Allow all REST requests except for the URIs listed below', 'midgard')
		);

		printf('<option value="deny" %s>%s</option>',
				selected($auth_mode, 'deny', false),
				__('Deny all REST requests except for the URIs listed below', 'midgard')
		);

		echo '</select>';
	}

	/**
	 * Callback to exempt_uri callback field
	 */
	public function exempt_uri_callback() {
		$exempt = array_key_exists('exempt_uri', $this->options) ? $this->options['exempt_uri'] : array();
		if( ! is_array( $exempt ) ) {
			$exempt = array();
		}

		$exempt = implode("\n", $exempt);

		printf('<textarea rows="8" class="large-text" id="%1$s[exempt_uri]" name="%1$s[exempt_uri]">%2$s</textarea>',
			$this->settings_key,
			$exempt
			);

		echo '<p>';

		_e( 'To exempt REST URIs from the authentication rule enter them one URI per line. For example:', 'midgard');
		echo '<code>/wp-json/ff/v1/midgard/name-of-feed</code>';

		echo '<br>';

		_e( 'You may use <strong>*</strong> as a wildcard at the end of a URI to match everything in a namespace. For example:');
		echo '<code>/wp-json/ff/v1/midgard/*</code>';

		echo '</p>';
	}
}

// init the options page
if( is_admin() ) {
	$options_page = new Midgard_Security_Page();
}
