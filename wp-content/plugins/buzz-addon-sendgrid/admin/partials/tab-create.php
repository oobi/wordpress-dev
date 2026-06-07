<?php

/**
 * Content of the "Create a Campaign" tab
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

// get default subject from options
$cm_settings 		= get_option( 'sendgrid_settings' );
$default_subject 	= isset( $cm_settings['sendgrid_subject'] ) ? $cm_settings['sendgrid_subject'] 	: Buzz_Newsletter_Sendgrid_Campaign_Page::$default_subject;
$proxy 				= isset( $cm_settings['sendgrid_proxy'] ) ? $cm_settings['sendgrid_proxy'] 	: false;
$subject = '';

// Create campaign in Sendgrid
if( isset( $_POST['create-campaign'] ) ) {

	// if form-newsletter-to-send-custom is set, use that instead of form-newsletter-to-send
	if( !empty( $_POST['form-newsletter-to-send-custom'] ) ) {
		$newsletter_url	= esc_url( $_POST['form-newsletter-to-send-custom'] );
	} else {
		$newsletter_url	= Buzz_Addon_Email_View::get_email_url( $_POST['form-newsletter-to-send'] );
	}

	// translate into proxy URL if specified
	// e.g proxy.com?url={{newsletter_url}}
	if( $proxy ) {
		$newsletter_url = $proxy . urlencode($newsletter_url);
	}

	// transform POST data into options formats
	$list_id 		= isset( $_POST['contact-list'] ) ? $_POST['contact-list'] : array();
	$segment 		= isset( $_POST['list-segment'] ) ? $_POST['list-segment'] : array();
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
				<p class="description">Choose the newsletter to create a new email campaign.</p>

			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="form-subject">Subject</label>
			</th>
			<td valign="top">

				<input type="text" class="large-text" name="form-subject" value="<?php echo $subject; ?>">
				<p class="description">Customise the subject for this campaign or use the default setting.</p>

			</td>
		</tr>
		<tr id="contact-list">
			<th scope="row">
				<label for="contact-list">Contact List</label>
			</th>
			<td valign="top">

				<fieldset>
					<?php
						// Get list of contact lists and display as checkboxes
						$lists = $ffm->get_lists();

						foreach( $lists as $list ) {
							// list
							printf( '<label><input class="contact-list" type="checkbox" name="contact-list[]" value="%s">%s</label><br>',
								$list->id,
								$list->name);

							// segments
							if( !empty( $list->Segments ) ) {
								echo '<div class="segments">';
								foreach( $list->Segments as $segment ) {
									printf( '<label><input class="list-segment" type="checkbox" name="list-segment[]" data-list-id="%s" value="%s">%s</label><br>',
										$segment->parent_list_ids[0],
										$segment->id,
										$segment->name);
								}
								echo '</div>';
							}
						}

					?>
				</fieldset>
				<p class="description">Choose the contact list/segment to send the email campaign to.</p>

			</td>
		</tr>
	</table>

	<p class="submit">
		<input class="button button-primary" id="create-campaign" name="create-campaign" type="submit" value="<?php esc_attr_e('Create Email Campaign'); ?>" />
	</p>

</form>
