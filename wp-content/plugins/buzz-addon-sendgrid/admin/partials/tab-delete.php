<?php

/**
 * Content of the "Delete a Campaign" tab
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

// Delete Campaign
if( isset( $_POST['delete-campaign'] ) ) {

	// get POST data
	$delete_campaign_id	= $_POST['campaign-to-delete'];
	$delete_campaign_id_send_at	= $_POST['campaign-to-delete-send-at'];


	// delete campaign
	$delete_result = $ffm->delete_campaign( $delete_campaign_id,$delete_campaign_id_send_at );

	// show success/error
	$ffm->show_messages( $delete_result, 'Campaign deleted successfully.' );

} ?>

<h3>Delete a Campaign</h3>

<?php
// Get list of campaigns
$campaigns = $ffm->get_current_campaigns();

// Get the campaign record to delete
$campaign = $ffm->get_single_campaign( $cid );
?>

<div id="delete-warning" class="error">
	<h2>WARNING</h2>
	<p>Submitting the form below will delete the selected campaign <strong>permanently</strong>.<br>
		There is <strong>no way to restore a deleted campaign</strong>.</p>
</div>

<form method="post" id="delete-tab">

	<table class="widefat">
		<thead>
			<tr>
				<th class="row-title">Campaign Summary</th>
				<th></th>
			</tr>
		</thead>
		<tr>
			<td class="row-title">ID</td>
			<td valign="top"><?php echo $campaign->id; ?></td>
		</tr>
		<tr class="alternate">
			<td class="row-title">Name</td>
			<td valign="top"><?php echo $campaign->name; ?></td>
		</tr>
		<tr>
			<td class="row-title">Created</td>
			<td valign="top"><?php echo $campaign->created_at; ?></td>
		</tr>
		<tr class="alternate">
			<td class="row-title">Preview</td>
			<td valign="top"><a target="_blank" href="<?php echo "https://mc.sendgrid.com/single-sends/$campaign->id/preview"; ?>">View</a></td>
		</tr>
	</table>

	<?php // create hidden field
	printf( '<input name="campaign-to-delete" type="hidden" value="%s">',
			$campaign->id ); 
	printf( '<input name="campaign-to-delete-send-at" type="hidden" value="%s">',
			$campaign->send_at );?>

	<p class="submit">
		<input class="button button-primary" id="delete-campaign" name="delete-campaign" type="submit" value="<?php esc_attr_e('Permanently Delete Email Campaign'); ?>" />
	</p>

</form>