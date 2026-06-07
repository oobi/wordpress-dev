=== Buzz Newsletter 2018 ===
Contributors: Firefly Interactive
Donate link: http://www.thebuzz.net.au
Tags: theme, newsletter, buzz
Requires at least: 4.6
Tested up to: 4.9.8
Stable tag: 4.9.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Plugin Dependencies ==

The following plugins are required to use this theme
* Kirki https://wordpress.org/plugins/kirki/
* Timber https://wordpress.org/plugins/timber-library/


== Changelog ==

### 2.1.1
* Remove css file that doesn't exist

### 2.1
* Add twig debug output

### 2.0.1
* Remove widget block editor

### v2.0
* Remove font dependencies from Kirki

### v1.9
* Add post excerpts to tag/category list views

### v1.8.6
* check for existence of meta key

### v1.8.5
* Fix bad include reference for post/single
* Fix render for articles not attached to newsletter

### v1.8.4
* Add slug IDs to category headings

### v1.8.3
* Fix display of blog sub-title in header (previously showing title twice)

### v1.8.2
* Add support for Public Post Preview plugin

### v1.8.1
* Add support for Password Protected plugin - did not previously allow changing of login logo

### v1.8
* Add Facebook meta tag support to base theme

### v1.7.1
* Fix padding between dates in email view

### v1.7
* Remove embedded Timber and Kirki. Must now be included with separate plugins.
* Move dates templates from the theme into the Dates Addon plugin
* Add new optional widget areas (mirrored in email view):
	* above featured articles
	* between featured/index articles
	* below index articles
	* article sidebar (not present in email view)
* add new dates widgets for displaying dates
* Fix spacing of tags in article teasers
* Remove image resize capability in editor
* Wait for plugins to be ready before initialising Buzz classes (functions.php)

### v1.6.3
* Fix navigation dropdown menu link spacing

### v1.6.2
* Add taxonomy classes to all article teaser wrappers

### v1.6.1
* Add taxonomy classes to tax archive page body class
* Fix bug with taxonomy body classes overriding wordpress default body classes

### v1.6
* Add tag support for Buzz Addon Taxonomies plugin
* Add category/tag classes to all category titles, tag links and the body class on article pages

### v1.5.1
* Fix uncategorized articles not showing section title (introduced in v1.5.0)

### v1.5
* Show unpublished articles in front end view if logged in as editor
* Escape text attibutes of images where post title is used as alt/title
* Update category ordering to work with changes in buzz-addon-taxonomies v2.0
* Refactor newsletter controller to use a pluck for categories/taxonomies

### v1.4.5
* Fix bug with features articles with no featured image not showing excerpt in email view
* Actually add styles to the stylesheet referenced in v1.4.4

### v1.4.4
* Adjust padding of mobile menu sections to make more consistent

### v1.4.3
* Fix social links not appearing in mobile menu
* Prevent resizing of objects in editor (except for images)

### v1.4.2
* Change default gallery thumbnail size to WordPress "medium"
* Add CSS grid support to galleries

### v1.4.1
* Fix Read More links on homepage from breaking over newlines
* Fix padding on index link in nav in mobile view
* Add default customizer colour to links in footer menu row
* Remove default font weight from primary nav links
* Make widget area headings slightly bigger
* Remove section-title HTML element if field is empty
* Replace "add text padding" checkboxes with HTML class fields
* Add sidebar HTML class field in order to customise width, padding, etc

### v1.4
* Add text padding control to index articles in email view
* Add site logo to login page

### v1.3.1
* Fix classes added to tables from TinyMCE Formats dropdown so that styles get applied correctly

### v1.3
* Add customizer options for table colours

### v1.2
* Add ability to use Buzz Taxonomies Add On
* Rework index articles to always be returned in categorized format (even if add on plugin is disabled) to ensure standardised data
* Add Customizer options for sidebar category titles in article view
* Add ability to use dropdowns in the navbar
* Change all get_posts to get as BuzzPost class
* Add newsletter method to BuzzPost to easily get an articles parent newsletter

### v1.1
* Globalise defaults for Kirki settings
* Provide convenience method for accessing theme mods with Kirki defaults

### v1.0
* Initial release
