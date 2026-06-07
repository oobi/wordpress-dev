<?php
/**
 * Plugin Name:   Firefly Facebook Meta Tags
 * Plugin URI:    https://www.fi.net.au
 * Text Domain:   ff-facebook-meta-tags
 * Domain Path:   /languages
 * Description:   Insert Facebook meta tags into wp-head to retrieve featured image and post description.
 * Author:        Firefly Interactive
 * Version:       1.0.0
 * Licence:       GPLv3+
 * Author URI:    http://www.fi.net.au
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/* Load Class. */
require_once plugin_dir_path( __FILE__ ) . 'meta-tags.php';
new FF\FireflyFacebookMetaTags();