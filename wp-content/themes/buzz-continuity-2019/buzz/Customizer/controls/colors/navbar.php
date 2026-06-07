<?php
$group_id = 'navbar';

// Group Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_${group_id}_info",
	'label'       => ff__( 'Main Navigation' ),
	'section'     => $section_id,
	// 'default'     => '',
	'priority'    => 10,
) );

// Navbar
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_menu",
	'label'       	=> ff__( 'Navbar' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#000000',
		'text'			=> '#FFFFFF',
		'hover'			=> '#333333',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Links' ),
		'hover'			=> ff__( 'Link Hover' )
	],
	'output'		=> [
		[ 'choice' => 'background', 'element' => '#primary-navigation', 						'property' => 'background-color' ],
		[ 'choice' => 'background', 'element' => '#primary-navigation .dropdown-menu', 			'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '#primary-navigation', 						'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#primary-navigation .nav-link', 				'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#primary-navigation .current-menu-item .nav-link', 	'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#primary-navigation .navbar-toggler-icon', 	'property' => 'color' ],
		[ 'choice' => 'hover', 		'element' => '#primary-navigation .nav-link:hover', 		'property' => 'background-color' ],

		// Email View
		[ 'choice' => 'background', 'element' => '#email-view #navbar', 						'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #navbar', 						'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #navbar .newsletter-info', 		'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '#email-view #navbar A', 						'property' => 'color' ],
	]
) );

// Navbar
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_handsetmenu",
	'label'       	=> ff__( 'Handset Menu' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#000000',
		'text'			=> '#FFFFFF',
		'close_icon'	=> '#FFFFFF',
		'close_bg'		=> '#000000',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Links' ),
		'close_icon'	=> ff__( 'Close Icon' ),
		'close_bg'		=> ff__( 'Close Icon Background' ),
	],
	'output'		=> [

		[ 'choice' => 'background', 'element' => '.offcanvas',			 						'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '.offcanvas__navigation .menu-item A',			'property' => 'color' ],
		[ 'choice' => 'text', 		'element' => '.offcanvas .navbar-social .nav-link',			'property' => 'color' ],
		[ 'choice' => 'close_icon', 'element' => '.offcanvas .close',							'property' => 'color' ],
		[ 'choice' => 'close_bg', 	'element' => '.offcanvas .close',							'property' => 'background-color' ],

	]
) );
