<?php

namespace FF\Midgard\XML;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard_xml
 * @subpackage midgard_xml/admin
 * @author     Firefly Interactive
 */
class Midgard_XML_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data($feed_id) {

		// get the RSS feed URL
		$feed_url = get_post_meta( $feed_id, 'midgard_feed_uri', true );

		ExceptionThrower::Start();

		// retrieve raw data (XML)
		try{
			$args = Midgard_Common::wp_remote_get_params();
			$response = wp_remote_get($feed_url, $args );
			$rawdata = is_array($response) ? $response['body'] : '';
		} catch(\Exception $ex) {
			return $this->error_message('Unable to retrieve data - ' . $ex->getMessage() );
		}

		try{
			// remove colons from tags as simplexml_load_string will ignore them
			$modified = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $rawdata);

			// transform XML into object
			$data = simplexml_load_string( $modified, null, LIBXML_NOCDATA );
		} catch(\Exception $ex) {
			return  $this->error_message('Unable to read XML data');
		}

		ExceptionThrower::Stop();

		return $this->transform_data( $data, $feed_id );
	}


	/**
	 * Transform data into json format and (optionally) set root node
	 *
	 * @param Object $data - XML Object
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($data, $feed_id) {
		return parent::transform_data($data, $feed_id);
	}

}
