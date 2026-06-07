=== Firefly Midgard - Instagram ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 4.8.1
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Instagram feed support to Midgard.

== Description ==

Consume calendar data from sources such as Google Calendar and output as JSON.

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-instagram` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. access Midgard Instagram settings to input Instagram app credentials

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###2.0
* Legacy API deprecated. Use new Basic Display API to authenticate and grab content.

###1.2.1
* Fix link to instagram auth settings

###1.2
* Refactor remote call to use wp_remote_request
* Added error catching for state: user id invalid

###1.1
* Refactor settings to render on a tab in master plugin rather than having its own page
* Requires Midgard 2.3+

###1.0.4
* Add a stop to exception handler

###1.0.3
* improved plugin uninstall routine to support multisite - now deletes plugin options from all blogs

###1.0.2
* added uninstall methods to clean up database on plugin delete

###1.0.1
* Fixed call_remote method to allow for both GET and POST verbs (needed to get access token)

###1.0
* Release

###0.1
* Initial build
