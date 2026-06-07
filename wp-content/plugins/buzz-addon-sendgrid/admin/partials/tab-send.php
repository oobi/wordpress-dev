<?php

/**
 * Content of the "Send a Campaign" tab
 *
 * Included in admin/ff-newsletter-sendgrid-integration-list-table.php
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    FF_Newsletter_sendgrid_Integration
 * @subpackage FF_Newsletter_sendgrid_Integration/admin/partials
 */

// get default campaign ID if passed via URL
$cid = isset( $_GET['cid'] ) ? $_GET['cid'] : NULL;

// Send test campaign
if( isset( $_POST['test-campaign'] ) ) {

	// get POST data
	$test_email	= $_POST['test-email'];
	$test_email_name	= $_POST['test-email-name'];
	$sender	= $_POST['test-sender'];

	// send test campaign
	$test_result = $ffm->send_test_campaign( $cid, $test_email, $sender, $test_email_name);

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

				<select name="campaign-to-send" id="campaign-to-send" readonly>
					<option value="0">Select Campaign</option>
					<?php
						// Get list of unsent campaigns and display as select box
						$campaigns = $ffm->get_unsent_campaigns();

						foreach( $campaigns as $campaign ) {
							printf( '<option value="%s" %s>%s</option>',
								$campaign->id,
								$cid === $campaign->id ? 'selected="selected"' : '', // default if value is provided,
								$campaign->name
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
        $campaign = $ffm->get_single_campaign( $cid, true );

		// Get the contact lists/segments and total number of emails to send
		$lists_and_segs = $ffm->get_campaign_list_info( $campaign ); 
		$sender_id = $campaign->email_config->sender_id;
		$from = $ffm->get_sender_info($sender_id);
				
		?>

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
				<td><?php echo $campaign->name; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Email Subject Line</label>
				</td>
				<td><?php echo $campaign->email_config->subject; ?></td>
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
				<td><?php echo $campaign->created_at; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">'From' Name</label>
				</td>
				<td><?php echo $from->name; ?></td>
			</tr>
			<tr>
				<td class="row-title">
					<label for="tablecell">'From' Email</label>
				</td>
				<td><?php echo $from->email; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">View Campaign</label>
				</td>
				<td><a id="edm-toggle" href="#" class="btn" style="padding: 5px 15px;">View Campaign Preview</a></td>
			</tr>
		</table>

		<!-- EDM Preview start -->
		<div id="edm-preview" style="display:none;">
			<?php echo $campaign->email_config->html_content; ?>
		</div>
		<!-- EDM Preview end -->

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="test-email">Send Preview (test) Email</label>
				</th>
				<td valign="top">
					<input id="test-sender" type="hidden" name="test-sender" value="<?php echo $sender_id; ?>" />
					<input id="test-email-name" type="hidden" name="test-email-name" value="<?php echo $campaign->name; ?>" />

					<input id="test-email" type="email" name="test-email" />
					<input class="button button-secondary" id="test-campaign" name="test-campaign" type="submit" value="<?php esc_attr_e('Send Test Email'); ?>"/>
					&nbsp;
					<p class="description">Enter an email to send a test campaign to.</p>

				</td>
			</tr>

		</table>
		
		<div style="border-top: 1px solid #999; margin-top: 1rem;">
			<p class="submit">
				<input class="button button-primary" id="send-campaign" name="send-campaign" type="submit" value="<?php esc_attr_e('Send Email Campaign'); ?>" />
			</p>
			<p class="description">This will schedule the campaign to send immediately.</p>
		</div>

	<?php endif; //isset $cid ?>

</form>
