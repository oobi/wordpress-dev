<?php

/**
 * Content of the "Current Campaigns" tab
 *
 * Included in admin/list-table.php
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    Buzz_Mailchimp
 * @subpackage Buzz_Mailchimp/admin/partials
 */
?>

<h3>Current Campaigns</h3>

<?php
	// set up pagination params
	$page_size 	= 10;
	$page_num 	= isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;

	// Get array of current campaigns
	$campaigns = $ffm->get_campaigns( $page_size, $page_num );
	$ffm->show_error( $campaigns );

	// Transform campaigns array into List Table options format
	$campaign_array = array();
	$campaign_data 	= array();

	if( $campaigns && array_key_exists( 'campaigns', $campaigns ) ) {

		// set the timezone and date format
		$date_format = 'Y-m-d H:i:s';
		date_default_timezone_set('Australia/Sydney');

		foreach( $campaigns['campaigns'] as $campaign ) {
			// format dates
			$create_time 	= date( $date_format, strtotime( $campaign['create_time'] ) );
			$send_time 		= date( $date_format, strtotime( $campaign['send_time'] ) );

			// fill column data
			$campaign_data['id'] 					= $campaign['id'];
			$campaign_data['list_name']				= $campaign['recipients']['list_name'] . ' (' . $campaign['recipients']['recipient_count'] . ')';
			$campaign_data['name'] 					= $campaign['settings']['title'];
			$campaign_data['status']				= $campaign['status'];
			$campaign_data['create_time']			= $create_time;
			$campaign_data['send_time']				= $campaign['send_time'] ? $send_time : '---';
			$campaign_data['emails_sent']			= $campaign['send_time'] ? $campaign['emails_sent'] : '---';
			$campaign_data['archive_url_long']		= $campaign['long_archive_url'];
			array_push( $campaign_array, $campaign_data );
		}
	}

	// Set up List Table of Campaigns
	$options = array(
		'current_page'		=> $page_num,
		'page_size'			=> $page_size,
		'total_items'		=> $campaigns['total_items'],
		'columns'			=> array(
								'id' 					=> 'ID',
								'name' 					=> 'Name',
								'create_time'			=> 'Created',
								'list_name'				=> 'Contact List',
								'status' 				=> 'Status',
								'send_time'				=> 'Send Time',
								'emails_sent'			=> 'Emails Sent'),
		'data' 				=> $campaign_array
	);
	$list_table = new FF_Newsletter_Mailchimp_List_Table( $options );

	// Display the table
	$list_table->display();

?>

