=== Firefly Midgard - JWT Authentication ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds, authentication, security
Requires at least: 4.6
Tested up to: 4.8.1
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Generate a JSON Web Token (JWT) for the currently logged in user.
This token can be used to authenticate remotely against REST services.

Requires the plugin ff-jwt-authentication-for-wp-rest-api - must be installed and active.

To generate a token go to http://yoursite.com?midgard-auth-jwt

The token will be returned in the URL as the 'midgard-auth-jwt' parameter value.
http://yoursite.com?midgard-auth-jwt=XXXXX

The parameter key is configurable in options.


== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-auth-jwt` to the `/wp-content/plugins/` directory
1. Install and activate "Firefly JWT Authentication for REST plugin"
1. Activate both plugins through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###1.5.2
* Added default token expiry

###1.5.1
* Bug fix - missing name spaces

###1.5
* Replace dependency for JWT library/plugin with the Firefly no-conflict version

###1.4.1
* Added default token expiry

###1.4
* Add Custom HTML option
* Added default markup/styles to results page

###1.3
* Auth tab shows next to security in config
* Make JWT URL parameter configurable
* Remove query params (unnecessary) and fall back to $_GET
* Remove secondary 'code' parameter (unnecessary) and return the JWT in the "midgard-auth-jwt" key
* Fix issue where requests that do NOT result in a redirect to external login portal were not handled correctly due to missing SERVER var.

###1.2
* Merged settings page with main Midgard plugin

###1.1
* Add JSON Path expression to select root node of each feed

###1.0
* Initial build