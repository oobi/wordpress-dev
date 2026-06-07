<?php

namespace FF\Envira;

class REST_Controller extends \WP_REST_controller {

	/**
	 * The base to use in the API route.
	 *
	 * @var string
	 */
	protected $base = 'ff-envira';

	/**
	 * The namespace for these routes.
	 *
	 * @var string
	 */
	protected $namespace = 'ff/v1';

	/**
	 * A list of available image sizes
	 * @var array
	 */
	protected $sizes;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->sizes = $this->get_image_sizes();
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

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
					),
				),
			),

			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		// Register the individual object endpoint route.
		register_rest_route( $this->namespace, "/{$this->base}/(?P<id>[\d]+)", array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'args'                => array(
					'context' => array(
						'default' => 'view',
					),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
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
		// get the gallery item slug as defined in Envira settings
		$slug = get_option('envira-gallery-slug', 'envira');

		// post query args
		$args = array(
			// input params for paging
			'posts_per_page' => (int) $request['page-size'],
			'offset' 		 => (int) $request['page-size'] * ( (int) $request['page'] - 1 ),
			// other post query params
			'post_status' 	 => 'publish',
			'post_type' 	 => $slug

		);

		// retrieve matching galleries
		$galleries = get_posts( $args );

		$return = array();

		foreach ( $galleries as $gal ) {
			$data     = $this->prepare_item_for_response( $gal, $request );
			$return[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $return );
		$response->header( 'X-WP-Total', count( $galleries ) );

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
		$slug = get_option('envira-gallery-slug', 'envira');
		$id   = (int) $request['id'];

		// get post
		$gallery = get_post( $id );

		// make sure it's the right type and it's published
		if($gallery->post_type != $slug || $gallery->post_status != 'publish') {
			$gallery = null;
		}

		$data = $this->prepare_item_for_response( $gallery, $request );
		$response = rest_ensure_response( $data );
		return $response;
	}

	/**
	 * Prepare the item for the REST response.
	 *
	 * @param stdClass        $item    WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request ) {

		// if item not found send back an error message
		if(!$item) {
			return new \WP_error('no_gallery_found', 'No gallery found');
		}

		// get image metadata
		$gallery_data = get_post_meta( $item->ID, '_eg_gallery_data', true );
		$gallery_data = isset($gallery_data['gallery']) ? $gallery_data['gallery'] : array();

		// initialise return data
		$images = array();

		// collect extended image data
		if($gallery_data && is_array($gallery_data)) {
			foreach($gallery_data as $id=>$img) {

				// do not process this image if it's not active
				if($img['status'] != 'active') {
					continue;
				}

				$size_urls = array();

				// retrieve URL for each size - return with width/height information
				// @see get_image_sizes()
				foreach($this->sizes as $key=>$s) {
					$src = wp_get_attachment_image_src($id, $key);
					$src = is_array($src) ? $src[0] : $src;

					$size_urls[] = array_merge( $s, array(
						'size' 	=> $key,
						'src'	=> $src
					));
				}

				// add full size to sizes
				$src = wp_get_attachment_image_src($id, 'full');
				if(is_array($src) && count($src) >= 3) {
					$size_urls[] = array(
						'size' 		=> 'full',
						'src'		=> $src[0],
						'crop' 		=> false,
						'width'		=> $src[1],
						'height'		=> $src[2]
					);
				}


				// add image to result array
				$images[] = array(
					'ID'			=> $id,
					'src'			=> isset( $img['src'] ) ? $img['src'] : '',
					'thumb'			=> isset( $img['thumb'] ) ? $img['thumb'] : '',
					'mobile_thumb'	=> isset( $img['mobile_thumb'] ) ? $img['mobile_thumb'] : '',
					'title'			=> isset( $img['title'] ) ? $img['title'] : '',
					'alt'			=> isset( $img['alt'] ) ? $img['alt'] : '',
					'caption'		=> isset( $img['caption'] ) ? $img['caption'] : '',
					'sizes'			=> $size_urls
				);
			}
		}

		// base data
		$data = array(
			'id'          => $item->ID,
			'post_title'  => $item->post_title,
			'post_name'   => $item->post_name,
			'post_date'   => $this->prepare_date_response($item->post_date),
			'post_modified' => $this->prepare_date_response($item->post_modified),
			//'categories'  => $this->prepare_category_response( $item ),
			'images'	  => $images
		);

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

	/**
	 * Prepare the link_category items for a given link.
	 *
	 * This will simply give an array of category names.
	 *
	 * @param stdClass $item
	 *
	 * @return array
	 */
	//protected function prepare_category_response( $item ) {
	//	return wp_get_object_terms( $item->link_id, 'link_category', array( 'fields' => 'names' ) );
	//}


	/**
	* Get information about available image sizes
	*/
	function get_image_sizes( $size = '' ) {

		global $_wp_additional_image_sizes;

		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width' => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}

		// Get only 1 size if found
		if ( $size ) {
			if( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}
		return $sizes;
	}
}