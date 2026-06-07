<?php

namespace Firefly\Buzz;

use Firefly\Buzz\Core\Config;

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
        'Firefly\Buzz\Setup\Menu',
        'Firefly\Buzz\Setup\Admin',
        'Firefly\Buzz\Setup\Images',
        'Firefly\Buzz\Setup\Editor',
        'Firefly\Buzz\Setup\Scripts',
        'Firefly\Buzz\Setup\Gallery',
        'Firefly\Buzz\Setup\Widgets',
        'Firefly\Buzz\Setup\PostTypes',
        'Firefly\Buzz\Setup\ThemeSupport',
        'Firefly\Buzz\Setup\PageTemplates',

        // API
        'Firefly\Buzz\Api\Api',

        // Providers
		'Firefly\Buzz\Timber\Init',

		// Buzz specific setup
        'Firefly\Buzz\Setup\Newsletter',
        'Firefly\Buzz\Setup\ArticleTemplates',
        'Firefly\Buzz\Customizer\Customizer',
    ];

    function __construct()
    {
        if( self::$loaded ) {
            return self;
        }

        self::$loaded = true;

		$this->check_php_version()
			 ->add_hooks()
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
        if ( version_compare(PHP_VERSION, '7.0', '<=') ) {
            wp_die( 'Whoops! PHP 7.0 or greater is required. You are currently using ' . PHP_VERSION );
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
        Config::bind('config', require( __DIR__ . '/config.php'));
        return $this;
	}

	/**
	 * Add any actions or filters requried by features or plugins
	 */
	protected function add_hooks() {
		add_filter( 'buzz_email_template', array($this, 'set_email_template') );
		add_filter( 'buzz_print_template', array($this, 'set_print_template') );

		return $this;
	}

	/**
	 * Set the email template path
	 * @param $templates - default array of template paths to add to or replace
	 * Add your template in the front
	 */
	public function set_email_template( $templates ) {
		return array( 'controllers/single-newsletter-email.php');
	}

	/**
	 * Set the print template path
	 * @param $templates - default array of template paths to add to or replace
	 * Add your template in the front or return a new array
	 */
	public function set_print_template( $templates ) {
		return array( 'controllers/single-newsletter-print.php');
	}
}

