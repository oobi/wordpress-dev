<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    Buzz_Addon_Email_View
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// // only remove data if WP_DEBUG is false (ie. on a live site)
// if( defined('WP_DEBUG') && WP_DEBUG === false ) {

// 	if( is_multisite() ) {
// 		global $wpdb;
// 		$site_ids 			= $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

// 		// loop through all sites on network
// 		foreach ( $site_ids as $site_id ) {
// 			switch_to_blog( $site_id );
// 			remove_plugin_data( $site_id );
// 			restore_current_blog();
// 		}

// 	} else {
// 		remove_plugin_data();
// 	}

// }

// /**
//  * Delete all the plugin generated data
//  */
// function remove_plugin_data( $site_id=null ) {
// 	// TODO: remove plugin data from the site here
// }