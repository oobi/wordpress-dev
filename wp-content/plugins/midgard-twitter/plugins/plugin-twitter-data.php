<?php

namespace FF\Midgard\Twitter;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard_twitter
 * @subpackage midgard_twitter/admin
 * @author     Firefly Interactive
 */
class Midgard_Twitter_Data extends Midgard_Plugin_Data_Base {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data($feed_id) {
		ExceptionThrower::Start();

		$settings = get_option( 'midgard_twitter_settings' );
		if(!$settings) $settings = array();

		// OAuth settings - ensure all keys are present
		$auth = array_merge( array(
			'consumer_key' 		=> '',
			'consumer_secret' 	=> '',
			'access_token' 		=> '',
			'access_token_secret' => ''
		), $settings ) ;

		// create Twitter OAuth connection object
		$connection = new TwitterOAuth( $auth['consumer_key'], $auth['consumer_secret'], $auth['access_token'], $auth['access_token_secret'] );

		// assemble query parameters from feed settings
		$screen_name 		= get_post_meta( $feed_id, 'midgard_twitter_screen_name', true );
		$tweet_count 		= intval( get_post_meta( $feed_id, 'midgard_twitter_tweet_count', true ) );
		$exclude_replies 	= intval( get_post_meta( $feed_id, 'midgard_twitter_exclude_replies', true ) );

		// This setting is saved as "EXCLUDE retweets" rather than "INCLUDE retweets" in the front end to match the "exlude replies" setting
		// "exclude retweets" value now needs to be flipped to become "include retweets" for the Twitter API
		$include_retweets 	= intval(get_post_meta( $feed_id, 'midgard_twitter_exclude_retweets', true )) ? 0 : 1;

		// This setting is saved as "EXCLUDE retweets" rather than "INCLUDE retweets" in the front end to match the "exlude replies" setting
		// "exclude retweets" value now needs to be flipped to become "include retweets" for the Twitter API
		$include_media 	= intval(get_post_meta( $feed_id, 'midgard_twitter_exclude_retweets', true )) ? 0 : 1;

		// 'Extended mode' - includes full tweet text and media
		$extended 	= intval(get_post_meta( $feed_id, 'midgard_twitter_extended', true )) ? 1 : 0;

		// increase the specified tweet count (up to max 200)
		// this is necessary due to the way Twitter API gets tweets. 'count' is applied first, then replies/retweets are subtracted
		// we will artifically grab more tweets, then manually restrict to the count value later
		$increased_count 	= ( $tweet_count + 50 ) <= 200 ? $tweet_count + 50 : 200;

		$params = array(
			'screen_name'		=> empty( $screen_name ) ? '???nobody???' : $screen_name, // default to non-existent user
			'count'				=> $increased_count,
			'exclude_replies' 	=> $exclude_replies,
			'include_retweets' 	=> $include_retweets
		);

		if( $extended ) {
			$params['tweet_mode'] = 'extended';
		}

		// execute call to Twitter API
		try {
			$data = $connection->get( 'statuses/user_timeline', $params );
		} catch(\Exception $ex) {
			return $this->error_message('Unable to retrieve data - ' . $ex->getMessage() );
		}

		// check for errors in return JSON
		if( isset($data->errors) ) {
			foreach($data->errors as $e) {
				return $this->error_message( $e->message );
			}
		}

		ExceptionThrower::Stop();

		// reduce the data to specified count limit
		// see comment above $increased_count definition for more info
		if( !empty( $data ) && !empty( $tweet_count ) ) {
			$data = array_slice( $data, 0, $tweet_count );
		}

		return $this->transform_data( $data, $feed_id );
	}


	/**
	 * Transform data into json format
	 *
	 * @param Object $data - Twitter data Object
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data( $data, $feed_id ) {
		// nothing to do
		return parent::transform_data( $data, $feed_id );
	}

}
