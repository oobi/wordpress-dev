<?php

namespace Firefly;

use Firefly\Setup\Config;

class Init
{

    private static $loaded = false;

    /*
     |--------------------------------------------------------------------------
     | Dependencies
     |--------------------------------------------------------------------------
     |
     | Add your namespaced class dependies We will loop over and use them
     |
     */
    private $dependencies = [
        // WordPress Set up
        'Firefly\Setup\Admin',
        'Firefly\Setup\Editor',
        'Firefly\Setup\Images',
        'Firefly\Setup\Menu',
        'Firefly\Setup\PageTemplates',
        'Firefly\Setup\Scripts',
        'Firefly\Setup\ThemeSupport',
        'Firefly\Setup\Widgets',
        'Firefly\Setup\BlockStyles',

		// Editor setup
        'Firefly\Gutenberg\Gutenberg',

		// Kirki Customizer
        'Firefly\Customizer\Customizer',

        // Providers
        'Firefly\Carbon\Init',
        'Firefly\Timber\Init',

		// Buzz specific setup
        'Firefly\Buzz\Newsletter',
        'Firefly\Buzz\ArticleTemplates',

		// Shortcodes
		'Firefly\Shortcodes\BuzzCredit',
		'Firefly\Shortcodes\CurrentYear',
		'Firefly\Shortcodes\NewsletterTitle',
        'Firefly\Shortcodes\NewsletterTitle',
        'Firefly\Shortcodes\BuzzDates',
    ];

    function __construct()
    {
        if( self::$loaded ) {
            return self;
        }


        self::$loaded = true;

        $this->check_php_version()
		->register_error_handler()
		->set_config();

        // Invoke all class dependencies
        foreach( $this->dependencies as $class ) {
            if( class_exists( $class ) ) {
                new $class;
            }
        }
    }

    protected function check_php_version()
    {
        if ( version_compare(PHP_VERSION, '7.1', '<=') ) {
            wp_die( 'Whoops! PHP 7.1 or greater is required. You are currently using ' . PHP_VERSION );
        };

        return $this;
    }

    protected function register_error_handler()
    {
        if( defined('WP_DEBUG') && WP_DEBUG && ! is_admin() ) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }

        return $this;
    }

    protected function set_config()
    {
        Config::bind('theme', require( __DIR__ . '/theme.php'));
        return $this;
    }
}
