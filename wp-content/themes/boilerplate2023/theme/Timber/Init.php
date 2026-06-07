<?php

namespace FireFly\Timber;

use Timber\Timber;
use Firefly\Timber\Context;

class Init
{

    function __construct()
    {
        $timber = new Timber();

        $this->validate();

        Timber::$dirname = ['resources/views', 'views'];

        new Context;
    }

    private function validate()
    {
        if ( ! class_exists( 'Timber' ) ) {
            add_action( 'admin_notices', function() {
                echo '<div class="error"><p>Timber not activated. Make sure you are loading the correct path from your vendor directory.</p></div>';
            });

            add_filter('template_include', function ($template) {
                return get_stylesheet_directory() . '/theme/Timber/no-timber.php';
            });

            return false;
        }

        return true;
    }
}