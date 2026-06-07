<?php

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once(__DIR__ . '/vendor/autoload.php');
}

use Timber\Timber;

/*
|--------------------------------------------------------------------------
| Initialize The Firefly Theme
|--------------------------------------------------------------------------
*/

if (class_exists('Firefly\\Init')) {
	new \Firefly\Init();
} else {
	wp_die('You must install Composer dependencies. Run <strong>composer install</strong> now');
}


/*
|--------------------------------------------------------------------------
| Features
|--------------------------------------------------------------------------
*/

// disable widgets block editor
add_filter('use_widgets_block_editor', '__return_false');
// fixes the template picker in the editor
remove_theme_support('block-templates');

/**
 * Access to Gravity Forms functions for editor role
 */
add_action('admin_init', function () {
	$role = get_role('editor');
	$role->add_cap('gform_full_access');
});


/*
|--------------------------------------------------------------------------
| Other Settings
|--------------------------------------------------------------------------
*/

// define paginaton params
// Timber::get_pagination() will use this
function get_pagination()
{
	return Timber::get_pagination(3);
}

// add class to body tag if a page has a featured image
add_filter('body_class', function ($classes) {
	if (is_page()) {
		if (has_post_thumbnail()) {
			$classes[] = 'page-has-featured-image';
		}
	}

	if (is_page_template('page-home.php')) {
		$classes[] = 'page-has-featured-image';
	}

	return $classes;
});

// set posts per page for different post types
add_action('pre_get_posts', function ($query) {
	if (!is_admin() && $query->is_main_query()) {
		if (is_category() || is_tag() || is_home() || is_author()) {
			$query->set('posts_per_page', '13');
		}
	}
});

// Defaults Images and Galleries to open to the media URL.
add_action('after_setup_theme', function () {
	update_option('image_default_link_type', 'media');
});
