<?php // Navbar

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_navbar';
\Kirki::add_section( $section_id, [
	'title' => ff__( 'Navbar' ),
	'description' => ff__( 'Customize the elements of the newsletter navbar.' ),
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
	'label'       => ff__( 'Main Navigation' ),
	'section'     => $section_id,
	// 'default'     => '',
	'priority'    => 10,
]);

// Navbar
\Kirki::add_field( $this->config_id, [
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_navbar",
	'label'       	=> ff__( 'Navbar' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background' => '#007bff',
		'text'	=> '#FFFFFF',
		'hover'	=> '#333333',
	],
	'choices' => [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Links' ),
		'hover'			=> ff__( 'Link Hover' )
	],
	'output' => [
		[
			'choice' => 'background',
			'element' => ':root', 						
			'property' => '--navbar-background'
		],
		[
			'choice' => 'text',
			'element' => ':root', 						
			'property' => '--navbar-nav-link'
		],
		[
			'choice' => 'hover',
			'element' => ':root', 						
			'property' => '--navbar-nav-link-hover'
		],
	]
]);