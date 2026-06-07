<?php
/**
 * Plugin Name:   Firefly - Custom REST Types and Fields
 * Plugin URI:    https://www.fi.net.au
 * Text Domain:   ff_rest_api_cf_types
 * Domain Path:   /languages
 * Description:   Enable custom post types and taxonomies to be displayed via REST. Include custom fields in REST post output.
 * Author:        Firefly Interactive
 * Version:       1.0.2
 * Licence:       GPLv3+
 * Author URI:    http://www.fi.net.au
 * Last Change:   2016-08-08
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action(
	'plugins_loaded',
	array( FF_Rest_CF_Types::get_instance(), 'plugin_setup' )
);

class FF_Rest_CF_Types {

	/**
	 * key which stores settings in the database (e.g. email_view_swettings)
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
	 * Constructor
	 *
	 * @since  0.0.1
	 * @return Rest_CF_Types
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'reset-custom-settings-page';
		$this->config = array(
			'settings_key'  => 'rest_custom_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'Firefly REST Custom',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Firefly REST Custom',		//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'rest_custom_settings'		//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// Set class property and apply defaults if not set
		$options = get_option( $this->config['settings_key'] );
		if(! $options) $options = array();

		$defaults = array(
			'rest_enable_post_types' => array(),
			'rest_enable_taxonomies' => array(),
			'rest_include_custom'   => array()
		);

		$this->options = array_merge(
			$defaults,  // defaults
			$options    // actuals
		);
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
		if(! is_admin() ) {
			// add REST filters
			add_action( 'init', array( $this, 'add_rest_filters' ));
			// add REST support
			add_action( 'init', array( $this, 'add_custom_rest_support' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'options_page_create' ) );
			add_action( 'admin_init', array( $this, 'options_page_init' ) );
		}
	}
	public function add_rest_filters() {
		// filters for modifying REST output
		$custom = $this->options['rest_include_custom'];
		foreach( $custom as $type ) {
			add_filter( 'rest_prepare_' . $type, array( $this, 'prepare_add_custom_fields' ), 20, 1 );
		}

		// limit items returned
		$types = get_post_types( array( 'public' => true ), 'names');
		foreach( $types as $type ) {
			add_filter( 'rest_prepare_' . $type, array( $this, 'prepare_limit_items' ), 21, 1 );
		}
	}


	//////////////////////////////////////////////////////////////////////
	// OPTIONS PAGE
	//////////////////////////////////////////////////////////////////////


	/**
	 * Add an options page to the "settings" menu
	 */
	public function options_page_create() {
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
				settings_fields( 'rest_enable_group' );			// Option group
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
			'rest_enable_group', 			// ID
			'Enable REST', 		// Title
			array($this, 'callback_post_type_settings_section'), 					// Callback
			$this->settings_page 		// Page
		);

		add_settings_section(
			'custom_group', 			// ID
			'Include Custom Fields', 		// Title
			array($this, 'callback_custom_field_settings_section'), 					// Callback
			$this->settings_page 		// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'rest_enable_group', 			// Option group
			$this->settings_key, 		// Option name
			array( $this, 'sanitize' ) 	// Sanitize
		);

		add_settings_field(
			'rest_enable_post_types', 		                    // ID
			'Custom Post Types', 						    // Title
			array( $this, 'callback_post_types' ), 	        // Callback
			$this->settings_page, 				            // Page
			'rest_enable_group'					                // Section ID
		);

		add_settings_field(
			'rest_enable_taxonomies',                             // ID
			'Custom Taxonomies', 						    // Title
			array( $this, 'callback_taxonomies' ), 	        // Callback
			$this->settings_page, 				            // Page
			'rest_enable_group'					                // Section ID
		);

		add_settings_field(
			'rest_include_custom',               // ID
			'Post Types', 						            // Title
			array( $this, 'callback_custom_fields' ), 	        // Callback
			$this->settings_page, 				            // Page
			'custom_group'					                // Section ID
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 * @return array
	 */
	public function sanitize( $input ) {

		return $input;

	}

	/**
	 * Callback for settings section
	 */
	public function callback_post_type_settings_section() {
		echo '<p>Disabled items are already enabled for REST in their definition.</p>';
	}

	/**
	 * Callback for settings section
	 */
	public function callback_custom_field_settings_section() {
		echo '<p>Enabled items will include a custom_fields key in the REST response. This may include built-in post types.</p>';
	}

	/**
	 * Callback to custom post types
	 */
	public function callback_post_types() {
		// get publicly queriable custom post types
		$types = get_post_types( array( 'public' => true, '_builtin'=> false ), 'objects');
		$option = $this->options['rest_enable_post_types'];

		foreach($types as $type=>$obj) {
			// if the setting is already present we'll indicate this by disabling the checkbox
			$disabled = property_exists($obj, 'show_in_rest') && $obj->show_in_rest;

			// checked if option selected
			$checked = in_array($type, $option) || $disabled;

			// rest base for reference
			$rest_base = property_exists($obj, 'rest_base') && $obj->rest_base ? $obj->rest_base : $type;

			printf(
				'<input type="checkbox" id="type_%1$s" name="%3$s[rest_enable_post_types][]" value="%1$s" %4$s %5$s> <label for="type_%1$s">%2$s <em>(%6$s)</em></label><br>',
				$type,
				$obj->label,
				$this->settings_key,
				$checked ? 'checked' : '',
				$disabled ? 'disabled' : '',
				$rest_base
			);

		}
	}

	/**
	 * Callback to custom taxonomies
	 */
	public function callback_taxonomies() {
		// get publicly queriable custom post types
		$types = get_taxonomies( array( '_builtin'=> false ), 'objects');
		$option = $this->options['rest_enable_taxonomies'];

		foreach($types as $type=>$obj) {
			// if the setting is already present we'll indicate this by disabling the checkbox
			$disabled = property_exists($obj, 'show_in_rest') && $obj->show_in_rest;

			// checked if option selected
			$checked = in_array($type, $option) || $disabled;

			// rest base for reference
			$rest_base = property_exists($obj, 'rest_base') && $obj->rest_base ? $obj->rest_base : $type;

			printf(
				'<input type="checkbox" id="type_%1$s" name="%3$s[rest_enable_taxonomies][]" value="%1$s" %4$s %5$s> <label for="type_%1$s">%2$s <em>(%6$s)</em></label><br>',
				$type,
				$obj->label,
				$this->settings_key,
				$checked ? 'checked' : '',
				$disabled ? 'disabled' : '',
				$rest_base
			);
		}

	}


	/**
	 * Callback to include custom fields in post types
	 */
	public function callback_custom_fields() {
		// get publicly queriable custom post types
		$types = get_post_types( array( 'public' => true ), 'objects');
		$option = $this->options['rest_include_custom'];

		foreach($types as $type=>$obj) {

			// checked if option selected
			$checked = in_array($type, $option);

			printf(
				'<input type="checkbox" id="cust_%1$s" name="%3$s[rest_include_custom][]" value="%1$s" %4$s> <label for="cust_%1$s">%2$s</label><br>',
				$type,
				$obj->label,
				$this->settings_key,
				$checked ? 'checked' : ''
			);
		}
	}


	//////////////////////////////////////////////////////////////////////
	// ALTER THE RESPONSE OUTPUT
	//////////////////////////////////////////////////////////////////////

	/**
	 * Alter the response structure to include custom fields
	 * @param $response
	 * @return array
	 */
	public function prepare_add_custom_fields( $response ) {
		$post_id = $response->data['id'];
		$custom_data = array();

		// custom field suite custom_data
		if(function_exists('CFS')) {
			$params = array( 'post_id' => $post_id );
			$fields = CFS()->find_fields( $params );

			// init field custom_data
			$cfs = array();

			foreach( $fields as $field ) {
				$fieldname = $field['name'];
				if( $field['parent_id'] == 0) {
					$cfs[ $fieldname ] = CFS()->get( $fieldname, $post_id );
				}
			}

			$custom_data['cfs'] = $cfs;
		}

		// post metadata (incl any manually defined custom fields)
		$custom_data['metadata'] = get_post_meta( $post_id );

		// set response key
		$response->data['custom_fields'] = $custom_data;

		return $response;
	}

	/**
	 * Alter the response structure to limit returned keys
	 * @param $response
	 * @return array
	 */
	public function prepare_limit_items( $response ) {
		$items = array();
		if ( isset( $_GET[ 'items' ] ) ) {
			$items = explode( ',', $_GET[ 'items' ] );
		}

		// include only set items, or all items if none defined
		if( !empty( $items ) ) {
			$new_response = array();
			if( property_exists( $response, 'data' )) {
				foreach( $items as $item ) {
					if( array_key_exists( $item, $response->data ) ) {
						$new_response[$item] = $response->data[$item];
					}
				}
			}
			$response = $new_response;
		}

		return $response;
	}


	/**
	 * Add REST API support to an already registered post types and taxonomies.
	 */
	public function add_custom_rest_support() {
		global $wp_post_types, $wp_taxonomies;

		$post_types = $this->options['rest_enable_post_types'];
		$taxonomies = $this->options['rest_enable_taxonomies'];

		foreach( $post_types as $name ) {
			if ( isset( $wp_post_types[ $name ] ) ) {
				$obj = $wp_post_types[ $name ];
				$obj->show_in_rest = true;
				if( !property_exists($obj, 'rest_base') ) {
					$obj->rest_base = $name;
				}
				if( !property_exists($obj, 'rest_controller_class') ) {
					$obj->rest_controller_class = 'WP_REST_Posts_Controller';
				}
			}
		}

		foreach( $taxonomies as $name ) {
			if ( isset( $taxonomies[ $name ] ) ) {
				$obj = $wp_taxonomies[ $name ];
				$obj->show_in_rest = true;
				if( !property_exists($obj, 'rest_base') ) {
					$obj->rest_base = $name;
				}
				if( !property_exists($obj, 'rest_controller_class') ) {
					$obj->rest_controller_class = 'WP_REST_Terms_Controller';
				}
			}
		}

	}

} // end class
