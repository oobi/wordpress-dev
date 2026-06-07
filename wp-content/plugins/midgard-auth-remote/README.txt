=== Firefly Midgard - Remote Authentication ===
Contributors: firefly
Donate link: www.fi.net.au
Tags: firefly, midgard, data, feeds, authentication, security
Requires at least: 4.6
Tested up to: 4.8.1
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Allow an external process to view secure content in this site by supplying a Midgard-supplied JWT token, and without going through the usual login form.

The JWT is validated with the external site and if valid then the chosen user is automatically logged in for the purpose of viewing private content.

The user you select should have the MINIMUM permission level necessary to view the restricted content (e.g. subscriber).

Generate a JSON Web Token (JWT) in the remote Midgard site and supply it in the URL like so:
http://yoursite.com?midgard-auth-remote=XXXXX

You may optionally hide/disable admin functions (access to dashboard and admin bar) for the chosen user.

It's best to define a user specially for this purpose. Ensure that the user has a strong password and an email address that nobody has access to (to avoid password reset issues).

NOTE: Use with caution.

== Installation ==

1. Make sure Midgard plugin is installed and active
1. Upload `midgard-auth-jwt` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

###1.0.2
* Bug fix - Fixed hiding admin bar on first request

###1.0.1
* Bug fix - JSON must decode to associative array. Trap decode errors.

###1.0
* Initial build