<?php

/**
 * The class that handles function calls to Campaign Monitor
 *
 *
 * @package	FF_Newsletter_Campaign_Monitor
 * @subpackage FF_Newsletter_Campaign_Monitor/admin
 * @author	 Firefly Interactive <info@fi.net.au>
 */

class Campaign_Monitor_Mailer_API {
	// mailer HTTP stuff
	private $mailer;
 	private $api_key;
	private $client_id;

	// defaults
 	private $default_from_name;
 	private $default_from_email;
 	private $default_reply_email;

	/**
	 * Create a new instance
	 */
	function __construct() {
		// get api key and plugin defaults
		$cm_settings 				= get_option( 'buzz_campaignmonitor_settings' );
		$this->api_key				= isset( $cm_settings['campaignmonitor_api_key'] ) 		? $cm_settings['campaignmonitor_api_key'] 		: '';
		$this->default_from_name 	= isset( $cm_settings['campaignmonitor_from_name'] ) 	? $cm_settings['campaignmonitor_from_name'] 	: '';
		$this->default_from_email	= isset( $cm_settings['campaignmonitor_from_email'] ) 	? $cm_settings['campaignmonitor_from_email'] 	: '';
		$this->default_reply_email	= isset( $cm_settings['campaignmonitor_reply_email'] ) 	? $cm_settings['campaignmonitor_reply_email'] 	: '';
	}

	/***************************************************************************************
	 * CAMPAIGN DISPLAY FUNCTIONS
	 ***************************************************************************************/

	/**
	 * Get all campaigns
	 *
	 * @return 	object  				Campaign data
	 */
	public function get_current_campaigns() {
		// delete the transient on URL param 'refresh'
		if ( isset( $_GET['refresh'] ) ) {
			if ( $_GET['refresh'] == '1' ) {
				delete_transient( 'campaign_monitor_current_campaigns' );
			}
		}

		// If transient is not set, regenerate the data and save transient
		if ( !( $current_campaigns = get_transient( 'campaign_monitor_current_campaigns' ) ) ) {
			$current_campaigns = $this->set_current_campaigns();
		}

		return $current_campaigns;
	}

	/**
	 * Set campaign data to transient
	 *
	 * @return 	object  				Campaign data
	 */
	private function set_current_campaigns() {

		// get campaign data
		$wrap	   	= $this->buzz_get_client_wrapper();
		$drafts	 	= $wrap->get_drafts();
		$scheduled  = $wrap->get_scheduled();
		$campaigns  = $wrap->get_campaigns();

		// give each campaign feed the correct status for display
		if( $drafts->was_successful() && $scheduled->was_successful() && $campaigns->was_successful() ) {
			$drafts 			= $this->append_campaign_status( $drafts->response, 'draft' );
			$scheduled 			= $this->append_campaign_status( $scheduled->response, 'scheduled' );

			// for some reason, the campaigns response is not always an array
			if (isset($campaigns->response->Results)) {
				$campaigns 	= $this->append_campaign_status($campaigns->response->Results, 'sent');
			}
			else {
				$campaigns 	= $this->append_campaign_status($campaigns->response, 'sent');
			}

			// combine the campaign types
			$current_campaigns 	= array_merge( $drafts, $scheduled, $campaigns );
		}
		// else do not return any campaigns
		else {
			$current_campaigns 	= null;
		}

		// set the campaign data in a transient and return
		set_transient( 'campaign_monitor_current_campaigns', $current_campaigns, 60 * 60 * 24);
		return $current_campaigns;
	}

	/**
	 * Append the passed status to each campaign in the object
	 *
	 * @return 	object 				Campaign data
	 */
	private function append_campaign_status( $campaigns, $status ) {
		foreach ( $campaigns as $campaign ) {
			$campaign->status = $status;
		}
		return $campaigns;
	}

	 /**
	  * Get a single campaign
	  *
	  * @param 		int 	$campaign_id 	ID of the campaign to get
	  * @return 	object 					Campaign data
	  */
	 public function get_campaign( $campaigns, $campaign_id ) {
		foreach( $campaigns as $campaign ) {
			if( $campaign->CampaignID == $campaign_id ) {
				return $campaign;
			}
		}
	}

	/**
	  * Get sent campaigns
	  *
	  * @return 	object 					Campaign data
	  */
	public function get_sent_campaigns() {
		$wrap	 = $this->buzz_get_client_wrapper();
		$result  = $wrap->get_campaigns();

		if( $result->was_successful() ) {
			return $result->response;
		} else {
			echo 'Something went wrong!';
		}
	}

	/**
	  * Get unsent campaigns
	  *
	  * @return 	object 					Campaign data
	  */
	public function get_unsent_campaigns() {
		$wrap	   = $this->buzz_get_client_wrapper();
		$result	 = $wrap->get_drafts();

		if( $result->was_successful() ) {
			return $result->response;
		} else {
			return $result->response;
		}
	}

	/***************************************************************************************
	 * LISTS & SEGMENT DISPLAY FUNCTIONS
	 ***************************************************************************************/

	/**
	 * Get a contact list by ID
	 *
	 * @param 	string	$id 			The contact list ID
	 * @return 	object					Contact List data
	 */
	public function get_list_by_ID( $id ) {
		$auth 	= array('api_key' => $this->api_key);
		$wrap 	= new CS_REST_Lists( $id, $auth );
		$result = $wrap->get();
		return $result->response;
	}

	/**
	 * Get contact lists, segments and recipient count that apply to a single campaign
	 * in nice format for display
	 *
	 * @param 	string	$campaign_id 	 	The campaign ID
	 * @return 	object 						Contact List data
	 */
	public function get_campaign_list_info( $cid ) {
		$list_info = array();
		$total_emails = 0; // use to keep tally of total number of emails to send

		// Get the lists and segments
		$auth 	= array('api_key' => $this->api_key);
		$wrap 	= new CS_REST_Campaigns( $cid, $auth );
		$result = $wrap->get_lists_and_segments();

		// Add lists and their subscriber count to return array
		foreach( $result->response->Lists as $list ) {
			$num_subs 		= $this->get_list_subscribers( $list->ListID );
			$total_emails 	+= $num_subs;

			$l 				= array( 'type' 	=> 'list',
									 'id' 		=> $list->ListID,
									 'name' 	=> $list->Name,
									 'count' 	=> $num_subs );
			$list_info[] = $l;
		}

		// Add segment parents, segments and the segment subscriber count to return array
		foreach( $result->response->Segments as $seg ) {
			$num_subs 		= $this->get_segment_subscribers( $seg->SegmentID );
			$total_emails 	+= $num_subs;

			// Get the segment parent list and if it is not already in the return array, add it
			// used in display to show which list the segment(s) belong to
			$parent_list 	= $this->get_list_by_ID( $seg->ListID );
			if( !in_array( $parent_list->ListID, array_column( $list_info, 'id' ) ) ) {
				$pl 		= array( 'type' 	=> 'parent',
									 'id'		=> $parent_list->ListID,
									 'name'		=> $parent_list->Title,
									 'segments'	=> array() );
				$list_info[] = $pl;
			}

			// Add the segments to the parent in return array
			$s = array( 'type' 		=> 'segment',
						'id' 		=> $seg->SegmentID,
						'list_id' 	=> $seg->ListID,
						'name' 		=> $seg->Title,
						'count' 	=> $num_subs );
			$key = array_search( $seg->ListID, array_column( $list_info, 'id' ) );
			array_push( $list_info[$key]['segments'], $s );
		}

		return array(
			'lists'			=> $list_info,
			'total_emails'	=> $total_emails
		);
	}

	/**
	 * Get the active subscribers of a contact list
	 *
	 * @param 	string	$id		The List ID
	 * @return 	int 			The number of active subscribers
	 */
	public function get_list_subscribers( $id ) {
		$auth 	= array('api_key' => $this->api_key);
		$wrap 	= new CS_REST_Lists( $id, $auth );
		$result = $wrap->get_active_subscribers();

		if( $result->was_successful() ) {
			return $result->response->TotalNumberOfRecords;
		} else {
			return $result->response;
		}
	}

	/**
	 * Get the active subscribers of a segment list
	 *
	 * @param 	string	$id		The Segment ID
	 * @return 	int 			The number of active subscribers
	 */
	public function get_segment_subscribers( $id ) {
		$auth 	= array('api_key' => $this->api_key);
		$wrap 	= new CS_REST_Segments( $id, $auth );
		$result = $wrap->get_subscribers();

		if( $result->was_successful() ) {
			return $result->response->TotalNumberOfRecords;
		} else {
			return $result->response;
		}
	}

	/**
	 * Get all contact lists
	 *
	 * @return 	object 						Contact List data
	 */
	public function get_lists() {
		$lists_and_segs = array();
		$wrap		= $this->buzz_get_client_wrapper();
		$lists 		= $wrap->get_lists();  // all lists (ListID, Name)
		$segments 	= $wrap->get_segments(); // all segments (ListID, SegmentID, Title)

		if( $lists->was_successful() && $segments->was_successful() ) {
			// get segments and add to lists array
			foreach( $lists->response as $i => $list ) {
				array_push( $lists_and_segs, $list );
				$listID = $list->ListID;
				// $matching_segments = $this->get_campaign_monitor_segments( $list->ListID );

				// 2025-05-27 - more efficient way to get segments for each list (no need to make a separate API call for each list)
				// filter the $segments array to include items with matching ListID
				$matching_segments = array_filter( $segments->response, function( $seg ) use ( $listID ) {
					return $seg->ListID == $listID;
				});

				$lists_and_segs[$i]->Segments = $matching_segments;
			}
			return $lists_and_segs;
		} else {
			return $lists->response;
		}
	}

	/**
	 * Get segments
	 * @param 	string 	$list_id 		The ID of the list to get segments for
	 * @return 	object 					Segments data
	 */
	public function get_campaign_monitor_segments( $list_id ) {
		$auth = array( 'api_key' => $this->api_key );
		$wrap = new CS_REST_Lists( $list_id, $auth );
		$segments = $wrap->get_segments();

		if( $segments->was_successful() ) {
			return $segments->response;
		}
		else {
			return $lists->response;
		}
	}

	/***************************************************************************************
	 * CAMPAIGN SETUP FUNCTIONS
	 ***************************************************************************************/

	/**
	 * Create a campaign
	 * @param 	array 	$list_id 		The IDs of the list to send to
	 * @param 	string 	$subject 		The subject of the email
	 * @param 	string 	$newsletter_url The URL of the email-formatted newsletter
	 * @param 	array 	$segment_id		The IDs of the segment to send to
	 * @return 	object 	Success/Error object
	 */
	public function create_campaign( $list_id, $subject, $newsletter_url, $segment_id=false ) {
		// generate campaign name prefix (campaign names must be unique)
		$suffix		= '';
		$tz			= get_option('timezone_string');

		if( !empty($tz) ) {
			$date 		= new DateTime( 'now', new DateTimeZone( $tz ) );
			$suffix 	= ' (' . $date->format('h:i:sa') . ')';
		} else {
			$suffix 	= ' (' . date('h:i:sa') . ')';
		}

		// create campaign
		$auth = array( 'api_key' => $this->api_key );
		$wrap = new CS_REST_Campaigns( NULL, $auth );
		$result = $wrap->create( $this->get_client_id(), array(
			'Subject' 		=> $subject,
			'Name' 			=> $subject . $suffix,
			'FromName' 		=> $this->default_from_name,
			'FromEmail'		=> $this->default_from_email,
			'ReplyTo' 		=> $this->default_reply_email,
			'HtmlUrl' 		=> $newsletter_url,
			'ListIDs' 		=> is_array( $list_id ) && !empty( $list_id ) 		? $list_id 		: array(),
			'SegmentIDs' 	=> is_array( $segment_id ) && !empty( $segment_id ) ? $segment_id 	: array(),
		));
		return $result->response;
	}

	/**
	 * Delete a campaign
	 * @param 	string 	$campaign_id 	The ID of the campaign to delete
	 * @return 	object 	Success/Error object
	 */
	public function delete_campaign( $campaign_id ) {
		$auth 	= array( 'api_key' => $this->api_key );
		$wrap 	= new CS_REST_Campaigns( $campaign_id, $auth );
		$result = $wrap->delete();
		return $result->response;
	}

	/***************************************************************************************
	 * CAMPAIGN STATS
	 ***************************************************************************************/

	public function get_summary( $id ) {
		$auth 	= array('api_key' => $this->api_key);
		$wrap 	= new CS_REST_Campaigns( $id, $auth );
		$result = $wrap->get_summary();
		return $result->response;
	}

	/***************************************************************************************
	 * CAMPAIGN SEND FUNCTIONS
	 ***************************************************************************************/

	public function send_test_campaign( $id, $email ) {
		$auth = array( 'api_key' => $this->api_key );
		$wrap = new CS_REST_Campaigns( $id, $auth );
		$result = $wrap->send_preview( array( $email ), 'Fallback' );
		return $result->response;
	}

	public function send_campaign( $id ) {
		$auth = array('api_key' => $this->api_key);
		$wrap = new CS_REST_Campaigns( $id, $auth );

		$result = $wrap->send( array(
			'ConfirmationEmail' => $this->default_from_email,
			'SendDate' 			=> 'immediately'
			//'SendDate' => 'Date to send (yyyy-mm-dd or immediately)'
		));
		return $result->response;
	}

	/***************************************************************************************
	 * UTILITIES
	 ***************************************************************************************/

	/**
	 * Test whether API Key is valid
	 */
	public function api_key_is_valid( $key ) {
		$auth = array( 'api_key' => $key );
		$wrap = new CS_REST_General( $auth );

		$result = $wrap->get_clients();
		if( $result->was_successful() ) {
			$this->client_id = $result->response[0]->ClientID;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the client ID
	 */
	private function get_client_id() {
		$auth = array( 'api_key' => $this->api_key );
		$wrap = new CS_REST_General( $auth );

		$result = $wrap->get_clients();
		if($result->was_successful()) {
			return $result->response[0]->ClientID;
		} else {
			return null;
		}
	}

	/**
	 * Get client wrapper for calls to campaign monitor
	 */
	private function buzz_get_client_wrapper() {
		$auth = array( 'api_key' => $this->api_key );
		$wrap = new CS_REST_Clients( $this->get_client_id(), $auth );
		return $wrap;
	}

	/***************************************************************************************
	 * API CALL RESULT PROCESSING
	 ***************************************************************************************/

	/**
	 * Show error or success messages using Wordpress DIVs
	 * @param 	array 	$result 				The result returned from a Mailchimp function call
	 * @param 	string 	$success_message 		The string to show on success
	 * @param 	boolean	$redirect_on_success 	Redirect back to main screen on success message
	 */
	public function show_messages( $result, $success_message, $redirect_on_success=true ) {
		if( isset( $result->Message ) ) {
			echo '<div class="error"><p>'. $result->Message . '</p></div>';
		} else {
			echo '<div class="updated"><p>'. $success_message . '</p></div>';

			// redirect back to main page on success
			// can't use wp_redirect in this scenario as the headers are already modified
			if( $redirect_on_success ) {
				$redirect = admin_url( '/edit.php?post_type=newsletter&page=buzz-campaign-monitor' );
				print("<script>window.location.href='$redirect'</script>");
			}
		}
	}

 }
