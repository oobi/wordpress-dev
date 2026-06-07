<?php

namespace Firefly\Buzz\Setup;

use Firefly\Buzz\Core\Config;

class Images
{

    function __construct() {
		// setup theme
        add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

        if( Config::get('config')['allow_svg'] ) {
            add_filter('upload_mimes', array( $this, 'upload_mimes') );
        }
    }

    public function after_setup_theme() {
		foreach ( Config::get('config')['image_sizes'] as $image ) {
			add_image_size( $image['name'], $image['width'], $image['height'], $image['crop'] );
		}

        // Set the default content width.
        $GLOBALS['content_width'] = Config::get('config')['content_width'];
    }

    public function upload_mimes($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
	}

}