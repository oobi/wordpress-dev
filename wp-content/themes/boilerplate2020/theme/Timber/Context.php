<?php

namespace Firefly\Timber;

use Firefly\Setup\Config;
use Firefly\PageMenu;
use Firefly\Breadcrumb;

class Context
{

    function __construct()
    {
        add_filter('get_twig', [$this, 'add_to_twig']);
        add_filter('timber_context', [$this, 'add_to_context']);
    }

    public function add_to_context( $context )
    {
        $context['gtm_id']   		= carbon_get_theme_option('ff_gtm_id');
        $context['social_links']    = carbon_get_theme_option('ff_social_links');
        return $context;
    }

    public function add_to_twig($twig)
    {
        // Example filter
        // $twig->addFilter(new \Twig_SimpleFilter('the_content', function( $content ) {
        //     return apply_filters( 'the_content', $content );
        // }));

        return $twig;
    }
}