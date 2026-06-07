=== Buzz Add-On - Campaign Monitor ===
Contributors: firefly
Donate link: http://www.fi.net.au
Tags: firefly, buzz, email, campaign monitor
Requires at least: 4.6
Tested up to: 6.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Campaign Monitor support to The Buzz.

== Description ==

Adds Campaign Monitor support to The Buzz. Create and send campaigns right from the WordPress interface.
Supports multiple lists and segments.

== Installation ==

1. This is an add-on to "The Buzz". Make sure that the master plugin is installed and activated first.
1. Upload `buzz-addon-campaign-monitor` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Access the settings page to configure your Campaign Monitor API settings

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###2.3.2
* Fixed permission bug for site admins and editors accessing Campaign Monitor menu

###2.3.1
* Change permission so that editors can access Campaign Monitor menu

###2.3
* Update Camaign Monitor API to v7.1.
* Remove backslashes introduced into campaign subject when apostrophe is used

###2.2
* More efficient API calls to Campaign Monitor

###2.1
* Update Campaign Monitor API to v7. Use composer version.

###2.0
* Update Campaign Monitor code for PHP7.4 support
https://github.com/campaignmonitor/createsend-php

###1.6
* Add proxy url in CM settings to bypass the out of date cypher issues at the CM end.

###1.5.3
* Add custom URL option and field on create campaign page
* Require both Buzz Newsletter and Buzz Addon Email View plugins to be activated before this plugin will activate

###1.5.2
* Remove sortable columns in Current Campaigns table
* Fix bug introduced in 1.5.1 with redirecting affecting all successful campaign changes (update, delete, send). Now does not affect send.

###1.5.1
* Restore token format to %token% - fix issue with sanitization
* Redirect back to main tab after successful campaign creation

###1.5
* Fix API key validation on save - no longer requires two saves to store the value
* API key validation check uses the parameter key rather than the one from the DB
* Subject line is now rewritten in campaign creation to match tokenised input
* BREAKING CHANGE: Format of tokens in subject line changed from %token% to {{token}}

###1.4.1
* Add creation time to end of campaign name - to prevent duplicate name error

###1.4.0
* New feature - allow custom subject line for new campaigns, with an overridable default

###1.3.1
* Fixed delete campaign functionality

###1.3.0
* Added contact list, segments and total emails to send page
* Create campaign page now allows multiple contact lists to be selected at once

###1.2.1
* Remove updater code in favour of external group updater (better network support)

###1.2
* Removed unnecessary use of word 'settings' on options page labels

###1.0
* Initial release

== Upgrade Notice ==
