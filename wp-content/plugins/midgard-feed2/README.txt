=== Firefly Midgard - Feed Of Feeds ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 4.9.6
Stable tag: 4.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create a feed of other feed data.

== Description ==

Define a feed containing data from other feeds and output as JSON

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-feed2` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###1.2
* Implement wp_remote_get args from midgard common

###1.1.3
* Fix URL variable typo in data class

###1.1.2
* Replace file_get_contents with more secure method

###1.1.1
* Added better error message on data retrieval failure

###1.1
* Add JSON Path expression to select root node of each feed

###1.0
* Initial build