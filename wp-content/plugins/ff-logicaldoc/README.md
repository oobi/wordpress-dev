=== FF-LogicalDOC ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, LogicalDOC
Requires at least: 4.6
Tested up to: 5.3.2
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sync post metadata fields to LogicalDOC custom fields. Built for Actuaries Institute.

== Description ==

LogicalDOC is a cross-platform document management system. For more information on features see their website at https://www.logicaldoc.com/

This connector allows WordPress to send data to LogicalDOC to store articles as assets in the document store so they can be searched alongside other documents as part of an organisation-wide information repository.

The API requires at least LogicalDOC v8.0.0

== Installation ==

1. Upload `ff-logicaldoc` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Include the following constants in the wp-config.php

```php
define('LOGICAL_HOST', 'http://your-logical-url');
define('LOGICAL_USER', 'your-login');
define('LOGICAL_PASS', 'your-password');
define('LOGICAL_ROOT_FOLDER', 4);			// ID of folder where WordPress should create documents from posts
define('LOGICAL_TEMPLATE_ID', 9);			// ID custom field template to use (optional)
```

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==
###2.7.1
* PHP 8.2 compatibility

###2.7
* New required attributes from 8.5.x

###2.6
* Prevent users adding new terms to LogicalTag

###2.5
* Update to support new required attributes in Logical 8.3.x

###2.4
* delete synced items from Logical when sync is unchecked
  - When a user ticks the “sync” checkbox, data is sent to Logical as normal
  - When a user unchecks the “sync” checkbox, any linked document (identified by its Logical ID) will be deleted from the Logical system. This is irreversible, but the item can be re-synced at a later date.
  -  Any download links to unsynced documents will cease to function, even if the item is subsequently re-synced (these links would need to be recreated)
* Maintain sync with “Author” custom field
  - Remove Author as an editable meta field in WordPress (Logical meta field set)
  - Send “author” as comma delimited string of author names to Logical on sync

###2.3.2
* Strip slashes from meta fields to prevent sending \" \' to LogicalDOC.

###2.3.1
* Change date created meta fieldname in Logical to "created-date" rather than "date" due to naming collision with internal field in LoigicalDOC database.

###2.3
* Add date created meta field

###2.2
* Additional default settings

###2.1
* Default values for excerpt and author
* Make some fields NOT required
* Support for "co-authors plus" plugin

###2.0
* Add additional metadata support for Actuaries
* Add sync for LogicalDOC taxonomy terms

###1.1.1
* Bug fix - remove hard wired 'disabled' setting

###1.1
* Add an admin message and disable all functionality if config is incomplete.

###1.0
* Initial release

== Notes ==

Makes use of code from these libraries:
All credit to the authors!

###LogicalDoc Web Services API
https://docs.logicaldoc.com/en/web-services-api
