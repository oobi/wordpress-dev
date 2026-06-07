<?php

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_social';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Social Media' ),
	'description' 	=> ff__( 'Customize the social media icons and share buttons.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );


/****************************************************************
 * FIELDS
 ****************************************************************/


// \Kirki::add_field( $this->config_id, array(
// 	'type'     		=> 'fontawesome',
// 	'settings'  	=> "${section_id}_fa",
// 	'label'     	=> ff__( '' ),
// 	'section'		=> $section_id,
// ) );

\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'repeater',
	'settings'  	=> "${section_id}_feeds",
	'label'     	=> ff__( 'Social Icons' ),
	'description'  	=> ff__( 'Social Icons are used to link to <strong>your organisation\'s</strong> social media pages.' ),
	'section'		=> $section_id,
	'row_label'		=> [
		'type'			=> 'text',
		'value'			=> ff__( 'Social Icon' ),
	],
	'default'		=> [
		[
			'icon'	=> 'facebook',
			'url' 	=> '',
		],
		[
			'icon'	=> 'twitter',
			'url' 	=> '',
		],
		[
			'icon'	=> 'instagram',
			'url' 	=> '',
		],
		[
			'icon'	=> 'youtube',
			'url' 	=> '',
		],
	],
	'fields'		=> [
		// TODO: Icon "text" field to be replaced with a "fontawesome" field when Kirki v3.1 is released - currently does not work.
		'icon' => [
			'type'			=> 'text',
			'label' 		=> ff__( 'Icon' ),
			'description'	=> ff__( 'FontAwesome Icon String. <a href="https://fontawesome.com/icons" target="_blank">Reference here.</a>' ),
		],
		'url' => [
			'type'		=> 'link',
			'label' 	=> ff__( 'URL' ),
		],
	]
) );

\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'multicheck',
	'settings'  	=> "${section_id}_sharing",
	'label'     	=> ff__( 'Share buttons' ),
	'description'  	=> ff__( 'Share Buttons are used by the user to share articles on <strong>their feed</strong>' ),
	'section'		=> $section_id,
	'default'		=> [ 'facebook', 'twitter' ],
	'choices'		=> [
		'facebook' 		=> ff__( 'Facebook' ),
		'twitter' 		=> ff__( 'Twitter' ),
		'linkedin' 		=> ff__( 'LinkedIn' ),
	],
) );