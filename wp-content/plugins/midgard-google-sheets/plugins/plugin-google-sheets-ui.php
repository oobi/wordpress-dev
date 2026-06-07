<?php

namespace FF\Midgard\Sheets;

use FF\Midgard\Midgard_Plugin_UI_Base;
use FF\Midgard\Midgard_Common;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    midgard_rss
 * @subpackage midgard_rss/admin
 * @author     Firefly Interactive
 */
class Midgard_Google_Sheets_UI extends Midgard_Plugin_UI_Base {

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
		$midgard_google_sheets_id 		= esc_html(get_post_meta( $object->ID, 'midgard_google_sheets_id', true ) );
		$midgard_google_sheets_range 	= esc_html(get_post_meta( $object->ID, 'midgard_google_sheets_range', true ) );
		//$midgard_google_sheets_query 	= esc_html(get_post_meta( $object->ID, 'midgard_google_sheets_query', true ) );

		?>
		<div id="feed-section-<?php echo $this->feed_type; ?>" class="feed-section" data-hide-feed-uri="true">
			<p>
				<?php _e('Your spreadsheet ID can be found in the Google Sheets URL. For example:', 'midgard-google-sheets'); ?>
				<br>
				<code>https://docs.google.com/spreadsheets/d/<strong style="color:green">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</strong>/</code>
			</p>
			<p style="color:red; font-weight:bold">
				<?php _e('DO NOT paste the whole URL - just the ID portion highlighted above.', 'midgard-google-sheets'); ?>		
			</p>
			<p>
				<label for="midgard_google_sheets_id"><?php _e( "Google Sheet ID", 'midgard' ); ?><br>
					<input type="text" class="large-text" name="midgard_google_sheets_id" id="midgard_google_sheets_id" value="<?php echo $midgard_google_sheets_id; ?>"/>
				</label>
			</p>
			<p>
				<?php _e('The range defines the rows and columns you wish to retrieve from the spreadsheet. This works much like Excel. For example:', 'midgard-google-sheets'); ?>
				<br>
				<?php _e('<code>A1:C3</code> would retrieve the first three rows from columns A, B and C.', 'midgard-google-sheets'); ?>
				<br>
				<?php _e('<code>A1:C</code> would retrieve ALL rows from columns A, B and C.', 'midgard-google-sheets'); ?>
				<br>
				<?php _e('<code>Sales Data!A1:C</code> would retrieve ALL rows from columns A, B and C on the Sales Data tab (used for multi-sheet files).', 'midgard-google-sheets'); ?>
			</p>
			<p>
				<label for="midgard_google_sheets_range"><?php _e( "Data Range", 'midgard' ); ?><br>
					<input type="text" name="midgard_google_sheets_range" id="midgard_google_sheets_range" value="<?php echo $midgard_google_sheets_range; ?>"/>
				</label>
			</p>
			<?php /*
			<p>
				<?php _e('The Google Visualization API Query Language lets you perform various data manipulations with the query to the data source. You can leave this blank if you wnat the data returned unmodified.', 'midgard-google-sheets'); ?>
				<br>
				<a href="https://developers.google.com/chart/interactive/docs/querylanguage">Click here for more information</a>
			</p>
			<p>
				<label for="midgard_google_sheets_query"><?php _e( "Query (optional)", 'midgard' ); ?><br>
					<input type="text" name="midgard_google_sheets_query" id="midgard_google_sheets_query" value="<?php echo $midgard_google_sheets_query; ?>"/>
				</label>
			</p>
			*/ ?>
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
		$midgard_google_sheets_id 	 = ( isset( $_POST['midgard_google_sheets_id'] )    ? sanitize_text_field( $_POST['midgard_google_sheets_id'] ) : '' );
		$midgard_google_sheets_range = ( isset( $_POST['midgard_google_sheets_range'] ) ? sanitize_text_field( $_POST['midgard_google_sheets_range'] ) : '' );
		//$midgard_google_sheets_query = ( isset( $_POST['midgard_google_sheets_query'] ) ? sanitize_text_field( $_POST['midgard_google_sheets_query'] ) : '' );

		// add/update or delete values accordingly for all settings
		Midgard_Common::save_meta_values($post_id, 'midgard_google_sheets_id', $midgard_google_sheets_id);
		Midgard_Common::save_meta_values($post_id, 'midgard_google_sheets_range', $midgard_google_sheets_range);
		//Midgard_Common::save_meta_values($post_id, 'midgard_google_sheets_query', $midgard_google_sheets_query);
	}
}
