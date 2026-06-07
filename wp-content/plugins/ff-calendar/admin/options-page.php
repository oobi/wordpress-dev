<?php

namespace FF\Calendar;

class Options_Page {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The version of this API.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_version    The current version of this API.
	 */
	private $api_version;

	/**
	 * key which stores settings in the database (e.g. email_view_settings)
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
	 * Current options
	 */
	protected $options;

	/**
	 * Plugin cache
	 */
	protected $cache;

	/**
	 * Start up
	 */
	public function __construct( $plugin_name, $version ) {

		$this->cache = FF_Calendar::get_cache_instance();

		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		$this->api_version 	= FF_CALENDAR_API_VERSION;

		// Set up variables
		$this->settings_page = 'ff-calendar-settings-page';
		$this->config = array(
			'settings_key'  => FF_CALENDAR_SETTINGS_KEY,	//e.g. 'mailchimp_settings
			'page_title'  	=> 'Firefly Calendar Settings',	//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Firefly Calendar',			//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'ff_calendar_settings'		//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// Get the current options
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name

		////////////////////////////////////////////////////////////////////////////////////////////
		// check if old (string) settings set and no new (array) settings set
		// ie. upgraded from an old version (v1.7.0 or earlier)
		// TODO: Once people have upgraded, remove this code
		if( isset( $this->options['feed_url'] ) && !isset( $this->options['calendar_feeds'] ) ) {

			printf( '<div class="notice notice-warning is-dismissible"><p>%s <a href="%s">%s</a></p><p>%s <a href="%s">%s</a></p></div>',
					__( 'You have updated the Firefly Calendar plugin to version 1.8.0 or greater, which changed the way feeds are created.
							Please verify that your feeds are still working by', 'ff-calendar' ),
					admin_url( 'options-general.php?page=ff_calendar_settings&tab=settings' ),
					__( 'visiting the Settings page', 'ff-calendar' ),
					__( 'Shortcodes have also changed. Rebuild them by', 'ff-calendar' ),
					admin_url( 'options-general.php?page=ff_calendar_settings&tab=generate' ),
					__( 'visiting the shortcode generator', 'ff-calendar' )
			);

			// transform old settings to new format
			$feeds = explode("\n", $this->options['feed_url']);
			$new_settings = array();
			if( $feeds && is_array( $feeds ) ) {
				foreach( $feeds as $index => $feed ) {
					$f = explode( '|', $feed, 2 ); // feed array
					array_push( $new_settings, array(
						'id'			=> uniqid(),
						'name'			=> trim( $f[0] ),
						'url'			=> trim( $f[1] ),
						'category-key'	=> ''
					) );
				}
			}

			// save new settings
			$this->options['calendar_feeds'] = $new_settings;
			update_option( $this->settings_key, $this->options );
		}
		////////////////////////////////////////////////////////////////////////////////////////////
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
		// check active tab
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'generate';
		?>

		<div class="wrap">
			<h2><?php echo $this->config['page_title']; ?></h2>
			<h2 class="nav-tab-wrapper">
				<a href="?page=ff_calendar_settings&tab=generate" class="nav-tab <?php echo $active_tab == 'generate' ? 'nav-tab-active' : ''; ?>">Generate Shortcode</a>
				<a href="?page=ff_calendar_settings&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Feed Settings</a>
				<a href="?page=ff_calendar_settings&tab=cache" class="nav-tab <?php echo $active_tab == 'cache' ? 'nav-tab-active' : ''; ?>">Cache Control</a>
			</h2>

			<div class="tab-content" id="tab-<?php echo $active_tab; ?>">
				<?php
					// Show tab content depending on which is active
					switch( $active_tab ) {
						case 'generate' :
							include('partials/options-page-generate.php'); break;
						case 'settings' :
							include('partials/options-page-settings.php'); break;
						case 'cache' :
							include('partials/options-page-cache.php'); break;
						default :
							// show nothing
					}
				?>
			</div>
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
			'cache_group', 				// ID
			'Cache', 					// Title
			array(), 					// Callback
			$this->settings_page 		// Page
		);

		add_settings_section(
			'feed_group', 				// ID
			'Feeds', 					// Title
			array(), 					// Callback
			$this->settings_page 		// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'cache_group', 				// Option group
			$this->settings_key, 		// Option name
			array( $this, 'sanitize' ) 	// Sanitize
		);

		add_settings_field(
			'cache_active', 			                    // ID
			'Cache Active', 							    // Title
			array( $this, 'cache_active_callback' ), 	    // Callback
			$this->settings_page, 				            // Page
			'cache_group'					            	// Section ID
		);

		add_settings_field(
			'cache_time', 		                  			// ID
			'Cache time (seconds)',				   			// Title
			array( $this, 'cache_time_callback' ),  		// Callback
			$this->settings_page, 				            // Page
			'cache_group'					            	// Section ID
		);

		/* REGISTER SETTINGS */
		register_setting(
			'feed_group', 				// Option group
			$this->settings_key, 		// Option name
			array( $this, 'sanitize' ) 	// Sanitize
		);

		/* ADD SETTINGS FIELDS */
		add_settings_field(
			'calendar_feeds', 		              // ID
			'Calendar Feeds',			    	  // Title
			array( $this, 'calendar_feeds_callback' ),  // Callback
			$this->settings_page, 				  // Page
			'feed_group'					      // Section ID
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if( isset( $input['feed_url'] ) ) {
			$new_input['feed_url'] = wp_kses_post( $input['feed_url'] );
		}

		if( isset( $input['calendar_feeds'] ) ) {
			foreach( $input['calendar_feeds'] as $index => $feed ) {
				// Feed ID is not a user-editable field. Generate an ID if one doesn't already exist (should only happen once)
				$feed_id = empty( $feed['id'] ) ? uniqid() : $feed['id'];
				$new_input['calendar_feeds'][$index]['id'] = wp_kses_post( $feed_id );
				$new_input['calendar_feeds'][$index]['name'] = wp_kses_post( $feed['name'] );
				$new_input['calendar_feeds'][$index]['url'] = esc_url( $feed['url'] );
				$new_input['calendar_feeds'][$index]['category-key'] = wp_kses_post( $feed['category-key'] );
			}
		}

		if( isset( $input['cache_active'] ) ) {
			$new_input['cache_active'] = wp_kses_post( $input['cache_active'] );
		}

		if( isset( $input['cache_time'] ) ) {
			$new_input['cache_time'] = wp_kses_post( $input['cache_time'] );
		}

		return $new_input;

	}

	/**
	 * On options save callback - clear the cache
	 */
	public function on_options_save( $old_value, $new_value, $option ) {
		$this->cache->clear();
	}

	/**
	 * Callback to calendar feed fields
	 */
	public function calendar_feeds_callback() {
		$option_key = 'calendar_feeds';
		?>

		<table class="widefat ff-calendar-feeds">
			<thead>
				<tr>
					<th class="id"><?php esc_attr_e( 'ID', 'ff-calendar' ); ?></th>
					<th class="name">
						<?php esc_attr_e( 'Name', 'ff-calendar' ); ?>
						<span class="required">*</span>
					</th>
					<th class="url">
						<?php esc_attr_e( 'URL', 'ff-calendar' ); ?>
						<span class="required">*</span>
					</th>
					<th class="cat-key">
						<?php esc_attr_e( 'Category Key', 'ff-calendar' ); ?>
						<a class="ff-calendar-help dashicons dashicons-editor-help" href="#" title="What is this?"></a>
					</th>
					<th class="delete">&nbsp;</th>
					<th class="view">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if( isset( $this->options['calendar_feeds'] ) ) {
					foreach( $this->options['calendar_feeds'] as $index => $feed ) {
						$this->new_row( $option_key, $index, $feed );
					}
				}
				// if no feeds set up, add a blank row
				else {
					$this->new_row( $option_key );
				}
				?>
			</tbody>
		</table>

		<p>
			<input class="button-primary ff-calendar-add" type="button" value="<?php esc_attr_e( 'Add new feed', 'ff-calendar' ); ?>" />
		</p>
<?php
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
	 * Callback to cache time field
	 */
	public function cache_time_callback() {

		printf(
			'<input type="text" id="cache_time" name="%s[cache_time]" value="%s">',
			$this->settings_key,
			isset( $this->options['cache_time'] ) ? esc_attr( $this->options['cache_time']) : '3600'
		);
		echo '<p class="description">Number of seconds to hold feeds in the cache.</p>';

	}

	/**
	 * Print a new feed setting row
	 */
	private function new_row( $option_key, $index=0, $feed=array() ) {
		/* NOTE: If this markup changes, make sure the addNewFeed method (js/options-page.js) is also updated to match */
		printf( '<tr class="%s">',
				$index % 2 == 1 ? 'alternate' : '' );
		printf( '<td><input type="text" class="large-text ff-calendar-id" name="%s[%s][%s][id]" value="%s" placeholder="ID" readonly></td>',
				$this->settings_key, $option_key, $index,
				array_key_exists( 'id', $feed ) ? $feed['id'] : '' );
		printf( '<td><input type="text" class="large-text ff-calendar-name" name="%s[%s][%s][name]" value="%s" placeholder="Calendar Name" required></td>',
				$this->settings_key, $option_key, $index,
				array_key_exists( 'name', $feed ) ? $feed['name'] : '' );
		printf( '<td><input type="text" class="large-text ff-calendar-url" name="%s[%s][%s][url]" value="%s" placeholder="eg. http://www.example.com/calendar.ics" required></td>',
				$this->settings_key, $option_key, $index,
				array_key_exists( 'url', $feed ) ? $feed['url'] : '' );
		printf( '<td><input type="text" class="large-text ff-calendar-category-key" name="%s[%s][%s][category-key]" value="%s" placeholder="eg. X-CATEGORY"></td>',
				$this->settings_key, $option_key, $index,
				array_key_exists( 'category-key', $feed ) ? $feed['category-key'] : '' );
		printf( '<td><a class="ff-calendar-view dashicons dashicons-external" href="%s" target="_blank" title="View raw data"></a></td>',
				get_rest_url( null,
				array_key_exists( 'id', $feed ) ? '/' . $this->plugin_name . '/' . $this->api_version . '/events/' . $feed['id'] : '' ) );
		printf( '<td><a class="ff-calendar-delete dashicons dashicons-trash" href="#" title="Delete"></a></td>' );
		echo '</tr>';
	}

	/**
	 * Add a help menu to the options page
	 */
	public function help_menu() {
		$screen = get_current_screen();

		if( $screen->id != 'settings_page_ff_calendar_settings' ) {
			return false;
		}

		// help menu tabs
		$tabs = array(
			array(
				'id'       => 'ff-cal-overview',
				'title'    => __( 'Overview', 'ff-calendar' ),
				'content'  => sprintf( '<p>%s</p><p>%s</p><p>%s</p>',
										__( 'Firefly Calendar takes ICS feed URLs and transforms them into calendars.', 'ff-calendar' ),
										__( 'Calendars can be inserted onto any page via a shortcode, or any widget area via
											 the Firefly Calendar Widget.', 'ff-calendar' ),
										__( 'Calendars can display multiple feeds together in the same view and can be
											 configured to display as categories for filtering.', 'ff-calendar' )
							)
			),
			array(
				'id'       => 'ff-cal-feeds',
				'title'    => __( 'Creating categories', 'ff-calendar' ),
				'content'  => sprintf( '<p><strong>%s</strong><br>%s</p><p>%s</p><p><strong>%s</strong><br>%s</p>',
										__( 'How do I split my events into categories?', 'ff-calendar' ),
										__( 'If your ICS feed contains categories (most don\'t, so double check), you can use
											 the optional <em>Category Key</em> field on the Feed Settings tab to specify which
											 ICS component to use as a category. If you do not know which ICS component contains
											 your categories, you will need to inspect your ICS feed manually to find out*.
											 Download the .ics file and open in a plain text editor to see the raw data. Take note of the
											 component that holds the event category and paste this value in the <em>Category Key</em> field.', 'ff-calendar' ),
										__( '<em>* This is an advanced functionality and best handled by someone familiar with the ICS format.</em>', 'ff-calendar' ),
										__( 'My ICS feed does not have categories!', 'ff-calendar' ),
										__( 'If your ICS feed does not contain categories, you can recreate the functionality using
											 multiple feeds. Set up multiple feeds and leave the Category Key fields blank. The
											 Calendar will instead of the feed <em>Name</em> as a category.', 'ff-calendar' )
							)
			),
			array(
				'id'       => 'ff-cal-cache',
				'title'    => __( 'Cache', 'ff-calendar' ),
				'content'  => sprintf( '<p><strong>%s</strong><br>%s</p><p><strong>%s</strong><br>%s</p><p>%s</p>',
										__( 'Do I need to turn on the cache?', 'ff-calendar' ),
										__( 'It\'s recommended to turn it on. Turning it off will result in MUCH slower
											 page load speeds.', 'ff-calendar' ),
										__( 'I\'ve created a new event but it isn\'t showing up in the calendar!', 'ff-calendar' ),
										__( 'It\'s probably caught in the cache. The best thing to do is wait for
											 it to refresh itself. You can check the cache TTL (Time To Live) in the
											 <em>Cache Control</em> tab for an indication on how long you\'ll need to wait.', 'ff-calendar' ),
										__( 'You can empty the cache manually to force the feed to refresh,
											 but be warned that this will result in longer than normal load times for a while as the cache regenerates.', 'ff-calendar' )
							)
			)
		);

		// add the tabs
		foreach($tabs as $tab) {
			$screen->add_help_tab($tab);
		}

	}

}
