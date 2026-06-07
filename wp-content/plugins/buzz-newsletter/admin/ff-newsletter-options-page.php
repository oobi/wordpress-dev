<?php

class FF_Newsletter_Options_Page {

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
		$this->settings_page = 'newsletter-settings-page';
		$this->config = array(
			'settings_key'  => 'newsletter_settings',	//e.g. 'mailchimp_settings
			'page_title'  	=> 'Newsletter Settings',	//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Newsletter Settings',	//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'newsletter_settings'	//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// Actions
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );

		// display CSS in <head>
		add_action( 'wp_head', 	  array( $this, 'display_custom_css' ) );
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
			<h2><?php echo $this->config['page_title']; ?></h2>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'advanced_group' );			// Option group
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
			'advanced_group', 			// ID
			'Advanced Settings', 		// Title
			array(), 					// Callback
			$this->settings_page 		// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'advanced_group', 			// Option group
			$this->settings_key, 		// Option name
			array( $this, 'sanitize' ) 	// Sanitize
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'newsletter_custom_css', 			// ID
			'Custom CSS', 						// Title
			array( $this, 'css_callback' ), 	// Callback
			$this->settings_page, 				// Page
			'advanced_group'					// Section ID
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {

		$new_input = array();

		if( isset( $input['newsletter_custom_css'] ) ) {
			$new_input['newsletter_custom_css'] = wp_kses_post( $input['newsletter_custom_css'] );
		}

		return $new_input;

	}

	/**
	 * Callback to display Custom CSS field
	 */
	public function css_callback() {

		printf(
			'<textarea cols="80" rows="5" id="newsletter_custom_css" name="%s[newsletter_custom_css]">%s</textarea>',
			$this->settings_key,
			isset( $this->options['newsletter_custom_css'] ) ? esc_attr( $this->options['newsletter_custom_css']) : ''
		);
		echo '<p class="description">Do not wrap CSS in &lt;style&gt; tags </p>';

	}

	/**
	 * Show CSS in a <style> tag in the <head> if saved
	 */
	public function display_custom_css() {

		echo '<style type="text/css">body {background:red !important;}</style>';

/*		$newsletter_custom_css = ff_get_newsletter_option( 'newsletter_custom_css' );
		if( $newsletter_custom_css ) {
			echo '<style type="text/css">' . $newsletter_custom_css . '</style>';
		}
*/
	}

}

// init the options page
if( is_admin() ) {
	$options_page = new FF_Newsletter_Options_Page();
}


