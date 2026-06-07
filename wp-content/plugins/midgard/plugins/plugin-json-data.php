<?php

namespace FF\Midgard;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard
 * @subpackage midgard/admin
 * @author     Firefly Interactive
 */
class Midgard_JSON_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id
	 */
	protected function get_feed_data($feed_id) {
		// get the feed URL
		$feed_url = get_post_meta( $feed_id, 'midgard_feed_uri', true );

		ExceptionThrower::Start();

		// retrieve raw data (JSON)
		try{
			$args = Midgard_Common::wp_remote_get_params();
			$response = wp_remote_get($feed_url, $args );
			$data 	  = wp_remote_retrieve_body( $response );	// get body from response (safe)
		} catch(\Exception $ex) {
			return $this->error_message('Unable to retrieve data - ' . $ex->getMessage() );
		}

		$data = json_decode($data, true);

		if($data === NULL) {
			return $this->error_message('Unable to parse output as valid JSON');
		}

		ExceptionThrower::Stop();

		return $this->transform_data( $data, $feed_id );
	}

	/**
	 * Transform data into standard format
	 *
	 * @param Object $data - JSON
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($data, $feed_id) {
		return parent::transform_data($data, $feed_id);
	}


}
