<?php

/**
 * The settings page "Generate Shortcode" tab
 *
 * @link       http://www.fi.net.au
 * @since      1.0.0
 *
 * @package    FF_Calendar
 * @subpackage FF_Calendar/admin/partials
 */
?>

<?php
	$options = get_option( 'ff_calendar_settings' );

	// if there are no feeds, show warning
	if( !$options || ( !array_key_exists( 'calendar_feeds', $options ) && !empty( $options['calendar_feeds'] ) ) ) : ?>
		<p>No feeds have been set up. Go to the <a href="?page=ff_calendar_settings&tab=settings">Feed Settings</a> to import a calendar feed.</p>

<?php // else show the generate tab
	else :
		$feeds = $options['calendar_feeds'];
?>

<div id="ff-cal-shortcode-version">
	The current shortcode version is: <span class="current-version"><?php echo FF_CALENDAR_SHORTCODE_VERSION; ?></span>
</div>
<p>Use this form to generate a custom calendar shortcode</p>

<form name="ff-calendar-shortcode-generate">
	<table class="form-table">
		<tr>
			<th scope="row">Feeds <span class="required">*</span></th>
			<td>
				<?php
				// loop the feeds, extract the feed name and output checkboxes
				foreach( $feeds as $feed ) : ?>
					<input id="feed_id_<?php echo $feed['id']; ?>" class="feed-checkbox" name="<?php echo $feed['name']; ?>" value="<?php echo $feed['id']; ?>" checked="checked" type="checkbox">
					<label for="feed_id_<?php echo $feed['id']; ?>"><?php echo $feed['name']; ?></label><br>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr>
			<th scope="row">Default view <span class="required">*</span></th>
			<td>
				<select name="view">
					<option value="month">Month</option>
					<option value="listMonth">List</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">Calendar height</th>
			<td>
				<input id="height" class="height" name="height" type="number" value="" min="1">
				<label for="height">px</label><br>
			</td>
		</tr>
		<tr>
			<th scope="row">Custom mobile snap point</th>
			<td>
				<input id="snap" class="snap" name="snap" type="number" value="" min="1">
				<label for="snap">px</label><br>
			</td>
		</tr>
		<tr>
			<th scope="row">Week starts on</th>
			<td>
				<select name="weekstart">
					<option value="" selected="selected">-- Default --</option>
					<option value="1">Monday</option>
					<option value="2">Tuesday</option>
					<option value="3">Wednesday</option>
					<option value="4">Thursday</option>
					<option value="5">Friday</option>
					<option value="6">Saturday</option>
					<option value="7">Sunday</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">Hide Weekends</th>
			<td>
				<input id="noweekends" name="noweekends" class="noweekends-checkbox" value="true" type="checkbox">
			</td>
		</tr>
		<tr>
			<th scope="row">Hide Controls</th>
			<td>
				<input id="nocontrols" name="nocontrols" class="nocontrols-checkbox" value="true" type="checkbox">
			</td>
		</tr>
	</table>
</form>

<p>Copy and paste this shortcode onto the Calendar page</p>

<textarea id="shortcode-output" class="widefat" readonly>
	<?php // filled via JS ?>
</textarea>

<?php endif;