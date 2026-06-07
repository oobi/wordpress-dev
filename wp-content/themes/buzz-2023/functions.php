<?php

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once(__DIR__ . '/vendor/autoload.php');
}

if ( file_exists( __DIR__ . '/vendor/kirki-framework/kirki/kirki.php' ) ) {
    require_once  __DIR__ . '/vendor/kirki-framework/kirki/kirki.php';
}


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

// deactivate new widgets block editor
add_action('after_setup_theme', function () {
	remove_theme_support('widgets-block-editor');
});

// default to 'file' link in image gallery
add_action('after_setup_theme', function () {
	update_option('image_default_link_type', 'file');
});

// fix Kirki font folder location
add_filter('option_kirki_downloaded_font_files', function ($stored) {
	array_walk($stored, function (&$local, $remote) {
		$local = preg_replace('/^.*(\/fonts.*)/', WP_CONTENT_DIR . '${1}', $local);
	});
	return $stored;
});

// stop inserting kirki inline for email view
if (!is_customize_preview() || is_email_view()) {
	add_filter('kirki_output_inline_styles', '__return_false');
}
add_filter('kirki/config', function ($config = array()) {
	$config['styles_priority'] = 10;
	return $config;
});

// remove admin bar for email view
add_action('after_setup_theme', function () {
	if (is_email_view()) {
		show_admin_bar(false);
	}
});

// filter our &nbsp; in excerpts
add_filter('the_excerpt', function ($excerpt) {
	return trim(str_replace('&nbsp;', '', $excerpt));
}, 999, 1);

/**
 * Is this the print view?
 */
if (!function_exists('is_print_view')) :
	function is_print_view()
	{
		$has_print_view = class_exists('Buzz_Addon_Print_View');
		$is_print_view = $has_print_view && \Buzz_Addon_Print_View::is_print_view();
		return $is_print_view;
	}
endif;

if (!function_exists('is_email_view')) :
	function is_email_view()
	{
		$has_email_view = class_exists('Buzz_Addon_Email_View');
		$is_email_view = $has_email_view && \Buzz_Addon_Email_View::is_email_view();
		return $is_email_view;
	}
endif;


/**
 * Redirect template for email/print views
 */
add_filter('template_include', function($template)
{
	if (is_email_view()) {
		$template = locate_template('single-newsletter-email.php');
	}

	if (is_print_view()) {
		$template = locate_template('single-newsletter-print.php');
	}

	// otherwise return default template
	return $template;
}, 10);



/**
 *	Retrieve articles for newsletter nav
 */
function get_articles_from_newsletter($showCategoryHeadings)
{
	if (is_singular('article')) {
		$post = get_post();
		$parent_id = get_post_meta($post->ID, 'ff_parent_id', true);
	} else if (is_singular('newsletter')) {
		$parent_id = get_the_ID();
	} else {
		return [];
	}

	$articles = (new Firefly\Buzz\Articles($parent_id))->get();

	if ($showCategoryHeadings) {
		return Firefly\Buzz\Articles::categorize($articles->articles);
	}
	return $articles->articles;
}

if(!is_email_view() && !is_print_view()) {
	add_action('wp_body_open', function() {
		$context = Timber::context();
		$context['articles'] = get_articles_from_newsletter(true);
		$context['menu'] = new Timber\Menu('primary');
		Timber::render('layouts/offcanvas.html.twig', $context);
	}, 10, 0);
}