=== Buzz Add-On - Mailchimp Integration ===
Contributors: firefly
Donate link: http://www.fi.net.au
Tags: firefly, buzz, email, mailchimp
Requires at least: 4.6
Tested up to: 4.7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds MailChimp support to The Buzz.

== Description ==

Adds MailChimp support to The Buzz. Create and send campaigns right from the WordPress interface.
Supports multiple lists and segments.

== Installation ==

1. This is an add-on to "The Buzz". Make sure that the master plugin is installed and activated first.
1. Upload `buzz-addon-mailchimp` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Access the settings page to configure your MailChimp API settings

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###1.6.4
* Remove backslashes from subject line where apostrophes are used on campaign creation

###1.6.3
* Fix bug with current campaigns table not sorting by created time (most recently created first)
* Add custom URL option and field on create campaign page
* Require both Buzz Newsletter and Buzz Addon Email View plugins to be activated before this plugin will activate

###1.6.2
* Remove sortable columns in Current Campaigns table
* Fix errors on update/send pages when strings do not exist
* Add text to send summary to clarify when a segment has not been selected (ie. sending to the entire list)
* Fix error on campaign update when trying to change Contact Lists
* Fix bug introduced in 1.6.1 with redirecting affecting all successful campaign changes (update, delete, send). Now does not affect send.

###1.6.1
* Restore token format to %token% - fix issue with sanitization
* Redirect back to main tab after successful campaign creation

###1.6
* Fix API key validation on save - no longer requires two saves to store the value
* API calls uses the API key in arguments rather than forcing the one from the DB
* Subject line is now rewritten in campaign creation to match tokenised input
* BREAKING CHANGE: Format of tokens in subject line changed from %token% to {{token}}

###1.5.0
* New feature - allow custom subject line for new campaigns, with an overridable default

###1.4.2
* Remove updater code in favour of external group updater (better network support)

###1.0.1
* Removed unnecessary use of word 'settings' on options page labels

###1.0
* initial release

== Upgrade Notice ==