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

?>

<h3>Campaign Statistics</h3>

<form method="post" id="send-tab">

	<?php

	if( isset( $cid) && $cid != '0') :
		// Get the currently selected campaign stats
		$campaigns = $ffm->get_sent_campaigns();
        $campaign = $ffm->get_campaign( $campaigns, $cid );
        $campaign_stats = $ffm->get_summary( $cid );
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
				<td><?php echo $campaign->Name; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Subject</label>
				</td>
				<td><?php echo $campaign->Subject; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">From Name</label>
				</td>
				<td><?php echo $campaign->FromName; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">From Email</label>
				</td>
				<td><?php echo $campaign->FromEmail; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Reply To</label>
				</td>
				<td><?php echo $campaign->ReplyTo; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Sent Date</label>
				</td>
				<td><?php echo $campaign->SentDate; ?></td>
			</tr>
		    <tr>
				<td class="row-title">
					<label for="tablecell">Recipients</label>
				</td>
				<td><?php echo $campaign_stats->Recipients; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Total Opened</label>
				</td>
				<td><?php echo $campaign_stats->TotalOpened; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Clicks</label>
				</td>
				<td><?php echo $campaign_stats->Clicks; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Unsubscribed</label>
				</td>
				<td><?php echo $campaign_stats->Unsubscribed; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Bounced</label>
				</td>
				<td><?php echo $campaign_stats->Bounced; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">UniqueOpened</label>
				</td>
				<td><?php echo $campaign_stats->UniqueOpened; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Spam Complaints</label>
				</td>
				<td><?php echo $campaign_stats->SpamComplaints; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">View Campaign</label>
				</td>
				<td><a href="<?php echo $campaign_stats->WebVersionURL; ?>" target="_blank"</a>View Campaign</td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Forwards</label>
				</td>
				<td><?php echo $campaign_stats->Forwards; ?></td>
			</tr>
			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">Likes</label>
				</td>
				<td><?php echo $campaign_stats->Likes; ?></td>
			</tr>
            <tr>
				<td class="row-title">
					<label for="tablecell">Mentions</label>
				</td>
				<td><?php echo $campaign_stats->Mentions; ?></td>
			</tr>

		</table>

	<?php endif; //isset $cid ?>

</form>
