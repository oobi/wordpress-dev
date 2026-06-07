<?php
$group_id = 'header';

// Group Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_${group_id}_info",
	'label'       => ff__( 'Header' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Header Colours
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}",
	'label'       	=> ff__( 'Header' ),
	'description'  	=> ff__( 'Colours only visible on Text/Logo Header type' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#333333',
		'text'			=> '#FFFFFF',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Text' ),
	],
	'output'		=> [
		[ 'choice' => 'background', 'element' => '.site-header', 			'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '.site-header .homelink',	'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '.site-header .widget',	'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '.site-header .widget A',	'property' => 'color' ],

		// Email View
		[ 'choice' => 'background', 'element' => '#email-view #header', 			'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #header',				'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #header .title',		'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #header .subtitle',	'property' => 'color' ],
	],
) );
