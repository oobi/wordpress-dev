<?php
$group_id = 'footer';

// Group Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_${group_id}_info",
	'label'       => ff__( 'Footer' ),
	'section'     => $section_id,
	// 'default'     => '',
	'priority'    => 10,
) );

// Widgets
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_widgets",
	'label'       	=> ff__( 'Widgets Row' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#666666',
		'text'			=> '#FFFFFF',
		'headings'		=> '#FFFFFF',
		'links'			=> '#999999',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'headings'		=> ff__( 'Headings' ),
		'text'			=> ff__( 'Text' ),
		'links'			=> ff__( 'Links' ),
	],
	'output'		=> [
		[ 'choice' => 'background', 'element' => '#footer .widgets', 				'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '#footer .widgets', 				'property' => 'color' ],
		[ 'choice' => 'headings', 	'element' => '#footer .widgets .widget-title', 	'property' => 'color' ],
		[ 'choice' => 'links', 		'element' => '#footer .widgets A', 				'property' => 'color' ],
	]
) );

// Menu Row
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_menu",
	'label'       	=> ff__( 'Menu Row' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#333333',
		'text'			=> '#FFFFFF',
		'links'			=> '#333333',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Text' ),
		'links'			=> ff__( 'Links' )
	],
	'output'		=> [
		[ 'choice' => 'background', 'element' => '#footer .colophon', 			'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '#footer .colophon', 			'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#footer .colophon .credit A', 'property' => 'color' ],
		[ 'choice' => 'links', 		'element' => '#footer .colophon A', 		'property' => 'color' ],

		// Email View
		[ 'choice' => 'background', 'element' => '#email-view #email-footer', 				'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #email-footer TABLE', 		'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #email-footer TABLE A', 		'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #email-footer .unsubscribe',	'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #email-footer .unsubscribe A','property' => 'color' ],
	]
) );
