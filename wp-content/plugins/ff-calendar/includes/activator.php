<?php

namespace FF\Calendar;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    FF_Calendar
 * @subpackage FF_Calendar/includes
 * @author     Firefly Interactive <info@fi.net.au>
 */
class Activator {

	public static function activate() {

		// abort if user does not have permission
		if ( !current_user_can( 'activate_plugins' ) ) { 
			return; 
		}

		// rewrite rules flushed in Public_Init class

	}

}
