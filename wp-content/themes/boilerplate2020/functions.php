<?php

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
*/
if ( file_exists( __DIR__ . '/vendor/autoload.php')) {
    require_once(__DIR__ . '/vendor/autoload.php');
}

/*
|--------------------------------------------------------------------------
| Initialize The Firefly Theme
|--------------------------------------------------------------------------
*/
if (class_exists('Firefly\\Init')) {
    new \Firefly\Init();
} else {
    wp_die('You must install Composer dependencies. Run <strong>composer install</strong> now');
}

// disable widgets block editor
add_filter( 'use_widgets_block_editor', '__return_false' );