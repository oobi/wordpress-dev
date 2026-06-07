<?php

class Buzz_Addon_Dates_Widget extends WP_Widget {

	protected $sets = array();
	protected $templates = array();
	protected $timber_active = false;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// setup widget
		$widget_ops = array(
			'classname' => 'buzz_addons_dates',
			'description' => 'Show Buzz dates attached to the currently viewed newsletter',
		);
		parent::__construct( 'buzz_addons_dates', 'Buzz Dates', $widget_ops );

		// check if Timber is active
		if( !class_exists( 'Timber' ) ) {
			return;
		}

		// get date sets
		$this->sets 		= get_theme_mod( 'buzz_dates_sets' );
		$this->templates 	= Buzz_Addon_Dates_Data::get_templates();

		// Add Timber locations to render twigs
		Timber::$locations = BUZZ_ADDON_DATES_PATH . 'public/views';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if( ! is_array( $instance ) || ! isset( $instance['set'] ) || empty( $instance['set'] ) ) {
			return; // no set selected, so nothing to show
		}

		echo $args['before_widget'];

		$template = $instance['template'] ?? $this->templates[0]['slug']; // default to first template

		$params = [
			'set'			=> $instance['set'],
			'title' 		=> $instance['title'],
			'template'		=> $template,
			'format'		=> $instance['format'] ?: '%e %B', // if not blank, default
			'show_dates' 	=> $instance['show_dates'],
			'merge_dates' 	=> $instance['merge_dates'],
			'show_icons' 	=> $instance['show_icons'],
			// 'link_icon'		=> get_theme_mod( 'buzz_dates_link_icon', '' ),
			// 'class'			=> get_theme_mod( 'buzz_dates_class', '' ),
			// 'column_class'	=> get_theme_mod( 'buzz_dates_column_class', '' ),
		];

		// get data from date set
		$set_data = Buzz_Addon_Dates_Data::get_dates( $params );
		$params['data'] = $set_data ? $set_data : array();

		// render dates
		Timber::render( "$template.twig", $params );

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// options
		$options 	= get_option( 'buzz_addon_dates_widget' );

		// saved widget data
		$set 		= array_key_exists( 'set', $instance ) 			? $instance['set'] 			: '';
		$title 		= array_key_exists( 'title', $instance ) 		? $instance['title'] 		: '';
		$template	= array_key_exists( 'template', $instance ) 	? $instance['template'] 	: '';
		$format		= array_key_exists( 'format', $instance ) 		? $instance['format'] 		: '%e %B';
		$show_dates	= array_key_exists( 'show_dates', $instance )	? $instance['show_dates'] 	: '';
		$merge_dates= array_key_exists( 'merge_dates', $instance )	? $instance['merge_dates'] 	: '';
		$show_icons	= array_key_exists( 'show_icons', $instance )	? $instance['show_icons'] 	: '';
		?>

		<div class="buzz-addon-dates-widget-form">

			<!-- DATE SET -->
			<p>
				<label for="<?php echo $this->get_field_id( 'set' ); ?>"><?php _e( 'Date Set' ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'set' ); ?>" name="<?php echo $this->get_field_name( 'set' ); ?>">

					<?php
					foreach( $this->sets as $s ) {
						printf( '<option value="%s" %s>%s</option>',
									$s['class'],
									$s['class'] === $set ? 'selected="selected"' : '', // check if matches database value
									$s['label']
						);
					} ?>

				</select>
			</p>

			<!-- TITLE -->
			<p>
				<?php
				// TODO: update all other fields to use printf
				printf( '<label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s">',
						$this->get_field_id( 'title' ),
						__( 'Title' ),
						$this->get_field_name( 'title' ),
						esc_attr( $title )
				); ?>
			</p>

			<!-- TEMPLATES -->
			<p>
				<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template' ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>">

					<?php
					foreach( $this->templates as $t ) {
						printf( '<option value="%s" %s>%s</option>',
									$t['slug'],
									$t['slug'] === $template ? 'selected="selected"' : '', // check if matches database value
									$t['name']
						);
					} ?>

				</select>
			</p>

			<!-- DATE FORMAT -->
			<p>
				<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Date Format' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>"
						type="text" value="<?php echo esc_attr( $format ); ?>">
			</p>

			<!-- SHOW DATES -->
			<p>
				<?php
				printf( '<label for="%1$s"><input id="%1$s" name="%2$s" type="checkbox" %3$s> %4$s</label>',
					$this->get_field_id( 'show_dates' ),
					$this->get_field_name( 'show_dates' ),
					$show_dates ? 'checked' : '',
					__( 'Show dates' )
				); ?>
			</p>

			<!-- MERGE DATES -->
			<p>
				<?php
				printf( '<label for="%1$s"><input id="%1$s" name="%2$s" type="checkbox" %3$s> %4$s</label>',
					$this->get_field_id( 'merge_dates' ),
					$this->get_field_name( 'merge_dates' ),
					$merge_dates ? 'checked' : '',
					__( 'Merge dates listed on same day' )
				); ?>
			</p>

			<!-- SHOW ICONS -->
			<p>
				<?php
				printf( '<label for="%1$s"><input id="%1$s" name="%2$s" type="checkbox" %3$s> %4$s</label>',
					$this->get_field_id( 'show_icons' ),
					$this->get_field_name( 'show_icons' ),
					$show_icons ? 'checked' : '',
					__( 'Show icons' )
				); ?>
			</p>

		</div>

		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['set'] 		= ( !empty( $new_instance['set'] ) ) 		? strip_tags( $new_instance['set'] ) 		: '';
		$instance['title'] 		= ( !empty( $new_instance['title'] ) ) 		? strip_tags( $new_instance['title'] ) 		: '';
		$instance['template'] 	= ( !empty( $new_instance['template'] ) ) 	? strip_tags( $new_instance['template'] ) 	: '';
		$instance['format'] 	= ( !empty( $new_instance['format'] ) ) 	? $new_instance['format'] 					: '';
		$instance['show_icons'] = !empty( $new_instance['show_icons'] ); // true or false
		$instance['merge_dates']= !empty( $new_instance['merge_dates'] ); // true or false
		$instance['show_dates'] = !empty( $new_instance['show_dates'] ); // true or false
		return $instance;
	}
}