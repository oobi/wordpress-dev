<?php

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
*/
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once(__DIR__ . '/vendor/autoload.php');
}

/*
|--------------------------------------------------------------------------
| Initialize The Firefly Theme
|--------------------------------------------------------------------------
*/
if ( class_exists( 'Firefly\\Buzz\\Init' ) ) {
	new \Firefly\Buzz\Init();
}