<?php

namespace FF\Calendar;

class REST_Controller extends \WP_REST_controller {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		$this->api_version 	= FF_CALENDAR_API_VERSION;
	}

	/**
	 * Add a custom REST API endpoint.
	 */
	public function register_routes() {
		$data = new Data();

		/*******************************************************************
		 * ALL DATA
		 *******************************************************************/

		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 	// namespace
			'/all/', 										// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_all_data' ),
				'permission_callback' => '__return_true'
			)
		);

		/*******************************************************************
		 * FEED CONFIGS
		 *******************************************************************/

		// all feed configs
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 	// namespace
			'/feeds/', 										// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_configs' ),
				'permission_callback' => '__return_true'
			)
		);

		// specific feed config
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 	// namespace
			'/feeds/(?P<id>\w+)', 							// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_config' ),
				'permission_callback' => '__return_true'
			)
		);

		/*******************************************************************
		 * ALL FEED EVENTS (merged - all feeds)
		 *******************************************************************/

		// all feed data
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 	// namespace
			'/events/', 									// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_feeds_events' ),
				'permission_callback' => '__return_true'
			)
		);

		// all feed data (merged) with number of days limit
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 			// namespace
			'/events/days/(?P<days>([0-9])+)', 	// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_feeds_events' ),
				'permission_callback' => '__return_true',
				'args' => array(
					'start_date'	=> array(
						'default' => date( 'Y-m-d\TH:i:s' ),
						'sanitize_callback' => array( $this, 'sanitize_date' ),
					)
				)
			)
		);

		// all feed data (merged) with number of events limit
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 		// namespace
			'/events/limit/(?P<limit>([0-9])+)', 				// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_feeds_events' ),
				'permission_callback' => '__return_true',
				'args' => array(
					'offset'	=> array(
						'default' => 0,
						'sanitize_callback' => array( $this, 'sanitize_int' ),
					),
					'start_date'	=> array(
						'default' => date( 'Y-m-d\TH:i:s' ),
						'sanitize_callback' => array( $this, 'sanitize_date' ),
					)
				)
			)
		);

		/*******************************************************************
		 * SPECIFIC FEED EVENTS
		 *******************************************************************/

		// specific feed data
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 	// namespace
			'/events/(?P<id>[\w,]+)', 							// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_feeds_events' ),
				'permission_callback' => '__return_true'
			)
		);

		// specific feed data with number of days limit
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 		// namespace
			'/events/(?P<id>[\w,]+)/days/(?P<days>([0-9])+)', 		// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_feeds_events' ),
				'permission_callback' => '__return_true',
				'args' => array(
					'start_date'	=> array(
						'default' => date( 'Y-m-d\TH:i:s' ),
						'sanitize_callback' => array( $this, 'sanitize_date' ),
					)
				)
			)
		);

		// specific feed data with number of events limit
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 		// namespace
			'/events/(?P<id>[\w,]+)/limit/(?P<limit>([0-9])+)', 	// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_feeds_events' ),
				'permission_callback' => '__return_true',
				'args' => array(
					'offset'	=> array(
						'default' => 0,
						'sanitize_callback' => array( $this, 'sanitize_int' ),
					),
					'start_date'	=> array(
						'default' => date( 'Y-m-d\TH:i:s' ),
						'sanitize_callback' => array( $this, 'sanitize_date' ),
					)
				)
			)
		);

		/*******************************************************************
		 * CATEGORIES
		 *******************************************************************/

		// all feed categories
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 	// namespace
			'/categories/', 						// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_feeds_categories' ),
				'permission_callback' => '__return_true'
			)
		);

		// specific feed categories
		register_rest_route(
			$this->plugin_name . '/' . $this->api_version, 	// namespace
			'/categories/(?P<id>[\w,]+)', 						// route
			array(
				'methods' 	=> 'GET',
				'callback' 	=> array( $data, 'get_feeds_categories' ),
				'permission_callback' => '__return_true'
			)
		);


	}

	/**
	 * Check if a given parameter is integer
	 *
	 * @param 	{any} $value 		- input value
	 * @return 	{int}
	 */
	public function sanitize_int( $value ) {
		return intval( $value );
	}

	/**
	 * Check if a given parameter is a date
	 *
	 * @param 	{any} 	$value 		- input value
	 * @return 	{int}
	 */
	public function sanitize_date( $value ) {
		$time = strtotime( $value );
		if( $time === false ) {
			$time = strtotime( date( 'Y-m-d' ) );
		}
		return $time;
	}

}