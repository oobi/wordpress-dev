<?php


/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'ff_fonts';

new \Kirki\Section($section_id, [
	'title' => ff__('Fonts'),
	'description' => ff__('Customize the theme fonts.'),
	'priority' => 80,
	'panel'	=> $this->panel_id
]);

/****************************************************************
 * FONT URLS
 ****************************************************************/

new \Kirki\Field\Repeater([
	'settings' 		=> "{$section_id}_urls",
	'label'      	=> ff__( 'External Font URLs'),
	'section'    	=> $section_id,
	'priority'   	=> 5,
	'row_label'    => [
		'type'  => 'field',
		'value' => esc_html__( 'New font CSS', 'kirki' ),
		'field' => 'name',
	],
	'fields'		=> [
		'name' => [
			'type'        => 'text',
			'label'       => ff__( 'Font Slug'),
			'description' => ff__( 'Stylesheet identifier - unique, lowercase, no spaces. Use  "google_fonts" to override default Google Fonts in this theme.'),
			'default'     => 'google_fonts',
		],
		'src' => [
			'type'        => 'text',
			'label'       => ff__( 'Font CSS URL'),
			'description' => ff__( 'Enter the URL of the font css.'),
			'default'     => '',
		],
	],
]);

/****************************************************************
 * FONT DEFINITIONS
 ****************************************************************/

self::title($section_id, 'Font Definitions');

new \Kirki\Field\Text([
	'settings' 		=> "{$section_id}_body",
	'label'      	=> ff__( 'Body Font'),
	'section'    	=> $section_id,
	'priority'   	=> 10,
	'transport'		=> 'auto',
	'description'	=> ff__('Enter the font-family string for the body text.'),
	'default'		=> "'Open Sans', sans-serif",
	'output' => [
		[
			'element' => ':root',
			'property' => '--bs-body-font-family'
		],
	]
]);

new \Kirki\Field\Text([
	'settings' 		=> "{$section_id}_heading",
	'label'      	=> ff__( 'Heading Font'),
	'section'    	=> $section_id,
	'priority'   	=> 10,
	'transport'		=> 'auto',
	'description'	=> ff__('Enter the font-family string for headings.'),
	'default'		=> "'Open Sans', sans-serif",
	'output' => [
		[
			'element' => ':root',
			'property' => '--ff-headings-font-family'
		],
	]
]);

new \Kirki\Field\Text([
	'settings' 		=> "{$section_id}_serif",
	'label'      	=> ff__( 'Serif Font'),
	'section'    	=> $section_id,
	'priority'   	=> 10,
	'transport'		=> 'auto',
	'description'	=> ff__('Enter the font-family string for serif items.'),
	'default'		=> "'Domine', serif",
	'output' => [
		[
			'element' => ':root',
			'property' => '--ff-serif-font-family'
		],
	]
]);