=== Firefly Midgard - Calendar ICS ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 4.8.1
Stable tag: 4.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add iCal / vCal support to Midgard.

== Description ==

Consume calendar data from sources such as Google Calendar and output as JSON.

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-ical` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###1.9
* Standardised timezones on event transform (if an event has no timezone, we assume it is local time according to WordPress settings)

###1.8.1
* Bug fix date parsing for recurrence dates. Min/Minutes Sec/Seconds are now treated as the same key.

###1.8
* Fix bug from v1.7 with non-recurring dates not using "minute" and "second" keys.
* Changed hour/minute/second values to use integers rather than strings (as they are no longer used for display)

###1.7
* Change JSON feed output to use "minute" and "second" instead of "min" and "sec" to promote better compatibility with MomentJS

###1.6
* Update iCalCreator library
* Add timestamp in milliseconds to feed

###1.5.1
* Added better error message on data retrieval failure

###1.5
* Update iCalCreator library for PHP 7 compatibility

###1.4
* Use parent Midgard namespaces where required
* Add namespaces to all classes to avoid naming conflicts

###1.3.3
* Remove updater code in favour of external group updater (better network support)

###1.3.2
* Tidy up comments and naming conventions. Include README.

###1.3.1
* Initial build
