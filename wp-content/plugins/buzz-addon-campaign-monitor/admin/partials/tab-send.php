<?php

/**
 * Content of the "Send a Campaign" tab
 *
 * Included in admin/ff-newsletter-mailchimp-integration-list-table.php
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    FF_Newsletter_CampaignMonitor_Integration
 * @subpackage FF_Newsletter_CampaignMonitor_Integration/admin/partials
 */

// get default campaign ID if passed via URL
$cid = isset( $_GET['cid'] ) ? $_GET['cid'] : NULL;

// Send test campaign
if( isset( $_POST['test-campaign'] ) ) {

	// get POST data
	$test_email	= $_POST['test-email'];

	// send test campaign
	$test_result = $ffm->send_test_campaign( $cid, $test_email );

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

<form method="post" id="send-tab">

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="campaign-to-send">Campaign</label>
			</th>
			<td valign="top">

				<select name="campaign-to-send" id="campaign-to-send">
					<option value="0"></option>
					<?php
						// Get list of unsent campaigns and display as select box
						$campaigns = $ffm->get_unsent_campaigns();

						foreach( $campaigns as $campaign ) {
							printf( '<option value="%s" %s>%s</option>',
								$campaign->CampaignID,
								$cid === $campaign->CampaignID ? 'selected="selected"' : '', // default if value is provided,
								$campaign->Name
							);
						}
					?>
				</select>
				<p class="description">Choose the campaign you wish to send.</p>

			</td>
		</tr>

	</table>

	<?php

	if( isset( $cid) && $cid != '0' ) :

		// Get the currently selected campaign
		$campaigns = $ffm->get_unsent_campaigns( $cid );
        $campaign = $ffm->get_campaign( $campaigns, $cid );

		// Get the contact lists/segments and total number of emails to send
		$lists_and_segs = $ffm->get_campaign_list_info( $cid ); ?>

		<table id="campaign-summary" class="widefat">
			<thead>
				<tr>
					<th class="row-title">Campaign Summary</th>
					<th></th>
				</tr>
			</thead>
			<tr id="title">
				<td class="row-title">
					<label for="tablecell">Title</label>
				</td>
				<td><?php echo $campaign->Name; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Email Subject Line</label>
				</td>
				<td><?php echo $campaign->Subject; ?></td>
			</tr>
			<tr id="contact-list">
				<td class="row-title">
					<label for="tablecell">Contact Lists and Segments</label>
				</td>
				<td>
					<dl>
						<?php // Display lists and segments
						foreach( $lists_and_segs['lists'] as $list ) {
							// if a standalone contact list (no segments)
							if( $list['type'] == 'list' ) {
								printf( '<dt class="contact-list">%s (%s)</dt><dd></dd>',
										$list['name'],
										$list['count'] );
							}
							// else it's a parent list with segments
							else {
								printf( '<dt class="parent-list">%s</dt>',
										$list['name'] );
								foreach( $list['segments'] as $seg ) {
									printf( '<dd class="list-segment">%s (%s)</dd>',
										$seg['name'],
										$seg['count'] );
								}
							}
						} ?>
					</dl>
				</td>
			</tr>
			<tr id="total-emails" class="alternate">
				<td class="row-title">
					<label for="tablecell">Total emails to be sent</label>
				</td>
				<td><?php echo $lists_and_segs['total_emails']; ?></td>
			</tr>
			<tr>
				<td class="row-title">
					<label for="tablecell">Date Created</label>
				</td>
				<td><?php echo $campaign->DateCreated; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">'From' Name</label>
				</td>
				<td><?php echo $campaign->FromName; ?></td>
			</tr>
			<tr>
				<td class="row-title">
					<label for="tablecell">'From' Email</label>
				</td>
				<td><?php echo $campaign->FromEmail; ?></td>
			</tr>
		</table>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="test-email">Test Email</label>
				</th>
				<td valign="top">

					<input id="test-email" type="text" name="test-email" />
					<input class="button button-secondary" id="test-campaign" name="test-campaign" type="submit" value="<?php esc_attr_e('Send Test Email'); ?>"/>
					&nbsp;
					<p class="description">Enter an email to send a test campaign to.</p>

				</td>
			</tr>

		</table>

		<p class="submit">
			<input class="button button-primary" id="send-campaign" name="send-campaign" type="submit" value="<?php esc_attr_e('Send Email Campaign'); ?>" />
		</p>

	<?php endif; //isset $cid ?>

</form>
