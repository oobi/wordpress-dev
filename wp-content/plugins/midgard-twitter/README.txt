=== Firefly Midgard - Twitter ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 4.9.5
Stable tag: 4.9.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Twitter feed support to Midgard.

== Description ==

Add Twitter feed support to Midgard

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-twitter` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. access Midgard Twitter settings to input Twitter app credentials

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###1.4
* Add support for 'extended' tweet mode, which includes full tweet text and media URLs

###1.3.1
* Added better error message on data retrieval failure

###1.3
* Refactor settings to render on a tab in master plugin rather than having its own page
* Requires Midgard 2.3+

###1.2.4
* Fix namespace issue in exception handler

###1.2.3
* improved plugin uninstall routine to support multisite - now deletes plugin options from all blogs

###1.2.2
* added uninstall methods to clean up database on plugin delete

###1.2.1
* Updated textfield function in options page - now supports passing new values

###1.2
* Use parent Midgard namespaces where required
* Add namespaces to all classes to avoid naming conflicts

###1.1.4
* Added "Exlude Retweets" option - defaults to false
* Improved "Number of Tweets" option to be more accurate

###1.1.3
* Resolve clash with options from other plugins overwriting each other due to settings page not having unique group ID

###1.1.2
* Remove updater code in favour of external group updater (better network support)

###1.1.1
* Tidy up comments and naming conventions. Include README.

###1.1
* Add switch to include or exclude replies from the feed

###1.0
* Initial build
