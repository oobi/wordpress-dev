<?php

/**
 * Content of the "Create a Campaign" tab
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

// get default subject from options
$cm_settings 		= get_option( 'buzz_mailchimp_settings' );
$default_subject 	= isset( $cm_settings['mailchimp_subject'] ) ? $cm_settings['mailchimp_subject'] 	: Buzz_Mailchimp_Campaign_Page::$default_subject;
$subject = '';


// Create campaign in MailChimp
if( isset( $_POST['create-campaign'] ) ) {

	// if form-newsletter-to-send-custom is set, use that instead of form-newsletter-to-send
	if( !empty( $_POST['form-newsletter-to-send-custom'] ) ) {
		$newsletter_url	= esc_url( $_POST['form-newsletter-to-send-custom'] );
	} else {
		$newsletter_url	= Buzz_Addon_Email_View::get_email_url( $_POST['form-newsletter-to-send'] );
	}

	// transform POST data into options formats
	$list_id 		= $_POST['form-contact-list'];
	$segment 		= $_POST['form-list-segment'];
	$subject		= isset( $_POST['form-subject'] ) && !empty( $_POST['form-subject'] ) ? $_POST['form-subject'] : $default_subject;

	// get rid of slashes Wordpress inserts during form submission
	$subject = wp_unslash($subject); // sanitize subject

	// resolve tokens in subject
	$resolved_subject = $this->build_subject($_POST['form-newsletter-to-send'], $subject);

	// create campaign
	$create_result = $ffm->create_campaign( $list_id, $resolved_subject, $newsletter_url, $segment );

	// show success/error
	$ffm->show_messages( $create_result, 'Campaign created successfully.' );
}

?>

<h3>Create a Campaign</h3>

<form method="post" id="create-tab">

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="form-newsletter-to-send">Newsletter</label>
			</th>
			<td valign="top">

				<select name="form-newsletter-to-send" id="newsletter-to-send">
					<?php
						// Get list of newsletters and display as select box
						$newsletters = ff_get_newsletters();
						foreach( $newsletters as $newsletter ) {
							printf( '<option value="%s" data-subject="%s">%s &mdash; %s</option>',
								$newsletter->ID,
								esc_attr($this->build_subject($newsletter, $default_subject)),
								$newsletter->post_title,
								get_the_date( 'j F Y', $newsletter->ID )
							);
						}

						// add custom link - does not include a subject (triggers JS to show form-newsletter-to-send-custom field)
						echo '<option value="0">-- Insert custom URL --</option>';
					?>
				</select>
				<input type="url" class="regular-text" name="form-newsletter-to-send-custom" value="" placeholder="http://www.example.com/email">
				<p class="description">Choose a recent newsletter to create a new email campaign or insert a custom URL.</p>

			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="form-subject">Subject Line</label>
			</th>
			<td valign="top">

				<input type="text" class="large-text" name="form-subject" value="<?php echo $subject; ?>">
				<p class="description">Customise the email subject line for this campaign or use the default setting.</p>

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

						echo '<select name="form-contact-list">';
						foreach( $lists['lists'] as $list ) {
							printf( '<option value="%s">%s (%s)</option>',
								$list['id'],
								$list['name'],
								$list['stats']['member_count'] );
						}
						echo '</select>';
					?>
				</fieldset>
				<p class="description">Choose the contact list to send the email campaign to.</p>

			</td>
		</tr>
		<tr id="segments">
			<th scope="row">
				<label for="form-list-segment">Contact List Segment</label>
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
				<p class="description">Choose the segment of the contact list the campaign will be sent to.</p>

			</td>
		</tr>
	</table>

	<p class="submit">
		<input class="button button-primary" id="create-campaign" name="create-campaign" type="submit" value="<?php esc_attr_e('Create Email Campaign'); ?>" />
	</p>

</form>
