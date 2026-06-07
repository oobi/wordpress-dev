<?php
/*
|--------------------------------------------------------------------------
| Theme Configuration
|--------------------------------------------------------------------------
|
| This file is responsible for all theme settings. Specifically, setting
| the image sizes, gallery attrributes, menus, scripts, styles and
| API keys. These are accessible calling Config::get('key')
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
	'version' => '1.0.0',

	/*
	|--------------------------------------------------------------------------
	| Theme Name
	|--------------------------------------------------------------------------
	|
	| The name of the theme
	|
	*/
	'theme_name' => 'THEME NAME',

	/*
	|--------------------------------------------------------------------------
	| Text Domain
	|--------------------------------------------------------------------------
	|
	| The name of the theme
	|
	*/
	'text_domain' => 'firefly',

	/*
	|--------------------------------------------------------------------------
	| Content Width
	|--------------------------------------------------------------------------
	|
	| The size WordPress uses to resize images from
	|
	*/
	'content_width' => 1000,

	/*
	|--------------------------------------------------------------------------
	| Theme Colours
	|--------------------------------------------------------------------------
	|
	| Gutenberg colours
	|
	*/
	'theme_colors' => [
        'white' => '#fff',
        'gray-100' => '#f8f9fa',
        'gray-200' => '#e9ecef',
        'gray-300' => '#dee2e6',
        'gray-400' => '#ced4da',
        'gray-500' => '#adb5bd',
        'gray-600' => '#6c757d',
        'gray-700' => '#495057',
        'gray-800' => '#343a40',
		'gray-900' => '#212529',

		'black' =>    '#000',
		'blue' =>    '#007bff',
		'indigo' =>  '#6610f2',
		'purple' =>  '#6f42c1',
		'pink' =>    '#e83e8c',
		'red' =>     '#dc3545',
		'orange' =>  '#fd7e14',
		'yellow' =>  '#ffc107',
		'green' =>   '#28a745',
		'teal' =>    '#20c997',
		'cyan' =>    '#17a2b8',
    ],

	/*
	|--------------------------------------------------------------------------
	| Theme Text Sizes
	|--------------------------------------------------------------------------
	|
	| Gutenberg text sizes
	|
	*/
	'text_sizes' => [
		'small'				=> 12,
		'medium' 			=> 20,
		'large'				=> 40,
		'section-heading'	=> 48
	],

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
			'name' => 'cropped_wide',
			'width' => 800,
			'height' => 400,
			'crop' => true,
		],

		// Force the default WordPress image size to hard crop
		// Set the media options from dashboard/settings/media
		// Use an appropriate ratio 16:9 or 4:3 for these image sizes
		// These are used for in the srcset of all images on the site

		[
			'name' => 'cropped_medium',
			'width' => get_option('medium_size_w'),
			'height' => get_option('medium_size_h'),
			'crop' => true,
		],

		[
			'name' => 'cropped_large',
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
	| and image size to use
	|
	*/
	'gallery_settings' => [
		'thumbnail_size' 	=> false, // Set to "false" to use gallery-specific settings. Defaults to "thumbnail".
		'lightbox_size' 	=> 'full',	// Must be specified.
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
		'primary' 		=> [
			'label' => 'Primary',
			'args'  => ['depth' => -1]
		],
		'secondary' 	=> [
			'label' => 'Secondary',
			'args'  => []
		],
		'quick_links' 	=> [
			'label' => 'Quick Links',
			'args'  => []
		],
		'footer' 		=> [
			'label' => 'Footer',
			'args'  => []
		],
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
		// homepage slider
		[
			'name' => 'owl',
			'src' => '/assets/js/owl.carousel.min.js',
			'deps' => ['jquery'],
			'in_footer' => true
		],
		// lightbox substitute
		[
			'name' => 'lightcase',
			'src' => '/assets/js/lightcase.js',
			'deps' => ['jquery'],
			'in_footer' => true
		],
		// main custom scripts
		[
			'name' => 'firefly',
			'src' => '/assets/js/firefly.js',
			'deps' => ['jquery', 'owl', 'lightcase'],
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
			'name' => 'google_fonts',
			'src' => 'http://fonts.googleapis.com/css?family=Roboto:700,400,300',
			'deps' => [],
			'version' => null // do this for Google fonts if loading multiple fonts
		],
		[
			'name' => 'fontawesome',
			'src' => '/assets/css/fontawesome-all.min.css',
			'deps' => []
		],
		[
			'name' => 'firefly',
			'src' => '/assets/css/firefly.css',
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
		'http://fonts.googleapis.com/css?family=Roboto:700,400,300',
		get_theme_file_uri('assets/css/firefly-editor.css'),
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
			// [ 'name' 	=> 'firefly-admin',
			// 'src' 	=> '/assets/css/admin.css',
			// 'deps' 	=> []
			// ],
		],
		'scripts' => [
			[
			  'name' 		=> 'editor-firefly',
			  'src'			=> '/assets/js/editor-firefly.js',
			  'deps'		=> [],
			  'in_footer'	=> true
			],
		]
	],

	/*
	|--------------------------------------------------------------------------
	| API Keys
	|--------------------------------------------------------------------------
	|
	| Set any API keys required by the theme here
	|
	*/
	'gmaps_api_key' => 'XXXX',
];