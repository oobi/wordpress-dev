<?php
/**
 * Plugin Name:   Firefly MultiSite Global Analytics
 * Plugin URI:    https://www.fi.net.au
 * Text Domain:   multisite_global_analytics
 * Domain Path:   /languages
 * Description:   Output an analytics tracking code to ALL sites in a multisite installation
 * Author:        Firefly Interactive
 * Version:       1.2
 * Licence:       GPLv3+
 * Author URI:    http://www.fi.net.au
 * Last Change:   2016-08-19
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// set up plugin
add_action( 'plugins_loaded', array( Multisite_Global_Analytics::get_instance(), 'plugin_setup' ) );

class Multisite_Global_Analytics {

	/**
	 * key which stores settings in the database
	 */
	protected $settings_key;

	/**
	 * Constructor configuration (overridden by child classes)
	 */
	protected $config;

	/**
	 * Options
	 */
	protected $options;

	/**
	 * Slug for settings page
	 */
	private $settings_page;

	/**
	 * Check if plugin is network activated
	 */
	private $is_network_active;

	/**
	 * Prefix for network activated settings/options
	 */
	private $prefix;

	/**
	 * Constructor
	 *
	 * @since  0.0.1
	 * @return Multisite_Global_Analytics
	 */
	public function __construct() {

		// if method is undefined it is network activated
		$this->is_network_active 	= $this->network_active( 'ff-multisite-global-analytics/multisite-global-analytics.php' );
		$this->prefix 				= $this->is_network_active ? 'network_' : '';

		// Set up variables
		$this->settings_page = 'global-analytics-settings-page';
		$this->config = array(
			'settings_key'  => 'global_analytics',					//e.g. 'mailchimp_settings
			'page_title'  	=> 'Firefly Global Analytics',			//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Firefly Global Analytics',			//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'global_analytics_settings'			//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// Set class property and apply defaults if not set
		if( $this->is_network_active ) {
			$options = get_site_option( $this->config['settings_key'] );
		} else {
			$options = get_option( $this->config['settings_key'] );
		}

		if( !$options ) $options = array();

		$defaults = array(
			$this->prefix . 'whitelist_domains'		=> array(),
			$this->prefix . 'analytics_key' 			=> '',
			$this->prefix . 'enable_global_tracking'	=> false
		);

		$this->options = array_merge(
			$defaults,  // defaults
			$options    // actuals
		);
	}

	/**
	 * Check if plugin is network activated
	 */
	function network_active( $plugin ) {
		$network_active = false;
		if ( is_multisite() ) {
			$plugins = get_site_option( 'active_sitewide_plugins' );
			if ( isset( $plugins[$plugin] ) ) {
				$network_active = true;
			}
		}
		return $network_active;
	}

	/**
	 * Get an instance to this class
	 *
	 * @access public
	 * @since  0.0.1
	 * @return object
	 */
	public static function get_instance() {

		static $instance;

		if ( NULL === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Used for regular plugin work.
	 *
	 * @wp-hook  plugins_loaded
	 * @since    05/02/2013
	 * @return   void
	 */
	public function plugin_setup() {
		$this->add_hooks();
	}

	/**
	 * Define WordPress hooks
	 */
	public function add_hooks() {
		// public hooks
		if(! is_admin() ) {

			// only show tracking code if user is not logged in and setting is active
			$is_admin_user = is_user_logged_in() && ( current_user_can('administrator') || current_user_can('editor') );
			$is_active = $this->options[$this->prefix . 'enable_global_tracking'];

			if($is_active && !$is_admin_user) {
				add_action('wp_head', array($this, 'output_analytics_head'));
				// optionally put in footer
				//add_action('wp_footer', array($this, 'output_analytics'));
			}

		}
		// admin hooks
		else {

			// If Network Activated, create hooks to define settings page
			if( $this->is_network_active ) {
				// create the options page
				add_action( "network_admin_menu", array( $this, 'options_page_create' ) );
				add_action( 'admin_init', array( $this, 'options_page_init' ) );

				// register callback to save network options
				$key = $this->settings_key;
				add_action( "network_admin_edit_{$key}", array($this, 'save_network_options') );
			}
			// else just create the settings page
			else {
				add_action( 'admin_menu', array( $this, 'options_page_create' ) );
				add_action( 'admin_init', array( $this, 'options_page_init' ) );
			}

		}
	}

	//////////////////////////////////////////////////////////////////////
	// SAVE NETWORK OPTIONS
	//////////////////////////////////////////////////////////////////////

	public function save_network_options() {
		$new = $_POST[$this->settings_key];
		$options = $this->options;

		// set option values
		$options[$this->prefix . 'enable_global_tracking']  = isset( $new[$this->prefix . 'enable_global_tracking'] ) && $new[$this->prefix . 'enable_global_tracking'];
		$options[$this->prefix . 'whitelist_domains'] 		= isset( $new[$this->prefix . 'whitelist_domains'] ) ? explode("\n", $new[$this->prefix . 'whitelist_domains']) : array();
		$options[$this->prefix . 'analytics_key']  			= isset( $new[$this->prefix . 'analytics_key'] ) ? sanitize_text_field($new[$this->prefix . 'analytics_key']) : '';
		$options[$this->prefix . 'gtm_key']  				= isset( $new[$this->prefix . 'gtm_key'] ) ? sanitize_text_field($new[$this->prefix . 'gtm_key']) : '';

		// save
		update_site_option( $this->settings_key, $options );
	}

	//////////////////////////////////////////////////////////////////////
	// OPTIONS PAGE
	//////////////////////////////////////////////////////////////////////


	/**
	 * Add an options page to the "settings" menu
	 */
	public function options_page_create() {

		// If Network Activated, make a menu page
		if( $this->is_network_active ) {
			add_menu_page(
				$this->config['page_title'],		// Page Title
				$this->config['menu_title'],		// Menu Title
				'manage_options',					// Capability
				$this->config['menu_slug'],			// Menu Slug
				array( $this, 'create_admin_page' )	// Callback function
			);
		} // else make an Options Page
		else {
			add_options_page(
				$this->config['page_title'],		// Page Title
				$this->config['menu_title'],		// Menu Title
				'manage_options',					// Capability
				$this->config['menu_slug'],			// Menu Slug
				array( $this, 'create_admin_page' )	// Callback function
			);
		}

	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {

		if( $this->is_network_active ) {
		 	$action = network_admin_url('edit.php')  . '?action=' . $this->settings_key;
		} else {
			$action = 'options.php';
		} ?>

		<div class="wrap">
			<h2><?php echo $this->config['page_title']; ?></h2>
			<form method="post" action="<?php echo $action; ?>">
				<?php
				settings_fields( 'analytics_group' );			// Option group
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
	public function options_page_init() {

		/* ADD SECTIONS */
		add_settings_section(
			'analytics_group', 									// ID
			'Tracking Settings', 								// Title
			null,												// Callback
			$this->settings_page 								// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'analytics_group', 									// Option group
			$this->settings_key, 								// Option name
			array( $this, 'sanitize' ) 							// Sanitize
		);

		/* REGISTER FIELDS */
		add_settings_field(
			$this->prefix . 'enable_global_tracking', 	// ID
			'Enable global tracking', 					    			// Title
			array( $this, 'callback_enable' ), 	        				// Callback
			$this->settings_page, 				            			// Page
			'analytics_group'					            			// Section ID
		);

		// only relevant if network activated
		if( $this->is_network_active ) {
			add_settings_field(
				$this->prefix . 'whitelist_domains', // ID
				'White-list domains', 						    	// Title
				array( $this, 'callback_whitelist' ), 	     		// Callback
				$this->settings_page, 				         		// Page
				'analytics_group'					           		// Section ID
			);
		}

		add_settings_field(
			$this->prefix . 'analytics_key', // ID
			'Analytics Key', 						        // Title
			array( $this, 'callback_analytics_key' ), 	    // Callback
			$this->settings_page, 				            // Page
			'analytics_group'					            // Section ID
		);

		add_settings_field(
			$this->prefix . 'gtm_key', // ID
			'Google Tag Manager Key', 						        // Title
			array( $this, 'callback_gtm_key' ), 	    // Callback
			$this->settings_page, 				            // Page
			'analytics_group'					            // Section ID
		);


	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 * @return array
	 */
	public function sanitize( $input ) {


		// this is not called in network settings
		if( $this->is_network_active ) {
			return $input;
		}
		// else sanitize normally
		else {

			$new_input = array();

			if( isset( $input['enable_global_tracking'] ) ) {
				$new_input['enable_global_tracking'] = wp_kses_post( $input['enable_global_tracking'] );
			}

			if( isset( $input['analytics_key'] ) ) {
				$new_input['analytics_key'] = sanitize_text_field( $input['analytics_key'] );
			}

			if( isset( $input['gtm_key'] ) ) {
				$new_input['gtm_key'] = sanitize_text_field( $input['gtm_key'] );
			}
var_dump( $input );
die();
			return $new_input;
		}

	}

	/**
	 * field callback: enable/disable the analytics code injection
	 */
	public function callback_enable() {
		$fieldname  = $this->prefix . 'enable_global_tracking';
		$value 		= $this->options[$fieldname];
		$label 		= 'Enable global tracking code';

		printf(
			'<input type="checkbox" id="%1$s" name="%3$s[%1$s]" value="1" %4$s> <label for="%1$s">%2$s</label>',
			$fieldname,
			$label,
			$this->settings_key,
			$value ? 'checked' : ''
		);

	}

	/**
	 * field callback: URL whitelist field
	 */
	public function callback_whitelist() {
		$fieldname  = $this->prefix . 'whitelist_domains';
		$value 		= is_array($this->options[$fieldname]) ? implode("\n", $this->options[$fieldname]) : '';
		$label 		= '<p>Enter one domain (without http://) per line. Any site with a URL listed here will activate the tracking code.</p>';

		printf(
			'<textarea id="%1$s" name="%3$s[%1$s]" class="widefat" rows="5">%4$s</textarea> <p>%2$s</p>',
			$fieldname,
			$label,
			$this->settings_key,
			$value
		);
	}

	/**
	 * field callback: analytics key
	 */
	public function callback_analytics_key() {
		$fieldname 	= $this->prefix . 'analytics_key';
		$value 		= $this->options[$fieldname] ?? '';
		$label 		= '<p>Enter the Google Analytics key that will be used by all white-listed domains. This can be a UA or GA4 key. (e.g. UA-12345678-1)</p>';

		printf(
			'<input type="text" id="%1$s" name="%3$s[%1$s]" value="%4$s" class="widefat"> <p>%2$s</p>',
			$fieldname,
			$label,
			$this->settings_key,
			$value
		);
	}

	/**
	 * field callback: gtm key
	 */
	public function callback_gtm_key() {
		$fieldname 	= $this->prefix . 'gtm_key';
		$value 		= $this->options[$fieldname] ?? '';
		$label 		= '<p>Enter the Google Tag Manager key that will be used by all white-listed domains. (e.g. GTM-XXXXXX)</p>';

		printf(
			'<input type="text" id="%1$s" name="%3$s[%1$s]" value="%4$s" class="widefat"> <p>%2$s</p>',
			$fieldname,
			$label,
			$this->settings_key,
			$value
		);
	}


	//////////////////////////////////////////////////////////////////////
	// OUTPUT (analytics)
	//////////////////////////////////////////////////////////////////////

	public function output_analytics_head() {

		if( $this->is_network_active ) {
			$whitelist  = $this->options[$this->prefix . 'whitelist_domains'];
		}

		$analytics_key 	= trim($this->options[$this->prefix . 'analytics_key'] ?? false);
		$gtm_key 		= trim($this->options[$this->prefix . 'gtm_key'] ?? false );
		$domain 		= $_SERVER['SERVER_NAME'];

		if( $this->is_network_active && !in_array( $domain, $whitelist ) ) {
			return; // do not show analytics if not correct domain
		}

		if( $analytics_key ) : ?>

		<!-- Firefly Multisite Global Analytics -->
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $analytics_key; ?>"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', '<?php echo $analytics_key; ?>');
		</script>

		<?php
		endif;

		if( $gtm_key ) :  ?>

			<!-- Google Tag Manager --><script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push(
			{'gtm.start':new Date().getTime(),event:'gtm.js'}
			);var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer', '<?php echo $gtm_key; ?>');</script><!-- End Google Tag Manager -->

		<?php
		endif;

	}

} // end class