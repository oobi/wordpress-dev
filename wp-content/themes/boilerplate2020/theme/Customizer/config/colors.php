<?php // Navbar

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'firefly_colors';
\Kirki::add_section( $section_id, [
	'title' => ff__( 'Colors' ),
	'description' => ff__( 'Customize the theme colors.' ),
	'priority' => 80,
	'panel'	=> $this->panel_id
]);

/****************************************************************
 * FIELDS
 ****************************************************************/

$group_id = 'navbar';

// Group Label
\Kirki::add_field( $this->config_id, [
	'type'        => 'custom',
	'settings'    => "${section_id}_${group_id}_info",
	'label'       => ff__( 'Theme Colors' ),
	'section'     => $section_id,
	// 'default'     => '',
	'priority'    => 10,
]);

// Navbar
\Kirki::add_field( $this->config_id, [
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_theme_colors",
	'label'       	=> ff__( 'Theme Colors' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'primary' => '#007bff',
		'secondary' => '#6f42c1',
		'success' => '#28a745',
		'info' => '#17a2b8',
		'warning' => '#ffc107',
		'danger' => '#dc3545',
	],
	'choices' => [
		'primary'	=> ff__( 'Primary' ),
		'secondary'	=> ff__( 'Secondary' ),
		'success'	=> ff__( 'Success' ),
		'info'		=> ff__( 'Info' ),
		'warning'	=> ff__( 'Warning' ),
		'danger'	=> ff__( 'Danger' ),
	],
	'output' => [
		[
			'choice' => 'primary',
			'element' => ':root', 						
			'property' => '--primary'
		],
		[
			'choice' => 'secondary',
			'element' => ':root', 						
			'property' => '--secondary'
		],
		[
			'choice' => 'success',
			'element' => ':root', 						
			'property' => '--success'
		],
		[
			'choice' => 'info',
			'element' => ':root', 						
			'property' => '--info'
		],
		[
			'choice' => 'warning',
			'element' => ':root', 						
			'property' => '--warning'
		],
		[
			'choice' => 'danger',
			'element' => ':root', 						
			'property' => '--danger'
		],
	]
]);