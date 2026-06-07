<?php
/*
|--------------------------------------------------------------------------
| Theme Configuration
|--------------------------------------------------------------------------
|
| This file is responsible for all theme settings. Specifically, setting
| the image sizes, gallery attrributes, menus, scripts, styles and
| API keys. These are accessible calling Config::get('config')['xxx']
|
*/
return [

	/*
	|--------------------------------------------------------------------------
	| Version Number
	|--------------------------------------------------------------------------
	|
	| The number used for versioning the WordPress theme files
	|
	*/
	'version' => '1.6.4',

	/*
	|--------------------------------------------------------------------------
	| Buzz Customizer Options
	|--------------------------------------------------------------------------
	|
	| Set to false to remove the corresponding customizer option.
	|
	| Custom themes can pick and choose what they want (most should be FALSE)
	| 		If the options will do nothing in the custom theme, turn it off.
	|
	*/
	'buzz_customizer' => [
		'colors' 			=> true,
		'fonts' 			=> true,
		'branding' 			=> true,
		'navbar' 			=> true,
		'featured-articles' => true,
		'footer' 			=> true,
		'social-media' 		=> true,
		'index-page' 		=> true,
		'article-page' 		=> true,
		'custom-css' 		=> true,
		'add-ons'			=> true, // alternative to disabling the add-on plugin(s)
	],

	/*
	|--------------------------------------------------------------------------
	| Text Domain
	|--------------------------------------------------------------------------
	|
	| The text domain used for localisation
	|
	*/
	'text_domain' => 'ff_buzz',

	/*
	|--------------------------------------------------------------------------
	| Content Width
	|--------------------------------------------------------------------------
	|
	| The size WordPress uses to resize images from
	|
	*/
	'content_width' => 1170,

	/*
	|--------------------------------------------------------------------------
	| Image Sizes
	|--------------------------------------------------------------------------
	|
	| An array on image sizes used by the theme. Must contain name, width,
	| height, and crop key value pairs
	|
	*/
	'image_sizes' => [
		[
			'name' 		=> 'newsletter-hero',
			'width' 	=> 1170,
			'height' 	=> 366,
			'crop' 		=> true,
		],
		// Don't delete the article-hero if it is the same size as newsletter-hero
		// WP will only generate one size and you can call it with both names
		[
			'name' 		=> 'article-hero',
			'width' 	=> 1170,
			'height' 	=> 500,
			'crop' 		=> true,
		],
		[
			'name' 		=> 'article',
			'width' 	=> 640,
			'height' 	=> 360,
			'crop' 		=> true,
		],
		[
			'name' 		=> 'article-large',
			'width' 	=> 960,
			'height' 	=> 540,
			'crop' 		=> true,
		],
		// [
		// 	'name' 		=> 'article-portrait',
		// 	'width' 	=> 480,
		// 	'height' 	=> 530,
		// 	'crop' 		=> true,
		// ],
		[
			'name' 		=> 'email-hero',
			'width' 	=> 640,
			'height' 	=> 200,
			'crop' 		=> true,
		],
		[
			'name' 		=> 'email-thumb',
			'width' 	=> 145,
			'height' 	=> 109,
			'crop' 		=> true,
		],
		[
			'name' 		=> 'email-article',
			'width' 	=> 290,
			'height' 	=> 218,
			'crop' 		=> true,
		],
		[
			'name' 		=> 'email-article-large',
			'width' 	=> 600,
			'height' 	=> 450,
			'crop' 		=> true,
		],
		// [
		// 	'name' 		=> 'email-article-portrait',
		// 	'width' 	=> 183,
		// 	'height' 	=> 202,
		// 	'crop' 		=> true,
		// ],
		[
			'name' 		=> 'logo',
			'width' 	=> 0, // any width
			'height' 	=> 150,
			'crop' 		=> false,
		],

		// Force the default WordPress image size to hard crop
		// Set the media options from dashboard/settings/media
		// Use an appropriate ratio 16:9 or 4:3 for these image sizes
		// These are used for in the srcset of all images on the site

		// 992 x 558
		[
			'name' => 'medium',
			'width' => get_option('medium_size_w'),
			'height' => get_option('medium_size_h'),
			'crop' => true,
		],
		// 1200 x 675
		[
			'name' => 'large',
			'width' => get_option('large_size_w'),
			'height' => get_option('large_size_h'),
			'crop' => true,
		],
	],

	/*
	|--------------------------------------------------------------------------
	| SVG
	|--------------------------------------------------------------------------
	|
	| Allows SVG to be uploaded to the Media Library
	|
	*/
	'allow_svg' => true,

	/*
	|--------------------------------------------------------------------------
	| Gallery Settings
	|--------------------------------------------------------------------------
	|
	| An override of the WordPress gallery. Set the number of columns
	| and image size to use.
	|
	*/
	'gallery_settings' => [
		'thumbnail_size' 	=> 'medium', 	// Set to "false" to use gallery-specific settings. Defaults to "thumbnail".
		'lightbox_size' 	=> 'full',		// Must be specified.
	],

	/*
	|--------------------------------------------------------------------------
	| Menus
	|--------------------------------------------------------------------------
	|
	| The menus used by the theme. The key is used to store and access the
	| menu from the Timber context, the value is used as an label in
	| the appearance -> menu screen
	|
	*/
	'menus' => [
		'primary' 		=> 'Navbar',
		'footer' 		=> 'Footer',
	],

	/*
	|--------------------------------------------------------------------------
	| Scripts
	|--------------------------------------------------------------------------
	|
	| An array of scripts required by the theme
	|
	*/
	'scripts' => [
		// lightbox substitute
		[
			'name' => 'lightcase',
			'src' => '/assets/js/lightcase.js',
			'deps' => ['jquery'],
			'in_footer' => true
		],
		// FontAwesome 5 Pro
		[
			'name' => 'fontawesome',
			'src' => '/assets/js/fontawesome-all.min.js',
			'deps' => [],
			'in_footer' => true
		],
		// main custom scripts
		[
			'name' => 'firefly',
			'src' => '/assets/js/main.js',
			'deps' => ['jquery', 'lightcase'],
			'in_footer' => true
		],
		// Components
		[
			'name' => 'fireflyvue',
			'src' => '/assets/js/app.js',
			'deps' => [],
			'in_footer' => true
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Styles
	|--------------------------------------------------------------------------
	|
	| An array of styles required by the theme
	|
	*/
	'styles' => [
		[
			'name' => 'fontawesome',
			'src' => '/assets/css/fa-svg-with-js.css',
			'deps' => []
		],
		[
			'name' => 'firefly',
			'src' => '/assets/css/main.css',
			'deps' => []
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Editor Styles
	|--------------------------------------------------------------------------
	|
	| An array of styles required by TinyMCE Editor
	|
	*/
	'editor' => [
		get_theme_file_uri('assets/css/editor.css'),
		get_theme_file_uri('/assets/css/fa-svg-with-js.css'),
	],

	/*
	|--------------------------------------------------------------------------
	| Admin Scripts and Styles
	|--------------------------------------------------------------------------
	|
	| An array of scripts and styles required by the admin Dashboard
	|
	*/
	'admin' => [
		'styles' => [
			[ 'name' 	=> 'buzz-admin',
			  'src' 	=> '/assets/css/admin.css',
			  'deps' 	=> [] ],
		],
		'scripts' => [
			// [ 'name' 		=> '',
			//   'src'			=> '',
			//   'deps'		=> [],
			//   'in_footer'	=> true ],
		]
	],

];