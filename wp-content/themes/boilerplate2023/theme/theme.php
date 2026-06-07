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
	'theme_name' => 'Firefly Spark 2023',

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
	*/
	'theme_colors' => [
		"primary"   => "#005497",  // blue
		"secondary" => "#f3a519",  // yellow
		"tertiary"  => "#D8E6F0",  // light blue

		// black, white, grey
		'black' 	=> "#000000",
		"white" 	=> "#FFFFFF",
		"gray-100"	=> "#f8f9fa",
		"gray-300"	=> "#dee2e6",
		"gray-500"	=> "#999999",
		"gray-600"	=> "#6c757d",
		"gray-700"	=> "#495057",
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
		'medium' 			=> 16,
		'default' 			=> 18,
		'large'				=> 26,
		'x-large'			=> 40,
		'xx-large'			=> 60
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
			'width' => 1024,
			'height' => 576,
			'crop' => true,
		],

		[
			'name' => 'cropped_square',
			'width' => 1024,
			'height' => 1024,
			'crop' => true,
		],
		[
			'name' => 'cropped_square_thumbnail',
			'width' => 512,
			'height' => 512,
			'crop' => true,
		],

		[
			'name' => 'cropped_4x3',
			'width' => 1024,
			'height' => 768,
			'crop' => true,
		],

		[
			'name' => 'cropped_portrait_3x4',
			'width' => 768,
			'height' => 1024,
			'crop' => true,
		],

		[
			'name' => 'cropped_hero',
			'width' => 2000,
			'height' => 770,
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
			'args'  => ['depth' => 1]
		],
		'quick_links' 	=> [
			'label' => 'Quick Links',
			'args'  => ['depth' => 1]
		],
		'footer' 		=> [
			'label' => 'Footer',
			'args'  => ['depth' => 1]
		],
		'footer_cta' 		=> [
			'label' => 'Footer Action Buttons',
			'args'  => ['depth' => 1]
		],
		'header_cta' 		=> [
			'label' => 'Header Action Buttons',
			'args'  => ['depth' => 1]
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
		// slider
		[
			'name' => 'owl',
			'src' => '/assets/js/owl.carousel.min.js',
			'deps' => ['jquery'],
			'in_footer' => true
		],
		// main custom scripts
		[
			'name' => 'firefly',
			'src' => '/assets/js/firefly.js',
			'deps' => ['jquery', 'owl'],
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
			'src' => 'https://fonts.googleapis.com/css2?family=Domine:wght@400;600&family=Open+Sans:wght@400;500;700&display=swap',
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
	| An array of styles required by Gutenberg Editor
	|
	*/
	'editor' => [
		'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Source+Serif+Pro:wght@300;400;600&display=swap',

		get_theme_file_uri('assets/css/fontawesome-all.min.css'),
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
];