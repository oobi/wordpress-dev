<?php

/**
 * Content of the "Delete a Campaign" tab
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

// Delete Campaign
if( isset( $_POST['campaign-to-delete'] ) ) {

	// get POST data
	$delete_campaign_id	= $_POST['campaign-to-delete'];

	// delete campaign
	$delete_result = $ffm->delete_campaign( $delete_campaign_id );

	// show success/error
	$ffm->show_messages( $delete_result, 'Campaign deleted successfully.' );

}

?>

<h3>Delete a Campaign</h3>

<?php
// Get the campaign record to delete
$campaign = $ffm->get_campaign($cid);
if(!$ffm->is_error($campaign)) :
?>

<div id="delete-warning" class="error">
	<h2>WARNING</h2>
	<p>Submitting the form below will delete the selected campaign <strong>permanently</strong>.<br>
		There is <strong>no way to restore a deleted campaign</strong>.</p>
</div>


<form method="post" id="delete-tab">

	<?php // format dates 
		date_default_timezone_set('Australia/Sydney');
		$date_format = 'Y-m-d H:i:s';
		$create_time = date( $date_format, strtotime( $campaign['create_time'] ) );
	?>

	<table class="widefat">
		<thead>
			<tr>
				<th class="row-title">Campaign Summary</th>
				<th></th>
			</tr>
		</thead>
		<tr>
			<td class="row-title">ID</td>
			<td valign="top"><?php echo $campaign['id']; ?></td>
		</tr>
		<tr class="alternate">
			<td class="row-title">Name</td>
			<td valign="top"><?php echo $campaign['settings']['title']; ?></td>
		</tr>
		<tr>
			<td class="row-title">Created</td>
			<td valign="top"><?php echo $create_time; ?></td>
		</tr>
		<tr class="alternate">
			<td class="row-title">Contact List</td>
			<td valign="top"><?php echo $campaign['recipients']['list_name'] . ' (' . $campaign['recipients']['recipient_count'] . ')'; ?></td>
		</tr>
		<tr>
			<td class="row-title">Preview</td>
			<td valign="top"><a target="_blank" href="<?php echo $campaign['long_archive_url']; ?>">View</a></td>
		</tr>
	</table>

	<?php // create hidden field
	printf( '<input name="campaign-to-delete" type="hidden" value="%s">',
			$campaign['id'] ); ?>

	<p class="submit">
		<input class="button button-primary" id="delete-campaign" name="delete-campaign" type="submit" value="<?php esc_attr_e('Permanently Delete Email Campaign'); ?>" />
	</p>
</form>

<?php
else :
	echo "Campaign with ID {$cid} has been deleted";
endif;
?>
