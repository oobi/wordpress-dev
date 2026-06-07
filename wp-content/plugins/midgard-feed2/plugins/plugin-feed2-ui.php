<?php

namespace FF\Midgard\Feed2;

use FF\Midgard\Midgard_Plugin_UI_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    Midgard_Feed2
 * @subpackage Midgard_Feed2/admin
 * @author     Firefly Interactive
 */
class Midgard_Feed2_UI extends Midgard_Plugin_UI_Base {

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

		?>
		<div id="feed-section-<?php echo $this->feed_type; ?>" class="feed-section" data-hide-feed-uri="true">
			<p>
				<?php _e('Add links to your feeds below.', 'midgard-feed2'); ?>
			</p>

			<table class="widefat midgard-feed2-table">
				<thead>
					<tr>
						<th width="20%"><?php _e( 'Key', 'midgard-feed2' ); ?></th>
						<th><?php esc_attr_e( 'Feed URL', 'midgard-feed2' ); ?></th>
						<th width="20%"><?php esc_attr_e( 'Root Node', 'midgard-feed2' ); ?></th>
						<th width="30">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$feed_meta = get_post_meta($object->ID, 'midgard_feed2', true);
					// we have to strip out the escaped slashes on single quotes or it won't decode
					$feed2 			= json_decode( str_replace( "\'", "'" ,  $feed_meta), true );
					$feed2_is_array = intval( get_post_meta($object->ID, 'midgard_feed2_is_array', true) );

					if( $feed2 && is_array($feed2)) {
						foreach( $feed2 as $index=>$feed) {
							$feed = array_merge( array(
								'url'	=> '',
								'key'	=> '',
								'root'	=> ''
							), $feed);

							printf('<tr class="%s">', $index % 2 == 1 ? 'alternate' : '');
							printf( '<td><input type="text" class="large-text midgard-feed2-title" name="midgard-feed2[%s][key]" value="%s" placeholder="Page Title" required></td>', $index, $feed['key'] );
							printf( '<td><input type="text" class="large-text midgard-feed2-url" name="midgard-feed2[%s][url]" value="%s" placeholder="Page URL" required></td>', $index, $feed['url'] );
							printf( '<td><input type="text" class="large-text midgard-feed2-root" name="midgard-feed2[%s][root]" value="%s" placeholder="JSONPath Expression"></td>', $index, $feed['root'] );
							printf( '<td><a class="midgard-mapping-delete dashicons dashicons-trash" href="#" ></a></td>');
							echo '</tr>';
						}
					}
					?>

				</tbody>
			</table>

			<p>
				<?php _e('Select your preferred data format for the combined feed.', 'midgard-feed2'); ?>
			</p>
			<p>
				<label for="midgard-feed2-is-array0">
					<input type="radio" id="midgard-feed2-is-array0" name="midgard-feed2-is-array" value="0"  <?php checked('0', $feed2_is_array, true ); ?> >
					<?php _e('Associative array (default)', 'midgard-feed2'); ?>
				</label>
				&nbsp;&nbsp;
				<label for="midgard-feed2-is-array1">
					<input type="radio" id="midgard-feed2-is-array1" name="midgard-feed2-is-array" value="1"  <?php checked('1', $feed2_is_array, true ); ?> >
					<?php _e('Sequential array', 'midgard-feed2'); ?>
				</label>
			</p>

			<p>
				<input class="button-primary midgard-feed2-add" type="button" value="<?php esc_attr_e( 'Add new feed' ); ?>" />
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
		// note - we use json encoding so that quotes etc don't get messed up (otherwise we get errors deserializing)
		$new_feed2 		= ( isset( $_POST['midgard-feed2'] ) ? json_encode( $_POST['midgard-feed2'] ) : '' );
		$new_is_array 	= ( isset( $_POST['midgard-feed2-is-array'] ) ? intval( $_POST['midgard-feed2-is-array'] ) : '' );

		// add/update or delete values accordingly for all settings
		Midgard_Common::save_meta_values($post_id, 'midgard_feed2', 	$new_feed2);
		Midgard_Common::save_meta_values($post_id, 'midgard_feed2_is_array', 	$new_is_array);
	}

}
