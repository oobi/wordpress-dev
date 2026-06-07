<?php
class ff_twitter_widget extends WP_Widget
{
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function ff_twitter_widget() {
		$widget_ops = array('classname' => 'tweets-widget', 'description' => 'Twitter Widget feeds' );
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('ff_twitter_widget', __('Firefly : Twitter Widget'), $widget_ops, $control_ops);
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		$username = isset( $instance['username'] ) ? esc_attr( $instance['username'] ) : '';
		$numTweets = isset( $instance['numTweets'] ) ? esc_attr( $instance['numTweets'] ) : '';
	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<span>Widget Title</span><br />
				<input class="upcoming" id="<?php echo $this->get_field_id('title'); ?>" size="40" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</label>
		</p><br />
		<div class="clear"></div><br />
		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>">
				<span>Twitter Username </span><br />
				<small>http://www.twitter.com/your-username  - Only {your-username}</small>
				<input class="upcoming" id="<?php echo $this->get_field_id('username'); ?>" size="40" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($username); ?>" />
			</label>
		</p><br />
		<div class="clear"></div><br />
		<p>
			<label for="<?php echo $this->get_field_id('numTweets'); ?>">
				<span>Num of Tweets: </span>
				<input class="upcoming" id="<?php echo $this->get_field_id('numTweets'); ?>" size="2" name="<?php echo $this->get_field_name('numTweets'); ?>" type="text" value="<?php echo esc_attr($numTweets); ?>" />
			</label>
		</p>
		<div class="clear"></div>

	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['username'] = $new_instance['username'];
		$instance['numTweets'] = $new_instance['numTweets'];
		return $instance;
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);

		/* REQUIRES: TwitterOAuth
		 *	https://github.com/abraham/twitteroauth/tree/master/twitteroauth
		 *
		 *	Download and place in a /twitteroauth/ folder in your theme/plugin.
		 *
		 *
		 * Full guide here: http://www.problogdesign.com/wordpress/authenticate-your-twitter-api-calls-before-march/
		 *
		 * Uses:
		 * Twitter API call:
		 *	 http://dev.twitter.com/doc/get/statuses/user_timeline
		 * WP transient API.
		 *		http://www.problogdesign.com/wordpress/use-the-transients-api-to-list-the-latest-commenter/
		 */

		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);					// Title displayed on widget
		$name = empty($instance['username']) ? ' ' : apply_filters('widget_title', $instance['username']);				// Username to display tweets from.
		$numTweets = empty($instance['numTweets']) ? ' ' : apply_filters('widget_title', $instance['numTweets']); 		// Number of tweets to display.
		if(!is_numeric($numTweets)){$numTweets = 1;}

		$excludeReplies = true; 			// Leave out @replies
		$transName	  = 'list-tweets';		// Name of value in database.
		$cacheTime	  = 15;					// Time in minutes between updates.
		$backupName = $transName . '-backup';

		// clear cache for testing
		//delete_transient($transName);

		// display title and widget wrapper opening
		echo $before_widget;
		echo $before_title .$title. $after_title;

		// Do we already have saved tweet data? If not, lets get it.
		if(false === ($tweets = get_transient($transName) ) ) :

		  // Get the tweets from Twitter.
		  include get_stylesheet_directory().'/inc/twitteroauth/twitteroauth.php';

		  $connection = new TwitterOAuth(
			'sxaHPsqldLLwUawLPHABvJtom',		// Consumer key
			'HiCDUVdTKe3VL3J9jN3Z25dAQEYiMhBUTd06CzZwQuyJwXH4Xj',		// Consumer secret
			'119211745-lEIkAaXTeRUlw12hIHBWUhbKwdnVFcwM8Tfp4Uwe',		// Access token
			'm92TTpe6vElpSyVqwOIR9gtu7Q1FnYYo0aFxMskoqx7dK'		// Access token secret
		  );

		  // If excluding replies, we need to fetch more than requested as the
		  // total is fetched first, and then replies removed.
		  $totalToFetch = ($excludeReplies) ? max(50, $numTweets * 3) : $numTweets;

		  $fetchedTweets = $connection->get(
			'statuses/user_timeline',
			array(
			  'screen_name'	 => $name,
			  'count'		   => $totalToFetch,
			  'exclude_replies' => $excludeReplies
			)
		  );

		  // Did the fetch fail?
		  if($connection->http_code != 200) :
			$tweets = get_option($backupName); // False if there has never been data saved.

		  else :
			// Fetch succeeded.
			// Now update the array to store just what we need.
			// (Done here instead of PHP doing this for every page load)
			$limitToDisplay = min($numTweets, count($fetchedTweets));

			for($i = 0; $i < $limitToDisplay; $i++) :
			  $tweet = $fetchedTweets[$i];

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

			endfor;

			// Save our new transient, and update the backup.
			set_transient($transName, $tweets, 60 * $cacheTime);
			update_option($backupName, $tweets);
		  endif;
		endif;

		// Now display the tweets.
		?>

		<div id="twitterfeed">
		<ul class="fa-ul">
		<?php if(is_array($tweets)) : foreach($tweets as $t) : ?>
			<li>
				<i class="fa-li fa fa-commenting"></i>
				<?php echo $t['text']; ?>
				<em>
					<?php printf('<a href="%s" target="_blank">%s ago</a> &middot; ', $t['permalink'], human_time_diff($t['time'], current_time('timestamp'))); ?>
				</em>
			</li>
		<?php endforeach; endif; ?>
		</ul>
		</div>

		<?php

			// widget wrapper closing
			echo $after_widget;

		}
}
