=== FF-DocRaptorDOC ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, DocRaptor
Requires at least: 4.6
Tested up to: 5.7
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add hooks for PDF generation via DocRaptor

== Description ==

DocRaptor is a hosted HMTL-to-PDF generator.
https://docraptor.com/

This plugin provides a WordPress API to interface your content with the generator.

== Installation ==

1. Upload `ff-docraptor` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Include the following constants in the wp-config.php

```php
define('DOCRAPTOR_API_KEY', 'your-api-key');
```

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###1.2
* Patch for PHP 8.2

###1.1
* Replace file_get_contents with wp_remote_get

###1.0.1
* Fix plugin name/version constants to be unique

###1.0
* Initial release

== Notes ==

DocRaptor documentation
https://docraptor.com/documentation
