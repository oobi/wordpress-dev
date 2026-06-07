<?php


class Buzz_Addon_Email_View_Options_Page {

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
	 * Valid API key
	 */
	private $api_key_valid;

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->api_key_valid = NULL;
		$this->settings_page = 'email-view-page';
		$this->config = array(
			'service_name'	=> 'Email View',			//e.g. 'MailChimp
			'settings_key'  => 'email_view_settings',	//e.g. 'mailchimp_settings
			'page_title'  	=> 'Email View Settings',	//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Email View Settings',	//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'email_view_settings'	//e.g. 'mailchimp_settings
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
				settings_fields( 'unsubscribe_group' );			// Option group
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
			'unsubscribe_group', 		// ID
			'Unsubscribe Settings', 	// Title
			array( $this, 'unsubscribe_group_callback'), // Callback
			$this->settings_page 		// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'unsubscribe_group', 		// Option group
			$this->settings_key, 		// Option name
			array( $this, 'sanitize' ) 	// Sanitize
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'unsubscribe_link', 			// ID
			'Unsubscribe Link HTML', 		// Title
			array( $this, 'unsubscribe_link_callback' ), // Callback
			$this->settings_page, 			// Page
			'unsubscribe_group'				// Section ID
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {

		$new_input = array();
		if( isset( $input['unsubscribe_link'] ) ) {

			$allowed_tags = array(
				'a' => array(
					'href' => array(),
					'title' => array()
				),
				'br' => array(),
				'unsubscribe' => array()
			);

			$kses_html = wp_kses( $input['unsubscribe_link'], $allowed_tags );
			$new_input['unsubscribe_link'] = $kses_html;

		}

		return $new_input;
	}

	/**
	 * Callback to display Unsubscribe Group
	 */
	public function unsubscribe_group_callback() {
		echo '<p class="description">Under the <b>Spam Act of 2003</b>, unsubscribe links are <b>mandatory</b> on all commercial electronic
			messaging. Please set an unsubscribe link in the field below.<br>For more information, <a href="http://www.acma.gov.au/Industry/Marketers/Anti-Spam/" target="_blank">visit the ACMA website</a>.</p>';
	}

	/**
	 * Callback to display Unsubscribe Link field
	 */
	public function unsubscribe_link_callback() {
		$samples = array(
			'Email Marketer' 	=> htmlentities('<a href="%%unsubscribe%%">Unsubscribe</a>'),
			'MailChimp' 		=> htmlentities('<a href="*|UNSUB|*">Unsubscribe</a>'),
			'Campaign Monitor' 	=> htmlentities('<unsubscribe>Unsubscribe</unsubscribe>'),
			'Civi CRM' 			=> htmlentities('Sent by:<br/>{domain.address}<br/><br/>Unsubscribe: {action.unsubscribeUrl}'),
			'SendGrid' 			=> htmlentities('<a href="{{{unsubscribe}}}">Unsubscribe</a>')
		);

		printf(
			'<textarea cols="80" rows="5" id="unsubscribe_link" name="%s[unsubscribe_link]">%s</textarea>',
			$this->settings_key,
			isset( $this->options['unsubscribe_link'] ) ? esc_attr( $this->options['unsubscribe_link']) : ''
		);
		echo '<p class="description">Insert HTML for the unsubscribe link. Eg:</p>';
		echo '<table>';

		foreach($samples as $key => $sample) {
			printf('<tr><td>%s</td><td><code>%s</code></td></tr>', $key, $sample);
		}

		echo '</table>';
	}


}

// init the options page
if( is_admin() ) {
	$options_page = new Buzz_Addon_Email_View_Options_Page();
}