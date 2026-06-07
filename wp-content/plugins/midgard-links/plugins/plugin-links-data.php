<?php

namespace FF\Midgard\Links;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;

/**
 * The class to retrieve and handle remote data
 *
 * @package    Midgard_Links
 * @subpackage Midgard_Links/admin
 * @author     Firefly Interactive
 */
class Midgard_Links_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data($feed_id) {

		ExceptionThrower::Start();

		// retrieve raw data (Links)
		try{
			// get the link data
			$data = get_post_meta( $feed_id, 'midgard_links', true );

		} catch(\Exception $ex) {
			return $this->error_message('Unable to retrieve data - ' . $ex->getMessage() );
		}

		ExceptionThrower::Stop();

		return $this->transform_data( $data, $feed_id );
	}


	/**
	 * Transform data into json format
	 *
	 * @param Object $data - Links Object
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($data, $feed_id) {
		// decode JSON
		$data = is_string($data) ? json_decode($data, TRUE) : array();

		return parent::transform_data($data, $feed_id);
	}

}
