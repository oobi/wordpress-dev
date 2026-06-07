<?php

namespace FF\Twitter;

use \Abraham\TwitterOAuth\TwitterOAuth;

class TwitterRest {

	public $transientKey = FF_TWITTER_TRANSIENT;

	/**
	 * Start up
	 */
	public function __construct() {

	}

	/**
	 * Init Routes
	 */
	public function register_routes() {
		register_rest_route( 'ff-twitter/v1', '/list/(?P<num>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_tweets'),
			'args' => array(
				'num' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param ) && (int) $param;
					}
				),
			)
		) );

	}

	/**
	 * REST - list weeets
	 */
	public function list_tweets( $args ) {
		// number of tweets to retrieve
		$numTweets  = $args['num'] ?? 10;
				//
		$excludeReplies = true; 						// Leave out @replies
		$transName	  	= $this->transientKey . '-' . $numTweets;			// Name of value in database.
		$backupName 	= $transName . '-backup';		// backup value key


		// Do we already have saved tweet data? If not, lets get it.
		if(false === ($tweets = get_transient($transName) ) ) {
			// if we got in here then we're fetching from source

			$opt = array_merge(
				['consumer_key'=>'', 'consumer_secret'=>'', 'access_token'=>'', 'access_token_secret'=>'', 'twitter_handle'=>'', 'cache_time'=>900],
				get_option( 'ff-twitter-settings' ) ?? []
			);

			// make the connection
			$connection = new TwitterOAuth(
				$opt['consumer_key'],			// Consumer key
				$opt['consumer_secret'],		// Consumer secret
				$opt['access_token'],			// Access token
				$opt['access_token_secret']		// Access token secret
			);

			// decide how many to fetch
			// replies take up a space so we need to fetch more than we need if we are excluding them
			$totalToFetch = ($excludeReplies) ? max(50, $numTweets * 3) : $numTweets;

			$fetchedTweets = $connection->get(
				'statuses/user_timeline',
				array(
					'screen_name'	 	=> $opt['twitter_handle'],
					'count'		   		=> $totalToFetch,
					'exclude_replies' 	=> $excludeReplies
				)
			);

			$code = $connection->getLastHttpCode();

			 // Did the fetch fail?
			if($code != 200) {
				$tweets = get_option($backupName); // False if there has never been data saved.
			}
			// Fetch succeeded.
			else {
				// slice out the number we actually want
				$fetchedTweets = array_slice( $fetchedTweets, 0, $numTweets);

				foreach( $fetchedTweets as $tweet ) {
					// Core info.
					$name = $tweet->user->screen_name;
					$permalink = 'http://twitter.com/'. $name .'/status/'. $tweet->id_str;

					/* Alternative image sizes method: http://dev.twitter.com/doc/get/users/profile_image/:screen_name */
					$image = $tweet->user->profile_image_url;

					// Message. Convert links to real links.
					$pattern = '/http:(\S)+/';
					$replace = '<a href="${0}" target="_blank" rel="nofollow">${0}</a>';
					$text = preg_replace($pattern, $replace, $tweet->text);

					// Need to get time in Unix format.
					$time = $tweet->created_at;
					$time = date_parse($time);
					$uTime = mktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']);

					// Now make the new array.
					$tweets[] = array(
						'text' => $text,
						'name' => $name,
						'permalink' => $permalink,
						'image' => $image,
						'time' => $uTime
					);
				}

				// Save our new transient, and update the backup.
				set_transient($transName, $tweets, $opt['cache_time']);

				if(get_option($backupName)){
					update_option($backupName, $tweets);
				} else {
					add_option($backupName, $tweets);
			   	}

			}
		}

		return $tweets;
	}

}