<?php

namespace FF\Midgard\Instagram;

use FF\Midgard\Midgard_Plugin_UI_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    midgard_instagram
 * @subpackage midgard_instagram/admin
 * @author     Firefly Interactive
 */
class Midgard_Instagram_UI extends Midgard_Plugin_UI_Base {

	public function __construct( $feed_type, $feed_type_label ) {
		parent::__construct( $feed_type, $feed_type_label );
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
	public function add_feed_options( $object, $box ) {
		// go no further if the current feed type does not match this plugin's defined type
		//$feed_type = esc_attr( get_post_meta( $object->ID, 'midgard_feed_type', true ) );
		//if($feed_type != $this->feed_type) return;

		// get current values
		$midgard_instagram_user_id 		= esc_html( get_post_meta( $object->ID, 'midgard_instagram_user_id', true ) );
		$midgard_instagram_post_count 	= intval( get_post_meta( $object->ID, 'midgard_instagram_post_count', true ) );
		?>

		<div id="feed-section-<?php echo $this->feed_type; ?>" class="feed-section" data-hide-feed-uri="true">
			<p>
				<label for="midgard_instagram_post_count"><?php _e( "Number of Posts", 'midgard-instagram' ); ?><br>
					<input type="number" class="medium-text" name="midgard_instagram_post_count" id="midgard_instagram_post_count" value="<?php echo $midgard_instagram_post_count; ?>"/>
				</label>
			</p>
			<p>
				Please note that the Instagram API is rate limited to 200 requests per hour. Each image in the return set counts as one request.
				Bear this in mind when setting caching limits and/or clearing the cache. You can see the current number of requests in your app by going to the
				Facebook Developer console, selecting your app and opening <code>Products->Instagram->Basic Display Rate Limiting</code>.
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
		$midgard_instagram_user_id 		= ( isset( $_POST['midgard_instagram_user_id'] ) ? esc_html( $_POST['midgard_instagram_user_id'] ) : '' );
		$midgard_instagram_post_count 	= ( isset( $_POST['midgard_instagram_post_count'] ) ? intval( $_POST['midgard_instagram_post_count'] ) : '' );

		// add/update or delete values accordingly for all settings
		Midgard_Common::save_meta_values( $post_id, 'midgard_instagram_user_id', $midgard_instagram_user_id );
		Midgard_Common::save_meta_values( $post_id, 'midgard_instagram_post_count', $midgard_instagram_post_count );
	}
}
