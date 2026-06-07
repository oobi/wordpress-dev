<?php

namespace FF\Midgard;

class Midgard_Auth_Remote_Options_Page {

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
		// default JWT URL Key
		$default_key = 'midgard-auth-token';

		// Set up variables
		$this->settings_page = 'midgard-auth-remote-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_auth_remote_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard Auth Remote Settings',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard Auth Remote',				//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_auth_remote_settings'		//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// set options
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name

		// set default option for JWT key
		if(!isset($this->options['jwt_key'])) {
			$this->options['jwt_key'] = $default_key;
		}

		// Actions
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );

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
	public function create_admin_page() { ?>

		<div class="wrap">

			<h2><?php echo $this->config['page_title']; ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'midgard_remote_group' );			// Option group
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
			'midgard_remote_group', 					// ID
			'Remote Settings', 							// Title
			array(), 									// Callback
			$this->settings_page 						// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_remote_group', 					// Option group
			$this->settings_key, 						// Option name
			array( $this, 'sanitize' ) 					// Sanitize
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'validate_url', 			                // ID
			'Token Validation Endpoint URI',			// Title
			array( $this, 'validate_url_callback' ), 	// Callback
			$this->settings_page, 				        // Page
			'midgard_remote_group'					    // Section ID
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'jwt_key'		, 			                    // ID
			'JWT URL Key', 							  		// Title
			array( $this, 'jwt_key_callback' ), 	 	    // Callback
			$this->settings_page, 				            // Page
			'midgard_remote_group'				            // Section ID
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'log_in_as', 			                    // ID
			'User', 									// Title
			array( $this, 'log_in_as_callback' ), 	    // Callback
			$this->settings_page, 				        // Page
			'midgard_remote_group'					    // Section ID
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'prevent_admin', 		                    // ID
			'Prevent access to admin', 					// Title
			array( $this, 'prevent_admin_callback' ), 	// Callback
			$this->settings_page, 				        // Page
			'midgard_remote_group'					    // Section ID
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {

		$new_input = array();

		if( isset( $input['log_in_as'] ) ) {
			$new_input['log_in_as'] = wp_kses_post( $input['log_in_as'] );
		}

		if( isset( $input['validate_url'] ) ) {
			$new_input['validate_url'] = esc_url( $input['validate_url'] );
		}
		
		if( isset( $input['prevent_admin'] ) ) {
			$new_input['prevent_admin'] = 1;
		} else {
			$new_input['prevent_admin'] = 0;
		}

		if( isset( $input['jwt_key'] ) ) {
			$value = wp_kses_post( $input['jwt_key'] );
			if( empty($value) ) {
				$value = 'midgard_auth_jwt';
			}
			$new_input['jwt_key'] = wp_kses_post( $input['jwt_key'] );
		}

		return $new_input;

	}

	/**
	 * Callback to Log In As field
	 */
	public function log_in_as_callback() {

		printf(
			'<input type="text" id="log_in_as" name="%s[log_in_as]" value="%s">',
			$this->settings_key,
			isset( $this->options['log_in_as'] ) ? esc_attr( $this->options['log_in_as']) : ''
		);
		echo '<p class="description">';
		echo __('This user will be logged in to this site if the supplied JWT token is accepted by the remote endpoint.', 'midgard-auth-remote');
		echo '<br>';
		echo __('The user should have the minimum permissions required to view the content you are requesting', 'midgard-auth-remote');
		echo '</p>';

	}

	/**
	 * Callback to Validate URL field
	 */
	public function validate_url_callback() {

		printf(
			'<input type="text" class="large-text" id="validate_url" name="%s[validate_url]" value="%s">',
			$this->settings_key,
			isset( $this->options['validate_url'] ) ? esc_attr( $this->options['validate_url']) : ''
		);

		echo '<p class="description">';
		_e('The REST entpoint defined by the <a href="https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/" target="_blank">JWT Authentication plugin</a> on the remote site. This must be the site that issued the JWT.', 'midgard-auth-remote');
		echo '</p>';

	}

	/**
	 * Callback to Hide Admin Bar
	 */
	public function prevent_admin_callback() {
		$checked = isset( $this->options['prevent_admin'] ) ? $this->options['prevent_admin'] : 0;

		printf(
			'<input type="checkbox" id="prevent_admin" name="%s[prevent_admin]" value="1" %s>',
			$this->settings_key,
			checked( $checked, 1, false)
		);
		echo '<p class="description">';
		_e('Hide the admin bar for the logged in user and prevent access to dashboard', 'midgard-auth-remote');
		echo '</p>';

	}

	/**
	 * Callback to JWT Key field
	 */
	public function jwt_key_callback() {

		printf(
			'<input required type="text" id="jwt_key" name="%s[jwt_key]" value="%s">',
			$this->settings_key,
			isset( $this->options['jwt_key'] ) ? esc_attr( $this->options['jwt_key']) : ''
		);
		echo '<p class="description">';
		_e('This defines the URL key you will use to retrieve the token (JWT) value.', 'midgard-auth-remote');
		
		echo '<br>' . __('To supply the token for validation:', 'midgard-auth-remote');
		echo '<br><code>'  . get_site_url() . '/path/to/some-post<strong>?' . $this->options['jwt_key'] . '=XXXX</strong></code>';

		echo '</p>';
	}

}


