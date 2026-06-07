<?php

namespace FF\Twitter;

class TwitterOptions {

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
	private $tab_slug = 'twitter';
	private $tab_title = 'Twitter';

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'ff-twitter-settings-page';
		$this->config = array(
			'settings_key'  => 'ff-twitter-settings',			//e.g. 'mailchimp_settings
			'page_title'  	=> 'Twitter',							//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Twitter',						//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'ff-twitter-settings'			//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// get current settings, default to blank array
		$opt 			= get_option( $this->config['settings_key'] );
		$this->options 	= $opt ? $opt : array();


	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			$this->config['page_title'],		// Page Title
			$this->config['menu_title'],		// Menu Title
			'manage_options',					// Capability
			$this->config['menu_slug'],			// Menu Slug
			array( $this, 'create_admin_page' )	// Callback function
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		?>
		<div class="wrap">
			<h2><?php echo $this->config['page_title']; ?></h2>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'ff-twitter-group' );	// Option group
				do_settings_sections( $this->settings_page );	// Page
				submit_button();
			?>
			</form>
		</div>
		<?php
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
			'ff-twitter-group2', 							// ID
			'Twitter Settings', 							// Title
			array($this, 'twitter_callback2'), 				// Callback
			$this->settings_page 							// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'ff-twitter-group2', 							// Option group
			$this->settings_key, 							// Option name
			array( $this, 'sanitize' ) 						// Sanitize
		);

		add_settings_field(
			'twitter_handle', 			                   	// ID
			'Twitter Handle', 								// Title
			array( $this, 'text_field_callback' ), 			// Callback
			$this->settings_page, 				            // Page
			'ff-twitter-group2',							// Section ID
			array( 'name' => 'twitter_handle')
		);

		add_settings_field(
			'cache_time', 			                   		// ID
			'Cache Time (sec)', 							// Title
			array( $this, 'text_field_callback' ), 			// Callback
			$this->settings_page, 				            // Page
			'ff-twitter-group2',							// Section ID
			array( 'name' => 'cache_time', 'default' => (15*60) )
		);

		/* ADD SECTIONS */
		add_settings_section(
			'ff-twitter-group', 							// ID
			'Twitter API OAuth Settings', 					// Title
			array($this, 'twitter_callback'), 				// Callback
			$this->settings_page 							// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'ff-twitter-group', 							// Option group
			$this->settings_key, 							// Option name
			array( $this, 'sanitize' ) 						// Sanitize
		);

		add_settings_field(
			'consumer_key', 			                   	// ID
			'Consumer Key', 								// Title
			array( $this, 'text_field_callback' ), 			// Callback
			$this->settings_page, 				            // Page
			'ff-twitter-group',								// Section ID
			array( 'name' => 'consumer_key')
		);

		add_settings_field(
			'consumer_secret', 			                   	// ID
			'Consumer Secret', 								// Title
			array( $this, 'text_field_callback' ), 	// Callback
			$this->settings_page, 				            // Page
			'ff-twitter-group',								// Section ID
			array( 'name' => 'consumer_secret')
		);

		add_settings_field(
			'access_token', 			                   	// ID
			'Access Token', 								// Title
			array( $this, 'text_field_callback' ), 		// Callback
			$this->settings_page, 				            // Page
			'ff-twitter-group',								// Section ID
			array( 'name' => 'access_token')
		);

		add_settings_field(
			'access_token_secret', 			                // ID
			'Access Token Secret', 							// Title
			array( $this, 'text_field_callback' ), 		// Callback
			$this->settings_page, 				            // Page
			'ff-twitter-group',								// Section ID
			array( 'name' => 'access_token_secret')
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

		$fields = ['consumer_key', 'consumer_secret', 'access_token', 'access_token_secret', 'twitter_handle', 'cache_time'];

		foreach ($fields as $field ) {
			$new_input[$field] = wp_kses_post( $input[$field] );
		}

		return $new_input;
	}


	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT FIELD DISPLAY CALLBACKS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Settings section callback
	 */
	public function twitter_callback() {
		echo '
			<p>Follow the below steps to generate access tokens for an existing Twitter app:</p>
			<ol>
				<li>Login to your Twitter account on developer.twitter.com</li>
				<li>Navigate to the Twitter app dashboard and open the Twitter app for which you would like to generate access tokens.</li>
				<li>Navigate to the "Keys and Tokens" page.</li>
				<li>Select "Create" under the "Access token & access token secret" section.</li>
			</ol>
		';
	}

	/**
	 * Settings section callback
	 */
	public function twitter_callback2() {
		echo '
			The calls will be cached for a period of time to speed things up.
		';
	}

	/**
	 * Callback to auth_mode field
	 */
	public function text_field_callback($opt=array()) {
		$name = $opt['name'] ?? '';
		$default = $opt['default'] ?? '';
		$value = $this->options[$name] ?? $default;

		printf('<input class="large-text" type="text" id="%1$s[%2$s]" name="%1$s[%2$s]" value="%3$s" required>',
				$this->settings_key,
				$name,
				$value );
	}

}