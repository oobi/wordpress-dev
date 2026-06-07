<?php

namespace FF\Midgard;

use FF\Midgard\Midgard_Options_Page;

class Midgard_Auth_JWT_Options_Page {

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
	 * Default token expiry
	 */
	private $jwt_auth_expire_default;

	/**
	 * Tab settings
	 */
	private $tab_slug = 'auth-jwt';
	private $tab_title = 'Authentication';

	/**
	 * Start up
	 */
	public function __construct() {
		// default JWT URL Key
		$default_key = 'midgard-auth-jwt';

		// use the constant as a default expiry if defined
		$this->jwt_auth_expire_default  = Midgard_Auth_JWT::get_default_expiry();

		// Set up variables
		$this->settings_page = 'midgard-auth-jwt-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_auth_jwt_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard Auth JWT Settings',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard Auth JWT',				//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_auth_jwt_settings'		//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// get current settings
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name
		if(!isset($this->options['jwt_key'])) {
			$this->options['jwt_key'] = $default_key;
		}

		// Actions
		add_action('midgard_settings_tab', array($this, 'settings_tab'), 5);
		add_action('midgard_settings_tab_content', array($this, 'settings_tab_content'));
		add_action('admin_init', array( $this, 'page_init' ) );

		// display CSS in <head>
		add_action( 'wp_head', 	  array( $this, 'display_custom_css' ) );
	}


	/**
	 * Render form output if this is the current tab
	 */
	public function settings_tab_content() {
		if( Midgard_Options_Page::$current_tab == $this->tab_slug) {
			settings_fields( 'midgard_jwt_group' );		// Option group
			settings_fields( 'midgard_display_group' );		// Option group
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
		// Set class property
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name
		?>
		<div class="wrap">

			<h2><?php echo $this->config['page_title']; ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'midgard_jwt_group' );			// Option group
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
			'midgard_jwt_group', 							// ID
			'JWT Settings', 								// Title
			array(), 										// Callback
			$this->settings_page 							// Page
		);

		add_settings_section(
			'midgard_display_group', 						// ID
			'Display Settings', 							// Title
			array(), 										// Callback
			$this->settings_page 							// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_jwt_group', 					// Option group
			$this->settings_key, 					// Option name
			array( $this, 'sanitize' ) 				// Sanitize
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_display_group', 				// Option group
			$this->settings_key, 					// Option name
			array( $this, 'sanitize' ) 				// Sanitize
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'jwt_key'		, 			                    // ID
			'JWT URL Key', 							  		// Title
			array( $this, 'jwt_key_callback' ), 	 	    // Callback
			$this->settings_page, 				            // Page
			'midgard_jwt_group'					            // Section ID
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'token_expiry', 			                    // ID
			'Token Expiry', 							    // Title
			array( $this, 'token_expiry_callback' ), 	    // Callback
			$this->settings_page, 				            // Page
			'midgard_jwt_group'					            // Section ID
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'custom_css', 			                  		// ID
			'Custom Styles',						   	 	// Title
			array( $this, 'custom_css_callback' ), 	   	 	// Callback
			$this->settings_page, 				            // Page
			'midgard_display_group'					        // Section ID
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'custom_html', 			                  		// ID
			'Custom Markup', 						  	 	// Title
			array( $this, 'custom_html_callback' ), 	   	// Callback
			$this->settings_page, 				            // Page
			'midgard_display_group'					        // Section ID
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {

		$new_input = array();

		if( isset( $input['token_expiry'] ) ) {
			$new_input['token_expiry'] = wp_kses_post( $input['token_expiry'] );
		}

		if( isset( $input['custom_css'] ) ) {
			$new_input['custom_css'] = wp_kses_post( $input['custom_css'] );
		}

		if( isset( $input['custom_html'] ) ) {
			// $new_input['custom_html'] = wp_kses_post( $input['custom_html'] );
			$new_input['custom_html'] = wp_kses_post( esc_html( $input['custom_html'] ) );
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
	 * Callback to JWT Key field
	 */
	public function jwt_key_callback() {

		printf(
			'<input required type="text" id="jwt_key" name="%s[jwt_key]" value="%s">',
			$this->settings_key,
			isset( $this->options['jwt_key'] ) ? esc_attr( $this->options['jwt_key']) : ''
		);
		echo '<p class="description">';
		_e('This defines the URL key you will use to retrieve the token (JWT) value.', 'midgard-auth-jwt');

		echo '<br>' . __('To generate a JWT:', 'midgard-auth-jwt') . '<code>'  . get_site_url() . '?' . $this->options['jwt_key'] . '</code>';
		echo '<br>' . __('Return format:', 'midgard-auth-jwt') . '<code>'  . get_site_url() . '?' . $this->options['jwt_key'] . '=XXX</code>';

		echo '</p>';
	}

	/**
	 * Callback to Token Expiry field
	 */
	public function token_expiry_callback() {

		printf(
			'<input type="number" id="token_expiry" name="%s[token_expiry]" value="%s"> seconds',
			$this->settings_key,
			isset( $this->options['token_expiry'] ) ? esc_attr( $this->options['token_expiry']) : $this->jwt_auth_expire_default
		);
		echo '<p class="description">How long before forcing the user to re-authenticate.</p>';

	}

	/**
	 * Callback to Custom CSS field
	 */
	public function custom_css_callback() {

		printf(
			'<textarea id="custom_css" name="%s[custom_css]" cols="100" rows="10">%s</textarea>',
			$this->settings_key,
			isset( $this->options['custom_css'] ) ? esc_attr( $this->options['custom_css']) : ''
		);
		echo '<p class="description">(Advanced) Custom CSS to be injected into the result page. Leave blank to use default styles.</p>';
		echo '<p class="description">This option can be used in conjunction with the <strong>Custom Markup</strong> option, or by itself to override the styles of the default markup.</p>';

	}

	/**
	 * Callback to Custom HTML field
	 */
	public function custom_html_callback() {

		printf(
			'<textarea id="custom_html" name="%s[custom_html]" cols="100" rows="10">%s</textarea>',
			$this->settings_key,
			isset( $this->options['custom_html'] ) ? esc_attr( $this->options['custom_html']) : ''
		);
		echo '<p class="description">(Advanced) Custom HTML to be injected into the result page. Leave blank to use default markup.</p>';
		echo '<p class="description">Use <code>%1$s</code> to insert the current user\'s username.</p>';
		echo '<p class="description">Use <code>%2$s</code> to insert the custom styles. Remember to wrap in <code>&lt;style&gt;</code> tags!</p>';

	}

}

// init the options page
if( is_admin() ) {
	$options_page = new Midgard_Auth_JWT_Options_Page();
}


