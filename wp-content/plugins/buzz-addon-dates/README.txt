=== Buzz Add-On - Dates ===
Contributors: firefly
Donate link: http://www.fi.net.au
Tags: firefly, buzz, email, dates, calendar
Requires at least: 4.6
Tested up to: 6.3.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Date fields to the Buzz Newsletter

== Description ==

Add dates as custom fields to Buzz Newsletter. This lets you add date data in a newsletter-specific context.

== Installation ==

1. This is an add-on to "The Buzz Newsletters". Make sure that the master plugin is installed and activated first.
1. Upload `buzz-addon-dates` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###2.3
* Protect against empty widget instances

###2.2.2
* Fix deprecated use of interpolated strings (PHP 8.2)

###2.2.1
* Change the way widget is registered so it can be unregistered easily

###2.2.0
* Initialise the Kirki fields even if the theme does not explicitly implement a customizer panel

###2.1
* Bug fix - removing all dates is now possible


###2.0.2
* Add checks for when dates are avilable, but the widget has not been added to the page

###2.0.1
* Add classes to help Buzz 2018 theme fix padding between dates in email view

###2.0
* Templates moved from theme into plugin
* Add widget for displaying dates in theme
* Add customizer options for creating multiple date sets and custom icons
* Rework UI to handle new dates sets/icons and add drag/drop
* Remove date sorting (now dates can be sorted manually via drag/drop)
* Reorganized plugin files into folders
* Remove Pikaday in favour of HTML5 date field

###1.2.1
* Fix bug in Internet Explorer that prevents Add New Date button from working

###1.2.0
* Fix major bug with breaking data input fields when saving a page when description contains a single quote.
* This fix will break previously created dates. Ensure you backup your data before updating!

###1.1.0
* Change Description text field to a textarea field to allow for multi-line descriptions
* Add URL field
* Change colour of trash icon to red

###1.0.2
* Remove default date row when no dates defined (required fields were causing issues when trying to save a newsletter with no dates)

###1.0.1
* Remove required state from description field

###1.0.0
* Initial release