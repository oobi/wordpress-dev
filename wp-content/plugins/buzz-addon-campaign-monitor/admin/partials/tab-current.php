<?php

/**
 * Content of the "Current Campaigns" tab
 *
 * Included in admin/ff-newsletter-mailchimp-integration-list-table.php
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    FF_Newsletter_CampaignMonitor_Integration
 * @subpackage FF_Newsletter_CampaignMonitor_Integration/admin/partials
 */
?>

<h3>Current Campaigns</h3>
<?php
$campaigns = $ffm->get_current_campaigns();

// Transform campaigns array into List Table options format
$campaign_array = array();
$campaign_data = array();

if( !empty( $campaigns ) ) {

	foreach( $campaigns as $campaign ) {
		// fill column data
		$campaign_data['id']                        = $campaign->CampaignID;
		$campaign_data['name']                      = $campaign->Name;
		$campaign_data['created']                   = isset( $campaign->DateCreated ) ? $campaign->DateCreated : '---';
		$campaign_data['status']                    = $campaign->status;
		$campaign_data['sent_date']                 = isset( $campaign->SentDate ) ? $campaign->SentDate : '---';
		$campaign_data['emails_sent']               = isset( $campaign->TotalRecipients ) ? $campaign->TotalRecipients : '0';
		if( isset($campaign->PreviewURL ) ) {
			$campaign_data['url']                   = $campaign->PreviewURL;
		}
		if( isset( $campaign->WebVersionURL ) ) {
			$campaign_data['url']                   = $campaign->WebVersionURL;
		}
		array_push( $campaign_array, $campaign_data );
	}
}

// Display List Table of Campaigns
$options = array(
    'columns'			=> array(
                            'id' 					=> 'ID',
                            'name' 					=> 'Name',
                            'created'               => 'Created',
                            'status'                => 'Status',
                            'sent_date'             => 'Send Time',
                            'emails_sent'           => 'Emails Sent'),

    'sortable_cols'		=> array(
                            'id' 					=> array('id', false),
                            'name' 					=> array('name', false),
                            'created'               => array('created', false),
                            'status'                => array('status', false),
                            'sent_date'             => array('sent_date', false),
                            'emails_sent'           => array('emails_sent', false)),
    'data' 				=> $campaign_array
);
$list_table = new FF_Newsletter_Campaign_Monitor_List_Table( $options );
$list_table->display();

?>
