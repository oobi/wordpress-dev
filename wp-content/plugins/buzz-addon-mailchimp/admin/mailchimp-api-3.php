<?php

/**
 * The class that handles function calls to MailChimp
 *
 *
 * @package    Buzz_Mailchimp
 * @subpackage Buzz_Mailchimp/admin
 * @author     Firefly Interactive <info@fi.net.au>
 */

class Buzz_Mailchimp_API {
	// mailer HTTP stuff
	private $api_key;
	private $auth;
	private $api_endpoint_template = 'https://<dc>.api.mailchimp.com/3.0';
	private $api_endpoint;
    private $verify_ssl   = false;

	// defaults
	private $default_from_name;
	private $default_from_email;
	private $default_reply_email;

    /**
     * Create a new instance
     */
    function __construct() {
		// get api key and plugin defaults
		$mc_settings 				= get_option( 'buzz_mailchimp_settings' );
		$this->api_key				= isset( $mc_settings['mailchimp_api_key'] ) 		? $mc_settings['mailchimp_api_key'] 	: '-';
		$this->default_from_name 	= isset( $mc_settings['mailchimp_from_name'] ) 		? $mc_settings['mailchimp_from_name'] 	: '';
		$this->default_from_email	= isset( $mc_settings['mailchimp_from_email'] ) 	? $mc_settings['mailchimp_from_email'] 	: '';
		$this->default_reply_email	= isset( $mc_settings['mailchimp_reply_email'] ) 	? $mc_settings['mailchimp_reply_email'] : '';

		// init params based on API key
		$this->init( $this->api_key );
    }

	/**
	 * Init the mailer endpoint
	 */
	function init( $api_key ) {
		if( !isset($api_key) || empty($api_key) ) {
			// if no API key is set, we can't do anything
			$this->api_key = '';
			$this->api_endpoint = '';
			return;
		}

		$this->api_key = $api_key;
		// $ignore is not used in this construct but needs to be included in list() because we want the second parameter
        list( $ignore, $datacentre ) = explode('-', $this->api_key);
        $this->api_endpoint = str_replace('<dc>', $datacentre, $this->api_endpoint_template);

		//set auth key
		$this->auth = base64_encode('user:' . $api_key);
	}

	/***************************************************************************************
	 * CAMPAIGN DISPLAY FUNCTIONS
	 ***************************************************************************************/

	/**
	 * Get all campaigns
	 *
	 * @param 	int 	$page_size 		The number of campaigns in a page
	 * @param 	int 	$page_num 		The current page number (first page is 1)
	 * @return 	object  				Campaign data
	 */
	public function get_campaigns( $page_size=20, $page_num=1 ) {
		$args = array(
			'apikey'	=> $this->api_key,
			'count'		=> $page_size,
			'offset'	=> $page_size * ($page_num-1), // zero-index $page_num
			'sort_field'=> 'create_time',
			'sort_dir'	=> 'DESC'
		);

		$result = $this->call_remote('campaigns', $args, 'GET');
		return $this->process_result( $result );
	}

	/**
	 * Get all unsent campaigns
	 * @return 	object 	Campaign data
	 */
	public function get_unsent_campaigns() {
		$args = array(
			'apikey'	=> $this->api_key,
			'status'	=> 'save'
		);
		$result = $this->call_remote('campaigns', $args, 'GET');
		return $this->process_result( $result );
	}

	/**
	 * Get a single campaign
	 *
	 * @param 	int 	$campaign_id 		ID of the campaign to get
	 * @return 	object 						Campaign data
	 */
	public function get_campaign( $campaign_id ) {
		$args = array(
			'apikey'	=> $this->api_key
		);
		$result = $this->call_remote("campaigns/{$campaign_id}", $args, 'GET');
		return $this->process_result( $result );
	}

	/**
	 * Get a single campaign report
	 * @return 	object 	Campaign report data
	 */
	public function get_campaign_report( $campaign_id ){
		$args = array(
			'apikey'	=> $this->api_key
		);
		$result = $this->call_remote("reports/{$campaign_id}", $args, 'GET');
		return $this->process_result( $result );
	}

	/***************************************************************************************
	 * LISTS & SEGMENT DISPLAY FUNCTIONS
	 ***************************************************************************************/

	/**
	 * Get all contact lists
	 * @return 	object 	Contact List data
	 */
	public function get_lists() {
		$args = array(
			'apikey'	=> $this->api_key
		);
		$result = $this->call_remote('lists', $args, 'GET');
		return $this->process_result( $result );
	}

	/**
	 * Get a single contact list
	 * @return 	object 	Contact List data
	 */
	public function get_list( $list_id ) {
		$args = array(
			'apikey'	=> $this->api_key
		);
		$result = $this->call_remote("lists/{$list_id}", $args, 'GET');
		return $this->process_result( $result );
	}

	/**
	 * Get a single contact list's name
	 * @param 	string	$list_id			The ID of the list to get name of
	 * @return 	string 	Contact List name
	 */
	public function get_list_name( $list_id ) {
		$list = $this->get_list( $list_id );
		return isset( $list['name'] ) ? $list['name'] : false;
	}

	/**
	 * Get segments
	 * @param 	string 	$list_id 	The ID of the list to get segments for
	 * @return 	object 	Segments data
	 */
	public function get_segments( $list_id ) {
		$args = array(
			'apikey'	=> $this->api_key
		);
		$result = $this->call_remote("/lists/{$list_id}/segments", $args, 'GET');
		return $this->process_result( $result );
	}

	/***************************************************************************************
	 * CAMPAIGN SETUP FUNCTIONS
	 ***************************************************************************************/

	/**
	 * Create a campaign
	 * @param 	string 	$list_id 		The ID of the list to send to
	 * @param 	string 	$subject 		The subject of the email
	 * @param 	string 	$newsletter_url The URL of the email-formatted newsletter
	 * @param 	string 	$segment_id		The ID of the segment to send to
	 * @return 	object 	Success/Error object
	 */
	public function create_campaign( $list_id, $subject, $newsletter_url, $segment_id=NULL ) {

		// create campaign
		$args = array(
			'apikey'		=> $this->api_key,
			'type' 			=> 'regular',
			'settings'		=> array(
				'title'				=> $subject,
				'subject_line'		=> $subject,
				'from_name'			=> $this->default_from_name,
				'reply_to'			=> $this->default_reply_email
			),
			'recipients' 	=> array(
				'list_id' 		=> $list_id,
			),

			'content'		=> array( 'url' => $newsletter_url )
		);

		if( !empty( $segment_id ) ) {
			$args['recipients']['segment_opts'] = array( 'saved_segment_id' => intval($segment_id) );
		}

		$result = $this->call_remote('campaigns', $args, 'POST');

		// set content
		if( !$this->is_error( $result ) ) {
			$campaign_id = $result['id'];
			$result = $this->set_campaign_content( $campaign_id, $newsletter_url );
		}

		return $this->process_result( $result );
	}

	/**
	 * Set the content for a campaign
	 * @param 	string 	$campaign_id 	The ID of the campaign to update
	 * @param 	string 	$url 	URL of the content to set
	 * @return 	object 	Success/Error object
	 */
	public function set_campaign_content( $campaign_id, $url ) {
		// create campaign
		$args = array(
			'apikey'		=> $this->api_key,
			'url'			=> $url
		);

		$result = $this->call_remote( "campaigns/{$campaign_id}/content", $args, 'PUT' );
		return $this->process_result( $result );
	}

	/**
	 * Delete a campaign
	 * @param 	string 	$campaign_id 	The ID of the campaign to delete
	 * @return 	object 	Success/Error object
	 */
	public function delete_campaign( $campaign_id ) {
		$args = array(
			'apikey'	=> $this->api_key
		);
		$result = $this->call_remote( "campaigns/{$campaign_id}", $args, 'DELETE' );

		return $this->process_result( $result );
	}

	/***************************************************************************************
	 * CAMPAIGN SEND FUNCTIONS
	 ***************************************************************************************/

	/**
	 * Send a test campaign
	 * @param 	string 	$campaign_id 	The ID of the campaign to test send
	 * @param 	string 	$test_email 	The email to send a test to
	 * @param 	string 	$send_type 		type of test to run (possible values are plaintext or html)
	 * @return 	object 	Success/Error object
	 */
	public function send_test_campaign( $campaign_id, $test_email, $send_type='html' ) {
		$args = array(
			'apikey'		=> $this->api_key,
			'test_emails'	=> array( $test_email ),
			'send_type'		=> $send_type
		);
		$result = $this->call_remote("/campaigns/{$campaign_id}/actions/test", $args);
		return $this->process_result( $result );
	}

	/**
	 * Send a campaign
	 * @param 	string 	$campaign_id 	The ID of the campaign to send
	 * @return 	object 	Success/Error object
	 */
	public function send_campaign( $campaign_id ) {
		$args = array(
			'apikey'	=> $this->api_key
		);
		$result = $this->call_remote("/campaigns/{$campaign_id}/actions/send", $args);
		return $this->process_result( $result );
	}

	/**
	 * Update a campaign's options and segments
	 * @param 	string 	$campaign_id 	The ID of the campaign
	 * @param 	string 	$campaign_name 	The name of the campaign
	 * @param 	string 	$subject	 	The email subject line
	 * @param 	string 	$from_name	 	The name to send campaign from
	 * @param 	string 	$reply_to	 	The email to reply to
	 * @param 	string 	$list_id	 	The ID of the contact list
	 * @param 	string 	$segment_id 	The ID of the list segment
	 * @return 	object 	Success/Error object
	 */
	public function update_campaign( $campaign_id, $campaign_name, $subject, $from_name, $reply_to, $list_id, $segment_id ) {
		$errors = array();

		// call update function
		$campaign_args = array(
			'apikey'	=> $this->api_key,
			'settings'	=> array(
				'subject_line'	=> $subject,
				'title' 		=> $campaign_name,
				'from_name'		=> $from_name,
				'reply_to'		=> $reply_to
			),
			'recipients' => array(
				'list_id'		=> $list_id
			)
		);

		if( !empty( $segment_id ) ) {
			$campaign_args['recipients']['segment_opts'] = array( 'saved_segment_id' => intval($segment_id) );
		}

		$campaign_result = $this->call_remote("/campaigns/{$campaign_id}", $campaign_args, 'PATCH');

		// return any errors found
		return $this->process_result($campaign_result, 'Campaign options update failed.');
	}

	/***************************************************************************************
	 * UTILITIES
	 ***************************************************************************************/

	/**
	 * Test whether API Key is valid
	 */
	public function api_key_is_valid( $apikey ) {
		$args = array(
			'apikey'	=> $apikey,
			'fields' 	=> 'account_name'
		);

		$result = $this->call_remote('', $args, 'GET');
		return $this->process_result( $result );
	}


	/***************************************************************************************
	 * API CALL RESULT PROCESSING
	 ***************************************************************************************/

	/**
	 * Process the result array to check for errors before passing back
	 * @param 	array 	$result			The result array from Mailchimp call
	 * @param 	string	$fail_message	Message to display if error occurs
	 * @return 	array 	Error array if result contains an error. Else returns result object back if no errors.
	 */
	private function process_result( $result, $fail_message="Operation failed" ) {
		$error = array(
		    "type" 		=> "error",
		    "title"		=> '',
			"status"	=> -1,
		    "detail"  	=> "An unknown error occurred processing your request. Please try again later.",
			"instance"	=> "",
			'message'	=> $fail_message
		);

		// is this an error? Error key will only be set if MailChimp reports an unhandled error
		if( $this->is_error($result) ) {
			$error = array_merge($error, $result);
			$error['error'] = true;
			return $error;
		}
		// not an error!
		else {
			return $result;
		}
	}

	/**
	 * Check if a result array (returned from Mailchimp call) is an error or a success
	 * @return boolean 		True if result is an error, False if not an error
	 */
	public function is_error( $result ) {
		// multiple results
		if( isset($result[0]) ) {
			foreach($result as $r) {
				if( $this->_is_error($r) ) {
					return true;
				}
			}
			return false;
		}
		// single result
		else {
			return $this->_is_error($result);
		}
	}

	private function _is_error( $result ) {
		return isset($result['status']) && is_numeric($result['status']) && $result['status'] >= 400;
	}

	/**
	 * Show error or success messages using Wordpress DIVs
	 * @param 	array 	$result 				The result returned from a Mailchimp function call
	 * @param 	string 	$success_message 		The string to show on success
	 * @param 	boolean	$redirect_on_success 	Redirect back to main screen on success message
	 */
	public function show_messages( $result=array(), $success_message="Operation complete", $redirect_on_success=true ) {
		if($this->is_error($result)) {
			$this->show_error( $result );
		}
		// otherwise we're all good!
		else {
			echo '<div class="updated"><p>'. $success_message . '</p></div>';

			// redirect back to main page on success
			// can't use wp_redirect in this scenario as the headers are already modified
			if( $redirect_on_success ) {
				$redirect = admin_url( '/edit.php?post_type=newsletter&page=buzz-mailchimp' );
				print("<script>window.location.href='$redirect'</script>");
			}
		}
	}

	public function show_error( $result ) {
		// multiple results?
		if( isset($result[0]) ) {
			foreach($result as $r) {
				// is the result an error?
				if($this->is_error($r)) {
					$this->_show_error($r);
				}
			}
		}
		// one result (assoc array)
		else {

			// is the result an error?
			$this->_show_error($result);
		}
	}

	public function _show_error($result) {
		if($this->is_error($result)) {
			echo '<div class="error"><p><b>' . $result['title'] . '</b></p>';
			printf( '<p>%s %s</p>', $result['detail'], $result['message']);
			echo '</div>';
		}
	}

	/***************************************************************************************
	 * API CALL
	 * This stuff actually interfaces with MailChimp HTTP API
	 ***************************************************************************************/

    /**
     * Performs the underlying HTTP request. Not very exciting
	 * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
     * @param  string $method The API method to call, e.g. 'lists/list'
     * @param  array  $args   An array of arguments to pass to the method. Will be json-encoded for you.
	 * @param  {string} verb  HTTP verb to use for the request (GET, POST, PATCH, PUT, DELETE)
     * @return array          Associative array of json decoded API response.
     */
    private function call_remote($method, $args=array(), $verb="POST", $timeout = 10) {
        // $args['apikey'] = $this->api_key;

        $url 		= $this->api_endpoint.'/'.$method;
		$json_data 	= '';

		// validate HTTP verb is in supported set
		$supported_verbs = array('GET','POST','PATCH','PUT','DELETE');
		if(!in_array($verb, $supported_verbs)) {
			$verb = 'POST';
		}

		if( $verb == 'GET') {
			$query = http_build_query($args);
			$url .= '?' . $query;
		} else {
			$json_data = json_encode($args);
		}

		// make request with CURL
        if (function_exists('curl_init') && function_exists('curl_setopt')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.$this->auth));
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			if($verb != 'GET') {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
            	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
			}
            $result = curl_exec($ch);
            curl_close($ch);
        }
		// fallback to file_get_contents
		else {
            $result    = file_get_contents($url, null, stream_context_create(array(
                'http' => array(
                    'protocol_version' => 1.1,
                    'user_agent'       => 'PHP-MCAPI/3.0',
                    'method'           => $verb,
                    'header'           => "Content-type: application/json\r\n".
                                          "Connection: close\r\n" .
                                          "Content-length: " . strlen($json_data) . "\r\n",
                    'content'          => $json_data,
                ),
            )));
        }

        return $result ? json_decode($result, true) : false;
    }



}
