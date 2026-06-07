<?php
/**
 * Intercepts the notifications sent from the OneSignal plugin.
 * Check categories on published posts and send to users with matching OneSignal tags.
 * and remove URL field
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly OneSignal Notifications
 * Plugin URI:        www.fi.net.au
 * Description:       Intercepts OneSignal notifications and applies filters
 * Version:           1.2.0
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ff-onesignal-categories
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class FF_OneSignal_Notifications {

	private $options = array();

	function __construct() {
		// add options page
		require_once plugin_dir_path( __FILE__ ) . 'options-page.php';

		// get options
		$this->options = get_option( 'ff_onesignal_settings' ); 	// Option name

		// add filter
		add_filter( 'onesignal_send_notification', array( $this, 'filter' ), 10, 4 );
	}

	/**
	 * Filter recipients based on post category
	 * @see https://documentation.onesignal.com/reference#create-notification
	 */
	function filter($fields, $new_status, $old_status, $post) {

		// If categories option is enabled
		if( isset( $this->options['categories_enabled'] ) ) {

			// get an array of all post category slugs
			$cats = wp_get_post_categories( $post->ID, array('fields' => 'slugs') );

			// for each slug, add it to filters array
			$filters = array();
			foreach( $cats as $index=>$cat ) {
				$filters[] = array("field" => "tag", "key" => $cat, "relation" => "=", "value" => "true");
				if( $index < count($cats)-1 ) {
					$filters[] = array("operator" => "OR");
				}
			}

			// apply filters
			$fields['filters'] = $filters;

		}

		// force remove url (if we don't do this, the pop-up notification will link to the post URL in browser rather than the app)
		$fields['url'] = '';

		// Increase the count of iOS app badge by 1
		// $fields['content_available'] = true;
		$fields['ios_badgeType'] = 'Increase';
		$fields['ios_badgeCount'] = 1;

		return $fields;
	}

}

$ff_onesignal_notifications = new FF_OneSignal_Notifications;
?>