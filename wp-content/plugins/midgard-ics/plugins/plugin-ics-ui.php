<?php

namespace FF\Midgard\ICS;

use FF\Midgard\Midgard_Plugin_UI_Base;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    midgard_ics
 * @subpackage midgard_ics/admin
 * @author     Firefly Interactive
 */
class Midgard_ICS_UI extends Midgard_Plugin_UI_Base {

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
		?>
		<div id="feed-section-<?php echo $this->feed_type; ?>" class="feed-section">
			
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

	}
}
