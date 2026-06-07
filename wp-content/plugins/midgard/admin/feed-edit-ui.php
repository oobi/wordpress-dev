<?php

namespace FF\Midgard;

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    midgard
 * @subpackage midgard/admin
 * @author     Firefly Interactive
 */
class Midgard_Feed_Edit_UI {

	/**
	 * The default cache time (in seconds)
	 */
	private $default_cache_time;

	/**
	 * Constructor - set some values
	 */
	public function __construct() {
		$this->default_cache_time	= 100;
	}

	/**************************************************************************************
	 * CUSTOM POST TYPE METABOXES
	 **************************************************************************************/

	/**
	 * Remove default category metabox and add custom metaboxes
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function manage_custom_metaboxes() {

		// Add feed details meta box
		add_meta_box(
			'data-feed-details-meta-box',      				// Unique ID
			esc_html__( 'Feed Details' ), 					// Title
			array( $this, 'feed_details_meta_box' ), 		// Callback function - create the HTML for the meta box
			'data_feed',         							// Admin page (or post type)
			'normal',         								// Context
			'default'         								// Priority
		);

		// Add mappings meta box
		add_meta_box(
			'data-feed-mapping-meta-box',      				// Unique ID
			esc_html__( 'Mapping' ), 						// Title
			array( $this, 'feed_mapping_meta_box' ), 		// Callback function - create the HTML for the meta box
			'data_feed',         							// Admin page (or post type)
			'normal',         								// Context
			'default'         								// Priority
		);

		// import / export mappings meta box
		// Add mappings meta box
		add_meta_box(
			'data-feed-mapping-export-meta-box',      		// Unique ID
			esc_html__( 'Mapping Import/Export' ), 		// Title
			array( $this, 'feed_mapping_import_meta_box' ), // Callback function - create the HTML for the meta box
			'data_feed',         							// Admin page (or post type)
			'normal',         								// Context
			'default'         								// Priority
		);
	}

	/**
	 * Create the HTML for the details meta box.
	 *
	 * Callback for add_meta_box() function in manage_custom_metaboxes()
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	object		$object			The WordPress post object
	 * @param	array		$box			The metabox array containing data defined in add_meta_box
	 */
	public function feed_details_meta_box( $object, $box ) {
		wp_nonce_field( basename( __FILE__ ), 'midgard_nonce' ); ?>

		<?php // Feed REST URI
			if(!empty($object->post_name)) :
			$preview_uri = get_post_permalink($object->ID);
			$feed_uri = Midgard_REST_Controller::get_feed_uri($object->ID);
		?>
		<p>
			<label><?php _e( "Preview", 'midgard' ); ?><br>
				<?php
					printf('<a class="button-primary" href="%1$s" target="_blank">View JSON</a>', $preview_uri);
					echo '&nbsp;';
					printf('<a class="button-secondary" href="%1$s?nomap=1" target="_blank">Ignore Mapping (DEBUG)</a>', $preview_uri);					
				?>
			</label>
		</p>
		<p>
			<label><?php _e( "REST URI (for use in app only)", 'midgard' ); ?><br>
				<?php 
					printf('<code>%1$s</code>', $feed_uri);
				?>
			</label>
		</p>
		<?php endif; ?>

		<?php // Feed Type
			$feed_type = esc_attr( get_post_meta( $object->ID, 'midgard_feed_type', true ) );
		?>
		<p>
			<label for="midgard-feed-type"><?php _e( "Feed type", 'midgard' ); ?><br>
				<select id="midgard-feed-type" name="midgard-feed-type" data-feedtype="<?php echo $feed_type; ?>">
					<?php
						// other plugins may add additional types here via a hook
						do_action('midgard_feed_type_option', $object, $box );
					?>
				</select>
			</label>
		</p>

		<!-- The Midgard Feed URI may be hidden by a child plugin - it is identified here for this purpose
		so that the JavaScript can get a handle to it. -->
		<p id="midgard-feed-uri-wrapper">
			<?php // Feed URI
			$feed_uri = esc_url( get_post_meta( $object->ID, 'midgard_feed_uri', true ) );
			?>
			<label for="midgard-feed-uri"><?php _e( "Feed URI", 'midgard' ); ?><br>
				<input type="text" class="large-text" name="midgard-feed-uri" id="midgard-feed-uri" value="<?php echo $feed_uri; ?>"/>
			</label>
		</p>

		<?php // get cache time from database
		$cache_time = intval( get_post_meta( $object->ID, 'midgard_cache_time', true ) );

		// if cache time not set (new post), set to default value (from settings)
		empty( $cache_time ) ? $cache_time = Midgard::get_default_cache_time() : null;
		?>
		<p>
			<label for="midgard-cache-time"><?php _e( "Cache time", 'midgard' ); ?><br>
				<input type="number" name="midgard-cache-time" id="midgard-cache-time" value="<?php echo $cache_time; ?>"/>
				<?php _e('seconds', 'midgard'); ?>
			</label>
		</p>

		<div class="feed-section error">
			<p>This feed was created with a plugin (<?php echo $feed_type; ?>) that is no longer active.</p>
			<p>Please reactivate the plugin or reconfigure this feed as a new type.</p>
		</div>

		<?php
			// call any addon actions
			do_action('midgard_after_feed_meta_box', $object, $box );
		?>

	<?php }

	/**
	 * Create the HTML for the mappings meta box.
	 *
	 * Callback for add_meta_box() function in manage_custom_metaboxes()
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	object		$object			The WordPress post object
	 * @param	array		$box			The metabox array containing data defined in add_meta_box
	 */
	public function feed_mapping_meta_box( $object, $box ) { ?>

		<!-- MAPPING MODES -->
		<p><?php _e('You may optionally create a map to control how the output is formatted.', 'midgard'); ?></p>
		<p>
			<?php
				$mode = get_post_meta( $object->ID, 'midgard_mapping_mode', true );
			?>
			<label for="mapping-mode-none">
				<input id="mapping-mode-none" type="radio" name="midgard-mapping-mode" value="" <?php checked( $mode, '' ); ?> >
				<?php _e('None', 'midgard'); ?>
			</label>
			&nbsp;&nbsp;
			<label for="mapping-mode-simple">
				<input id="mapping-mode-simple"  type="radio" name="midgard-mapping-mode" value="simple" <?php checked( $mode, 'simple' ); ?> >
				<?php _e('Simple', 'midgard'); ?>
			</label>
			&nbsp;&nbsp;
			<label for="mapping-mode-advanced">
				<input id="mapping-mode-advanced" type="radio" name="midgard-mapping-mode" value="advanced" <?php checked( $mode, 'advanced' ); ?> >
				<?php _e('Advanced', 'midgard'); ?>
			</label>
		</p>

		<!-- SIMPLE MAPS -->
		<div class="midgard-map-mode" data-mode="simple">
			<!-- dive in deeper for root node -->
			<div id="feed-section-root">

				<p>
					<?php _e('If the data you are interested in is nested deeper into the JSON structure you can specify node you wish
					to use as the root here. The node path you specify (in <a href="https://github.com/Skyscanner/JsonPath-PHP/blob/master/README.md" target="_blank">JSONPath Syntax</a>) will be returned as the root and will
					be used for any mappings you create below. Tick the "array" option if the root node should be returned as an array.', 'midgard'); ?>
				</p>
				<p>
					<?php _e('Leave this blank to leave the existing root node in place.', 'midgard'); ?>
				</p>

				<table class="widefat">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Root Path', 'midgard' ); ?></th>
							<th width="30"><?php _e( 'Array', 'midgard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$root_path = sanitize_text_field( get_post_meta( $object->ID, 'midgard_feed_root_path', true ) );
						$root_multi = get_post_meta( $object->ID, 'midgard_feed_root_multi', true );
						if($root_multi === null) $root_multi = 1; // default true
						?>

						<tr>
							<td><input type="text" class="large-text" name="midgard-feed-root-path" id="midgard-feed-root-path" value="<?php echo $root_path; ?>"/></td>
							<td style="text-align:center"><input type="checkbox" value="1" name="midgard-feed-root-multi" <?php checked( $root_multi, '1' ); ?> /></td>
						</tr>

					</tbody>
				</table>

			</div>

			<p><?php _e('Simple mappings consist of a KEY and a PATH.', 'midgard'); ?></p>
			<p><?php _e('The KEY indicates the name of the data field which will appear in your JSON output. The PATH tells the processor where to find the value in the original feed data (using <a href="https://github.com/Skyscanner/JsonPath-PHP/blob/master/README.md" target="_blank">JSONPath Syntax</a>).', 'midgard'); ?></p>
			<p><?php _e('The "multiple" flag will return an array of matching values, otherwise the first matching value is returned.</p>'); ?></p>
			<table class="widefat midgard-mappings">
				<thead>
					<tr>
						<th width="25%" class="row-title"><?php esc_attr_e( 'Key', 'midgard' ); ?></th>
						<th><?php esc_attr_e( 'Path', 'midgard' ); ?></th>
						<th width="30"><?php esc_attr_e( 'Multiple', 'midgard' ); ?></th>
						<th width="30">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$map_meta = get_post_meta( $object->ID, 'midgard_feed_mappings', true );
					// we have to strip out the escaped slashes on single quotes or it won't decode
					$map = json_decode( str_replace( "\'", "'" ,  $map_meta), true );

					if($map && is_array($map)) {
						foreach($map as $index=>$m) {
							$m = array_merge(array(
								'key' => '',
								'path' => '',
								'multi' => 0
							), $m);

							printf('<tr class="%s">', $index % 2 == 1 ? 'alternate' : '');
							printf( '<td><input type="text" class="large-text midgard-mapping-key" name="midgard-feed-mappings[%s][key]" value="%s" placeholder="key_name" required></td>', $index, $m['key'] );
							printf( '<td><input type="text" class="large-text midgard-mapping-path" name="midgard-feed-mappings[%s][path]" value="%s" placeholder="JSONPath expression" required></td>', $index, $m['path'] );
							printf( '<td style="text-align:center"><input type="checkbox" value="1" class="midgard-mapping-multi" name="midgard-feed-mappings[%s][multi]" %s /></td>', $index, checked( $m['multi'], '1', FALSE ) );
							printf( '<td><a class="midgard-mapping-delete dashicons dashicons-trash" href="#" ></a></td>');
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>

			<p>
				<input class="button-primary midgard-add-mapping" type="button" value="<?php esc_attr_e( 'Add new mapping' ); ?>" />
			</p>
		</div>

		<!-- ADVANCED MAPS -->
		<div class="midgard-map-mode" data-mode="advanced">
			<p>
				<?php
					_e('Create custom feed output using ', 'midgard');
					printf('<a href="https://twig.sensiolabs.org/doc/1.x/templates.html" target="_blank">%s</a>', __('Twig markup', 'midgard') );
				?>
			</p>
			<p>
				<?php
					$twig = get_post_meta( $object->ID, 'midgard_mapping_twig', true );
					printf('<textarea class="large-text midgard-mapping-twig" rows="20" name="midgard-mapping-twig">%s</textarea>', esc_textarea($twig) );
				?>
			</p>
		</div>

	<?php
	}

	/**
	 * Create an import/export structure for the mapping data
	 *
	 * Callback for add_meta_box() function in manage_custom_metaboxes()
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	object		$object			The WordPress post object
	 * @param	array		$box			The metabox array containing data defined in add_meta_box
	 */
	public function feed_mapping_import_meta_box( $object, $box ) { ?>
		<p><?php _e('Backup or restore "simple" mappings using the buttons below. No data is modified until you save the feed.', 'midgard'); ?></p>
		<p><?php _e('To export your existing mappings, click export and copy the snippet in the text field below. To import, paste a previously saved snippet into the text field and click import.', 'midgard'); ?></p>
		<p>
			<input class="button-secondary midgard-export-mapping" type="button" value="<?php esc_attr_e( 'Export Mappings' ); ?>" />
			<input class="button-primary midgard-import-mapping" type="button" value="<?php esc_attr_e( 'Import Mappings' ); ?>" />
		</p>
		<p>
			<textarea id="midgard-mapping-data" name="" cols="80" rows="10" class="large-text"></textarea>
		</p>

	<?php }

	/**
	 * Save the article meta box's post data.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	int			$post_id		ID of the article
	 * @param	object		$post			The WordPress post object
	 */
	public function save_feed_meta( $post_id, $post ) {

		// Verify the nonce before proceeding.
		if ( !isset( $_POST['midgard_nonce'] ) || !wp_verify_nonce( $_POST['midgard_nonce'], basename( __FILE__ ) ) )
			return $post_id;

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post.
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		// Get the posted data and sanitize it for use as an HTML class.
		$new_meta_value_uri		= ( isset( $_POST['midgard-feed-uri'] ) ? esc_url_raw( $_POST['midgard-feed-uri'] ) : '' );
		$new_meta_value_cache 	= ( isset( $_POST['midgard-cache-time'] ) ? sanitize_html_class( $_POST['midgard-cache-time'] ) : '' );
		$new_meta_value_feed 	= ( isset( $_POST['midgard-feed-type'] ) ? sanitize_html_class( $_POST['midgard-feed-type'] ) : '' );
		$new_map_mode			= ( isset( $_POST['midgard-mapping-mode'] ) ? sanitize_html_class( $_POST['midgard-mapping-mode'] ) : '' );
		// sanitize but keep line breaks
		$new_map_twig			= ( isset( $_POST['midgard-mapping-twig'] ) ? sanitize_textarea_field($_POST['midgard-mapping-twig'] ) : '' );



		// note - we use json encoding so that quotes etc don't get messed up (otherwise we get errors deserializing)
		$new_meta_value_mappings = ( isset( $_POST['midgard-feed-mappings'] ) ? json_encode( $_POST['midgard-feed-mappings'] ) : '' );
		$new_json_root_path 	= ( isset( $_POST['midgard-feed-root-path'] ) ? ( $_POST['midgard-feed-root-path'] ) : '' );
		$new_json_root_multi  	= ( isset( $_POST['midgard-feed-root-multi'] ) ? ( $_POST['midgard-feed-root-multi'] ) : '' );

		// add/update or delete values accordingly for all settings
		Midgard_Common::save_meta_values($post_id, 'midgard_feed_uri', 			$new_meta_value_uri);
		Midgard_Common::save_meta_values($post_id, 'midgard_cache_time', 		$new_meta_value_cache);
		Midgard_Common::save_meta_values($post_id, 'midgard_feed_type', 		$new_meta_value_feed);
		Midgard_Common::save_meta_values($post_id, 'midgard_mapping_mode', 		$new_map_mode);
		Midgard_Common::save_meta_values($post_id, 'midgard_mapping_twig', 		$new_map_twig);
		Midgard_Common::save_meta_values($post_id, 'midgard_feed_mappings', 	$new_meta_value_mappings);
		Midgard_Common::save_meta_values($post_id, 'midgard_feed_root_path', 	$new_json_root_path);
		Midgard_Common::save_meta_values($post_id, 'midgard_feed_root_multi', 	intval($new_json_root_multi));

		// decache any stored values
		$cache = Midgard::get_cache_instance();
		$key = get_permalink( $post_id );
		$cache->deleteItem( $key );

		// call any addon actions
		do_action('midgard_after_save_feed_meta', $post_id, $post );

	}

}
