<?php

namespace FireFly\Buzz\Timber;

use Timber\Timber;
use Firefly\Buzz\Timber\Context;

class Init
{

    function __construct()
    {
        $timber = new \Timber\Timber();

        $this->validate();

        Timber::$dirname = array('resources/views', 'resources/components');

        new Context;
    }

    private function validate()
    {
        if ( ! class_exists( 'Timber' ) ) {
            add_action( 'admin_notices', function() {
                echo '<div class="error"><p>Timber not activated. Make sure you are loading the correct path from your vendor directory.</p></div>';
            });

            add_filter('template_include', function($template) {
                return get_stylesheet_directory() . '/config/Providers/Timber/no-timber.html';
            });

            return false;
        }

        return true;
    }
}