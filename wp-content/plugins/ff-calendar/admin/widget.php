<?php

namespace FF\Calendar;

/**
 * The class to create a widget for displaying a calendar
 *
 * @package    ff-calendar
 * @subpackage ff-calendar/admin
 * @author     Firefly Interactive
 */
class FF_Calendar_Widget extends \WP_Widget {

	// the widget id
	public $id;

	// the widget name
	public $name;

	// the widget description
	public $description;

	// the shortcode version
	public $shortcode_version;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		// set widget data
		$this->id 					= 'FF_Calendar_Widget';
		$this->name 				= __( 'Firefly Calendar', 'ff_calendar' );
		$this->description 			= __( 'A Firefly Calendar widget.', 'ff_calendar' );
		$this->shortcode_version 	= FF_CALENDAR_SHORTCODE_VERSION;

		parent::__construct(
			$this->id, // Base ID
			$this->name, // Name
			array( 'classname' => 'ff-calendar-widget', 'description' => $this->description ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
     	echo $args['before_widget'];

		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] );
			if ( !empty( $instance['url'] ) ) {
				echo '<span class="widget-url"><a href="' . $instance['url'] . '">' . ($instance['link_text'] ? $instance['link_text'] : 'All Events') . '</a></span>';
			}
			echo $args['after_title'];
		}

		// only output shortcode if IDs are present
		if( isset( $instance['ids'] ) && !empty( $instance['ids']) ) {
			// create shortcode from instance
			// include widget="true" in shortcode for use in shortcode callback
			$shortcode = sprintf( '[ff-calendar %s widget="true" ids="%s" view="%s" height="%s" nocontrols="%s" limit-num="%s" limit-type="%s"]',
				$this->shortcode_version,
				implode( ',', $instance['ids'] ), // already checked this exists and has values
				isset( $instance['view'] ) 				? $instance['view'] 				: '',
				isset( $instance['height'] ) 			? $instance['height'] 				: '',
				isset( $instance['nocontrols'] ) 		? $instance['nocontrols'] 			: '',
				isset( $instance['limit_num'] ) 		? $instance['limit_num'] 			: '',
				isset( $instance['limit_type'] ) 		? $instance['limit_type'] 			: ''
			);

			// if is "ff-calendar" shortcode
			if ( !empty( $shortcode ) && has_shortcode( $shortcode, 'ff-calendar' ) ) {
				do_shortcode( $shortcode );
// echo $shortcode; // debug
			}

		}

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		// options
		$options 		= get_option( 'ff_calendar_settings' );
		$feeds_array	= $options['calendar_feeds'];

		// saved widget data
		$title 				= array_key_exists( 'title', $instance ) 		? $instance['title'] 				: '';
		$ids 				= array_key_exists( 'ids', $instance ) 			? $instance['ids'] 					: array();
		$view 				= array_key_exists( 'view', $instance ) 		? $instance['view'] 				: '';
		$height 			= array_key_exists( 'height', $instance ) 		? $instance['height'] 				: '';
		$limit_num 			= array_key_exists( 'limit_num', $instance ) 	? $instance['limit_num'] 			: '';
		$limit_type 		= array_key_exists( 'limit_type', $instance ) 	? $instance['limit_type'] 			: '';
		$url 				= array_key_exists( 'url', $instance)			? $instance['url']					: '';
		$link_text 			= array_key_exists( 'link_text', $instance)		? $instance['link_text']			: '';
		?>

		<div class="ff-calendar-widget-form">

			<!-- DEFAULTS, cannot change in widget -->
			<input id="<?php echo $this->get_field_id( 'nocontrols' ); ?>" name="<?php echo $this->get_field_name( 'nocontrols' ); ?>"
						type="hidden" value="true">

			<!-- TITLE -->
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>"
						type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>

			<!-- IDs -->
			<p>
				<label for="<?php echo $this->get_field_id( 'ids' ); ?>"><?php _e( 'Feeds' ); ?> <span class="required">*</span></label>
				<br>
				<?php // loop the feeds, extract the feed name and output checkboxes
					foreach( $feeds_array as $key => $value ) {
						printf( '<input type="checkbox" name="%1$s" id="%2$s" value="%3$s" %4$s> <label for="%2$s">%5$s</label><br>',
							$this->get_field_name( 'ids' ) . '[]',
							$this->get_field_id( 'ids' ) . '-' . $key,
							$value['id'],
							is_array( $ids ) && in_array( $value['id'], $ids ) ? 'checked' : '',
							$value['name']
						);

					} ?>
			</p>

			<!-- VIEW -->
			<p>
				<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php _e( 'View' ); ?> <span class="required">*</span></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'view' ); ?>" name="<?php echo $this->get_field_name( 'view' ); ?>">
					<option value="upcoming" <?php echo $view == 'upcoming' ? 'selected="selected"' : '' ; ?>>Upcoming</option>
					<option value="month" <?php echo $view == 'month' ? 'selected="selected"' : '' ; ?>>Month</option>
					<option value="listMonth" <?php echo $view == 'listMonth' ? 'selected="selected"' : '' ; ?>>List</option>
				</select>
			</p>

			<!-- HEIGHT -->
			<p>
				<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height (px)' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>"
						type="number" value="<?php echo $height; ?>">
			</p>

			<!-- CALENDAR LIMITS -->
			<p>
				<label for="<?php echo $this->get_field_id( 'limit_num' ); ?>"><?php _e( 'Limit calendar to next' ); ?></label>
				<input class="small" id="<?php echo $this->get_field_id( 'limit_num' ); ?>" name="<?php echo $this->get_field_name( 'limit_num' ); ?>"
						type="number" value="<?php echo esc_attr( $limit_num ); ?>" min="1">
				<select class="small" id="<?php echo $this->get_field_id( 'limit_type' ); ?>" name="<?php echo $this->get_field_name( 'limit_type' ); ?>">
					<option value="day" <?php echo $limit_type == 'day' ? 'selected="selected"' : '' ; ?>>days</option>
					<option value="event" <?php echo $limit_type == 'event' ? 'selected="selected"' : '' ; ?>>events</option>
				</select>
			</p>

			<!-- CALENDAR PAGE URL -->
			<p>
				<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'Calendar Page URL' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>"
						type="text" value="<?php echo esc_attr( $url ); ?>">
			</p>

			<!-- CALENDAR PAGE LINK TEXT -->
			<p>
				<label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Link Text' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>"
						type="text" value="<?php echo esc_attr( $link_text ); ?>">
			</p>

		</div>

		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		if( is_array( $new_instance['ids'] ) ) {
			foreach( $new_instance['ids'] as $value ) {
				if( trim( $value ) !== '' ) {
					$instance['ids'][] = $value;
				}
			}
		} else {
			$instance['ids'] 			= ( !empty( $new_instance['ids'] ) ) 			? strip_tags( $new_instance['ids'] ) 				: '';
		}

		$instance['title'] 				= ( !empty( $new_instance['title'] ) ) 			? strip_tags( $new_instance['title'] ) 				: '';
		$instance['view'] 				= ( !empty( $new_instance['view'] ) ) 			? strip_tags( $new_instance['view'] ) 				: '';
		$instance['height'] 			= ( !empty( $new_instance['height'] ) ) 		? strip_tags( $new_instance['height'] ) 			: '';
		$instance['nocontrols']			= ( !empty( $new_instance['nocontrols'] ) ) 	? strip_tags( $new_instance['nocontrols'] ) 		: '';
		$instance['limit_num'] 			= ( !empty( $new_instance['limit_num'] ) ) 		? strip_tags( $new_instance['limit_num'] ) 			: '';
		$instance['limit_type'] 		= ( !empty( $new_instance['limit_type'] ) ) 	? strip_tags( $new_instance['limit_type'] ) 		: '';
		$instance['url'] 				= ( !empty( $new_instance['url'] ) ) 			? strip_tags( $new_instance['url'] ) 				: '';
		$instance['link_text']			= ( !empty( $new_instance['link_text'] ) )		? strip_tags( $new_instance['link_text'] ) 			: '';
		return $instance;
	}

}
