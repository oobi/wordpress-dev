=== Firefly OneSignal Notifications ===
Contributors: Firefly Interactive
Tags: posts, notifications, onesignal
Requires at least: 3.5
Tested up to: 4.9.6
Requires PHP: 5.6
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Intercepts OneSignal notifications and applies filters

== Description ==

Intercepts the notifications sent from the OneSignal plugin.
Check categories on published posts and send to users with matching OneSignal tags.
Also removes URL field from notification

== Installation ==

1. Upload `ff-onesignal-notifications` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 1.2.0 =
* Add badge count increment for iOS devices
* Add settings page to control enabling category filter

= 1.1.0 =
* Remove URL field from notification.
* Add class wrapper around functions.
* Add README

= 1.0 =
* Initial release
