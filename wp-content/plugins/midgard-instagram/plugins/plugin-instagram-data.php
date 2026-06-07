<?php

namespace FF\Midgard\Instagram;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard_instagram
 * @subpackage midgard_instagram/admin
 * @author     Firefly Interactive
 */
class Midgard_Instagram_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data( $feed_id ) {
		ExceptionThrower::Start();

		$settings = get_option('midgard_instagram_settings');
		if(!$settings) $settings = array();

		// OAuth settings - ensure all keys are present
		$auth = array_merge( array(
			'access_token'	=> ''
		), $settings ) ;

		// assemble query parameters from feed settings
		$token 			= $settings['access_token'] ?? '';
		$user_id 		= $settings['user_id'] ?? '';
		$post_count 	= intval( get_post_meta( $feed_id, 'midgard_instagram_post_count', true ) );

		// get the recent media
		try {

			$data = InstagramAPI::get_media($token, $user_id, $post_count);
			//$data = Remote::call_remote( 'users/' . $user_id . '/media/recent', array( 'count' => $post_count ) );

			// if data is false, user_id is invalid - throw an exception
			if( $data === false ) {
				trigger_error('User ID invalid');
			}
		} catch(\Exception $ex) {
			return $this->error_message( $ex->getMessage() );
		}

		ExceptionThrower::Stop();

		return $this->transform_data( $data, $feed_id );
	}


	/**
	 * Transform data into json format
	 *
	 * @param Object $data - Instagram data Object
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($data, $feed_id) {
		// nothing to do
		return parent::transform_data($data, $feed_id);
	}

}
