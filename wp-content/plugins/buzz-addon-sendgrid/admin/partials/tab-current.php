<?php

/**
 * Content of the "Current Campaigns" tab
 *
 * Included in admin/ff-newsletter-sendgrid-integration-list-table.php
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    FF_Newsletter_sendgrid_Integration
 * @subpackage FF_Newsletter_sendgrid_Integration/admin/partials
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
        $cid = $campaign->id;
        $campaign = $ffm->get_single_campaign( $cid );

		// fill column data
		$campaign_data['id']                        = $campaign->id;
		$campaign_data['name']                      = $campaign->name;
		$campaign_data['created']                   = isset( $campaign->created_at ) ? $campaign->created_at : '---';
		$campaign_data['status']                    = str_replace('triggered','sent',$campaign->status);
		$campaign_data['sent_date']                 = isset( $campaign->send_at ) ? $campaign->send_at : '---';
		//$campaign_data['emails_sent']             = isset( $campaign->recipients ) ? $campaign->recipients : '0';

		if( isset($campaign->id ) ) {
			$campaign_data['url']                   = "https://mc.sendgrid.com/single-sends/$campaign->id/preview";
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
                            //'emails_sent'           => 'Emails Sent'
                            ),

    'sortable_cols'		=> array(
                            'id' 					=> array('id', false),
                            'name' 					=> array('name', false),
                            'created'               => array('created', false),
                            'status'                => array('status', false),
                            'sent_date'             => array('sent_date', false),
                            //'emails_sent'           => array('emails_sent', false)
                            ),
    'data' 				=> $campaign_array
);

$list_table = new FF_Newsletter_Sendgrid_List_Table( $options );
$list_table->display();

?>
