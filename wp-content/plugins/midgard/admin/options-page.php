<?php

namespace FF\Midgard;

class Midgard_Options_Page {

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
	 * STATIC tab properties
	 */
	protected static $tab_url_base 		= '?page=midgard_settings&tab=';
	protected static $default_tab 		= 'cache';
	public static $current_tab;

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'midgard-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard Settings',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard Settings',		//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_settings'		//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// determime active settings tab
		$tab 	 	 		= isset($_GET['tab']) ? $_GET['tab'] : self::$default_tab;
		self::$current_tab 	= strtolower($tab);

		// Actions
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );

		// tabs
		add_action('midgard_settings_tab_content', array($this, 'cache_tab_content'));

		// display CSS in <head>
		add_action( 'wp_head', 	  array( $this, 'display_custom_css' ) );
	}

	/**
	 * Static utility method for setting appropriate tab URLs
	 */
	public static function tab_url($tab_slug, $full=false) {
		$url = self::$tab_url_base . urlencode($tab_slug);

		if($full) {
			$url = admin_url('options-general.php' . $url);
		}

		return $url;
	}

	/**
	 * Static utility method for outputting a tab
	 */
	public static function tab_output($tab_slug, $tab_title) {
		$url = self::tab_url($tab_slug) ;
		$active = self::$current_tab == $tab_slug ? 'nav-tab-active' : '';

		return sprintf('<a href="%s" class="nav-tab %s">%s</a>',
							$url,
							$active,
							$tab_title);
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

		add_action( "admin_print_styles-{$page}", array($this, 'enqueue_options_styles') );
		add_action( "admin_print_scripts-{$page}", array($this, 'enqueue_options_scripts') );
	}

	/**
	 * Provide hook for sub-plugins to enqueue styles
	 */
	public function enqueue_options_styles() {
		do_action('midgard_options_styles-' . self::$current_tab);
	}

	/**
	 * Provide hook for sub-plugins to enqueue scripts
	 */
	public function enqueue_options_scripts() {
		do_action('midgard_options_scripts-' . self::$current_tab);
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

			<h2 class="nav-tab-wrapper">
				<?php
					echo self::tab_output('cache', 'Cache');
					do_action('midgard_settings_tab');
				?>
			</h2>

			<form method="post" action="options.php">
			<?php
				do_action('midgard_settings_tab_content');
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render output of cache tab fields
	 */
	public function cache_tab_content() {
		if( self::$current_tab == self::$default_tab) {
			settings_fields( 'midgard_cache_group' );		// Option group
			do_settings_sections( $this->settings_page );	// Page
			submit_button();
		}
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {

		/* ADD SECTIONS */
		add_settings_section(
			'midgard_cache_group', 							// ID
			'Cache Settings', 								// Title
			array( $this, 'cache_group_callback' ), 		// Callback
			$this->settings_page 							// Page
		);

		add_settings_section(
			'midgard_filesystem_group',						// ID
			'File System',		 					// Title
			array( $this, 'filesystem_group_callback' ), // Callback
			$this->settings_page 					// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_cache_group', 					// Option group
			$this->settings_key, 					// Option name
			array( $this, 'sanitize' ) 				// Sanitize
		);

		/* ADD SETTINGS FIELDS */

		add_settings_field(
			'cache_active', 			                    // ID
			'Cache Active', 							    // Title
			array( $this, 'cache_active_callback' ), 	    // Callback
			$this->settings_page, 				            // Page
			'midgard_cache_group'					            // Section ID
		);

		add_settings_field(
			'default_cache_time', 		                    // ID
			'Default cache time (seconds)',				    // Title
			array( $this, 'default_cache_time_callback' ),  // Callback
			$this->settings_page, 				            // Page
			'midgard_cache_group'					            // Section ID
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if( isset( $input['cache_active'] ) ) {
			$new_input['cache_active'] = 1;
		} else {
			// unset on inactive
		}

		if( isset( $input['default_cache_time'] ) ) {
			$new_input['default_cache_time'] = intval( $input['default_cache_time'] );
		} else {
			$new_input['default_cache_time'] = 3600;
		}

		return $new_input;
	}


	/**
	 * Callback for cache group
	 */
	 public function cache_group_callback() {
		 echo '<p>';
		 _e('Disabling cache will cause all feeds to retrieve live data from the source for every request. This is useful for development but should be disabled for production.', 'midgard');
		 echo '</p>';
	 }

	 /**
	  * Callback for filesystem group
	  */
	public function filesystem_group_callback() {
		echo '<p>';
		if( file_exists( MIDGARD_CACHE_DIR )) {
			_e('The midgard cache folder for this site was successfully created at', 'midgard');
		} else {
			_e('The midgard cache folder for this site could not be created.', 'midgard');
			echo '<br>';
			_e('The expected path is below. Please check file permissions.', 'midgard');
		}
		echo '<br><code>' . MIDGARD_CACHE_DIR . '</code>';
		echo '</p>';
	}


	/**
	 * Callback to API Key field
	 */
	public function api_key_callback() {

		printf(
			'<input type="text" id="api_key" name="%s[api_key]" value="%s">',
			$this->settings_key,
			isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key']) : ''
		);
		echo '<p class="description">API Key Description</p>';

	}

	/**
	 * Callback to cache active field
	 */
	public function cache_active_callback() {

		printf(
			'<input type="checkbox" id="cache_active" name="%s[cache_active]" value="1" %s>',
			$this->settings_key,
			isset( $this->options['cache_active'] ) ? 'checked' : ''
		);
		echo '<p class="description">Turn caching on or off</p>';

	}

	/**
	 * Callback to default cache time field
	 */
	public function default_cache_time_callback() {

		printf(
			'<input type="text" id="default_cache_time" name="%s[default_cache_time]" value="%s">',
			$this->settings_key,
			isset( $this->options['default_cache_time'] ) ? esc_attr( $this->options['default_cache_time']) : '3600'
		);
		echo '<p class="description">Default number of seconds to hold an item in the cache. <br>You may override this value for each feed.</p>';

	}



}

// init the options page
if( is_admin() ) {
	$options_page = new Midgard_Options_Page();
}


