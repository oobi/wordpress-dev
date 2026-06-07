=== Firefly Midgard - WordPress ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 4.9.6
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add WordPress support to Midgard. This plugin adds JWT authentication to the basic JSON functionality so that you can
access feed data which requires a login. If the remote site does not require authentication you're better off using
the basic JSON feed type.

Requires the plugin jwt-authentication-for-wp-rest-api - must be installed and active on the remote site
(i.e. the site with the feed data which requires authentication)

== Description ==

Consume WordPress data and output as JSON.

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-wordpress` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###2.1
* Implement wp_remote_get args from midgard common

###2.0
* Separate WordPress token authentication into settings page
* Allow multiple pre-registration of auth tokens in settings and then just pick the required token in feed

###1.0.1
* Hide login fields when token has been saved / show again if cleared

###1.0
* Initial build
