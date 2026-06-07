<?php

namespace FF\Midgard;

class Midgard_Push_Notifications {

	function __construct() {
		// get the options page settings
		$options = get_option( 'midgard_push_notifications_settings' );
	}

	function run() {
		/** Set up options page */
		require_once(dirname(__FILE__) . '/options-page.php');
	}

}