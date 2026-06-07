<?php

class FF_OneSignal_Notifications_Options_Page {

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
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'ff-onesignal-settings-page';
		$this->config = array(
			'settings_key'  => 'ff_onesignal_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'Firefly OneSignal Notification Settings',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Firefly OneSignal',			//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'ff_onesignal_settings'		//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// Actions
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		$page = add_options_page(
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
		// Set class property
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name
		?>
		<div class="wrap">

			<h2><?php echo $this->config['page_title']; ?></h2>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'ff_onesignal_notification_group' );		// Option group
					do_settings_sections( $this->settings_page );	// Page
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {

		/* ADD SECTIONS */
		add_settings_section(
			'ff_onesignal_notification_group', 				// ID
			'Category Settings', 							// Title
			false, 											// Callback
			$this->settings_page 							// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'ff_onesignal_notification_group',	// Option group
			$this->settings_key, 					// Option name
			array( $this, 'sanitize' ) 				// Sanitize
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'categories_enabled', 			                // ID
			'Add categories to OneSignal Push Notifications',// Title
			array( $this, 'categories_enabled_callback' ), 	    // Callback
			$this->settings_page, 				            // Page
			'ff_onesignal_notification_group'				// Section ID
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if( isset( $input['categories_enabled'] ) ) {
			$new_input['categories_enabled'] = 1;
		} else {
			// unset on inactive
		}

		return $new_input;
	}


	/**
	 * Callback to cache active field
	 */
	public function categories_enabled_callback() {

		printf(
			'<input type="checkbox" id="categories_enabled" name="%s[categories_enabled]" value="1" %s>',
			$this->settings_key,
			isset( $this->options['categories_enabled'] ) ? 'checked' : ''
		);
		echo '<p class="description">Enable to pass post categories with the push notification on send</p>';

	}

}

// init the options page
if( is_admin() ) {
	$options_page = new FF_OneSignal_Notifications_Options_Page();
}


