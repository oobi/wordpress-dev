<?php

namespace Firefly\Timber;
use Timber\Twig_Filter;

class Context
{

    function __construct()
    {
        add_filter('get_twig', [$this, 'add_to_twig']);
        add_filter('timber_context', [$this, 'add_to_context']);
    }

    public function add_to_context( $context )
    {
		$context['social_links'] 	= carbon_get_theme_option('ff_social_links');
        $context['gtm_id'] 			= carbon_get_theme_option('ff_gtm_id');
        $context['colophon_text'] 	= carbon_get_theme_option('ff_colophon_text');
        $search_placeholder 		= carbon_get_theme_option('ff_search_placeholder');
        $context['search_placeholder'] = strlen($search_placeholder) > 0 ? $search_placeholder : 'Search for:';

		// alert
		$context['alert'] = [
			'enabled' => carbon_get_theme_option('alert_active') ?? false,
			'text' => carbon_get_theme_option('alert_label'),
			'link' => carbon_get_theme_option('alert_url'),
			'button_text' => carbon_get_theme_option('alert_link_text'),
			'target' => carbon_get_theme_option('alert_target'),
		];

        $context['logos'] = [
            'header-lg' => new \Timber\Image(get_theme_mod( 'ff_brand_header_logo_lg', '' )),
            'header-sm' => new \Timber\Image(get_theme_mod( 'ff_brand_header_logo_sm', '' )),
            'footer' => new \Timber\Image(get_theme_mod( 'ff_brand_footer_logo', get_template_directory_uri() . '/assets/images/logo-white.svg' ))
        ];

        return $context;
    }

    public function add_to_twig($twig)
    {
        // 'Dump' filter
        $twig->addFilter(new Twig_Filter('dump', function( $content ) {
            return '<pre>' . json_encode($content, JSON_PRETTY_PRINT) . '</pre>';
        }));

        return $twig;
    }
}