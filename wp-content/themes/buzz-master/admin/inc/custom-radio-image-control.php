<?php
/**
 * Class to create a custom layout control
 */
class FF_Custom_Radio_Image_Control extends WP_Customize_Control {

	// for some wordpress reason this type can't match the $type in FF_Custom_Radio_Image_Control
	// This value just overrides the class public var $type
	// no idea why but it breaks if we try to make it match
	// if you DON'T send it through then the field won't render
	// TODO: try and work this out
	public $type = 'radioimage-xxx';

	/**
	 * Render the content on the theme customizer page
	 */
	public function render_content() {
		if ( empty( $this->choices ) )
					return;

		$name = '_customize-radio-' . $this->id;

		if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description ; ?></span>
			<?php endif;

			foreach ( $this->choices as $value => $label ) : ?>
				<label>
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
					<?php echo $label ; ?><br/>
				</label>
			<?php endforeach;
	}
}