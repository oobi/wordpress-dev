<?php

namespace FF\Midgard\Sheets;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard_rss
 * @subpackage midgard_rss/admin
 * @author     Firefly Interactive
 */
class Midgard_Google_Sheets_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data($feed_id) {
		// spreadsheet query params
		$spreadsheet_id 	= get_post_meta( $feed_id, 'midgard_google_sheets_id', true );
		$spreadsheet_range 	= get_post_meta( $feed_id, 'midgard_google_sheets_range', true );

		// google auth params
		$settings 		= get_option( 'midgard_google_sheets_settings' );

		$access_token 	= $settings['credentials']; 
		$client_secret 	= $settings['client_secret_path'];
		// init data set
		$rawdata = array();

		ExceptionThrower::Start();
		try{
			if(empty($access_token)) {
				throw new \Exception('Empty access token - please update credentials');
			}

			// get a new Google client instance
			$client = Midgard_Google_Client::get_client($client_secret);			
			// Set the client access token
			$client->setAccessToken(json_encode($access_token));
			// Create a new service instance
			$service = new \Google_Service_Sheets($client);
			// request the data
			$response = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);
			$rawdata = $response->getValues();

		} catch(\Exception $ex) {
			return $this->error_message('Unable to retrieve data ' . $ex->getMessage() );
		}

		ExceptionThrower::Stop();
		return $this->transform_data( $rawdata, $feed_id );
	}


	/**
	 * Transform data into associative arrays so we can use mappings
	 * Columns will ne numbered sequentially - c0..cN
	 *
	 * @param Object $data - JSON Data
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($data, $feed_id) {
		$newdata = array();
		foreach($data as $row) {
			$item = array();
			foreach($row as $index=>$col) {
				$item['c' . $index]  = trim($col);
			}
			$newdata[] = $item;
		}

		return parent::transform_data($newdata, $feed_id);
	}

}
