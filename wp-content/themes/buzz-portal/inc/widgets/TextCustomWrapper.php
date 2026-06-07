<?php
/**
 * text with custom wrapper widget
 *
 * @since 2.8.0
 */
class TextCustomWrapper extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function TextCustomWrapper() {
		$widget_ops = array('classname' => 'widget_text_custom_wrapper', 'description' => __('Arbitrary text or HTML with custom CSS wrapper'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('text_custom_wrapper', __('Text Custom Wrapper'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title 		= apply_filters( 'widget_title', 	empty( $instance['title'] ) 	? '' : $instance['title'], $instance, $this->id_base );
		$text 		= apply_filters( 'widget_text', 	empty( $instance['text'] ) 		? '' : $instance['text'], $instance );
		$cssid 		= apply_filters( 'widget_cssid', 	empty( $instance['cssid'] ) 	? '' : $instance['cssid'], $instance );
		$cssclass 	= apply_filters( 'widget_cssclass', empty( $instance['cssclass'] ) 	? '' : $instance['cssclass'], $instance );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="<?php echo $cssclass; ?>" id="<?php echo $cssid; ?>"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['cssclass'] 	= strip_tags($new_instance['cssclass']);
		$instance['cssid'] 		= strip_tags($new_instance['cssid']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'cssclass' => '', 'cssid' => '' ) );
		$title 		= strip_tags($instance['title']);
		$text 		= esc_textarea($instance['text']);
		$cssclass 	= strip_tags($instance['cssclass']);
		$cssid 		= strip_tags($instance['cssid']);
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
			
			<p><label for="<?php echo $this->get_field_id('cssclass'); ?>"><?php _e('CSS Class:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cssclass'); ?>" name="<?php echo $this->get_field_name('cssclass'); ?>" type="text" value="<?php echo esc_attr($cssclass); ?>" /></p>
			
			<p><label for="<?php echo $this->get_field_id('cssid'); ?>"><?php _e('CSS ID:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cssid'); ?>" name="<?php echo $this->get_field_name('cssid'); ?>" type="text" value="<?php echo esc_attr($cssid); ?>" /></p>
	
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
	
			<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
		<?php
	}
}