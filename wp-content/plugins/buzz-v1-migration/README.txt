=== Buzz V1 Migration Tool ===
Contributors: firefly
Donate link: http://www.fi.net.au
Tags: firefly, buzz, email
Requires at least: 4.6
Tested up to: 4.7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Migrates Newsletter v1 posts and taxonomies to Buzz Newsletter v2+

The old version of the newsletter used taxonomies for issues, where as the new versions use a custom post type for that purpose.

The migration tool scans the existing data and creates newsletter issues from the old data.
It is non-destructive so your original data is not affected should you wish to revert to the original v1 implementation.

== Installation ==

1. Upload `buzz-v1-migration` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Activate The Buzz plugin
1. Leave the legacy theme active
1. If you are on a multi site installation DO NOT NETWORK ACTIVATE THE PLUGIN
1. Under the Tools menu in WordPress administrator you'll find the updater page where you can scan for newsletters and perform the update.
1. After you have migrated sucessfully you can activate a Buzz theme.

== Changelog ==

###1.1.2
* Remove updater code in favour of external group updater (better network support)

###1.1.1
* Initial release
