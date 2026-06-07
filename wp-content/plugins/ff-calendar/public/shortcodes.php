<?php

namespace FF\Calendar;

/**
 * The class to create the shortcodes used to display the calendar
 *
 * @package    ff-calendar
 * @subpackage ff-calendar/admin
 * @author     Firefly Interactive
 */
class Shortcodes {

	/**
	 * Constructor
	 */
	public function __construct() {
		// set up shortcode
		add_shortcode( 'ff-calendar', array( 'FF\Calendar\Shortcodes', 'shortcode_callback' ) );
	}

	/**
	 * Get the shortcode version
	 */
	public static function get_version( $attr ) {
		// loop through attributes and return version number if found
		// version num is a value-less attribute so it does not have a String key
		foreach ( $attr as $key => $value ) {
			if ( is_int( $key ) ) {
				return $value;
			}
		}
		return 'v1'; // if version number is not found, assume it is version 1
	}

	/**
	 * Return shortcode data
	 */
	public static function shortcode_callback( $attr ) {
// var_dump($attr); // debug

		// determine if callback called from widget
		// widget output needs to be echoed. Does not work with output buffer
		$is_widget = array_key_exists( 'widget', $attr );

		// trim spaces and commas from IDs string
		$ids = trim( $attr['ids'], ', ' );

		// begin buffer (this allows echoing the calendar at any point of a page)
		ob_start();	?>

		<div class="ff-calendar-wrapper <?php echo $is_widget ? 'widget' : 'page'; ?>">

			<!-- calendar -->
			<div class="ff-calendar"
				data-version="<?php echo self::get_version( $attr ); ?>"
				data-view="<?php echo $attr['view']; ?>"
				data-ids="<?php echo $ids; ?>"
				data-height="<?php echo isset( $attr['height'] ) ? $attr['height'] : ''; ?>"
				data-snap="<?php echo isset( $attr['snap'] ) ? $attr['snap'] : ''; ?>"
				data-weekstart="<?php echo isset( $attr['weekstart'] ) ? $attr['weekstart'] : ''; ?>"
				data-noweekends="<?php echo isset( $attr['noweekends'] ) ? $attr['noweekends'] : ''; ?>"
				data-nocontrols="<?php echo isset( $attr['nocontrols'] ) ? $attr['nocontrols'] : ''; ?>"
				data-limit-num="<?php echo isset( $attr['limit-num'] ) ? $attr['limit-num'] : ''; ?>"
				data-limit-type="<?php echo isset( $attr['limit-type'] ) ? $attr['limit-type'] : ''; ?>"
				></div>

			<!-- loading -->
			<div class="ff-calendar-loading">
				<div class="inner"><i class="fa fa-fw fa-spinner fa-spin"></i> Loading...</div>
			</div>

			<!-- details template -->
			<script type="text/html" id="ff-tpl-calendar-details">
				<div class="ff-calendar-details">
					<div class="ff-calendar-modal-header">
						<h2 class="title"><!-- filled in JS --></h2>
					</div>
					<div class="ff-calendar-modal-content">
						<div class="date"><span><!-- filled in JS --></span></div>
						<div class="time"><span><!-- filled in JS --></span></div>
						<div class="segment"><span><!-- filled in JS --></span></div>
						<div class="location"><span><!-- filled in JS --></span></div>
						<p class="description"><!-- filled in JS --></p>
						<div class="categories"><span><!-- filled in JS --></span></div>
					</div>
				</div>
			</script>

			<!-- category template -->
			<script type="text/html" id="ff-tpl-calendar-categories">
				<div class="ff-calendar-categories">
					<div class="ff-calendar-modal-header">
						<h2 class="title">Categories</h2>
					</div>
					<div class="ff-calendar-modal-content">
						<form id="calendar_categories" class="clearfix">
							<!-- filled in JS -->
						</form>
					</div>
				</div>
			</script>

		</div>

		<?php // return the output
		$output = ob_get_clean();

		if( $is_widget ) {
			echo $output;
			return;
		} else {
			return $output;
		}
	}

}