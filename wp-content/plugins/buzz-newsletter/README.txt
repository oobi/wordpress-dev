=== Buzz Newsletter ===
Contributors: firefly
Donate link: http://www.fi.net.au
Tags: firefly, buzz, email
Requires at least: 4.6
Tested up to: 6.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create elegantly simple newsletters in minutes.


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `buzz-newsletter` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

### 3.7.2
* Bugfix - run article sort on newsletter editor load, save and publish. 
Fix issue when new article is added and sort order needs to be updated. 

### 3.7.1
* Remove unwanted xml declaration from inliner output

### 3.7
* Update style inliner for PHP 8.2 compatibility
* Fix style inliner issue with empty style directive

### 3.6.4
* Fix PHP Warning: array_key_exists() expects parameter 2 to be array, null given in public/ff-newsletter-public.php on line 137

### 3.6.3
* Fix issue with reported duplicate class simple_html_dom

### 3.6.2
* PHP 7.3 fixes (email view)

###3.6.1 (in progress)
* Fix vertical alignment of issue name in issue metabox

###3.6
* Remove newsletter issue edit feature from publish meta box into its own meta box (gutenberg compatibility)
* Remove some unused functionality (disabling comments in toolbar etc)
* Remove rogue console log in javascript

###3.5.2
* Fix bug with newsletter filter attempting to add itself to media library

###3.5.1
* Fix bug where you cannot drag articles in newsletter edit screen if the featured image fails to load
* Prevent articles on newsletter edit screen inheriting box-sizing styles from other plugins stylesheets

###3.5.0
* Show drafts in front end newsletter view if logged in as a user with editor/administrator permissions
* Increase visibility of draft articles on newsletter edit screen
* Reduce height of articles on newsletter edit screen
* Flag unpublished pages using title filter
* Add temporary check to register_nav_menu function in preparation for phasing it out in future release
* Show which newsletter an article is attached to in the article list admin screen
* Add newsletter filter to the article list screen
* Minor refactor of CSS/JS/markup in the newsletter admin view to support enhancements to category plugin
* Some minor rewording of some of the prompts

###3.4.0
* Clean up code (remove all unused code)
* Add actions to end of all metaboxes for extending via add-ons
* Fix article thumbnail size on Newsletter edit page

###3.3.4
* FF_Newsletter_Common::is_newsletter_view checks $newsletter->id for is_single(). This allows us to send things that are not WordPress post objects into this method (e.g. Timber)

###3.3.3
* Enabled rest routes for newsletter and article custom post types

###3.3.2
* Remove updater code in favour of external group updater (better network support)

###3.3.1
* Fixed error with non-categorized sidebars trying to sort themselves by category

###3.3
* Added sort by description to categorized articles/sidebars

###3.1.0
* Changed the way ff_get_articles_list function works - now returns an array of articles rather than HTML markup

###3.0.4
* Another bug fix for unsubscribe links to ensure that the inliner does not change the formatting or convert to entities
(which breaks certain email marketing software)
* Update style linliner to latest version

###3.0.3
* Fix to inliner to ignore unsubscribe links

###3.0.2
* Tweak style inliner to remove JavaScript

###3.0.1
* Fixed issue with Draft/Private Newsletters not appearing in Article newsletter selection dropdown.

###3.0.0
* Initial release