=== Plugin Name ===
Contributors: Laurent Dinclaux
Tags: nav menu, 3.0, submenu, sub-menu, child, child menu, portion of menu, menu portion
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 0.7-beta4
Submenu is a  WordPress plug-in that enhances the WordPress 3.x navigation system.

== Description ==

Submenu Pro is a  WordPress plug-in that enhances the WordPress 3.x navigation system. When you have a website based on WordPress with a lot of pages, but you need a custom menu, it can be tedious to have to add a menu entry for each created page.

Submenu Pro just does it automatically.

Submenu Pro also allows you to put menus or portion of menus anywhere in your site: sidebar, templates or page contents.

Languages available: english, french

== Installation ==

1. Upload `gecka-submenu` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. [Read the documentation](http://gecka-apps.com/documentation/geka-submenu-pro/)

== Screenshots ==

1. Advanced custom menu widget
2. Autopopulating with posts
3. Autopopulating with taxonomies

== Changelog ==

= 100.0.1-firefly =
* fixed issue where bool was fed to foreach loop.

= 99.1.0-firefly =
* fixed php 8.0 compatibility issue (abuse of 'abs()' function)

= 99.0.8-firefly =
* fixed constructors to support PHP 7.1

= 0.7 =
* performances enhancements
* better widget with specific class support
* depth parameter support to autopopulate menus
* added a gk_submenu_widget_fallback action to use to display custom content
  when the widget has no menu entries to display

= 0.6 =
* add WPML support
* add a custom menu walker to enable full customization of menu items output using filters
* fixed: menu slug usage to specify a menu in shortcodes
* fixed: wrong submenu when viewing a category archive
* fixed: javascript in admin for wordpress 3.1
* Allow to autopopulate any menu item (was restricted to pages)
* Allow to specify a specific taxonomy when autopopulating using posts
* Allow to specify a "start_from" parameter when autopopulating using taxonomies

= 0.5.3 =
* add menu item description positioning
* fix template action
* fix start_from parameter that wasn't used

= 0.5.2 =

* corrected readme file

= 0.5.1 =

* bug fix

= 0.5 =
* new and better way to autopopulate menu
* pro version functionalities
* other fixes
* new widget

= 0.4.2 =
* More fixes
* Menu parameter is no more mandatory, gets the lowest ID menu if not set

= 0.4.1 =
* Fixed bug in submenu builder

= 0.4 =
* Fixed some notice errors
* Experimental functionnality to autopopulate a page menu item with its subpages for WP 3.0 nav menu system

= 0.3 =
* Added template tag and shortcode support

= 0.2 =
* Bugs fixes
* Localization support (French added).

= 0.1 =
* First version.
