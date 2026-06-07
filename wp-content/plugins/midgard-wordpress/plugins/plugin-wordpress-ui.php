<?php

namespace FF\Midgard\WordPress;

use FF\Midgard\Midgard_Plugin_UI_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    midgard_wordpress
 * @subpackage midgard_wordpress/admin
 * @author     Firefly Interactive
 */
class Midgard_WordPress_UI extends Midgard_Plugin_UI_Base {

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

		// all instances
		$instances = Midgard_WordPress::getAuthTokens();

		// currently configured instance ID
		$wp_id = esc_html( get_post_meta( $object->ID, 'midgard-wordpress-id', true ) );
		?>

		<div id="feed-section-<?php echo $this->feed_type; ?>" class="feed-section">

			<p>
				<label><?php _e( "Auth Token Label", 'midgard-wordpress' ); ?></label><br>
				<?php if( $instances && is_array($instances) ) : ?>
					<select name="midgard-wordpress-id" class="regular-text">
						<option value=""><?php _e('No auth token', 'midgard-wordpress'); ?></option>
						<?php
							foreach($instances as $instance) {
								printf('<option value="%s" %s>%s</option>',
									$instance['id'],
									selected($instance['id'], $wp_id, true),
									$instance['label']
								);
							}
						?>
					</select>
				<?php endif; ?>

				<span class="description"><?php _e('Configure WordPress tokens in Midgard settings', 'midgard-wordpress'); ?></span>
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
		// Get the posted data and sanitize it
		$wp_id 	= ( isset( $_POST['midgard-wordpress-id'] ) ? wp_kses( $_POST['midgard-wordpress-id'],'' ) : '' );

		// add/update or delete values accordingly for all settings
		Midgard_Common::save_meta_values( $post_id, 'midgard-wordpress-id', $wp_id );

	}
}
