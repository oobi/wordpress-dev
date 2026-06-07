=== Buzz Add-On - Print View ===
Contributors: firefly
Donate link: http://www.fi.net.au
Tags: firefly, buzz, print
Requires at least: 4.6
Tested up to: 6.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a print compatible template to the Buzz Newsletter plugin


== Installation ==

This section describes how to install the plugin and get it working.

1. This is an add-on to "The Buzz". Make sure that the master plugin is installed and activated first.
1. Upload `buzz-addon-print-view` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Changelog ==
###1.4.1
* bug fix checkbox meta flags saving multiple values

###1.4
* Add page break metabox
* Add checkbox to suppress feature image in print

###1.3
* Add 'is_print_view' utility method

###1.2
* Only activate print plugin and define constant when main plugin is active. This gives themes a better clue as to what functionality is actually available.
* Filter the template name to allow themes to set their own template path for the print layout. The new filter is 'buzz-print-template' and takes an array of template names passed to locate_template(...)

###1.1.2
* Remove updater code in favour of external group updater (better network support)

###1.1.0
* Added a new function to get the Print View URL by newsletter ID
* The above function also now handles Newsletters that are in Draft mode

###1.0.0
* Initial release