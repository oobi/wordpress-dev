<?php 
/*********************************************************************
 * Widget Area
 *********************************************************************/

// set widget classes according to selected layout option
// $col_size uses Bootstrap column sizes. Total must add up to 12.
$widget_layout = get_theme_mod( 'ff_widget_layout' );

// hidden by default
$col1 = $col2 = $col3 = $col4 = 'hidden';

// decide on relative column widths depending on layout
switch( $widget_layout ) {
	// options
	case 'col-12' 		:
		$col1 = 12;
		break;
	case 'col-3-9' 		:
		$col1 = 3;
		$col2 = 9;
		break;
	case 'col-9-3' 		:
		$col1 = 9;
		$col2 = 3;
		break;
	case 'col-6-3-3' 	:
		$col1 = 6;
		$col2 = 3;
		$col3 = 3;
		break;
	case 'col-3-6-3' 	:
		$col1 = 3;
		$col2 = 6;
		$col3 = 3;
		break;
	case 'col-3-3-6' 	:
		$col1 = 3;
		$col2 = 3;
		$col3 = 6;
		break;
	case 'col-3-3-3-3' 	:
		$col1 = 3;
		$col2 = 3;
		$col3 = 3;
		$col4 = 3;
		break;
	// default to 2col
	case 'col-6-6'		:
	default				:
		$col1 = 6;
		$col2 = 6;

}

if ( is_active_sidebar( 'footer_widget_1' ) || is_active_sidebar( 'footer_widget_2' ) ||
	 is_active_sidebar( 'footer_widget_3' ) || is_active_sidebar( 'footer_widget_4' ) ) : ?>
	<tr><td id="widgets">
		<table border="0" cellpadding="0" cellspacing="15" width="640">

			<!-- quick links -->
			<tr>
				<?php if ( is_active_sidebar( 'footer_widget_1' ) && $col1 !== 'hidden' ) : ?>
					<td class="col-sm-<?php echo $col1; ?> col-xs-<?php echo $col1==3 ? '6' : '12'; ?>" valign="top">
						<?php dynamic_sidebar( 'footer_widget_1' ); ?>
					</td>
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer_widget_2' )  && $col2 !== 'hidden' ) : ?>
					<td class="col-sm-<?php echo $col2; ?> col-xs-<?php echo $col2==3 ? '6' : '12'; ?>" valign="top">
						<?php dynamic_sidebar( 'footer_widget_2' ); ?>
					</td>
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer_widget_3' )  && $col3 !== 'hidden' ) : ?>
					<td class="col-sm-<?php echo $col3; ?> col-xs-<?php echo $col3==3 ? '6' : '12'; ?>" valign="top">
						<?php dynamic_sidebar( 'footer_widget_3' ); ?>
					</td>
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer_widget_4' )  && $col4 !== 'hidden' ) : ?>
					<td class="col-sm-<?php echo $col4; ?> col-xs-<?php echo $col4==3 ? '6' : '12'; ?>" valign="top">
						<?php dynamic_sidebar( 'footer_widget_4' ); ?>
					</td>
				<?php endif; ?>
			</tr><!-- quick links -->

		</table>
	</td></tr><!-- #widgets -->
<?php endif; ?>