<?php // Theme colours

// define transport type for colours and link underline
// 'refresh' for debugging
// $transport = 'postMessage';

/****************************************************************
 * PANEL
 ****************************************************************/

$section_id = 'buzz_colors';
\Kirki::add_section($section_id, array(
	'title' 		=> ff__('Theme Colours'),
	'description' 	=> ff__('Set the theme palette for the content editor'),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
));

Kirki::add_field('theme_config_id', [
	'type'        => 'multicolor',
	'settings'    => "{$section_id}_theme_colors",
	'label'       => ff__('Theme Colours'),
	'section'     => $section_id,
	'priority'    => 10,
	'choices'     => [
		'color1' => 'Color 1',
		'color2' => 'Color 2',
		'color3' => 'Color 3',
		'color4' => 'Color 4',
		'color5' => 'Color 5',
		'color6' => 'Color 6',
		'color7' => 'Color 7',
		'color8' => 'Color 8',
		'color9' => 'Color 9',
		'color10' => 'Color 10',
		'color11' => 'Color 11',
		'color12' => 'Color 12',
	],
	'default'     => [
		'color1' => '#000000',
		'color2' => '#007bff',
		'color3' => '#6610f2',
		'color4' => '#6f42c1',
		'color5' => '#e83e8c',
		'color6' => '#dc3545',
		'color7' => '#fd7e14',
		'color8' => '#ffc107',
		'color9' => '#28a745',
		'color10' => '#20c997',
		'color11' => '#17a2b8',
		'color12' => '#FFFFFF',
	],
]);