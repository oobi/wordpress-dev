<?php

namespace FF\Midgard\WordPress;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard_wordpress
 * @subpackage midgard_wordpress/admin
 * @author     Firefly Interactive
 */
class Midgard_WordPress_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data($feed_id) {
		// get the feed URL
		$feed_url = get_post_meta( $feed_id, 'midgard_feed_uri', true );
		$token_id = get_post_meta( $feed_id, 'midgard-wordpress-id', true );
		$token    = $token_id ? Midgard_WordPress::getAuthToken($token_id) : null;

		ExceptionThrower::Start();

		// retrieve raw data (JSON)
		try{

			// do a request with auth token
			if( $token ) {

				$opts = Midgard_Common::wp_remote_get_params();
				$opts['headers'] = 'Authorization: Bearer ' . $token['token'];

				$response = wp_remote_get($feed_url, $opts);
			}
			// otherwise do a regular request
			else {
				$response = wp_remote_get($feed_url);
			}

			$data = is_array($response) ? $response['body'] : '';

		} catch(\Exception $ex) {
			return $this->error_message('Unable to retrieve data - ' . $ex->getMessage() );
		}

		$data = json_decode($data, true);

		if($data == NULL) {
			return $this->error_message('Unable to parse output as valid JSON');
		}

		ExceptionThrower::Stop();

		return $this->transform_data( $data, $feed_id );
	}


	/**
	 * Transform data into json format and (optionally) set root node
	 *
	 * @param Object $data - WordPress Object
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($data, $feed_id) {
		return parent::transform_data($data, $feed_id);
	}

}
