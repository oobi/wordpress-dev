<?php


class Buzz_Addon_Sendgrid_Options_Page {

	/**
	 * key which stores settings in the database (e.g. sendgrid_settings)
	 */
	protected $settings_key;

	/**
	 * Constructor configuration (overridden by child classes)
	 */
	protected $config;

	/**
	 * Holds whether the api key is valid or not
	 */
	public $api_key_valid;

	/**
	 * Slug for settings page
	 */
	private $settings_page;

	private $options;
	
	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->api_key_valid = NULL;
		$this->settings_page = 'sendgrid-settings-page';
		$this->config = array(
			'service_name'	=> 'Sendgrid',					//e.g. 'Sendgrid
			'settings_key'  => 'sendgrid_settings',			//e.g. 'Sendgrid_settings
			'page_title'  	=> 'Sendgrid',					//e.g. 'Sendgrid Settings
			'menu_title'  	=> 'Sendgrid',					//e.g. 'Sendgrid Settings
			'menu_slug'  	=> 'sendgrid_settings'			//e.g. 'Sendgrid_settings
		);

		$this->settings_key = $this->config['settings_key'];
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
	// OVERRIDE METHOD
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name
		?>
		<div class="wrap">
			<h2>Sendgrid</h2>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'buzz_cm_core_group' );				// Option group
				settings_fields( 'buzz_cm_email_group' );				// Option group
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
	// OVERRIDE METHOD
	public function page_init() {

		/* ADD SECTIONS */
		add_settings_section(
			'buzz_cm_core_group', 		// ID
			'Core Settings', 			// Title
			array(), 					// Callback
			$this->settings_page 		// Page
		);

		add_settings_section(
			'buzz_cm_email_group', 				// ID
			'Email Defaults', 			// Title
			array(), 					// Callback
			$this->settings_page 		// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'buzz_cm_core_group', 				// Option group
			$this->settings_key, 		// Option name
			array( $this, 'sanitize' ) 	// Sanitize
		);

		register_setting(
			'buzz_cm_email_group', 				// Option group
			$this->settings_key, 		// Option name
			array( $this, 'sanitize' )	// Sanitize
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'sendgrid_api_key', 				// ID
			'API Key', 						// Title
			array( $this, 'api_key_callback' ), // Callback
			$this->settings_page, 			// Page
			'buzz_cm_core_group'					// Section ID
		);

		add_settings_field(
			'sendgrid_api_key_valid', 			// ID
			'', 								// Title - do not show for hidden fields
			array( $this, 'api_key_valid_callback' ), // Callback
			$this->settings_page, 				// Page
			'buzz_cm_core_group'				// Section ID
		);

		add_settings_field(
			'sendgrid_from_name', 		// ID
			'Default "From" Name', 		// Title
			array( $this, 'from_name_callback' ), // Callback
			$this->settings_page, 		// Page
			'buzz_cm_email_group' 	// Section ID
		);

		add_settings_field(
			'sendgrid_from_email', 	// ID
			'Default "From" Email', 	// Title
			array( $this, 'from_email_callback' ), // Callback
			$this->settings_page, 		// Page
			'buzz_cm_email_group' 	// Section ID
		);

		add_settings_field(
			'sendgrid_reply_to', 		// ID
			'Default "Reply To" Email', // Title
			array( $this, 'reply_email_callback' ), // Callback
			$this->settings_page, 		// Page
			'buzz_cm_email_group' 	// Section
		);

		add_settings_field(
			'sendgrid_subject', 		// ID
			'Default Subject', // Title
			array( $this, 'subject_callback' ), // Callback
			$this->settings_page, 		// Page
			'buzz_cm_email_group' 	// Section
		);

		/*
		add_settings_field(
			'sendgrid_proxy', 		// ID
			'Proxy URL', // Title
			array( $this, 'proxy_callback' ), // Callback
			$this->settings_page, 		// Page
			'buzz_cm_email_group' 	// Section
		);
		*/
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if( isset( $input['sendgrid_api_key'] ) ) {
			$new_input['sendgrid_api_key'] = sanitize_text_field( $input['sendgrid_api_key'] );

			// validate the new key
			$ffm = new Sendgrid_Mailer_API();
			$result = $ffm->api_key_is_valid( $new_input['sendgrid_api_key'] );
			$valid = $result && !isset( $result['status'] );

			// set the validator input to the new value
			$new_input['sendgrid_api_key_valid'] = $valid ? 'true' : 'false';
		}

		if( isset( $input['sendgrid_from_name'] ) ) {
			$new_input['sendgrid_from_name'] = sanitize_text_field( $input['sendgrid_from_name'] );
		}

		if( isset( $input['sendgrid_from_email'] ) ) {
			if( sanitize_email( $input['sendgrid_from_email'] ) ) {
				$new_input['sendgrid_from_email'] = sanitize_email( $input['sendgrid_from_email'] );
			} else {
				add_settings_error( 'sendgrid_from_email_error',  esc_attr( 'invalid_email' ), "Email address in the 'Default From Email' field is invalid.", 'error' );
			}
		}

		if( isset( $input['sendgrid_reply_email'] ) ) {
			if( sanitize_email( $input['sendgrid_reply_email'] ) ) {
				$new_input['sendgrid_reply_email'] = sanitize_email( $input['sendgrid_reply_email'] );
			} else {
				add_settings_error( 'sendgrid_reply_email_error',  esc_attr( 'invalid_email' ), "Email address in the 'Default Reply To Email' field is invalid.", 'error' );
			}

		}

		if( isset( $input['sendgrid_subject'] ) && ! empty( $input['sendgrid_subject'] )) {
			$new_input['sendgrid_subject'] = esc_attr( strip_tags( $input['sendgrid_subject'] ) );
		} else {
			$new_input['sendgrid_subject'] = Buzz_Newsletter_Sendgrid_Campaign_Page::$default_subject;
		}

		/*
		if( isset( $input['sendgrid_proxy'] ) && ! empty( $input['sendgrid_proxy'] )) {
			$new_input['sendgrid_proxy'] = sanitize_text_field( $input['sendgrid_proxy'] );
		}
		*/

		return $new_input;
	}

	/**
	 * Callback to display Api Key field
	 */
	public function api_key_callback() {
		// check if api key is valid
		$ffm = new Sendgrid_Mailer_API();
		$result = $ffm->api_key_is_valid( $this->options['sendgrid_api_key'] );
		$this->api_key_valid = $result && !isset( $result['statusCode'] );

		// display field
		printf(
			'<div class="%s"><input type="text" class="regular-text" id="api_key" name="%s[sendgrid_api_key]" value="%s" /> <span class="dashicons %s"></span> %s</div>',
			$this->api_key_valid ? 'valid' : 'invalid',
			$this->settings_key,
			isset( $this->options['sendgrid_api_key'] ) ? esc_attr( $this->options['sendgrid_api_key'] ) : '',
			$this->api_key_valid ? 'dashicons-yes' : 'dashicons-no',
			$this->api_key_valid ? 'Valid Key' : 'Invalid Key'
		);
		echo '<p class="description">This key is available in the ' . $this->config['service_name'] . ' admin interface. Plugin does not work without it.</p>';
	}

	/**
	 * Callback to display Api Key Validation field
	 */
	public function api_key_valid_callback() {

		// do not create field if api key has not been set
		printf(
			'<input type="hidden" id="api_key_valid" name="%s[sendgrid_api_key_valid]" value="%s"/>',
			$this->settings_key,
			$this->api_key_valid ? 'true' : 'false'
		);

	}

	/**
	 * Callback to display From Name field
	 */
	public function from_name_callback() {
		printf(
			'<input type="text" class="regular-text" id="from_name" name="%s[sendgrid_from_name]" value="%s" />',
			$this->settings_key,
			isset( $this->options['sendgrid_from_name'] ) ? esc_attr( $this->options['sendgrid_from_name']) : ''
		);
		echo '<p class="description">The default name recipients will see on sent campaigns.</p>';
	}

	/**
	 * Callback to display From Email field
	 */
	public function from_email_callback() {
		printf(
			'<input type="text" class="regular-text" id="from_email" name="%s[sendgrid_from_email]" value="%s" />',
			$this->settings_key,
			isset( $this->options['sendgrid_from_email'] ) ? esc_attr( $this->options['sendgrid_from_email']) : ''
		);
		echo '<p class="description">The default email address recipients will see on sent campaigns.</p>';
	}

	/**
	 * Callback to display Reply Email field
	 */
	public function reply_email_callback() {
		printf(
			'<input type="text" class="regular-text" id="reply_email" name="%s[sendgrid_reply_email]" value="%s" />',
			$this->settings_key,
			isset( $this->options['sendgrid_reply_email'] ) ? esc_attr( $this->options['sendgrid_reply_email']) : ''
		);
		echo '<p class="description">The default email address recipients will be able to reply to. Can be different to the "From" address.</p>';
	}

	/**
	 * Callback to display Subject field
	 */
	public function subject_callback() {
		printf(
			'<input type="text" class="large-text" id="subject" name="%s[sendgrid_subject]" value="%s" />',
			$this->settings_key,
			isset( $this->options['sendgrid_subject'] ) ? esc_attr( $this->options['sendgrid_subject']) : ''
		);
		echo '<p class="description">The default subject line for a campaign. Use the tags below to insert dynamic content:</p>';

		echo '<p class="subject-tags">';
		foreach( Buzz_Newsletter_Sendgrid_Campaign_Page::$subject_tokens as $token) {
			printf('<a class="tag button-secondary" data-target="#subject" data-value="%1$s">%1$s</a>', $token);
		}
		echo '</p>';
	}

	/**
	 * Callback to display Subject field
	 */
	public function proxy_callback() {
		printf(
			'<input type="text" class="large-text" id="proxy" name="%s[sendgrid_proxy]" value="%s" />',
			$this->settings_key,
			isset( $this->options['sendgrid_proxy'] ) ? esc_attr( $this->options['sendgrid_proxy']) : ''
		);
		echo '<p class="description">Sendgrid has been having trouble importing campaigns from certain URLs where the SSL cyphers are incompatible. This is a known issue. For the time being, you can specify a proxy URL which is compatible.</p>';
	}

}
