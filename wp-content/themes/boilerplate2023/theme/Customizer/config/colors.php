<?php // Navbar

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'ff_colors';

new \Kirki\Section($section_id, [
	'title' => ff__('Colours'),
	'description' => ff__('Customize the theme colours.'),
	'priority' => 80,
	'panel'	=> $this->panel_id
]);

/****************************************************************
 * PALETTE
 ****************************************************************/

// Theme Palette
new \Kirki\Field\Multicolor(
	[
		'settings'   	=> "{$section_id}_palette",
		'label'       	=> ff__('Theme Colors'),
		'section'     	=> $section_id,
		'description' 	=> esc_html__( 'Enter your brand colours. These swatches will apply defaults to all the other colour pickers. You will need to publish and refresh the browser window before the swatches update in the other controls', 'kirki' ),
		'transport'		=> 'auto',
		'alpha'			=> true,
		'default'   	=> self::kirkiColorDefaults(),
		'choices' => [
			'primary'	=> ff__('Primary'),
			'secondary'	=> ff__('Secondary'),
			'tertiary'	=> ff__('Tertiary'),
			'success'	=> ff__('Success'),
			'info'		=> ff__('Info'),
			'warning'	=> ff__('Warning'),
			'danger'	=> ff__('Danger'),
			'custom1'	=> ff__('Custom 1'),
			'custom2'	=> ff__('Custom 2'),
			'custom3'	=> ff__('Custom 3'),
			'custom4'	=> ff__('Custom 4'),
			'custom5'	=> ff__('Custom 5')
		],
		'output' => [
			[
				'choice' => 'primary',
				'element' => ':root',
				'property' => '--bs-primary'
			],
			[
				'choice' => 'secondary',
				'element' => ':root',
				'property' => '--bs-secondary'
			],
			[
				'choice' => 'tertiary',
				'element' => ':root',
				'property' => '--bs-tertiary'
			],
			[
				'choice' => 'success',
				'element' => ':root',
				'property' => '--bs-success'
			],
			[
				'choice' => 'info',
				'element' => ':root',
				'property' => '--bs-info'
			],
			[
				'choice' => 'warning',
				'element' => ':root',
				'property' => '--bs-warning'
			],
			[
				'choice' => 'danger',
				'element' => ':root',
				'property' => '--bs-danger'
			],
			[
				'choice' => 'custom1',
				'element' => ':root',
				'property' => '--ff-custom1'
			],
			[
				'choice' => 'custom2',
				'element' => ':root',
				'property' => '--ff-custom2'
			],
			[
				'choice' => 'custom3',
				'element' => ':root',
				'property' => '--ff-custom3'
			],
			[
				'choice' => 'custom4',
				'element' => ':root',
				'property' => '--ff-custom4'
			],
			[
				'choice' => 'custom5',
				'element' => ':root',
				'property' => '--ff-custom5'
			],
			[
				'choice' => 'custom6',
				'element' => ':root',
				'property' => '--ff-custom6'
			],
			[
				'choice' => 'custom7',
				'element' => ':root',
				'property' => '--ff-custom7'
			],
			[
				'choice' => 'custom8',
				'element' => ':root',
				'property' => '--ff-custom8'
			],
			[
				'choice' => 'custom9',
				'element' => ':root',
				'property' => '--ff-custom9'
			],
			[
				'choice' => 'custom10',
				'element' => ':root',
				'property' => '--ff-custom10'
			],
		]
	]
);

/****************************************************************
 * BODY/BACKGROUND
 ****************************************************************/

 new \Kirki\Field\MultiColor(
	[
		'settings'     => "{$section_id}_page",
		'label'       => ff__( 'Page/Global'),
		'section'     	=> $section_id,
		'transport'		=> 'auto',
		'alpha'			=> true,
		'default'   	=> [
			'page_bg'	=> self::kirkiColorDefaults()['black'],
			'content_bg'	=> self::kirkiColorDefaults()['white'],
			'text'			=> self::kirkiColorDefaults()['black'],
			'link'		=> self::kirkiColorDefaults()['primary'],
			'hover'		=> self::kirkiColorDefaults()['secondary']
		],
		'choices'     => [
			'page_bg'	=> 'Page Background',
			'content_bg'	=> 'Content Background',
			'text'	=> 'Body Text',
			'link'	=> 'Links',
			'hover'	=> 'Link Hover',
		],
		'output' => [
			[
				'choice' => 'page_bg',
				'element' => ':root',
				'property' => '--ff-page-bg'
			],
			[
				'choice' => 'content_bg',
				'element' => ':root',
				'property' => '--ff-page-content-bg'
			],
			[
				'choice' => 'text',
				'element' => ':root',
				'property' => '--bs-body-color'
			],
			[
				'choice' => 'link',
				'element' => ':root',
				'property' => '--bs-link-color'
			],
			[
				'choice' => 'hover',
				'element' => ':root',
				'property' => '--bs-link-hover-color'
			]
		]
	]
);


/****************************************************************
 * SECONDARY NAV
 ****************************************************************/

new \Kirki\Field\MultiColor(
	[
		'settings'     => "{$section_id}_secondary_nav",
		'label'       => ff__( 'Secondary Navigation'),
		'section'     	=> $section_id,
		'transport'		=> 'auto',
		'alpha'			=> true,
		'default'   	=> [
			'bg'	=> self::kirkiColorDefaults()['primary'],
			'link'	=> self::kirkiColorDefaults()['white'],
			'hover'	=> self::kirkiColorDefaults()['secondary'],
			'cta'	=> self::kirkiColorDefaults()['secondary'],
		],
		'choices'     => [
			'bg'	=> 'Background',
			'link'	=> 'Links',
			'hover'	=> 'Link Hover',
			'cta'	=> 'Call To Action',
		],
		'output' => [
			[
				'choice' => 'bg',
				'element' => ':root',
				'property' => '--ff-secondary-nav-background'
			],
			[
				'choice' => 'link',
				'element' => ':root',
				'property' => '--ff-secondary-nav-link'
			],
			[
				'choice' => 'hover',
				'element' => ':root',
				'property' => '--ff-secondary-nav-link-hover'
			],
			[
				'choice' => 'cta',
				'element' => ':root',
				'property' => '--ff-secondary-nav-cta'
			],
		]
	]
);

/****************************************************************
 * PRIMARY NAV
 ****************************************************************/

// Navbar
new \Kirki\Field\MultiColor([
	'settings'   	=> "{$section_id}_primary_nav",
	'label'       	=> ff__( 'Primary Navigation' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> true,
	'default'		=> [
		'bg' => self::kirkiColorDefaults()['white'],
		'link'		=> self::kirkiColorDefaults()['black'],
		'hover'		=> self::kirkiColorDefaults()['primary'],
	],
	'choices' => [
		'bg'		=> ff__( 'Background' ),
		'link'		=> ff__( 'Links' ),
		'hover'		=> ff__( 'Link Hover' )
	],
	'output' => [
		[
			'choice' => 'bg',
			'element' => ':root',
			'property' => '--ff-navbar-background'
		],
		[
			'choice' => 'link',
			'element' => ':root',
			'property' => '--ff-nav-link'
		],
		[
			'choice' => 'hover',
			'element' => ':root',
			'property' => '--ff-nav-link-hover'
		],
	]
]);

// Submenu
new \Kirki\Field\MultiColor([
	'settings'   	=> "{$section_id}_primary_submenu",
	'label'       	=> ff__( 'Main Navigation' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> true,
	'default'		=> [
		'bg' 		=> self::kirkiColorDefaults()['white'],
		'link'		=> self::kirkiColorDefaults()['black'],
		'hover'		=> self::kirkiColorDefaults()['primary'],
	],
	'choices' => [
		'bg'		=> ff__( 'Background' ),
		'link'		=> ff__( 'Links' ),
		'hover'		=> ff__( 'Link Hover' )
	],
	'output' => [
		[
			'choice' => 'bg',
			'element' => ':root',
			'property' => '--ff-nav-submenu-background'
		],
		[
			'choice' => 'link',
			'element' => ':root',
			'property' => '--ff-nav-submenu-link'
		],
		[
			'choice' => 'hover',
			'element' => ':root',
			'property' => '--ff-nav-submenu-link-hover'
		],
	]
]);

/****************************************************************
 * FOOTER
 ****************************************************************/

 // Footer
 new \Kirki\Field\MultiColor([
	'settings'   	=> "{$section_id}_footer",
	'label'       	=> ff__( 'Footer' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> true,
	'default'		=> [
		'bg' 		=> self::kirkiColorDefaults()['black'],
		'text'		=> self::kirkiColorDefaults()['gray-500'],
		'heading'	=> self::kirkiColorDefaults()['white'],
		'link'		=> self::kirkiColorDefaults()['gray-500'],
		'hover'		=> self::kirkiColorDefaults()['white'],
	],
	'choices' => [
		'bg'		=> ff__( 'Background' ),
		'heading'	=> ff__( 'Headings' ),
		'text'		=> ff__( 'Text' ),
		'link'		=> ff__( 'Links' ),
		'hover'		=> ff__( 'Link Hover' )
	],
	'output' => [
		[
			'choice' => 'bg',
			'element' => ':root',
			'property' => '--ff-footer-bg'
		],
		[
			'choice' => 'text',
			'element' => ':root',
			'property' => '--ff-footer-color'
		],
		[
			'choice' => 'heading',
			'element' => ':root',
			'property' => '--ff-footer-widget-heading'
		],
		[
			'choice' => 'link',
			'element' => ':root',
			'property' => '--ff-footer-link'
		],
		[
			'choice' => 'hover',
			'element' => ':root',
			'property' => '--ff-footer-link-hover'
		],
	]
]);

// Colophon
new \Kirki\Field\MultiColor([
	'settings'   	=> "{$section_id}_colophon",
	'label'       	=> ff__( 'Colophon' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> true,
	'default'		=> [
		'bg' 		=> self::kirkiColorDefaults()['black'],
		'text'		=> self::kirkiColorDefaults()['gray-600'],
		'link'		=> self::kirkiColorDefaults()['gray-600'],
		'hover'		=> self::kirkiColorDefaults()['white'],
	],
	'choices' => [
		'bg'		=> ff__( 'Background' ),
		'text'		=> ff__( 'Text' ),
		'link'		=> ff__( 'Links' ),
		'hover'		=> ff__( 'Link Hover' )
	],
	'output' => [
		[
			'choice' => 'bg',
			'element' => ':root',
			'property' => '--ff-colophon-bg'
		],
		[
			'choice' => 'text',
			'element' => ':root',
			'property' => '--ff-colophon-color'
		],
		[
			'choice' => 'link',
			'element' => ':root',
			'property' => '--ff-colophon-link'
		],
		[
			'choice' => 'hover',
			'element' => ':root',
			'property' => '--ff-colophon-link-hover'
		],
	]
]);