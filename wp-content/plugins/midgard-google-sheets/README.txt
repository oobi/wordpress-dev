=== Firefly Midgard - Google Sheets ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 4.7.3
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Google Sheets support to Midgard.

== Description ==

Consume data from Google Sheets and output as JSON.

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-facebook` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. access Midgard Sheets settings to input Google API credentials

== Frequently Asked Questions ==
Q. How do I use this plugin in local development?
A. Save https://curl.haxx.se/ca/cacert.pem on your local file system
   In your php.ini insert or edit the following line: curl.cainfo = "[pathtothisfile]\cacert.pem"

== Screenshots ==

== Changelog ==

###1.1.2
* Fix issue with credentials display in options page when not in String format
* Automatically grab auth code from google OAuth return URL

###1.1.1
* Fix issue with credentials field saving bad data when auth code not specified

###1.1
* Refactor settings to render on a tab in master plugin rather than having its own page
* Requires Midgard 2.3+

###1.0.5
* improved plugin uninstall routine to support multisite - now deletes plugin options from all blogs

###1.0.4
* added uninstall methods to clean up database on plugin delete

###1.0.3
* Error trapped missing auth token on feed output

###1.0.2
* Suppress file not found warnings when reading stored JSON credentials
* bug fix - static method in utils incorrectly defined
* Add instructions on grabbing spreadsheet ID

###1.0.1
* Changed procedure for entering settings
* Add application name to Google Client

###1.0
* Move storage of credentials (access token) from file to database
* Map service response to Midgard consumable object

###0.5
* Use parent Midgard namespaces where required
* Add namespaces to all classes to avoid naming conflicts

###0.4
* Resolve clash with options from other plugins overwriting each other due to settings page not having unique group ID

###0.3
* Remove updater code in favour of external group updater (better network support)

###0.2
* Tidy up comments and naming conventions. Include README.

###0.1
* Initial build
