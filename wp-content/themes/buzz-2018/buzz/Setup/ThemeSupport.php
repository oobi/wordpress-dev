<?php

namespace Firefly\Buzz\Setup;

class ThemeSupport
{

    public function __construct()
    {
        add_action( 'after_setup_theme', array( $this, 'add_theme_support' ));
    }

    public function add_theme_support()
    {
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );

        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );
    }
}
