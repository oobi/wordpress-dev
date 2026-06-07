<?php

/**
 * Content of the "Send a Campaign" tab
 *
 * Included in admin/list-table.php
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    Buzz_Mailchimp
 * @subpackage Buzz_Mailchimp/admin/partials
 */

// get default campaign ID if passed via URL
$cid = isset( $_GET['cid'] ) ? $_GET['cid'] : NULL;

// Send test campaign
if( isset( $_POST['test-campaign'] ) ) {

	// get POST data
	$test_email	= $_POST['test-email'];
	$test_type = $_POST['test-type'];

	// send test campaign
	$test_result = $ffm->send_test_campaign( $cid, $test_email, $test_type );

	// show success/error
	$ffm->show_messages( $test_result, 'Test email successfully sent.', false );

}

// Send campaign
if( isset( $_POST['send-campaign'] ) ) {

	// get POST data
	$send_campaign	= $_POST['campaign-to-send'];

	// send campaign
	$send_result = $ffm->send_campaign( $send_campaign );

	// show success/error
	$ffm->show_messages( $send_result, 'Campaign sent successfully.', false );

}

?>


<h3>Send a Campaign</h3>

<?php
// Get the campaign to send
$campaign = $ffm->get_campaign($cid);
$ffm->show_error( $campaign );
if(!$ffm->is_error($campaign)) :

	// campaign settings
	$title 			= isset( $campaign['settings']['title'] ) 			? $campaign['settings']['title'] 		: '';
	$subject_line 	= isset( $campaign['settings']['subject_line'] ) 	? $campaign['settings']['subject_line']	: '';
	$from_name 		= isset( $campaign['settings']['from_name'] ) 		? $campaign['settings']['from_name'] 	: '';
	$reply_to 		= isset( $campaign['settings']['reply_to'] )		? $campaign['settings']['reply_to'] 	: '';
	$list_id 		= isset( $campaign['recipients']['list_id'] )			? $campaign['recipients']['list_id'] 			: '';
	$segment_text	= isset( $campaign['recipients']['segment_text'] )		? $campaign['recipients']['segment_text'] 		: '';
	$recipient_count= isset( $campaign['recipients']['recipient_count'] )	? $campaign['recipients']['recipient_count'] 	: '';
?>

	<form method="post" id="send-tab">

		<input type="hidden" name="campaign-to-send" id="campaign-to-send" value="<?php echo $cid; ?>">

		<?php
			// format dates
			date_default_timezone_set('Australia/Sydney');
			$date_format = 'Y-m-d H:i:s';
			$create_time = date( $date_format, strtotime( $campaign['create_time'] ) ); ?>

			<table id="campaign-summary" class="widefat">
				<thead>
					<tr>
						<th class="row-title">Campaign Summary</th>
						<th></th>
					</tr>
				</thead>
				<tr id="title">
					<td class="row-title">Title</td>
					<td><?php echo $title; ?></td>
				</tr>
				<tr class="alternate">
					<td class="row-title">Email Subject Line</td>
					<td><?php echo $subject_line; ?></td>
				</tr>
				<tr id="contact-list">
					<td class="row-title">Contact List</td>
					<td><?php echo $ffm->get_list_name( $list_id ); ?></td>
				</tr>
				<tr class="alternate">
					<td class="row-title">Contact List Segment</td>
					<td><?php echo !empty( $segment_text ) ? $segment_text : '<i>No segment (sending to entire list)</i>'; ?></td>
				</tr>
				<tr id="total-emails">
					<td class="row-title">Total emails to be sent</td>
					<td><?php echo $recipient_count; ?></td>
				</tr>
				<tr class="alternate">
					<td class="row-title">Created</td>
					<td><?php echo $create_time; ?></td>
				</tr>
				<tr>
					<td class="row-title">'From' Name</td>
					<td><?php echo $from_name; ?></td>
				</tr>
				<tr class="alternate">
					<td class="row-title">'Reply To' Email</td>
					<td><?php echo $reply_to; ?></td>
				</tr>
				<tr>
					<td class="row-title">Preview</td>
					<td valign="top"><a target="_blank" href="<?php echo $campaign['long_archive_url']; ?>">View</a></td>
				</tr>
			</table>

			<?php 	//////////////////////////////////////////////////////////////////////////////////////////////////////
					// CC NOTE : I can't find any way in API v3 to query the number of tests remaining (or sent)
					//////////////////////////////////////////////////////////////////////////////////////////////////////
			?>

			<fieldset>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="test-email">Test Email</label>
					</th>
					<td valign="top">
						<input id="test-email" type="text" name="test-email" class="regular-text" placeholder="Test email address"/>
						<br>
						<select id="test-type" name="test-type">
							<option value="html">HTML</option>
							<option value="plaintext">Plain Text</option>
						</select>

						<input class="button button-primary" id="test-campaign" name="test-campaign" type="submit" value="<?php esc_attr_e('Send Test Email'); ?>"/>
						<p class="description">Enter an email to send a test campaign to.</p>
					</td>
				</tr>
			</table>
			</fieldset>

			<?php // if campaign hasn't been tested yet, warn user
			/*
			$is_tested =  $campaign['tests_sent'] > 0 ? TRUE : FALSE;
			if( !$is_tested ) : ?>
				<div class="error inline">
					<p><b>This campaign has not been tested yet.</b> It is strongly recommended to send a test email using the field above before sending the campaign.</p>
				</div>
			<?php endif; */?>

			<?php
			// shall we allow sending based on status?
			$allowed = true; ?>

			<?php if( $campaign['status'] != 'save') :
				$txt = '';
			?>

				<div class="error inline">
					<?php
						switch($campaign['status']) {
							case 'paused' :
								$txt = "The campaign is currently paused. You should resume it or cancel before trying to resend.";
								$allowed = false;
								break;
							case 'schedule' :
								$txt = 'The campaign is currently scheduled to send at a future time. Are you sure you want to send it now?';
								break;
							case 'sending' :
								$txt = 'The campaign is currently sending. Please wait for it to complete.';
								$allowed = false;
								break;
							case 'sent' :
								$txt = 'The campaign has already been sent. Are you sure you wish to send it again?';
								break;
						}
					?>
					<p><?php echo $txt; ?></p>
				</div>
			<?php endif; ?>

			<p class="submit">
				<a class="button button-secondary" href="?post_type=newsletter&page=buzz-mailchimp&tab=update&cid=<?php echo $cid; ?>">Edit Campaign Details</a>
				<?php if($allowed) : ?>
					<input class="button button-primary" id="send-campaign" name="send-campaign" type="submit" value="<?php esc_attr_e('Send Email Campaign'); ?>" />
				<?php endif; ?>
			</p>
	</form>



<?php
else :

	echo "Cannot find a campaign with the id {$cid}";

endif; // is_error
?>