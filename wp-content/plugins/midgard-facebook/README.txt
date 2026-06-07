=== Firefly Midgard - Facebook ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 4.8.1
Stable tag: 4.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Facebook feed support to Midgard.

== Description ==

Consume calendar data from sources such as Google Calendar and output as JSON.

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-facebook` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. access Midgard Facebook settings to input Facebook app credentials

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###2.0.1
* Update Facebook SDK to 5.7
* Update field definitions to support the new graph API and deprecated fields
* Fix auth / token generator redirect

###1.4
* Update Facebook PHP SDK to v5.6.2
* Added Facebook login authentication in order to get around 'Page Public Content Access' errors
* Removed OAuth Test section on options page (no longer useful)

###1.3
* Added new facebook post data (story, icon, type, permalink_url, message_tags, shares, source)

###1.2.1
* Added better error message on data retrieval failure

###1.2
* Refactor settings to render on a tab in master plugin rather than having its own page
* Requires Midgard 2.3+

###1.1.4
* data retrieval now gets full-size image as well as thumbnail

###1.1.3
* improved plugin uninstall routine to support multisite - now deletes plugin options from all blogs

###1.1.2
* added uninstall methods to clean up database on plugin delete

###1.1.1
* Updated textfield function in options page - now supports passing new values

###1.1
* Use parent Midgard namespaces where required
* Add namespaces to all classes to avoid naming conflicts

###1.0
* Add options page fields and create JSON feed data

###0.4
* Resolve clash with options from other plugins overwriting each other due to settings page not having unique group ID

###0.3
* Remove updater code in favour of external group updater (better network support)

###0.2
* Tidy up comments and naming conventions. Include README.

###0.1
* Initial build
