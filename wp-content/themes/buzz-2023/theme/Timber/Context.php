<?php

namespace Firefly\Timber;

// use Firefly\Setup\Config;
// use Firefly\PageMenu;
// use Firefly\Breadcrumb;
use Firefly\Customizer\Customizer;
use Firefly\Buzz\Newsletter;
use Firefly\Buzz\Articles;

// polyfill for strftime
use function PHP81_BC\strftime;

class Context
{

	function __construct()
	{
		add_filter('get_twig', [$this, 'add_to_twig']);
		add_filter('timber_context', [$this, 'add_to_context']);
		// get the context
	}

	public function add_to_context($context)
	{
		$context['social_links'] = carbon_get_theme_option('ff_bz_social_links');
		// Add On Plugins active
		$context['addons']['email_view'] 	= class_exists('Buzz_Addon_Email_View');
		$context['addons']['print_view'] 	= class_exists('Buzz_Addon_Print_View');
		$context['addons']['taxonomies'] 	= class_exists('Buzz_Addon_Taxonomies');
		$context['addons']['dates'] 		= class_exists('Buzz_Addon_Dates');

		// static variables from plugins
		$context['static_var']['category']	= $context['addons']['taxonomies'] ? \Buzz_Addon_Taxonomies::$category 	: false;
		$context['static_var']['tag']		= $context['addons']['taxonomies'] ? \Buzz_Addon_Taxonomies::$tag		: false;

		// NOTE: Only add customizer data is used in multiple different twigs
		// 		 Everything else should be in a controller or called directly in the twig with theme.theme_mod()

		// Site logo
		$site_logo_id 					= Customizer::get_theme_mod('buzz_branding_site_logo');
		$context['site_logo_url']	    = wp_get_attachment_url($site_logo_id);
		$context['site_logo_image']	    = wp_get_attachment_image($site_logo_id, 'logo');

		// Home Screen logo
		$home_screen_logo_id 			= Customizer::get_theme_mod('buzz_branding_home_screen_logo');
		$context['home_screen_logo_url'] = wp_get_attachment_url($home_screen_logo_id);

		// Header
		$context['header_type']		    = Customizer::get_theme_mod('buzz_branding_header_type');
		$context['header_image_xl']  = wp_get_attachment_image(Customizer::get_theme_mod('buzz_branding_header_image_xl'), 'banner', '', ['class' => 'xl img-responsive']);
		$context['header_image_large']  = wp_get_attachment_image(Customizer::get_theme_mod('buzz_branding_header_image_large'), 'banner', '', ['class' => 'large img-responsive']);
		$context['header_image_small']  = wp_get_attachment_image(Customizer::get_theme_mod('buzz_branding_header_image_small'), 'banner', '', ['class' => 'small img-responsive']);
		$context['header_logo']			= wp_get_attachment_image(Customizer::get_theme_mod('buzz_branding_header_text_logo'), 'logo');
		$context['header_text_title']	= Customizer::get_theme_mod('buzz_branding_header_text_title');
		$context['header_text_subtitle'] = Customizer::get_theme_mod('buzz_branding_header_text_subtitle');

		$context['header_image_timber_xl']  = new \Timber\Image(Customizer::get_theme_mod('buzz_branding_header_image_xl'));
		$context['header_image_timber_large'] = new \Timber\Image(Customizer::get_theme_mod('buzz_branding_header_image_large'));
		$context['header_image_timber_print'] = new \Timber\Image(Customizer::get_theme_mod('buzz_branding_header_image_large'));
		$context['header_image_timber_small'] = new \Timber\Image(Customizer::get_theme_mod('buzz_branding_header_image_small'));

		if (class_exists('Buzz_Addon_Print_View')) {
			$context['header_image_print']  = wp_get_attachment_image(get_theme_mod('buzz_print_image'), 'full', '', ['class' => 'banner-print']);
		}

		if (class_exists('Buzz_Addon_Email_View')) {
			$context['header_image_email']  = wp_get_attachment_image(get_theme_mod('buzz_email_image'), 'banner-email', '', ['class' => 'small img-responsive']);
		}

		// Newsletter hero
		$context['hero']['show'] 		= Customizer::get_theme_mod('buzz_index_page_hero_show');
		$context['hero']['position'] 	= Customizer::get_theme_mod('buzz_index_page_hero_position');

		// Navbar options
		$context['show_nav_title']	= Customizer::get_theme_mod('buzz_navbar_title');
		$context['show_nav_date']	= Customizer::get_theme_mod('buzz_navbar_date');
		$context['show_nav_search']	= Customizer::get_theme_mod('buzz_navbar_search');
		$context['show_nav_articles']	= Customizer::get_theme_mod('buzz_navbar_articles');
		$context['date_format'] 	= Customizer::get_theme_mod('buzz_navbar_date_format');
		$context['date_position'] 	= Customizer::get_theme_mod('buzz_navbar_date_position');

		// Social media links and share buttons
		$context['social']			= Customizer::get_theme_mod('buzz_social_feeds');
		$context['share_buttons']	= Customizer::get_theme_mod('buzz_social_sharing');

		// Footer options
		$footer_prefix 	= 'buzz_footer_';
		$context['footer']['copyright']		= Customizer::get_theme_mod("{$footer_prefix}copyright");
		$context['footer']['class']			= Customizer::get_theme_mod("{$footer_prefix}class");
		$context['footer']['widget_class']	= Customizer::get_theme_mod("{$footer_prefix}widget_class");
		$context['footer']['colophon_class'] = Customizer::get_theme_mod("{$footer_prefix}colophon_class");

		global $post;

		if ($post) {
			if (is_home() || (is_singular() && in_array($post->post_type, ['newsletter', 'article']))) {
				// get categorised list of articles for dropdown menu
				if ($post->post_type == 'newsletter') {
					$nid = $post->ID;
				} else {
					$nid = $post->ff_parent_id;
				}

				$newsletter = Newsletter::get($nid, true);
				$articles = (new Articles($newsletter))->get()->articles;
				$context['navbar']['articles'] 		= Articles::categorize($articles);
				$context['navbar']['articles']['title']		= Customizer::get_theme_mod('buzz_article_page_sidebar_title');
			}
		}

		return $context;
	}

	public function add_to_twig($twig)
	{

		// Custom Date Filter that wraps each section in a span with class
		$twig->addFilter(new \Twig_SimpleFilter('datewrap', function ($datestring, $format) {

			$output = '';
			$date = strtotime($datestring);

			return date($format, $date);

			// if not a valid date, do nothing
			if (! $date) {
				return '';
			}

			// for each letter in the PHP date format string, wrap in a span (non-word characters passed through)
			$fmt = str_split($format);
			foreach ($fmt as $f) {
				if (preg_match('/[a-zA-Z]/', $f)) {
					$output .= sprintf('<span class="%s">%s</span>', $f, date($f, $date));
				} else {
					$output .= $f;
				}
			}

			return $output;
		}));



		/**
		 * Call strftime formatter
		 * http://php.net/manual/en/function.strftime.php
		 */
		$twig->addFilter(new \Twig_SimpleFilter('strftime', function ($datestring, $format = '%e %B') {
			if (!$format) {
				$format = '%e %B';
			}

			$output = '';
			$timestamp = strtotime($datestring);

			// if not a valid date, do nothing
			if (! $timestamp) {
				return '';
			}

			// add in a placeholder reference for orginal (2st, 2nd, 3rd etc)
			$format = str_replace('%O', date('S', $timestamp), $format);

			$output = nl2br(strftime($format, $timestamp));

			return $output;
		}));


		// 'Dump' filter
		$twig->addFilter(new \Twig_SimpleFilter('dump', function ($content) {
			return '<pre>' . json_encode($content, JSON_PRETTY_PRINT) . '</pre>';
		}));

		return $twig;
	}
}
