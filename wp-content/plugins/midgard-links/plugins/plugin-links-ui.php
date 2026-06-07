<?php

namespace FF\Midgard\Links;

use FF\Midgard\Midgard_Plugin_UI_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    Midgard_Links
 * @subpackage Midgard_Links/admin
 * @author     Firefly Interactive
 */
class Midgard_Links_UI extends Midgard_Plugin_UI_Base {

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
				<?php _e('Add your link URLs below.', 'midgard-links'); ?>
			</p>

			<table class="widefat midgard-links-table">
				<thead>
					<tr>
						<th width="40%"><?php _e( 'Title', 'midgard-links' ); ?></th>
						<th><?php esc_attr_e( 'URL', 'midgard-links' ); ?></th>
						<th width="30">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$link_meta = get_post_meta($object->ID, 'midgard_links', true);
					// we have to strip out the escaped slashes on single quotes or it won't decode
					$links = json_decode( str_replace( "\'", "'" ,  $link_meta), true );

					if( $links && is_array($links)) {
						foreach( $links as $index=>$link) {
							$link = array_merge( array(
								'url'	=> '',
								'title'	=> '',
								'desc'	=> ''
							), $link);

							printf('<tr class="%s r1">', $index % 2 == 1 ? 'alternate' : '');
							printf( '<td><input type="text" class="large-text midgard-links-title" name="midgard-links[%s][title]" value="%s" placeholder="Page Title" required></td>', $index, $link['title'] );
							printf( '<td><input type="text" class="large-text midgard-links-url" name="midgard-links[%s][url]" value="%s" placeholder="Page URL" required></td>', $index, $link['url'] );
							printf( '<td><a class="midgard-links-delete dashicons dashicons-trash" href="#" ></a></td>');
							echo '</tr>';

							printf('<tr class="%s r2">', $index % 2 == 1 ? 'alternate' : '');
							printf( '<td colspan="2"><textarea class="large-text midgard-links-desc" name="midgard-links[%s][desc]" placeholder="Description">%s</textarea></td>', $index, $link['desc']);
							printf( '<td>&nbsp;</td>');
							echo '</tr>';

							printf('<tr class="%s r3">', $index % 2 == 1 ? 'alternate' : '');
							printf( '<td colspan="3"><input type="checkbox" id="midgard-links[%1$s][external]" class="midgard-links-external" name="midgard-links[%1$s][external]" value="1" %2$s><label for="midgard-links[%1$s][external]">Open link in external browser?</label></td>',
									$index,
									isset( $link['external'] ) && $link['external'] ? 'checked' : '' );
							echo '</tr>';
						}
					}
					?>

				</tbody>
			</table>

			<p>
				<input class="button-primary midgard-links-add" type="button" value="<?php esc_attr_e( 'Add new link' ); ?>" />
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
	public function save_feed_options( $post_id, $post ) {

		$links = array();
		$new_links = '';

		if( isset( $_POST['midgard-links'] )) {

			// ensure external flag is always present and converted to boolean
			foreach( $_POST['midgard-links'] as $link ) {
				if( isset( $link['external'] ) ) {
					$link['external'] = true;
				} else {
					$link['external'] = false;
				}
				$links[] = $link;
			}

			// Get the posted data and sanitize it for use as an HTML class.
			// note - we use json encoding so that quotes etc don't get messed up (otherwise we get errors deserializing)
			$new_links 	= json_encode( $links );
		}

		// add/update or delete values accordingly for all settings
		Midgard_Common::save_meta_values($post_id, 'midgard_links', $new_links);
	}

}
