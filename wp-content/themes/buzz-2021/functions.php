<?php

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
*/
if ( file_exists( __DIR__ . '/vendor/autoload.php')) {
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
if (class_exists('Firefly\\Init')) {
    new \Firefly\Init();
} else {
    wp_die('You must install Composer dependencies. Run <strong>composer install</strong> now');
}

// deactivate new widgets block editor
add_action( 'after_setup_theme', function() {
	remove_theme_support( 'widgets-block-editor' );
});

// default to 'file' link in image gallery
add_action( 'after_setup_theme', function() {
    update_option( 'image_default_link_type', 'file' );
});

// fix Kirki font folder location
add_filter('option_kirki_downloaded_font_files', function ($stored) {
    array_walk($stored, function (&$local, $remote) {
        $local = preg_replace('/^.*(\/fonts.*)/', WP_CONTENT_DIR . '${1}', $local);
    });
    return $stored;
});

// stop inserting kirki inline for email view
if (!is_customize_preview() || is_email_view() ) {
	add_filter( 'kirki_output_inline_styles', '__return_false' );
}
add_filter( 'kirki/config', function( $config = array() ) {
    $config['styles_priority'] = 10;
    return $config;
} );

// remove admin bar for email view
add_action('after_setup_theme', function() {
	if (is_email_view()) {
  		show_admin_bar(false);
	}
});