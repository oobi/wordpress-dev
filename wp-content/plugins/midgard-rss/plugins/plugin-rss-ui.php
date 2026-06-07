<?php

namespace FF\Midgard\RSS;

use FF\Midgard\Midgard_Plugin_UI_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    midgard_rss
 * @subpackage midgard_rss/admin
 * @author     Firefly Interactive
 */
class Midgard_RSS_UI extends Midgard_Plugin_UI_Base {

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
		$excerpt_length 	= intval( get_post_meta( $object->ID, 'midgard_rss_excerpt_length', true ) );
		?>
		<div id="feed-section-<?php echo $this->feed_type; ?>" class="feed-section">
			<p>
				<?php _e('Optionally specify the length of the excerpt (article summary). If specified, an excerpt of that length will be generated from each 
			    item description and included in the resultant data using the key <code>midgard_excerpt</code>. Set to zero to omit the excerpt altogether.', 'midgard-rss'); ?>
			</p>
			<p>
				<label for="midgard-rss-excerpt-length"><?php _e( "Excerpt Length", 'midgard-rss' ); ?><br>
					<input type="text" name="midgard-rss-excerpt-length" id="midgard-rss-excerpt-length" value="<?php echo $excerpt_length; ?>"/>
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
		$new_excerpt_length 	= ( isset( $_POST['midgard-rss-excerpt-length'] ) ? intval( $_POST['midgard-rss-excerpt-length'] ) : '' );
		
		// add/update or delete values accordingly for all settings
		Midgard_Common::save_meta_values($post_id, 'midgard_rss_excerpt_length', $new_excerpt_length);
	}
}
