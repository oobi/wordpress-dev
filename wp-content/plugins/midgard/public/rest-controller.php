<?php

namespace FF\Midgard;

class Midgard_REST_Controller extends \WP_REST_controller {

	/**
	 * The base to use in the API route.
	 *
	 * @var string
	 */
	protected $base = 'midgard';

	/**
	 * The namespace for these routes.
	 *
	 * @var string
	 */
	protected $namespace = 'ff/v1';

	/**
	 * The slug of the data feed post type
	 */
	protected $post_type = 'data_feed';

	/**
	 * Constructor
	 */
	public function __construct() {

	}

	/**
	 * Get the feed URI for the specified feed ID
	 */
	public static function get_feed_uri($id=0) {
		$feed = $id > 0 ? get_post($id) : null;

		if($feed) {
			$slug = $feed->post_name;
			return get_rest_url(null, "/ff/v1/midgard/$slug");
		} else {
			return get_rest_url(null, "/ff/v1/midgard/");
		}
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		// permissions callback
		// DEPRECATED
		/*
		$permission_callback = function () {

			// default to give NO permission
			$has_permission = false;

			// get security options
			$security_options = get_option( 'midgard_security_settings' );
			$allowed_roles = isset( $security_options['access_roles'] ) ? $security_options['access_roles'] : array();

			// if no roles checked - treat as public
			if( empty( $allowed_roles ) ) {
				$has_permission = true;
			}
			// check if currrent user has one of the allowed roles
			else {
				$user = wp_get_current_user();

				// if user logged in
				if( isset( $user->ID ) && (int)$user->ID != 0 ) {
					// intersect array - if not empty, user has correct access role
					$intersected = array_intersect( $allowed_roles, $user->roles );
					$has_permission = !empty( $intersected );
				}
			}

			return $has_permission;
		};
		*/

		// Register the general endpoint route.
		register_rest_route( $this->namespace, "/{$this->base}", array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'args'                => array(
					'page-size' => array(
						'default'           => 10,
						'sanitize_callback' => array($this, 'sanitize_int'),
					),
					'page' => array(
						'default'           => 1,
						'sanitize_callback' => array($this, 'sanitize_int'),
					)
				)//,
				//'permission_callback' => $permission_callback
			)
		) );

		// Register the individual object endpoint route.
		register_rest_route( $this->namespace, "/{$this->base}/(?P<id>[a-zA-Z0-9_-]+)", array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'args'                => array(
					'context' => array(
						'default' => 'view',
					),
					'nomap'		=> array(
						'default' 	=> false,
						'sanitize_callback' => array($this, 'sanitize_int')
					)
				)//,
				//'permission_callback' => $permission_callback
			)
		) );

	}

	/**
	 * Check if a given parameter is integer
	 *
	 * @param {any} $value - input value
	 *
	 * @return int
	 */
	public function sanitize_int( $value ) {
		return intval( $value );
	}


	/**
	 * Get a collection of items.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		// post query args
		$args = array(
			// input params for paging
			'posts_per_page' => (int) $request['page-size'],
			'offset' 		 => (int) $request['page-size'] * ( (int) $request['page'] - 1 ),
			// other post query params
			'post_status' 	 => 'publish',
			'post_type' 	 => $this->post_type

		);

		// retrieve matching galleries
		$feeds = get_posts( $args );

		$return = array();

		foreach ( $feeds as $feed ) {
			$data     = $this->prepare_item_for_response( $feed, $request, 'list' );
			$return[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $return );
		$response->header( 'X-WP-Total', count( $feeds ) );

		return $response;
	}

	/**
	 * Get one item from the collection.
	 *
	 * @param array|WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		// get the gallery item slug as defined in Envira settings
		$id_or_slug  = $request['id'];

		// get by ID if integer
		if( is_numeric($id_or_slug )) {
			$feed = get_post( $id_or_slug );
		}
		// otherwise treat as slug
		else {
			$feed = get_page_by_path( $id_or_slug, OBJECT, $this->post_type );
		}

		// make sure it's the right type and it's published
		if($feed) {
			if($feed->post_type != $this->post_type || $feed->post_status != 'publish') {
				$feed = null;
			}
		}

		$data = $this->prepare_item_for_response( $feed, $request, 'full' );
		if( is_wp_error( $data )) {
			$data = array(
				'error'		=> $data->get_error_message()
			);
		}
		$response = rest_ensure_response( $data );
		return $response;
	}

	/**
	 * Prepare the item for the REST response.
	 *
	 * @param stdClass        $item    WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 * @param string 		  $mode    list or full
	 *
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request, $mode='full' ) {

		// if item not found send back an error message
		if(!$item) {
			return new \WP_error('no_feed_found', 'No feed found');
		}

		// get feed metadata
		$feed_type 			= get_post_meta( $item->ID, 'midgard_feed_type', true );
		$feed_cache_time 	= get_post_meta( $item->ID, 'midgard_cache_time', true );
		// ingnore mappings?
		$nomap 				= !!$request['nomap'];

		// base data
		$data = array(
			'id'          => $item->ID,
			'title'  	  => $item->post_title,
			'type'	  	  => $feed_type,
			'slug'   	  => $item->post_name,
			'cache_time'  => $feed_cache_time
		);

		// get remote feed data
		if($mode == 'full') {
			$feed_data = Midgard_Common::get_data($item->ID, $nomap);
			$data['timestamp'] = $feed_data['timestamp'];

			// set error condition if appropriate
			if(isset($feed_data['error'])) {
				$data['error'] = $feed_data['error'];
			}

			// set feed data
			$data['data'] = $feed_data['data'];
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->filter_response_by_context( $data, $context );
		$data    = rest_ensure_response( $data );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		return $data;
	}

	/**
	 * Check the post_date_gmt or modified_gmt and prepare any post or
	 * modified date for single post output.
	 *
	 * @param string|null $date
	 *
	 * @return string|null ISO8601/RFC3339 formatted datetime.
	 */
	protected function prepare_date_response( $date ) {
		if ( '0000-00-00 00:00:00' === $date ) {
			return null;
		}

		return mysql_to_rfc3339( $date );
	}

}