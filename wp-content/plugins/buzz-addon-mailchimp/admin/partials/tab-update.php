<?php

/**
 * Content of the "Update a Campaign" tab
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

// if $cid is not set, show error
if( empty($cid) ) {
	echo '<div class="error"><p>No campaign selected.</p></div>';
	return;
}

// Update Campaign Options
if( isset( $_POST['update-options'] ) ) {

	// get POST data
	$form_campaign_name	= $_POST['form-campaign-name'];
	$form_email_subject = $_POST['form-email-subject'];
	$form_from_name		= $_POST['form-from-name'];
	$form_from_email	= $_POST['form-reply-to'];
	$form_contact_list	= $_POST['form-contact-list']; // Resetting the list_id will cause all segment options to be dropped. Must re-init those each time.
	$form_list_segment	= $_POST['form-list-segment'];

	// update campaign options
	$update_result = $ffm->update_campaign( $cid, $form_campaign_name, $form_email_subject, $form_from_name,
											$form_from_email, $form_contact_list, $form_list_segment );

	// show success/error
	$ffm->show_messages($update_result, 'Campaign options updated successfully.');
}

// Get campaign according to $cid
$args = array(
	'apikey'	=> $api_key,
	'filters'	=> array( 'campaign_id' => $cid )
);
$campaign = $ffm->get_campaign($cid);
$ffm->show_error($campaign);

?>

<?php if( !$ffm->is_error($campaign) ) :

	// campaign settings
	$title 			= isset( $campaign['settings']['title'] ) 			? $campaign['settings']['title'] 		: '';
	$subject_line 	= isset( $campaign['settings']['subject_line'] ) 	? $campaign['settings']['subject_line'] : '';
	$from_name 		= isset( $campaign['settings']['from_name'] ) 		? $campaign['settings']['from_name'] 	: '';
	$reply_to 		= isset( $campaign['settings']['reply_to'] )		? $campaign['settings']['reply_to'] 	: '';
?>
<h3>Update Campaign Options</h3>

<form method="post" id="update-campaign-options">
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="form-campaign-name">Campaign Name</label>
			</th>
			<td valign="top">
				<input id="form-campaign-name" type="text" name="form-campaign-name" value="<?php echo $title; ?>"/>
				<p class="description">Campaign name for internal reference. Will not be seen by recipients.</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="form-email-subject">Email Subject</label>
			</th>
			<td valign="top">
				<input id="form-email-subject" type="text" name="form-email-subject" value="<?php echo $subject_line; ?>"/>
				<p class="description">The text that appears on the sent email subject line.</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="form-from-name">"From" Name</label>
			</th>
			<td valign="top">
				<input id="form-from-name" type="text" name="form-from-name" value="<?php echo $from_name; ?>"/>
				<p class="description">The name recipients will see on the sent campaign.</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="form-reply-to">"Reply To" Email</label>
			</th>
			<td valign="top">
				<input id="form-reply-to" type="text" name="form-reply-to" value="<?php echo $reply_to; ?>"/>
				<p class="description">The email address recipients will see on the sent campaign. Must be a valid email address.</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="form-contact-list">Contact List</label>
			</th>
			<td valign="top">

				<fieldset>
					<?php
						// Get list of contact lists and display as radio buttons
						$lists = $ffm->get_lists();
						$ffm->show_error($lists);

						if(!$ffm->is_error($lists)) {
							echo '<select name="form-contact-list">';
							foreach( $lists['lists'] as $list ) {
								printf( '<option value="%s" %s>%s (%s)</option>',
									$list['id'],
									$list['id'] == $campaign['recipients']['list_id'] ? 'selected="selected"' : '',
									$list['name'],
									$list['stats']['member_count'] );
							}
							echo '</select>';

						}
					?>
				</fieldset>
				<p class="description">The contact list the campaign will be sent to.</p>

			</td>
		</tr>


		<tr id="segments">
			<th scope="row">
				<label for="form-list-segments">Contact List Segment</label>
			</th>
			<td valign="top">

				<fieldset class="segment">
					<div class="spinner"></div>
					<?php
						// get the currently set segment id and attach to SELECT (for consumption via JS)
						$saved_segment_id = isset($campaign['recipients']['segment_opts']['saved_segment_id']) ? $campaign['recipients']['segment_opts']['saved_segment_id'] : NULL;
					?>
					<select name="form-list-segment" data-value="<?php echo $saved_segment_id; ?>">
						<option value=""></option>
						<?php // filled in via AJAX ?>
					</select>
				</fieldset>
				<p class="description">The list segment the campaign will be sent to.</p>

			</td>
		</tr>


	</table>

	<p class="submit">
		<input class="button button-primary" id="update-options" name="update-options" type="submit" value="<?php esc_attr_e('Update Campaign Options'); ?>" />
	</p>

</form>

<?php endif; ?>