<?php

namespace FF\Midgard\Facebook;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;
use Facebook\Facebook;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard_facebook
 * @subpackage midgard_facebook/admin
 * @author     Firefly Interactive
 */
class Midgard_Facebook_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data( $feed_id ) {
		ExceptionThrower::Start();

		$settings = get_option('midgard_facebook_settings');
		if(!$settings) $settings = array();

		// OAuth settings - ensure all keys are present
		$auth = array_merge( array(
			'app_id' 		=> '',
			'app_secret' 	=> '',
			'access_token'	=> '',
		), $settings ) ;

		// set up connection
		$connection = new Facebook([
			'app_id' 				=> $auth['app_id'],
			'app_secret' 			=> $auth['app_secret'],
			'default_access_token' 	=> $auth['access_token'], // optional
		]);

		// get feed fields
		$page_id 		= get_post_meta( $feed_id, 'midgard_facebook_page_id', true );
		$post_count 	= get_post_meta( $feed_id, 'midgard_facebook_post_count', true );

		// execute call to Facebook API
		try {
			$fields = implode(',',[
				'created_time',
				'message',
				'status_type',
				'story',
				'icon',
				'permalink_url',
				'attachments{title,description}',
				//'media{source}',
				//'attachments{unshimmed_url}',
				//'attachments{media{source}}',
				'picture',
				'full_picture',
				'tags',
				'place'
			]);
			// source,description,type,place,created_time,message,message_tags,picture,full_picture,link,name,caption,shares
			$args = sprintf( '/%s?fields=posts.limit(%s){%s}',
							$page_id,
							$post_count,
							$fields );
			$response = $connection->get( $args );
			$data = $response->getDecodedBody();

			// just get the data we need
			$data = $data['posts']['data'];

		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			return $this->error_message('Facebook Graph API returned an error: ' . $e->getMessage() );
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			return $this->error_message('Facebook SDK returned an error: ' . $e->getMessage() );
		} catch(\Exception $ex) {
			return $this->error_message('Unable to retrieve data - ' . $ex->getMessage() );
		}

		ExceptionThrower::Stop();

		return $this->transform_data( $data, $feed_id );
	}


	/**
	 * Transform data into json format
	 *
	 * @param Object $data - Facebook data Object
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data($data, $feed_id) {
		// nothing to do
		return parent::transform_data($data, $feed_id);
	}

}
