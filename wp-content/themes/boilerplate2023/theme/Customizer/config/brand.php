<?php // Images

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'ff_brand';

new \Kirki\Section($section_id, [
	'title' => ff__('Brand Logos'),
	'description' => ff__('Customize the logos used in the theme.'),
	'priority' => 80,
	'panel'	=> $this->panel_id
]);

/****************************************************************
 * LARGE LOGO
 ****************************************************************/

 self::title($section_id, 'Desktop Logo');

// Large logo
new \Kirki\Field\Image(
	[
		'settings'    => $section_id . '_header_logo_lg',
		'label'       => ff__('Header Logo (large)'),
		'section'     => $section_id,
		'default'     => get_template_directory_uri() . '/assets/images/logo-color-stacked.svg',
		'priority'    => 10,
		'output' => [
			[
				'choice' => 'primary',
				'element' => ':root',
				'property' => '--ff-header-logo-lg',
				'value_pattern' => 'url($)',
			]
		]
	]
);

new \Kirki\Field\Slider(
	[
		'settings'    => $section_id . '_header_logo_lg_width',
		'label'       => ff__('Header Logo (large) width'),
		'section'     => $section_id,
		'default'     => 96,
		'transport'		=> 'auto',
		'choices'     => [
			'min'  => 0,
			'max'  => 200,
			'step' => 1,
		],
		'output' => [
			[
				'element' => ':root',
				'property' => '--ff-header-logo-width-lg',
				'units' => 'px'
			]
		]
	]
);

new \Kirki\Field\Slider(
	[
		'settings'    => $section_id . '_header_logo_lg_height',
		'label'       => ff__('Header Logo (large) height'),
		'section'     => $section_id,
		'default'     => 128,
		'transport'		=> 'auto',
		'choices'     => [
			'min'  => 0,
			'max'  => 200,
			'step' => 1,
		],
		'output' => [
			[
				'element' => ':root',
				'property' => '--ff-header-logo-height-lg',
				'units' => 'px'
			]
		]
	]
);


/****************************************************************
 * MOBILE LOGO
 ****************************************************************/

 self::title($section_id, 'Mobile Logo');

// Mobile logo
new \Kirki\Field\Image(
	[
		'settings'    => $section_id . '_header_logo_sm',
		'label'       => ff__('Header Logo (mobile)'),
		'section'     => $section_id,
		'default'     => get_template_directory_uri() . '/assets/images/logo-color.svg',
		'priority'    => 10,
		'output' => [
			[
				'choice' => 'primary',
				'element' => ':root',
				'property' => '--ff-header-logo',
				'value_pattern' => 'url($)',
			]
		]
	]
);

new \Kirki\Field\Slider(
	[
		'settings'    => $section_id . '_header_logo_sm_width',
		'label'       => ff__('Header Logo (mobile) width'),
		'section'     => $section_id,
		'default'     => 160,
		'transport'		=> 'auto',
		'choices'     => [
			'min'  => 0,
			'max'  => 200,
			'step' => 1,
		],
		'output' => [
			[
				'element' => ':root',
				'property' => '--ff-header-logo-width',
				'units' => 'px'
			]
		]
	]
);

new \Kirki\Field\Slider(
	[
		'settings'    => $section_id . '_header_logo_sm_height',
		'label'       => ff__('Header Logo (mobile) height'),
		'section'     => $section_id,
		'default'     => 64,
		'transport'		=> 'auto',
		'choices'     => [
			'min'  => 0,
			'max'  => 200,
			'step' => 1,
		],
		'output' => [
			[
				'element' => ':root',
				'property' => '--ff-header-logo-height',
				'units' => 'px'
			]
		]
	]
);


/****************************************************************
 * HEADROOM LOGO
 ****************************************************************/

self::title($section_id, 'Headroom Logo');

// Mobile logo
new \Kirki\Field\Image(
	[
		'settings'    => $section_id . '_header_logo_headroom',
		'label'       => ff__('Header Logo (headroom)'),
		'section'     => $section_id,
		'default'     => get_template_directory_uri() . '/assets/images/logo-color.svg',
		'priority'    => 10,
		'output' => [
			[
				'choice' => 'primary',
				'element' => ':root',
				'property' => '--ff-header-logo-headroom',
				'value_pattern' => 'url($)',
			]
		]
	]
);

new \Kirki\Field\Slider(
	[
		'settings'    => $section_id . '_header_logo_headroom_width',
		'label'       => ff__('Header Logo (headroom) width'),
		'section'     => $section_id,
		'default'     => 128,
		'transport'		=> 'auto',
		'choices'     => [
			'min'  => 0,
			'max'  => 200,
			'step' => 1,
		],
		'output' => [
			[
				'element' => ':root',
				'property' => '--ff-header-logo-width-headroom',
				'units' => 'px'
			]
		]
	]
);

new \Kirki\Field\Slider(
	[
		'settings'    => $section_id . '_header_logo_headroom_height',
		'label'       => ff__('Header Logo (headroom) height'),
		'section'     => $section_id,
		'default'     => 64,
		'transport'		=> 'auto',
		'choices'     => [
			'min'  => 0,
			'max'  => 200,
			'step' => 1,
		],
		'output' => [
			[
				'element' => ':root',
				'property' => '--ff-header-logo-height-headroom',
				'units' => 'px'
			]
		]
	]
);


/****************************************************************
 * FOOTER LOGO
 ****************************************************************/

self::title($section_id, 'Footer Logo');

new \Kirki\Field\Image([
	'settings'    => $section_id . '_footer_logo',
	'label'       => ff__('Footer Logo'),
	'section'     => $section_id,
	'default'     => get_template_directory_uri() . '/assets/images/logo-white.svg',
	'priority'    => 10,
	'choices'   => [
		'save_as' => 'id',
	],
]);

new \Kirki\Field\Slider(
	[
		'settings'    => $section_id . '_footer_logo_width',
		'label'       => ff__('Footer Logo width'),
		'section'     => $section_id,
		'default'     => 225,
		'transport'		=> 'auto',
		'choices'     => [
			'min'  => 0,
			'max'  => 300,
			'step' => 1,
		],
		'output' => [
			[
				'element' => ':root',
				'property' => '--ff-footer-logo-width',
				'units' => 'px'
			]
		]
	]
);

new \Kirki\Field\Slider(
	[
		'settings'    => $section_id . '_footer_logo_height',
		'label'       => ff__('Footer Logo height'),
		'section'     => $section_id,
		'default'     => 90,
		'transport'		=> 'auto',
		'choices'     => [
			'min'  => 0,
			'max'  => 300,
			'step' => 1,
		],
		'output' => [
			[
				'element' => ':root',
				'property' => '--ff-footer-logo-height',
				'units' => 'px'
			]
		]
	]
);