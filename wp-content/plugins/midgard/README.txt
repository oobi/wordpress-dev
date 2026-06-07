=== Midgard ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds
Requires at least: 4.6
Tested up to: 5.4.2
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ingest and manage feeds and field mappings from remote sources

== Description ==

Midgard is designed to be a "middleware" between a remote data source and a consumer application. It allows you to create
"feeds" from different data sources and convert to JSON output for consumption.

You can pick and choose which fields appear in your output via "mappings". If the remote data source format changes,
you can simply update the mapping and your consumer apps will never know the difference.

Data can be cached locally for a configurable period of time. This helps with reducing server load for busy APIs.

The plugin ships with a JSON connector, which can be used to map things like WP REST output from other WordPress sites.

Midgard is extensible to consume many forms of remote data. Connectors currently exist for RSS, XML, ICal, Twitter etc.

Feed REST URLs look like :
http://your-site.com/wp-json/ff/v1/midgard/feed-slug			(retrieve single feed by slug)
http://your-site.com/wp-json/ff/v1/midgard/99					(retrieve single feed by ID)
http://your-site.com/wp-json/ff/v1/midgard/						(retrieve list of available feeds)

== Advanced Mapping Example ==

```
[
{% for item in data %}
	{
		"date" : "{{item.date}}",
		"title" : "{{item.title}}",
		"link" : "{{item.link}}",
		"excerpt" : "{{strip_tags(item.excerpt.rendered)}}"
	}{{ (loop.last) ? '' : ',' }}
{% endfor %}
]
```

== Installation ==

1. Upload `midgard` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###2.20
* Simplify CORS implementation to not fight with WordPress headers sent by inbuilt REST controller
* Add access to Pragma and Cache-Control headers

###2.16
* OPTIONS preflight should not require auth header
* Set CORS headers on /midgard/ requests

###2.15.2
* Fix bug with midgard feeds displaying an error when trying to read external feeds with no data (ie. valid, just blank)

###2.15.1
* Fix bug with displaying feeds with errors

###2.15
* Successfully retrieved data is now cached to database so that when cache expires we are still able to provide
  the most recent "good" data if the source is unavailable or throwing errors
* Logic is as follows:
** Data retrieved from source
** If valid it is cached and written to feed meta as 'midgard_cache_data'
** If cache expires and source data is no longer valid then the database value is returned.

###2.14
* Increase fetch timeout to 30 sec. Configure as global variable
* Add ability to filter these params using 'midgard_wp_remote_get_params' filter
* @see https://codex.wordpress.org/Function_Reference/wp_remote_get for available parameters

###2.13.0
* Added Twig wrapper for 'html' function which strips nasty tags and converts newlines to BR

###2.12.2
* "Midgard" name retained in menu

###2.12.1
* Fix bug with administrators not having access to Feeds on multisite installs
* Changed menu name from "Midgard" to "App Feeds"

###2.12
* Move hashing to after mappings are applied so they don't get clobbered.
* Type check array elements before adding hash key

###2.11
* More robust method of managing add/update for empty meta fields

###2.10
* Add a unique identifier to each data item and set. Each item will now include a hash with the key "_midgard_hash"

###2.9
* return NULL rather than FALSE when node cannot be mapped from source (node not found)

###2.8
* Add strip_tags method to advanced mapping
* Add example of advanced mapping markup to readme

###2.7.2
* Bug fix default cache setting storage

###2.7.1
* Replace file_get_contents with more secure method

###2.7
* Add hooks to allow sub-plugins to add their own styles/scripts to options pages

###2.6.3
* Fix issue where security whitelist (exemptions) not saving due to validation error
* Make whitelist URLs site relative

###2.6.2
* Fix missing namespace for Exception thrower in JSON plugin

###2.6.1
* Improve help text on security settings page
* Fix validation issue on security page where exceptions not specified

###2.6
* Change the way authentication of the feeds is handled (security tab) by removing role restrictions.
* Add facility to white list (or black list) REST endpoints to require auth or not

###2.5.3
* Restrict Feeds menu option to admins only

###2.5.2
* Added default to security page options param. Prevents errors when no option exists.

###2.5.1
* Added note to REST URI label to clarify its use

###2.5
* Update cache/twig dependencies

###2.4.1
* Security / auth tabs show first in config

###2.4
* Refactor the preview implementation and limit access to only admins.
* Remove "schema" from the REST definitions as it caused problems with preflight checks (POST)
* Bug fix security tab - empty checkboxes do not throw errors.
* Exclude feeds from search
* Hide Midgard menu item from non-admins

###2.3
* Add hooks to allow sub-plugins to render their setttings pages inside tabs on the main settings page.

###2.2
* Feed 'view' mode (not REST) is now output with a JSON content type header
* Remove redundant overview page

###2.1.1
* Display REST links in edit mode for reference

###2.1
* Implement REST controller for retrieving data

###2.0.2
* fixed bug with feed mapping method that caused errors to be thrown on feed

###2.0.1
* cache folder now deletes itself on plugin uninstall
* improved plugin uninstall routine to support multisite - now deletes plugin options from all blogs

###2.0
* Add mapping 'mode' selector (none, simple, advanced)
* Add 'advanced mapping' support via Twig templates
* This is a breaking change. The default is 'none', which will effectively disable mappings for existing feeds. Previously created feeds will need to be switched to 'simple' and then re-saved. No data is lost.

###1.4.6
* added uninstall methods to clean up database on plugin delete

###1.4.5
* Moved Root Path section and made generic for global use
* Removed unused code

###1.4.4
* Preserve error messages when using mappings

###1.4.3
* Remove debug dumps during initialisation

###1.4.2
* Remove duplicate class attribute in mappings table

###1.4.1
* Move all app pages into their own menu section

###1.4
* Add namespaces to all classes to avoid naming conflicts

###1.3.5
* Remove updater code in favour of external group updater (better network support)

###1.3.4
* Tidy up comments and naming conventions. Include README.

###1.3.3
* Bug fix : common method save_meta_values not differentiating between zero and empty string

###1.3.2
* Initial release

== Notes ==

Makes use of code from these GitHub repositories:
All credit to the authors!

###JSONPath
https://github.com/Skyscanner/JsonPath-PHP

###PHPFastCache
https://github.com/PHPSocialNetwork/phpfastcache

###Twig 1.0
https://twig.sensiolabs.org