=== Buzz Add-On - Email View ===
Contributors: firefly
Donate link: http://www.fi.net.au
Tags: firefly, buzz, email
Requires at least: 4.6
Tested up to: 4.9.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds an email compatible template to the Buzz Newsletter plugin

Register email style sheets like so:

```php
if( class_exists( Buzz_Addon_Email_View) ) {
   Buzz_Addon_Email_View::register_style(...)
   Buzz_Addon_Email_View::enqueue_style(...)
}
```


== Installation ==

This section describes how to install the plugin and get it working.

1. This is an add-on to "The Buzz". Make sure that the master plugin is installed and activated first.
1. Upload `buzz-addon-email-view` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Access the Email Settings page to configure your unsubscribe link (required)

== Changelog ==

###1.6
* Improved removal of javascript in email template - now filters Gravity Forms.

###1.5.5
* Fix deprecated dynamic property

###1.5.4
* Add trailing slash to method which retrieves email view URL

###1.5.3
* Fix issue handling null parent newsletter

###1.5.2
* Remove scripts added to email view by the GA Google Analytics plugin

###1.5.1
* Remove scripts added to email view by the Firefly MultiSite Global Analytics plugin

###1.5
* Filter the template name to allow themes to set their own template path for the email layout. The new filter is 'buzz-email-template' and takes an array of template names passed to locate_template(...)

###1.4.0
* Ensure plugin cannot run without main Buzz Newsletter plugin being active
* Move Buzz_Addon_Email_View class to it's own file

###1.3.1
* Bug Fix: Move disable scripts and analytics hooks to 'wp' action when $post is available

###1.3
* Remove global method "ff_get_email_link" as it was only being used internally.
* Remove emoji scripts/styles for email rendering
* Add static methods for dealing with email style sheets
* Add static method and checks for is_email_view
* Disable Monster Analytics script output for email view

###1.2.4
* Changed email articles category sort so it works the same way as main newsletter categories

###1.2.3
* Remove updater code in favour of external group updater (better network support)

###1.2.1
* updated format array method to allow categorisation in email view

###1.1.3
* Removed unused actions.php file
* Allowed BR tags in unsubscribe field on options page
* Add get_unsubscribe_link static method

###1.1.2
* Prevent unwanted scripts / styles from rendering in email view

###1.1.1
* Prevent wp-embed script from rendering in footer (WP 4.4.1)

###1.1.0
* Added a new function to get the Email View URL by newsletter ID
* The above function also now handles Newsletters that are in Draft mode

###1.0.0
* Initial release