<?php

namespace FF\Midgard;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * This class is intended to be extended by sub-plugins
 *
 * @package    midgard
 * @subpackage midgard/base
 * @author     Firefly Interactive
 */
class Midgard_Plugin_UI_Base {

	/**
	 * String given to the feed type described by this plugin
	 */
	protected $feed_type;
	protected $feed_type_label;

	/**
	 * Constructor - set some convenience values
	 *
	 * @param	string		$feed_type			feed type string
	 * @param	string		$feed_type_label	feed type label string
	 */
	public function __construct($feed_type, $feed_type_label) {
		$this->feed_type 		= $feed_type;
		$this->feed_type_label  = $feed_type_label;
	}

	/**
	 * Add an option to the feed type meta box
	 *
	 * Called by hook from within the master plugin UI - data_feed_child_meta_box(...)
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	object		$object			The WordPress post object
	 * @param	array		$box			The metabox array containing data defined in add_meta_box
	 */
	public function add_feed_type_options($object, $box) {
		$feed_type = esc_attr( get_post_meta( $object->ID, 'midgard_feed_type', true ) );
		printf('<option value="%s" %s>%s</option>',
				$this->feed_type,
				$feed_type === $this->feed_type ? 'selected="selected"' : '',
				$this->feed_type_label);
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


		/*// get current url value
		$ics_url = esc_url( get_post_meta( $object->ID, 'midgard_ics_url', true ) );
		?>
		<div id="feed-section-<?php echo $this->feed_type; ?>" class="feed-section">
			<p>
				<label for="midgard-ics-url"><?php _e( "ICS URL", 'midgard' ); ?><br>
					<input type="url" name="midgard-ics-url" id="midgard-ics-url" class="large-text" value="<?php echo $ics_url; ?>"/>
				</label>
			</p>
		</div>
		<?php */
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

		// // Get the posted data and sanitize it for use as an HTML class.
		// $new_meta_value_ics_url 	= ( isset( $_POST['midgard-ics-url'] ) ? esc_url_raw( $_POST['midgard-ics-url'] ) : '' );

		// // add/update or delete values accordingly for all settings
		// Midgard_Common::save_meta_values($post_id, 'midgard_ics_url', 	$new_meta_value_ics_url);
	}
}
