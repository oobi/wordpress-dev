<?php

namespace FF\Midgard\Twitter;

use FF\Midgard\Midgard_Plugin_UI_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    midgard_twitter
 * @subpackage midgard_twitter/admin
 * @author     Firefly Interactive
 */
class Midgard_Twitter_UI extends Midgard_Plugin_UI_Base {

	public function __construct( $feed_type, $feed_type_label ) {
		parent::__construct($feed_type, $feed_type_label);
	}

	/**
	 * Create the HTML for the meta box.
	 *
	 * Called via main plugin hook 'midgard_after_feed_meta_box'
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	object		$object			The WordPress post object
	 * @param	array		$box			The metabox array containing data defined in add_meta_box
	 */
	public function add_feed_options($object, $box ) {
		// go no further if the current feed type does not match this plugin's defined type
		//$feed_type = esc_attr( get_post_meta( $object->ID, 'midgard_feed_type', true ) );
		//if($feed_type != $this->feed_type) return;


		// get current values
		$midgard_twitter_screen_name 	= esc_html( get_post_meta( $object->ID, 'midgard_twitter_screen_name', true ) );
		$midgard_twitter_tweet_count 	= intval( get_post_meta( $object->ID, 'midgard_twitter_tweet_count', true ) );
		$midgard_twitter_exclude_replies = get_post_meta( $object->ID, 'midgard_twitter_exclude_replies', true );
		$midgard_twitter_exclude_retweets = get_post_meta( $object->ID, 'midgard_twitter_exclude_retweets', true );
		$midgard_twitter_extended = get_post_meta( $object->ID, 'midgard_twitter_extended', true );

		// default to true
		if( $midgard_twitter_exclude_replies === '') {
			$midgard_twitter_exclude_replies = 1;
		}

		// default to false
		if( $midgard_twitter_exclude_retweets === '') {
			$midgard_twitter_exclude_retweets = 0;
		}

		// default to false
		if( $midgard_twitter_extended === '') {
			$midgard_twitter_extended = 0;
		}

		?>

		<div id="feed-section-<?php echo $this->feed_type; ?>" class="feed-section" data-hide-feed-uri="true">
			<p>
				<label for="midgard_twitter_screen_name"><?php _e( "Twitter Screen Name", 'midgard-twitter' ); ?><br>
					<input type="text" class="large-text" name="midgard_twitter_screen_name" id="midgard_twitter_screen_name" value="<?php echo $midgard_twitter_screen_name; ?>"/>
				</label>
			</p>
			<p>
				<label for="midgard_twitter_screen_name"><?php _e( "Number of Tweets", 'midgard-twitter' ); ?><br>
					<input type="number" class="medium-text" name="midgard_twitter_tweet_count" id="midgard_twitter_tweet_count" value="<?php echo $midgard_twitter_tweet_count; ?>"/>
				</label>
			</p>
			<p>
				<label><?php _e( "Exclude Replies", 'midgard-twitter' ); ?></label><br>
				<label>
					<input type="radio" name="midgard_twitter_exclude_replies" id="midgard_twitter_replies" value="1" <?php checked( $midgard_twitter_exclude_replies, '1', true ); ?> />
					Yes
				</label>
				&nbsp;&nbsp;
				<label>
					<input type="radio" name="midgard_twitter_exclude_replies" id="midgard_twitter_replies" value="0" <?php checked( $midgard_twitter_exclude_replies, '0', true ); ?> />
					No
				</label>
			</p>
			<p>
				<label><?php _e( "Exclude Retweets", 'midgard-twitter' ); ?></label><br>
				<label>
					<input type="radio" name="midgard_twitter_exclude_retweets" id="midgard_twitter_retweets" value="1" <?php checked( $midgard_twitter_exclude_retweets, '1', true ); ?> />
					Yes
				</label>
				&nbsp;&nbsp;
				<label>
					<input type="radio" name="midgard_twitter_exclude_retweets" id="midgard_twitter_retweets" value="0" <?php checked( $midgard_twitter_exclude_retweets, '0', true ); ?> />
					No
				</label>
			</p>
			<p>
				<label><?php _e( "Include Media and Full Text", 'midgard-twitter' ); ?></label><br>
				<label>
					<input type="radio" name="midgard_twitter_extended" id="midgard_twitter_extended" value="1" <?php checked( $midgard_twitter_extended, '1', true ); ?> />
					Yes
				</label>
				&nbsp;&nbsp;
				<label>
					<input type="radio" name="midgard_twitter_extended" id="midgard_twitter_extended" value="0" <?php checked( $midgard_twitter_extended, '0', true ); ?> />
					No
				</label>
			</p>
		</div>
		<?php
	}


	/**
	 * Save the data for the additional fields added
	 *
	 * Called via main plugin hook 'midgard_after_save_article_meta'
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	int			$post_id		ID of the article
	 * @param	object		$post			The WordPress post object
	 */
	public function save_feed_options($post_id, $post) {
		// Get the posted data and sanitize it for use as an HTML class.
		$midgard_twitter_screen_name 	= ( isset( $_POST['midgard_twitter_screen_name'] ) ? esc_html( $_POST['midgard_twitter_screen_name'] ) : '' );
		$midgard_twitter_tweet_count 	= ( isset( $_POST['midgard_twitter_tweet_count'] ) ? intval( $_POST['midgard_twitter_tweet_count'] ) : '' );
		$midgard_twitter_exclude_replies = ( isset( $_POST['midgard_twitter_exclude_replies'] ) ? intval( $_POST['midgard_twitter_exclude_replies'] ) : '0' );
		$midgard_twitter_exclude_retweets = ( isset( $_POST['midgard_twitter_exclude_retweets'] ) ? intval( $_POST['midgard_twitter_exclude_retweets'] ) : '0' );
		$midgard_twitter_extended = ( isset( $_POST['midgard_twitter_extended'] ) ? intval( $_POST['midgard_twitter_extended'] ) : '0' );

		// add/update or delete values accordingly for all settings
		Midgard_Common::save_meta_values($post_id, 'midgard_twitter_screen_name', $midgard_twitter_screen_name);
		Midgard_Common::save_meta_values($post_id, 'midgard_twitter_tweet_count', $midgard_twitter_tweet_count);
		Midgard_Common::save_meta_values($post_id, 'midgard_twitter_exclude_replies', $midgard_twitter_exclude_replies);
		Midgard_Common::save_meta_values($post_id, 'midgard_twitter_exclude_retweets', $midgard_twitter_exclude_retweets);
		Midgard_Common::save_meta_values($post_id, 'midgard_twitter_extended', $midgard_twitter_extended);
	}
}
