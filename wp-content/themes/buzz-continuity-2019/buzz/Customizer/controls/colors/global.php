<?php
$group_id = 'global';

// Group Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_${group_id}_info",
	'label'       => ff__( 'Global' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Background
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'background',
	'settings'   	=> "${section_id}_${group_id}_bg",
	'label'       	=> ff__( 'Background' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background-color'     	=> '#CCCCCC',
		'background-image'      => '',
		'background-repeat'     => 'repeat',
		'background-position'   => 'center center',
		'background-size'       => 'cover',
		'background-attachment' => 'scroll',
	],
	'output'		=> [
		[ 'element' => 'body', 			'property' => 'background' ], // does all styling in one
		// Email View
		[ 'element' => '#email-view', 	'property' => 'background' ], // Email View CSS will strip all except background-color
	]
) );

// Search
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_search",
	'label'       	=> ff__( 'Search' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	 => '#333333',
		'text'			 => '#FFFFFF',
		'btn-background' => '#333333',
		'btn-text'		 => '#FFFFFF',
		'btn-hover'	 	 => '#666666',
		'btn-hover-txt'	 => '#FFFFFF',
	],
	'choices'		=> [
		'background'		=> ff__( 'Input Background' ),
		'text'				=> ff__( 'Input Text' ),
		'btn-background'	=> ff__( 'Button Background' ),
		'btn-text'			=> ff__( 'Button Text' ),
		'btn-hover'			=> ff__( 'Button Background Hover' ),
		'btn-hover-txt'		=> ff__( 'Button Text Hover' ),
	],
	'output'		=> [
		// input
		[ 'choice' => 'background', 'element' => '.search-form INPUT', 				'property' => 'background-color' ],
		[ 'choice' => 'background',	'element' => '.search-form INPUT:focus', 		'property' => 'background-color' ],
		[ 'choice' => 'text',		'element' => '.search-form INPUT', 				'property' => 'color' ],
		[ 'choice' => 'text',		'element' => '.search-form INPUT::placeholder', 'property' => 'color' ],
		[ 'choice' => 'text',		'element' => '.search-form INPUT:focus', 		'property' => 'color' ],
		// button
		[ 'choice' => 'btn-background', 'element' => '.search-form BUTTON', 		'property' => 'background-color' ],
		[ 'choice' => 'btn-text', 		'element' => '.search-form BUTTON', 		'property' => 'color' ],
		[ 'choice' => 'btn-hover', 		'element' => '.search-form BUTTON:hover', 	'property' => 'background-color' ],
		[ 'choice' => 'btn-hover-txt', 	'element' => '.search-form BUTTON:hover', 	'property' => 'color' ],
	]
) );

// Buttons
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_buttons",
	'label'       	=> ff__( 'Buttons' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	 => '#333333',
		'text'			 => '#FFFFFF',
	],
	'choices'		=> [
		'background'		=> ff__( 'Button Background' ),
		'text'				=> ff__( 'Button Text' ),
	],
	'output'		=> [
		[ 'choice' => 'background', 'element' => 'BUTTON', 		'property' => 'background-color' ],
		[ 'choice' => 'background', 'element' => 'A.btn', 		'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => 'BUTTON', 		'property' => 'color' ],
		[ 'choice' => 'text',		'element' => 'A.btn', 		'property' => 'color' ],

		// email view
		[ 'choice' => 'background', 'element' => '#email-view .btn', 	'property' => 'background-color' ],
		[ 'choice' => 'text',		'element' => '#email-view .btn', 	'property' => 'color' ],
		[ 'choice' => 'text',		'element' => '#email-view .btn A', 	'property' => 'color' ],
	]
) );
