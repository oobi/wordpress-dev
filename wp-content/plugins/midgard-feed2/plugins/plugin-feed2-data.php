<?php

namespace FF\Midgard\Feed2;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to retrieve and handle remote data
 *
 * @package    Midgard_Feed2
 * @subpackage Midgard_Feed2/admin
 * @author     Firefly Interactive
 */
class Midgard_Feed2_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data($feed_id) {

		ExceptionThrower::Start();

		// get feed list
		$feeds = json_decode( get_post_meta( $feed_id, 'midgard_feed2', true ), true );

		// get prefered data format
		$return_as_array = intval( get_post_meta($feed_id, 'midgard_feed2_is_array', true) );

		// init bucket for data
		$data = array();


		if( $feeds && is_array( $feeds )) {

			foreach( $feeds as $index=>$feed ) {

				// set sensible defaults in case of bad values
				$feed = array_merge(array(
					'url' => '',
					'key' => 'feed' . $index,
					'root'=> ''
				), $feed);

				// working values
				$url = $feed['url'];
				$key = $feed['key'];
				$root = $feed['root'];

				try{
					// get the data
					if( ! empty( $url ) ) {
						$remote_args = Midgard_Common::wp_remote_get_params();
						$response = wp_remote_get($url, $remote_args);
						$rawdata = is_array($response) ? $response['body'] : '';

						// attempt to decode as JSON
						$json = json_decode($rawdata, TRUE);

						// remap root node if set
						if( $json && !empty($root)) {
							$json = $this->find_node( $json, $root, true );
						}

						// store original string if not valid object when decoded
						$feed_data = $json ? $json : $rawdata;

						if( $return_as_array ) {
							$data[] = $feed_data;
						} else {
							$data[$key] = $feed_data;
						}
					}

				}
				// if there's a problem store a null
				catch(\Exception $ex) {
					if( $return_as_array ) {
						$data[] = null;
					} else {
						$data[$key] = null;
					}
					//$this->error_message('Unable to retrieve data - ' . $ex->getMessage() );
				}

				ExceptionThrower::Stop();
			}

		}


		return $this->transform_data( $data, $feed_id );
	}


	/**
	 * Transform data into json format
	 *
	 * @param Object $data - Feed2 Object
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($data, $feed_id) {
		// nothing to do
		return parent::transform_data($data, $feed_id);
	}

}
