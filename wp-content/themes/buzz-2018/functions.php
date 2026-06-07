<?php

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
*/
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once(__DIR__ . '/vendor/autoload.php');
}

if ( file_exists( __DIR__ . '/vendor/kirki-framework/kirki/kirki.php' ) ) {
require_once  __DIR__ . '/vendor/kirki-framework/kirki/kirki.php';
}

/*
|--------------------------------------------------------------------------
| Initialize The Firefly Theme
|--------------------------------------------------------------------------
*/
if ( class_exists( 'Firefly\\Buzz\\Init' ) ) {
	new \Firefly\Buzz\Init();
}

// disable block editor for widgets
add_filter( 'use_widgets_block_editor', '__return_false' );