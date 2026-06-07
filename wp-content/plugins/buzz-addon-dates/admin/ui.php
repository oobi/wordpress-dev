<?php

class Buzz_Addon_Dates_UI {

	// unique string prepend fieldnames and metabox ID
	protected $meta_key;
	protected $icons;

	// legacy compatibility -  pre version 1.3
	protected $legacy    = false;
	protected $legacy_meta_key;

	public function __construct() {
		$this->meta_key = Buzz_Addon_Dates_Data::$meta_key;
		$this->legacy_meta_key = Buzz_Addon_Dates_Data::$legacy_meta_key;


		$this->icons = get_theme_mod( 'buzz_dates_icons' );
		if( !is_array( $this->icons ) ) {
			$this->icons = array();
		}
	}

	/**
	 * Add custom metaboxes
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function manage_custom_metaboxes() {

		// get the date sets
		$sets 	= get_theme_mod( 'buzz_dates_sets' );

		if( is_array( $sets ) ) {
			foreach( $sets as $set ) {
				// Add custom newsletter meta box
				add_meta_box(
					'newsletter-dates-meta-box-' . $set['class'],    // Unique ID
					$set['label'], 									// Title
					array( $this, 'newsletter_dates_meta_box' ), 	// Callback function - create the HTML for the meta box
					'newsletter',         							// Admin page (or post type)
					'normal',         								// Context
					'default',         								// Priority
					array( 'set' => $set )							// Callback args
				);
			}
		}

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
	public function newsletter_dates_meta_box( $object, $box ) {
		wp_nonce_field( basename( __FILE__ ), 'buzz_dates_nonce' );

		$set 	= $box['args']['set'];
		$slug 	= sanitize_title_with_dashes($set['class']);
		$metabox_setting = $this->meta_key . '[' . $slug . ']';

		// printf( '<p>%s</p>', _e( 'Dates will appear in the ' . $set['label'] . ' section.', 'buzz-dates' ));

		// cook a template for use in the table (add new row)
		$template_fields = $this->dates_fields( $metabox_setting );
		$template_buttons = '<div class="buzz-dates-btn"><a title="delete" class="buzz-dates-delete dashicons dashicons-trash" href="#" ></a><span class="drag-handle" title="drag to sort"><span class="dashicons dashicons-arrow-up-alt2"></span><span class="dashicons dashicons-arrow-down-alt2"></span></span></div>';
		printf('<script id="%s" type="text/template"><div class="buzz-dates-row"><div class="buzz-dates-record">%s</div>%s</div></script>',
			$this->meta_key . '_row_template',
			$template_fields,
			$template_buttons
		);

		// get dates meta and output table
		$all_dates = $this->meta_value($this->meta_key, array() );

		// check for legacy dates if this is empty
		// only do this for ONE metabox
		if( !$this->legacy && empty( $all_dates ) ) {
			$dates = $this->meta_value($this->legacy_meta_key, array() );
			if( ! empty( $dates ) ) {
				$this->legacy = true;
			}
		} else {
			$dates = array_key_exists( $slug, $all_dates) ? $all_dates[$slug] : array();
		}

		?>

		<div data-setting="<?php echo $metabox_setting; ?>" id="<?php echo $metabox_setting; ?>" class="buzz-dates-row-container striped">
			<?php
				foreach( $dates as $index => $record ) {
					printf('<div class="buzz-dates-row"><div class="buzz-dates-record">%s</div>%s</div>',
						$this->dates_fields( $metabox_setting, $record, $index ),
						$template_buttons
					);
				}
			?>
		</div>

		<p>
			<input class="button-primary buzz-dates-add" type="button" value="<?php esc_attr_e( 'Add new date', 'buzz-dates' ); ?>" />
		</p>

	<?php
	}

	/**
	 * Save the dates meta box data
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	int			$post_id		ID of the article
	 * @param	object		$post			The WordPress post object
	 */
	public function save_dates_meta( $post_id, $post ) {

		// Verify the nonce before proceeding.
		if ( !isset( $_POST['buzz_dates_nonce'] ) || !wp_verify_nonce( $_POST['buzz_dates_nonce'], basename( __FILE__ ) ) )
			return $post_id;

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post.
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;


		$new_meta_value_dates = '';

		// Get the posted data and sanitize it
		if( isset( $_POST[$this->meta_key] ) ) {
			$new_meta_value_dates = $this->sanitize_dates_meta( $_POST[$this->meta_key] );
		}

		// add/update or delete values accordingly for all settings
		FF_Newsletter_Common::save_meta_values( $post_id, $this->meta_key, $new_meta_value_dates );
	}

	/**
	 * Sanitize the dates meta box data
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	Array			$array		The dates array
	 */
	public function sanitize_dates_meta( &$array ) {
		foreach ($array as $key => &$value) {
			// sanitize if value is not an array
			if( !is_array( $value ) )	{
				if( $key == 'description' ) {
					$value = sanitize_textarea_field( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
			}
			// go inside this function again
			else {
				$this->sanitize_dates_meta($value);
			}
		}
		return $array;
	}

	/**
	 * Output a dates record (set of fields)
	 */
	public function dates_fields( $metabox_setting, $record=array(), $index=0 ) {
		$record = array_merge( array(
			'date' 			=> '',
			'title' 		=> '',
			'description' 	=> '',
			'url' 			=> '',
			'icon'			=> ''
		), $record);


		// build field template
		$template_output = '';

		$title = $record['title'];
		$template_output .= $this->meta_field( 'title', 'Title', $title, [
			'required'	=>true,
			'default' 	=> '',
			'setting'	=> $metabox_setting,
			'index'		=> $index
		]);

		$date = $record['date'];
		$template_output .= $this->meta_field( 'date', 'Date', $date, [
			'required'	=> false,
			'type'		=>'date',
			'setting'	=> $metabox_setting,
			'index'		=> $index
		]);

		$url = $record['url'];
		$template_output .= $this->meta_field( 'url', 'URL', $url, [
			'required'	=>false,
			'type'		=> 'url',
			'setting'	=> $metabox_setting,
			'index'		=> $index
		]);

		$icon = $record['icon'];
		$options = array();
		foreach( $this->icons as $item ) {
			$options[$item['label']] = $item['image'];
		}

		$template_output .= $this->meta_field( 'icon', 'Icon', $icon, [
			'type'		=> 'select',
			'required'	=> false,
			'options'	=> $options,
			'setting'	=> $metabox_setting,
			'index'		=> $index
		]);

		$description = $record['description'];
		$template_output .= $this->meta_field( 'description', 'Description', $description, [
			'type'		=>'textarea',
			'rows'		=>2,
			'setting'	=> $metabox_setting,
			'index'		=> $index
		]);



		return $template_output;
	}

	/**
	 * Output a meta field
	 */
	protected function meta_field( $fieldname, $label, $value='', $args=array() ) {
		$args = array_merge( array(
			'type'	  	=> 'text',
			'required' 	=> false,
			'options'	=> array(),
			'default'	=> '',
			'index'		=> 0,
			'setting'	=> '',
			'placeholder'	=> $label
		), $args);

		$id       			= '_buzz_dates_' . esc_attr( $fieldname );
		$type     			= $args['type'];
		$required 			= $args['required'] ? 'required'  : '';
		$placeholder 		= $args['placeholder'];
		$required_indicator = empty($required) ? '' : ' <span style="color:red">*</span>';
		$wrapper_class 		= 'column';

		$out_label = sprintf( '<label for="%s" style="font-weight:bold">%s %s</label><br>',
								$id, $label, $required_indicator);

		// form field identifier for post
		$postname = sprintf( '%s[%s][%s]', $args['setting'], $args['index'], $fieldname );

		// use default if value is empty
		if( empty( $value ) ) {
			$value = $args['default'];
		}

		switch( $type ) {
			// SELECT
			case 'select':
				$options = '<option value="">- no icon -</option>';
				foreach($args['options'] as $option_label => $option_value) {
					$options .= sprintf('<option value="%1$s" %3$s>%2$s</option>',
						$option_value,
						$option_label,
						selected( $option_value, $value, false )
					);
				}
				$out_input = sprintf( '<select data-name="%2$s" id="%1$s" class="regular-text" name="%5$s" %3$s>%4$s</select>',
					$id,
					$fieldname,
					$required,
					$options,
					$postname );

				break;

			// TEXTAREA type
			case 'textarea':
				$rows = isset( $args['rows'] ) ? $args['rows'] : '';
				$out_input = sprintf( '<textarea data-name="%2$s" id="%1$s" class="large-text" name="%6$s" rows="%4$s" placeholder="%7$s" %5$s>%3$s</textarea>',
					$id,
					$fieldname,
					$value,
					$rows,
					$required,
					$postname,
					$placeholder);
				$wrapper_class = 'wide';
				break;

			// TEXT type
			default:
				$out_input = sprintf( '<input data-name="%3$s" id="%1$s" type="%2$s" class="regular-text" name="%6$s" value="%4$s" placeholder="%7$s" %5$s>',
					$id,
					$type,
					$fieldname,
					$value,
					$required,
					$postname,
					$placeholder);
				break;
		}

		$output = sprintf('<p class="dates-meta-field %1$s %2$s">%3$s%4$s</p>',
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
		$value = get_post_meta( $post->ID, $key, true);
		return $value ? $value : $default;
	}

}