=== Firefly Midgard - RSS ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 4.9.6
Stable tag: 4.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add RSS support to Midgard.

== Description ==

An RSS is like XML but with a more predictable structure. You can use the generic XML connector to parse RSS, however
this extension adds the facility to generate an excerpt of a defined length, as well as pre-configuring the item list
as the root node.

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-rss` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###1.5
* Implement wp_remote_get args from midgard common

###1.4.3
* Replace file_get_contents with more secure method

###1.4.2
* Added better error message on data retrieval failure

###1.4.1
* Fix namespace issue on exception handler

###1.4
* Use parent Midgard namespaces where required
* Add namespaces to all classes to avoid naming conflicts

###1.3.3
* Remove updater code in favour of external group updater (better network support)

###1.3.2
* Tidy up comments and naming conventions. Include README.

###1.3.1
* Initial build
