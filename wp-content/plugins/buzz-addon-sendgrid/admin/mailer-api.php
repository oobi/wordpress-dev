<?php

/**
 * The class that handles function calls to Sendgrid
 *
 *
 * @package	FF_Newsletter_Sendgrid
 * @subpackage FF_Newsletter_Sendgrid/admin
 * @author	 Firefly Interactive <info@fi.net.au>
 */

 require_once __DIR__ . '/call-wrapper/sendgrid-php.php';
class Sendgrid_Mailer_API {
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
		$cm_settings 				= get_option( 'sendgrid_settings' );
		$this->api_key				= isset( $cm_settings['sendgrid_api_key'] ) 		? $cm_settings['sendgrid_api_key'] 		: '';
		$this->default_from_name 	= isset( $cm_settings['sendgrid_from_name'] ) 		? $cm_settings['sendgrid_from_name'] 	: '';
		$this->default_from_email	= isset( $cm_settings['sendgrid_from_email'] ) 		? $cm_settings['sendgrid_from_email'] 	: '';
		$this->default_reply_email	= isset( $cm_settings['sendgrid_reply_email'] ) 	? $cm_settings['sendgrid_reply_email'] 	: '';
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
		if ( isset( $_GET['refresh'] )) {
			if ( $_GET['refresh'] == '1') {
				delete_transient( 'sendgrid_current_campaigns' );
			}
		}

		// If transient is not set, regenerate the data and save transient
		if ( !( $current_campaigns = get_transient( 'sendgrid_current_campaigns' ) ) ) {
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
		//$wrap	   	= $this->buzz_get_client_wrapper();
		//$drafts	 	= $this->get_drafts();
		//$scheduled  = $this->get_scheduled();
		$campaigns  = $this->get_campaigns();

		// give each campaign feed the correct status for display
		if( !empty($campaigns) ) {
			$current_campaigns 	= $campaigns;
		}
		// else do not return any campaigns
		else {
			$current_campaigns 	= null;
		}

		// set the campaign data in a transient and return
		set_transient( 'sendgrid_current_campaigns', $current_campaigns, 60 * 5);
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
	  * Get drafts
	  *
	  * @param 		$limit 	$limit 		Items to return
	  * @param 		$offset $offset 	Starting offset
	  * @return 	array 				Campaign data
	  */
	  public function get_drafts( $limit = 20, $offset = 0 ) {
		////////////////////////////////////////////////////
		// Retrieve all Campaigns #
		// GET /campaigns #

		$sg = new \SendGrid($this->api_key);

		$query_params = json_decode('{"limit": 1, "offset": 1}');

		try {
			$response = $sg->client->campaigns()->get(null, $query_params);

			// decode response and return
			return json_decode($response->body());
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return array($e->getMessage());
		}
	}

	/**
	  * Get Scheduled
	  *
	  * @param 		$limit 		$limit 		Items to return
	  * @param 		$offset 	$offset 	Starting offset
	  * @return 	mixed 					Campaign data
	  */
	  public function get_scheduled( $limit = 20, $offset = 0 ) {
		////////////////////////////////////////////////////
		// Retrieve all Campaigns #
		// GET /campaigns #

		$sg = new \SendGrid($this->api_key);

		$query_params = json_decode('{"limit": 1, "offset": 1}');

		try {
			$response = $sg->client->campaigns()->get(null, $query_params);

			// decode response and return
			return json_decode($response->body());
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return $e->getMessage();
		}
	}

	/**
	  * Get campaigns
	  *
	  * @param 		$limit 			Items to return
	  * @param 		$offset 		Starting offset
	  * @return 	object 			Campaign data
	  */
	public function get_campaigns( $limit = 12, $offset = 1 ) {
		$campaigns = $this->get_campaign_list_by_status();
		return $campaigns;
	}

	 /**
	  * Get a single campaign
	  *
	  * @param 		int 	$campaign_id 	ID of the campaign to get
	  * @return 	array 					Campaign data
	  */
	 public function get_single_campaign( $campaign_id, $refresh = false ) {
		// for send get campaign by API instead of cache

		// If transient is not set, regenerate the data and save transient
		if ( false === ( $c = get_transient( 'sendgrid_campaigns_' . $campaign_id ) ) ) {

			$sg = new \SendGrid($this->api_key);

			try {
				$response = $sg->client->marketing()->singlesends()->_($campaign_id)->get();

				//$this->pretty_print($response->body());
				$c = $response->body();

				// set the campaign data in a transient and return
				set_transient( 'sendgrid_campaigns_' . $campaign_id, $c, 60 * 60);

				// decode response and return
				return json_decode($c);
				
			} catch (Exception $ex) {
				$msg = $ex->getMessage();
				echo "Caught exception: " . $msg;
				return array($msg);
			}
		} else { // return transient, if exists
			$c = json_decode($c);

			//$this->pretty_print($c->status);
			
			// delete the transient on URL param 'refresh' if campaign status is 'scheduled'
			if ( isset( $_GET['refresh'] )) {
				if ( $_GET['refresh'] == '1' && $c->status == 'scheduled') {
					delete_transient( 'sendgrid_campaigns_' . $campaign_id );
				}
			}

			return $c;
			
		}

	}

	/**
	  * Get sent campaigns
	  *
	  * @return 	object 					Campaign data
	  */
	public function get_sent_campaigns() {
		$campaigns = $this->get_campaign_list_by_status(array("triggered") );
		return $campaigns;
	}

	/**
	  * Get unsent campaigns
	  *
	  * @return 	object 					Campaign data
	  */
	public function get_unsent_campaigns() {
		$campaigns = $this->get_campaign_list_by_status(array("draft","scheduled","sent"));

		return $campaigns;
	}

	public function get_campaign_list_by_status($status = array()) {
		////////////////////////////////////////////////////
		// Retrieve all Campaigns #
		// GET /campaigns #

		$sg = new \SendGrid($this->api_key);

		$query_params = array(
			"limit"		=> 50,
			//"offset"	=> 1,
			"status"	=> $status
		);

		try {
			$response = $sg->client->marketing()->singlesends()->search()->post( $query_params);
			
			if ( $this->was_successful($response->statusCode()) ) {
				// decode response and return
				$result = json_decode($response->body())->result;
				return $result;
			} else {
				return (object)[];
			}
			
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return $e->getMessage();
		}
	}

	/***************************************************************************************
	 * LISTS & SEGMENT DISPLAY FUNCTIONS
	 ***************************************************************************************/


	/**
	 * Get contact lists, segments and recipient count that apply to a single campaign
	 * in nice format for display
	 *
	 * @param 	array	$campaign_id 	 	The campaign object
	 * @return 	array 						Contact List data
	 */
	public function get_campaign_list_info( $campaign ) {
		$list_info = array();
		$segment_info = array();

		$total_emails = 0; // use to keep tally of total number of emails to send

		$recipients = $campaign->send_to;

		$lists = array();
		$segments = array();

		// get list ids
		if (!empty($recipients->list_ids)) {
			$lists = $recipients->list_ids;
		}
		// get segment ids
		if (!empty($recipients->segment_ids)) {
			$segments = $recipients->segment_ids;
		}
		
		// Add lists and their subscriber count to return array
		foreach( $lists as $list ) {
			$num_subs 		= $this->get_list_subscribers( $list );
			$total_emails 	+= $num_subs->contact_count;

			$l 				= array( 'type' 	=> 'list',
									 'id' 		=> $num_subs->id,
									 'name' 	=> $num_subs->name,
									 'count' 	=> $num_subs->contact_count );
			$list_info[] = $l;
		}

		// Add segment parents, segments and the segment subscriber count to return array
		foreach( $segments as $seg ) {
			$num_subs 		= $this->get_segment_subscribers( $seg );

			if ( is_object($num_subs) ) {
				$total_emails 	+= $num_subs->contacts_count;
			}

			$l 				= array( 'type' 	=> 'segment',
									 'id' 		=> $num_subs->id,
									 'name' 	=> $num_subs->name,
									 'count' 	=> $num_subs->contacts_count );
			$segment_info[] = $l;
			
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
	 * @return 	mixed 			The list object
	 */
	public function get_list_subscribers( $id ) {

		$sg = new \SendGrid($this->api_key);

		try {
			$response = $sg->client
				->marketing()
				->lists()
				->_($id)
				->get();

			if ( $this->was_successful($response->statusCode()) ) {
				$body = json_decode($response->body());
				return $body;
			} else {
				return array();
			}
		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
			return array();
		}

	}

	/**
	 * Get the active subscribers of a segment list
	 *
	 * @param 	string	$id		The Segment ID
	 * @return 	array 			The number of active subscribers
	 */
	public function get_segment_subscribers( $id ) {

		$sg = new \SendGrid($this->api_key);

		try {
			$response = $sg->client
				->marketing()
				->segments()
				->_($id)
				->get();

			if ( $this->was_successful($response->statusCode()) ) {
				$body = json_decode($response->body());
				return $body;
			} else {
				return array();
			}
		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
			return array();
		}
	}

	public function get_sender_info($id) {

		$sg = new \SendGrid($this->api_key);

		try {
			$response = $sg->client->marketing()->senders()->_($id)->get();

			if ( $this->was_successful($response->statusCode()) ) {
				$body = json_decode($response->body());
				return $body->from;
			} else {
				return array();
			}
		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
		}
	}
	/**
	 * Get all contact lists
	 *
	 * @return 	array 						Contact List data
	 */
	public function get_lists() {
		$lists_and_segs = array();

		$sg = new \SendGrid($this->api_key);

		$query_params = json_decode('{
			"page_size": 100
		}');

		try {
			$response = $sg->client->marketing()->lists()->get(null, $query_params);

			if ( $this->was_successful($response->statusCode()) ) {
				$lists = json_decode($response->body());

				// get segments and add to lists array
				foreach( $lists->result as $i => $list ) {
					array_push( $lists_and_segs, $list );
					$segments = $this->get_campaign_segments( $list->id );
					$lists_and_segs[$i]->Segments = $segments;
				}
				return $lists_and_segs;
			} else {
				return array();
			}

		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
			return array();
		}

	}

	/**
	 * Get segments
	 * @param 	string 	$list_id 		The ID of the list to get segments for
	 * @return 	object 					Segments data
	 */
	public function get_campaign_segments( $list_id ) {

		$sg = new \SendGrid($this->api_key);

		try {
			$response = $sg->client->_("marketing/segments/2.0")->get();

			if ( $this->was_successful($response->statusCode()) ) {
				$segments = json_decode($response->body());
				
				return $segments->results;
			} else {
				return (object)[];
			}
		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
			return (object)[];
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
	 * @return 	mixed 	Success/Error object
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

		// get content from newsletter EDM
		$edm = wp_remote_get( $newsletter_url, 
			$args = [
				'sslverify' => false,
			]
		);

		// retrieve body html
		$edm_body    	= wp_remote_retrieve_body( $edm );

		// convert content to plain text
		$edm_plain 		= wp_strip_all_tags($edm_body);

		$list_ids 		= is_array( $list_id ) 		&& !empty( $list_id ) 		? $list_id : array();
		$segment_ids 	= is_array( $segment_id ) 	&& !empty( $segment_id ) 	? $segment_id : array();

		$sg = new \SendGrid($this->api_key);

		////////////////////////////////////////////////////
		// Get all Sender Identities #
		// GET /sender id #
		try {
			$senders = $sg->client->senders()->get();

			if ($senders->statusCode() == 200 ) {
				$sender = json_decode($senders->body());
				$senderid = $sender[0]->id;
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		// replace unsubscribe tag
		$strSearch = "%7B%7B%7Bunsubscribe%7D%7D%7D";
		$strReplace = "{{{unsubscribe}}}";

		$edm_body = str_replace($strSearch,$strReplace,$edm_body);
		
		////////////////////////////////////////////////////
		// Create a Campaign #
		// POST /campaigns #
		
		$request_body = array(
				'name' => $subject . $suffix,
				'status' => 'draft',
				//'send_at' => $send_at,
				'email_config' => array(
					'subject' => $subject,
					'html_content' => $edm_body, 
					"plain_content" => $edm_plain,
					'generate_plain_content' => true,
					'editor' => 'code',
					'sender_id' => $senderid,
					//"suppression_group_id" => null,
					'custom_unsubscribe_url' => get_home_url(),
				),
				'send_to' => array(
					'list_ids' => $list_ids,
					'segment_ids' => $segment_ids
				)
			);
		
		try {
			$response = $sg->client->marketing()->singlesends()->post($request_body);

			if ( $this->was_successful($response->statusCode()) ) {
				$result = json_decode($response->body());
				$result->message = 'Campaign created successfully.';
				return $result;
			} else {
				return array();
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return array('message' => $e->getMessage());

		}

	}

	/**
	 * Delete a campaign
	 * @param 	string 	$campaign_id 	The ID of the campaign to delete
	 * @return 	array 	Success/Error object
	 */
	public function delete_campaign( $campaign_id, $send_at ) {

		$sg = new \SendGrid($this->api_key);

		try {
			
			$response = $sg->client
				->marketing()
				->singlesends()
				->_($campaign_id)
				->delete();

			if ( $this->was_successful($response->statusCode()) ) {
				$result = json_decode($response->body());
				return $result->result;
			} else {
				return array('There was an error deleting this campaign.');
			}

		} catch (Exception $ex) {

			echo "Caught exception: " . $ex->getMessage();
			return array();

		}
	}

	/***************************************************************************************
	 * CAMPAIGN STATS
	 ***************************************************************************************/

	public function get_summary( $campaign_id ) {

		if ( false === ( $c = get_transient( 'sendgrid_campaigns_get_summary_' . $campaign_id ) ) ) {
			$sg = new \SendGrid($this->api_key);

			// populate request
			$request_body = array(
				"aggregated_by" 	=> "total"
			);

			try {
				$response = $sg->client->marketing()->stats()->singlesends()->_($campaign_id)->get(null, $request_body);

				if ( $this->was_successful($response->statusCode()) ) {
					$result = json_decode($response->body());

					$c = $result->results[0];

					// set the campaign data in a transient and return
					set_transient( 'sendgrid_campaigns_get_summary_' . $campaign_id, $c, 60 * 60 * 4);

					// decode response and return
					return $c;

				} else {
					return array('There was an error retrieving the campaign stats.');
				}

			} catch (Exception $ex) {
				echo "Caught exception: " . $ex->getMessage();
			}
		} else {
			return $c;
		}

	}

	/***************************************************************************************
	 * CAMPAIGN SEND FUNCTIONS
	 ***************************************************************************************/

	/**
	 * Sends campaign test email 
	 * @param mixed $campaign_id
	 * @param mixed $email
	 * @param mixed $sender_id
	 * @param mixed $campaign_name
	 */
	public function send_test_campaign( $campaign_id, $email, $sender_id, $campaign_name ) {
		

		$sender_id = intval($sender_id); // convert to integer

		$sg = new \SendGrid($this->api_key);

		// get template object from email id
		$response = $sg->client->marketing()->campaigns()->_($campaign_id)->get();

		// decode body 
		$template = json_decode($response->body());

		// populate request
		$request_body = array(
			"emails" 					=> array($email),
			"sender_id" 				=> $sender_id,
			"template_id" 				=> $template->template_id,
			//"version_id_override" 		=> "55dcbd54-22c3-4948-8860-e5de901bf648"
		);
			
		try {
			$response = $sg->client->marketing()->test()->send_email()->post($request_body);

			if ( $this->was_successful($response->statusCode()) ) {
				$result = json_decode($response->body());
				return $result->result;
			} else {
				return array('There was an error sending a test email.');
			}

		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
		}
	}

	/**
	 * Schedules campaign to send immediately
	 * @param mixed $id
	 */
	public function send_campaign( $id, $time = "now" ) {

		$sg = new \SendGrid($this->api_key);

		// populate request
		$request_body = array(
			"send_at" 			=> $time,
			"status" 			=> "scheduled"
		);

		//$this->pretty_print($request_body); exit; 

		try {
			$response = $sg->client->marketing()->singlesends()->_($id)->schedule()->put($request_body);

			// 201 is success code
			if ( $this->was_successful($response->statusCode()) ) {
				$result = json_decode($response->body());

				// delete transient as status will change
				delete_transient( 'sendgrid_campaigns_' . $id);

				return $result;
			} else {
				return array('There was an error sending a test email.');
			}

		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
		}

	}

	/***************************************************************************************
	 * UTILITIES
	 ***************************************************************************************/

	/**
	 * Test whether API Key is valid
	 */
	public function api_key_is_valid( $key ) {

		$wrap = new \SendGrid($key );

		try {

			$response = $wrap->client
				->user()
				->account()
				->get();
			
				//$this->pretty_print($response); exit;
			
			if ( $this->was_successful($response->statusCode()) ) {
				return true;
			} else {
				//echo $response->body()->$errors->$message;
				$error_msg = json_decode($response->body());
				echo "<p>Response code: " . $response->statusCode() . ". Error Message: " . $error_msg->errors[0]->message . ".</p>";

				return false;
			}
			
		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
			return false;
		}

	}

	/**
	 * Get client wrapper for calls to Sendgrid
	 */
	private function buzz_get_client_wrapper() {

		$wrap = new \SendGrid($this->api_key);

		try {
			//$response = $wrap->client->scopes()->get();
			return $wrap;
		} catch (Exception $ex) {
			echo "Caught exception: " . $ex->getMessage();
		}
 
		return $wrap;
	}
	/**
	 * Summary of process_api_response
	 * @param array $response
	 * @return boolean
	 */
	private function was_successful($responseCode) {
		
		if ($responseCode == 200 ||$responseCode == 201) {
			return true;
		} 
		return false;
	}

	public function pretty_print($var) {
		echo "<pre>";
		print_r($var) ;
		echo "</pre>";
	}

	/***************************************************************************************
	 * API CALL RESULT PROCESSING
	 ***************************************************************************************/

	/**
	 * Show error or success messages using Wordpress DIVs
	 * @param 	array 	$result 				The result returned from a Sendgrid function call
	 * @param 	string 	$success_message 		The string to show on success
	 * @param 	boolean	$redirect_on_success 	Redirect back to main screen on success message
	 */
	public function show_messages( $result, $message, $redirect_on_success=true ) {
		//$this->pretty_print($result);
		if( !empty($result && isset($result->message)) )  {
			
			echo '<div class="error"><p>'. $result->message . '</p></div>';
		} else {
			echo '<div class="updated"><p>'. $message . '</p></div>';

			// redirect back to main page on success
			// can't use wp_redirect in this scenario as the headers are already modified
			if( $redirect_on_success ) {
				$redirect = admin_url( '/edit.php?post_type=newsletter&page=buzz-sendgrid&refresh=1' );
				print("<script>window.location.href='$redirect'</script>");
			}
		}
	}

 }
