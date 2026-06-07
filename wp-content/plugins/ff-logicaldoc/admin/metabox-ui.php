<?php

namespace FF\LogicalDoc;


/**
 * The class to add metabox to the post edit screen
 *
 * @package    ff-logicaldoc
 * @subpackage ff-logicaldoc/admin
 * @author     Firefly Interactive
 */
class Metabox_UI {

	// most recent error message reported by logical client after save
	protected $logical_last_error;

	protected $prefix = '_ff_logicaldoc_';

	/**
	 * Constructor - set some values
	 */
	public function __construct() {
		// get any logical messages and clear them out
		$logical_error = get_transient( 'ff-logical-error-message' );
		$logical_status = get_transient( 'ff-logical-last-status');
		delete_transient( 'ff-logical-error-message' );
		delete_transient( 'ff-logical-last-status' );

		// if we have an error or success show the appropriate admin notice
		if( $logical_error ) {
			$this->logical_last_error = $logical_error;
			add_action( 'admin_notices', array( $this, 'admin_notice_logical_error' ) );
		} else if( $logical_status == 'success') {
			add_action( 'admin_notices', array( $this, 'admin_notice_logical_success' ) );
		}
	}

	/**************************************************************************************
	 * CUSTOM POST TYPE METABOXES
	 **************************************************************************************/

	/**
	 * Enqueue scripts
	 */
	public function enqueue_admin_scripts() {
		$screen = get_current_screen();

		// scripts
		if( $screen->post_type == 'post' ) {
			wp_enqueue_style( 'ff-logical-css', plugin_dir_url( __FILE__ ) . 'css/admin.css' );
		}

	}

	/**
	 * Adds Custom options to the Publish Meta Box.
	 *
	 * Used to add a LogicalDOC sync checkbox.
	 */
	public function add_to_publish_meta_box() {

		// only run if 'post' post type
		$post_type = get_current_screen();
		if($post_type->id !== 'post')
			return;

		global $post;	?>

		<div class="misc-pub-section misc-pub-logicaldoc-sync">
			<?php // determine the field label depending on current post status
			$post_status = get_post_status( $post->ID );
			$new_status = $post_status == 'publish' ? 'update' : 'publish'; ?>

			<img style="max-width:100%; height:auto; display:block; margin:5px 0"
				src="data:image/png;base64,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">
			<?php $sync = intval( get_post_meta( $post->ID, '_ff_logicaldoc_sync', true ) ); ?>
			<input type="checkbox" id="_ff_logicaldoc_sync" name="_ff_logicaldoc_sync" value="1" <?php checked($sync, 1); ?>>
			<label for="_ff_logicaldoc_sync"><?php _e('Sync with LogicalDOC on ' . $new_status, 'ff-logicaldoc'); ?></label>

			<?php
			$doc_id = intval( get_post_meta( $post->ID, '_ff_logicaldoc_id', true ) );
			echo '<p>';
			if( $doc_id > 0 ) {
				printf(__('LogicalDOC ID: %s', 'ff-logicaldoc'), $doc_id);
			} else {
				_e('Not synced', 'ff-logicaldoc');
			}
			echo '</p>'; ?>


		</div>

		<?php
	}

	/**
	 * Add custom metaboxes
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function manage_custom_metaboxes() {

		// Add custom newsletter meta box
		add_meta_box(
			'logicaldoc-fields-meta-box',      				// Unique ID
			'<img style="max-width:100%; height:auto; display:block; margin:5px 0" alt="LogicalDOC"
				  src="data:image/png;base64,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">', 					// Title
			array( $this, 'logicaldoc_fields_meta_box' ), 	// Callback function - create the HTML for the meta box
			'post',         								// Admin page (or post type)
			'normal',         								// Context
			'default'         								// Priority
		);

	}

	/**
	 * Create the HTML for the newsletter dates meta box. Allows adding dates to newsletter issue.
	 *
	 * Callback for add_meta_box() function in manage_custom_metaboxes()
	 *
	 * @since	3.0.0
	 * @access	public
	 * @param	object		$post			The post object
	 */
	public function logicaldoc_fields_meta_box( $object, $box ) {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'logicaldoc_fields_nonce' );

		echo '<p>';
		_e( 'These properties will be synced with LogicalDOC.', 'ff-logicaldoc' );
		echo '<br>';
		_e( 'Required fields are marked with ', 'ff-logicaldoc' );
		echo '<span style="color:red">*</span>.';
		echo '</p>';

		echo '<div class="logical-meta-fields">';

		///////////////////////////////////
		// REQUIRED FIELDS
		///////////////////////////////////

		$created_date = $this->meta_value('date');
		echo $this->meta_field( 'date', 'Created Date', $created_date, [
			'required'	=> true,
			'type'		=>'date',
			'default'	=> get_the_date('Y-m-d', $post)
		]);

		// Author / Speaker - default to local author if not set
		$author_name= $this->meta_value('authors');
		$author_default = $this->get_author_names();

		echo $this->meta_field( 'authors', 'Author/Speaker', $author_default, [
					'type'	  => 'readonly',
					'required'=>false,
					'default' => $author_default
				]);

		// Copyright
		$copyright = $this->meta_value('copyright');
		echo $this->meta_field( 'copyright', 'Copyright Ownership', $copyright, [
					'type'		=> 'select',
					'required'	=> true,
					'options'	=> ['Institute', 'Speaker/Author', 'Public Domain', 'Other'],
					'default'	=> 'Institute'
				]);

		// CPD Points
		$cpd = $this->meta_value('cpd');
		echo $this->meta_field( 'cpd', 'CPD Points', $cpd, [
			'type'		=> 'range',
			'required'	=> true,
			'min'		=> 0,
			'max'		=> 12,
			'step'		=> 1,
			'default'	=> 1
		]);

		// Level
		$level = $this->meta_value('level');
		echo $this->meta_field( 'level', 'Level', $level, [
					'type'		=> 'select',
					'required'	=> true,
					'options'	=> ['All', 'Advanced', 'Introductory'],
					'default'	=> 'All'
				]);

		// Format
		$format = $this->meta_value('format');
		echo $this->meta_field( 'format', 'Format', $format, [
					'type'		=> 'select',
					'required'	=> true,
					'options'	=> ['Events', 'Workshops', 'Article', 'Elearning, MOOC/course',
									'Podcast/Vodcast', 'Publication', 'Tools and Resources',
									'Tutorials', 'Video', 'Webinar', 'Presentation Slides', 'Other'],
					'default'	=> 'Article'
				]);

		// Region
		$region = $this->meta_value('region');
		echo $this->meta_field( 'region', 'Region', $region, [
					'type'		=> 'select',
					'required'	=> true,
					'options'	=> ['Domestic and International', 'Domestic', 'International'],
					'default'	=> 'Domestic and International'
				]);

		// Membership
		$membership = $this->meta_value('membership');
		echo $this->meta_field( 'membership', 'Membership', $membership, [
					'type'		=> 'select',
					'required'	=> true,
					'options'	=> ['Members Only', 'All'],
					'default'	=> 'All'
				]);

		// Availability
		$availability = $this->meta_value('availability');
		echo $this->meta_field( 'availability', 'Availability', $availability, [
					'type'		=> 'select',
					'required'	=> true,
					'options'	=> ['Dashboard/Website', 'Website', 'Dashboard', 'Trader Website', 'All'],
					'default'	=> 'Dashboard/Website'
				]);

		///////////////////////////////////
		// OPTIONAL FIELDS
		///////////////////////////////////

		// Description - default to excerpt
		$description = $this->meta_value('description');
		$excerpt	 = get_the_excerpt($post);
		echo $this->meta_field( 'description', 'Description', $description, [
					'type'=>'textarea',
					'rows'=>5,
					'default'=>$excerpt
				]);

		// New Release expiry
		$new_release_expiry = $this->meta_value('new_release_expiry');
		echo $this->meta_field( 'new_release_expiry', 'New Release Expiry', $new_release_expiry, ['type'=>'date']);

		// // External Link
		// $external_link =  $this->meta_value('external_link', get_post_permalink($post->ID)); // default to post permalink
		// echo $this->meta_field( 'external_link', 'External Link', $external_link, ['type'=>'url']);

		// Event Type
		$event_type = $this->meta_value('event_type');
		echo $this->meta_field( 'event_type', 'Event Type', $event_type, [
					'type'		=> 'select',
					'required'	=> false,
					'options'	=> ['Majors/Seminars', 'Insights/Conferences', 'Other']
				]);

		// Event Age Group
		$event_age_group = $this->meta_value('age_group');
		echo $this->meta_field( 'age_group', 'Events Age Group', $event_age_group, [
					'type'		=> 'select',
					'required'	=> false,
					'options'	=> ['YAP', 'Retired Actuaries', 'All']
				]);

		// Internal Comments
		$comments = $this->meta_value('comments');
		echo $this->meta_field( 'comments', 'Comments (internal)', $comments, ['type'=>'textarea', 'rows'=>5]);

		// end meta fields
		echo '</div>';
	}

	/**
	 * Save the article meta box's post data.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	int			$post_id		ID of the article
	 * @param	object		$post			The WordPress post object
	 */
	public function save_post_meta( $post_id, $post ) {
		// ignore revisions - only sync published versions
		if( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post.
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		// init params to send to Logical and store in WP
		$params = [];

		if( isset( $_POST['logical_fields']) ) {
			// set meta fields
			$fields = array('date','authors','copyright','cpd','level','format','region','membership',
							'availability','description','new_release_expiry',//'external_link',
							'event_type','age_group','comments');

			$values = $_POST['logical_fields'];

			foreach( $fields as $field ) {

				$value = stripslashes($values[$field] ?? '');
				$logical_value = null;

				$type = 'string'; // string, int, double, date

				switch( $field ) {
					// overwrite authors with current
					case 'authors' :
						$value = $this->get_author_names($post);
						break;

					case 'comments' :
						$value = sanitize_textarea_field( $value );
						break;

					case 'date' :
					case 'new_release_expiry' :
						$type = 'date';
						$logical_value = date( 'Y-m-d H:i:sO', strtotime($value));
						break;

					default :
						$value = $value; // sanitize_text_field( $value );
						break;
				}


				// use logical value if explicitly set or fallback to WP value
				// WP and Logical want different formats for date
				$logical_value = $logical_value ?: $value;

				// store param for transmission to Logical
				$params[$field] = array(
					$type => $logical_value
				);

				// store as WordPress meta
				$fieldname = $this->prefix . $field;
				$this->save_meta_values($post_id, $fieldname, $value);

			}
		}

		// Get the posted data and send to LOGICAL if sync setting is true
		$sync		= ( isset( $_POST['_ff_logicaldoc_sync'] ) ? intval( $_POST['_ff_logicaldoc_sync'] ) : '' );
		$docid		= intval( get_post_meta( $post_id, '_ff_logicaldoc_id', true ) );

		// is the post published?
		$published = get_post_status( $post_id ) == 'publish';

		// if we have a docid then it was formerly synced
		// if sync is unset then we want to UNSYNC or DELETE the doc using that docid
		if( ! $sync && $docid ) {
			// try a LogicalDOC delete
			$logical = new Logical();
			$sid = $logical->login();

			// this is a valid request only if the docid exists
			// or if we have a docid and the doc exists in the logical repo (EDIT)
			// invalid items don't need to be deleted but it's still considered successful
			$valid = $logical->checkDocumentExists( $docid );

			if( $valid ) {
				$success = $logical->deleteDocument($docid);
			} else {
				$success = true;
			}

			if( $success ) {
				// disable sync and remove non-existent docid
				$docid = null;
				$sync = false;

				set_transient( 'ff-logical-last-status', 'success', 60 );
			} else {
				$message = sprintf( 'Unable to delete document %s in LogicalDOC - sync cannot be disabled. Please try again.', $docid);
				set_transient( 'ff-logical-error-message', $message, 60 );
				set_transient( 'ff-logical-last-status', 'error', 60 );
			}
		}
		// otherwise sync if published
		else if( $sync && $published ) {
			// try a LogicalDOC sync
			$logical = new Logical();
			$sid = $logical->login();

			// DEBUG
			// force an error
			// $docid = 99999999;
			// $doc = $logical->checkExists( 3356081 );

			// this is a valid request if we have NO docid (NEW)
			// or if we have a docid and the doc exists in the logical repo (EDIT)
			$valid = $docid ?  $logical->checkDocumentExists( $docid ) : true;

			// init return data
			$doc = null;

			// if valid request then create/update
			if( $valid ) {
				$doc = $logical->createOrUpdateDocument($post, $docid, $params);
			}
			// otherwise report that doc didn't exist
			else {
				$message = sprintf( 'Document %s does not exist in LogicalDOC repository. Sync has been disabled. Re-enable below to create a new copy of this document in LogicalDOC.', $docid);
				set_transient( 'ff-logical-error-message', $message, 60 );
				set_transient( 'ff-logical-last-status', 'error', 60 );

				// disable sync and remove non-existent docid
				$docid = null;
				$sync = false;
			}

			$logical->logout($sid);

			if($doc) {
				// 'CREATE' sends back a document with new ID. UPDATE sends nothing back
				if( isset($doc->document->id) ) {
					$docid = $doc->document->id;
				}
				set_transient( 'ff-logical-last-status', 'success', 60 );
			}

		}


		// add/update or delete values accordingly for all settings
		$this->save_meta_values($post_id, '_ff_logicaldoc_id', 		$docid);
		$this->save_meta_values($post_id, '_ff_logicaldoc_sync', 	$sync);
	}

	/**
	 * Utility method: Add, Update, Delete meta values
	 */
	public static function save_meta_values( $post_id, $meta_key, $new ) {

		// Get the old meta value of the custom field key.
		$old = get_post_meta( $post_id, $meta_key, true );

		// evaluate if $new is empty data
		$new_empty = ($new === '');

		// If a new meta value was added and there was no previous value, add it.
		if ( !$new_empty ) {

			$result = add_post_meta( $post_id, $meta_key, $new, true );
			if( ! $result ) {
				$result = update_post_meta( $post_id, $meta_key, $new );
			}

		// If there is no new meta value but an old value exists, delete it.
		} elseif ( $new_empty && $old ) {
			delete_post_meta( $post_id, $meta_key, $old );
		}
	}

	public function admin_notice_logical_success() {
		echo '<div class="notice notice-info is-dismissible">';
		printf( '<p>%s</p>', __( 'LogicalDOC operation completed sucessfully', 'ff-logicaldoc' ) );
		echo '</div>';
	}

	public function admin_notice_logical_error( $message ) {
		echo '<div class="notice notice-error">';
		printf( '<p>%s %s</p>', '<strong>LogicalDOC Error: </strong>' ,  $this->logical_last_error );
		echo '</div>';
	}


	/**
	 * Output a meta field
	 */
	protected function meta_field( $fieldname, $label, $value='', $args=array() ) {
		$args = array_merge( array(
			'type'	  	=> 'text',
			'required' 	=> false,
			'options'	=> array(),
			'default'	=> ''
		), $args);

		$id       = '_ff_logicaldoc_' . esc_attr( $fieldname );
		$type     = $args['type'];
		$required = $args['required'] ? 'required'  : '';
		$required_indicator = empty($required) ? '' : ' <span style="color:red">*</span>';
		$wrapper_class = 'column';

		$out_label = sprintf( '<label for="%s" style="font-weight:bold">%s %s</label><br>',
								$id, $label, $required_indicator);

		// use default if value is empty
		if( empty( $value ) ) {
			$value = $args['default'];
		}

		switch( $type ) {
			// SELECT
			case 'select':
				$options = '<option value=""></option>';
				foreach($args['options'] as $option) {
					$options .= sprintf('<option value="%1$s" %2$s>%1$s</option>)',
						$option,
						selected( $option, $value, false ));
				}
				$out_input = sprintf( '<select id="%s" class="regular-text" name="logical_fields[%s]" %s>%s</select>',
					$id,
					$fieldname,
					$required,
					$options );
				break;

			// RANGE
			case 'range' :
				$min = isset($args['min']) ? $args['min'] : 0;
				$max = isset($args['max']) ? $args['max'] : 100;
				$step = isset($args['step']) ? $args['step'] : 1;

				$out_input = sprintf('<input id="%1$s" class="regular-text" type="range" name="logical_fields[%2$s]" value="%3$s" %4$s min="%5$s" max="%6$s" step="%7$s" oninput="%1$s_value.value = %1$s.value">',
					$id,
					$fieldname,
					$value,
					$required,
					$min,
					$max,
					$step );

				$out_input .= sprintf('<output id="%s">%s</output>',
					$id . '_value',
					$value);
				break;

			// TEXTAREA type
			case 'textarea':
				$rows = isset( $args['rows'] ) ? $args['rows'] : '';
				$out_input = sprintf( '<textarea id="%1$s" class="large-text" name="logical_fields[%2$s]" rows="%4$s" %5$s>%3$s</textarea>',
							$id,
							$fieldname,
							$value,
							$rows,
							$required);
				$wrapper_class = 'wide';
				break;

			// READONLY type
			case 'readonly':
				$out_input = sprintf( '<input id="%1$s" type="%2$s" class="regular-text" name="logical_fields[%3$s]" value="%4$s" %5$s>',
							$id,
							'hidden',
							$fieldname,
							$value,
							'');
				$out_input .= $value;
				break;

			// TEXT type
			default:
				$out_input = sprintf( '<input id="%1$s" type="%2$s" class="regular-text" name="logical_fields[%3$s]" value="%4$s" %5$s>',
							$id,
							$type,
							$fieldname,
							$value,
							$required);
				break;
		}

		$output = sprintf('<p class="logical-meta-field %s %s">%s%s</p>',
						$wrapper_class,
						$type,
						$out_label,
						'<span class="input">' . $out_input . '</span>');

		return $output;
	}

	/**
	 * Get a meta value
	 */
	protected function meta_value( $key, $default='' ) {
		global $post;
		$value = get_post_meta( $post->ID, $this->prefix . $key, true);
		return $value ? $value : $default;
	}

	/**
	 * Get author name(s) - using coauthors plugin if available
	 */
	protected function get_author_names($post=0) {
		$post = get_post($post);

		// if no coauthor plugin, return author display name
		if (! function_exists( 'get_coauthors' ) ) {
			$author_id = $post->post_author;
			return get_the_author_meta( 'display_name', $author_id);
		}

		// get co-authors
		$authors = get_coauthors($post->ID);
		$author_names = [];

		foreach($authors as $author) {
			$author_names[] = $author->display_name;
		}

		return implode(', ', $author_names);
	}
}
