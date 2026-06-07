=== Buzz Add-On - Taxonomies ===
Contributors: firefly
Donate link: http://www.fi.net.au
Tags: firefly, buzz, email, taxonomy
Requires at least: 4.6
Tested up to: 6.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Taxonomies to the Buzz Newsletter

== Description ==

Add custom taxonomies to Buzz Newsletter. These let you categorise articles for display in sections.

== Installation ==

1. This is an add-on to "The Buzz". Make sure that the master plugin is installed and activated first.
1. Upload `buzz-addon-taxonomies` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###2.3
* Appending /issue/<id> to taxonomy url will limit returned articles to that issue
* Appending /issue/latest will limit returned articles to the latest issue

###2.2
* Fix taxonomy ordering not persisting in category view

###2.1
* Add show_in_rest property to allow this to work with Gutenberg

###2.0
* Add sortable category meta boxes to newsletter edit screen.
* Articles can be dragged and dropped in and out of categories
* Disable sorting for category metaboxes in newsletter edit view to avoid confusion about actual order of categories
* Make category and tag slugs a public static property
* Add drag and drop category re-ordering to the Article Categories edit screen

###1.1.4
* Add category indicator to articles on newsletter edit screen

###1.1.3
* Removed updater code missed in the last version

###1.1.2
* Remove updater code in favour of external group updater (better network support)

###1.0
* Initial release
