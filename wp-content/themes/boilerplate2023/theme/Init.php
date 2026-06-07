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
        'Firefly\Setup\Menu',
        'Firefly\Setup\Admin',
        'Firefly\Setup\Images',
        'Firefly\Setup\Editor',
        'Firefly\Setup\Scripts',
        'Firefly\Setup\Gallery',
        'Firefly\Setup\Widgets',
        'Firefly\Setup\WordPress',
        'Firefly\Setup\PostTypes',
        'Firefly\Setup\Taxonomy',
        'Firefly\Setup\ThemeSupport',
        'Firefly\Setup\PageTemplates',
        'Firefly\Gutenberg\Gutenberg',
        'Firefly\Customizer\Customizer',

		// Onboardng
		'Firefly\Onboarding\Onboarding',

        // Providers
        'Firefly\Carbon\Init',
        'Firefly\Timber\Init',

		// Shortcodes
		'Firefly\Shortcodes\FontAwesome',
		'Firefly\Shortcodes\SocialLinks',
		'Firefly\Shortcodes\IFrame',

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
        if ( version_compare(PHP_VERSION, '7.4', '<=') ) {
            wp_die( 'Whoops! PHP 7.4 or greater is required. You are currently using ' . PHP_VERSION );
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
