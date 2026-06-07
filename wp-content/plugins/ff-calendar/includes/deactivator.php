<?php

namespace FF\Calendar;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    FF_Calendar
 * @subpackage FF_Calendar/includes
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Deactivator {

	public static function deactivate() {
		
		// abort if user does not have permission
		if ( !current_user_can( 'activate_plugins' ) ) { 
			return; 
		}

		// flush the permalinks 
		flush_rewrite_rules();

	}

}
