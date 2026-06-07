<?php


class Buzz_V1_Migration_Options_Page {

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
		$this->api_key_valid = NULL;
		$this->settings_page = 'email-view-page';
		$this->config = array(
			'service_name'	=> 'Newsletter Migration Tool',	//e.g. 'MailChimp
			'settings_key'  => 'buzz_v1_migration',			//e.g. 'mailchimp_settings
			'page_title'  	=> 'Newsletter Migration Tool',	//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Newsletter Migration Tool',	//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'newsletter_migration_tool'	//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// Actions
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_post_convert_newsletters', array( $this, 'convert_newsletters' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_menu_page(
			$this->config['page_title'],		// Page Title
			$this->config['menu_title'],		// Menu Title
			'manage_options',					// Capability
			$this->config['menu_slug'],			// Menu Slug
			array($this, 'create_admin_page')	// function to run
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

			<?php
				include('options-page-process.php');
				include('options-page-content.php');
			?>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {

	}

	/**
	 * Do the conversion
	 */
	public function convert_newsletters() {
		var_dump('I am here');
		var_dump($_POST);
	}

	///////////////////////////////////////////////////////////////////////////
	// UTILS
	///////////////////////////////////////////////////////////////////////////

	//get term meta field
	public static function get_tax_meta($term_id, $key,$multi = false) {

		$t_id = (is_object($term_id))? $term_id->term_id: $term_id;
		$m = get_option( 'tax_meta_'.$t_id);
		if (isset($m[$key])){
			return $m[$key];
		} else {
			return '';
		}
	}

	// get term thumbnail
	public static function get_newsletter_thumb( $term_id ) {
		return get_tax_meta($term_id, 'thumbnail');
	}

}

// init the options page
if( is_admin() ) {
	$options_page = new Buzz_V1_Migration_Options_Page();
}