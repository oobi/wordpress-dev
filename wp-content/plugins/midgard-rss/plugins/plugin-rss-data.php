<?php

namespace FF\Midgard\RSS;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard_rss
 * @subpackage midgard_rss/admin
 * @author     Firefly Interactive
 */
class Midgard_RSS_Data extends Midgard_Plugin_Data_Base {

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
			$remote_args = Midgard_Common::wp_remote_get_params();
			$response = wp_remote_get($feed_url, $remote_args);
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
			return  $this->error_message('Unable to read RSS data');
		}

		ExceptionThrower::Stop();

		return $this->transform_data( $data, $feed_id );
	}


	/**
	 * Transform data into json format and generate an excerpt
	 *
	 * @param Object $data - XML Object
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($xml, $feed_id) {
		// convert XML to array
		$json = json_encode($xml);

		$array = json_decode($json,TRUE);

		// get the items
		$data = $array['channel']['item'];

		// set preferred excerpt length
		$excerpt_length = intval( get_post_meta( $feed_id, 'midgard_rss_excerpt_length', true ) );


		foreach( $data as $key => $d ) {

			// clean up description a little
			$desc = $d['description'];
			if(is_array($desc)) {
				if(!empty( $desc )) $desc = $desc[0];
				else 				$desc = '';
			}
			$desc = trim($desc);
			$data[$key]['description'] = $desc;

			// if excerpt length is greated than zero then generate the excerpt
			if( $excerpt_length > 0 ) {
				$stripped		= trim( strip_tags( $desc ) );
				$stripped 		= str_replace('&nbsp;', ' ', $stripped );

				if( strlen( $stripped ) > $excerpt_length ) {
					$pos 			= strpos($stripped, ' ', $excerpt_length);
					$excerpt		= substr( $stripped, 0, $pos );
					if(strlen( $stripped ) > strlen( $excerpt) ) {
						$excerpt .= '...';
					}
				} else {
					$excerpt = $stripped;
				}
				$data[$key]['midgard_excerpt'] = trim( $excerpt );
			}

			// force category to be array
			if( array_key_exists('category', $d) ) {
				if(is_string($d['category'])) {
					$data[$key]['category'] = array( $d['category'] );
				}
			} else {
				$data[$key]['category'] = array();
			}

		}

		return parent::transform_data($data, $feed_id);
	}

}
