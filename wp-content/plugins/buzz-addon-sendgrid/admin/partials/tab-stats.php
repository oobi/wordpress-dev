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

?>

<h3>Campaign Statistics</h3>

<form method="post" id="send-tab">

	<?php

	if( isset( $cid) && $cid != '0') :
		// Get the currently selected campaign stats
		$campaigns = $ffm->get_sent_campaigns();
        $campaign = $ffm->get_single_campaign( $cid );
        $campaign_stats = $ffm->get_summary( $cid );
		
		// Get the contact lists/segments and total number of emails to send
		$lists_and_segs = $ffm->get_campaign_list_info( $campaign ); 

		//$ffm->pretty_print($campaign->email_config);

		$sender_id = $campaign->email_config->sender_id;
		$from = $ffm->get_sender_info($sender_id);
	?>

		<table id="campaign-summary" class="widefat">
			<thead>
				<tr>
					<th class="row-title">Campaign Statistics</th>
					<th></th>
				</tr>
			</thead>
            <tr>
				<td class="row-title">
					<label for="tablecell">Campaign Name</label>
				</td>
				<td><?php echo $campaign->name; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Subject</label>
				</td>
				<td><?php echo $campaign->email_config->subject; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">From Name</label>
				</td>
				<td><?php echo $from->name; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">From Email</label>
				</td>
				<td><?php echo $from->email; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Sent Date</label>
				</td>
				<td><?php echo $campaign->send_at; ?></td>
			</tr>
		    <tr>
				<td class="row-title">
					<label for="tablecell">Recipients</label>
				</td>
				<td><?php echo $lists_and_segs['total_emails']; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Total Opened</label>
				</td>
				<td><?php echo $campaign_stats->stats->opens; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Clicks</label>
				</td>
				<td><?php echo $campaign_stats->stats->clicks; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Unsubscribed</label>
				</td>
				<td><?php echo $campaign_stats->stats->unsubscribes; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Bounced</label>
				</td>
				<td><?php echo $campaign_stats->stats->bounces; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">UniqueOpened</label>
				</td>
				<td><?php echo $campaign_stats->stats->unique_opens; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Spam Complaints</label>
				</td>
				<td><?php echo $campaign_stats->stats->spam_reports; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">View Campaign</label>
				</td>
				<td><a id="edm-toggle" href="#">View Campaign</a></td>
			</tr>

		</table>
		<div id="edm-preview" style="display:none;">
			<?php echo $campaign->email_config->html_content; ?>
		</div>
	<?php endif; //isset $cid ?>

</form>
