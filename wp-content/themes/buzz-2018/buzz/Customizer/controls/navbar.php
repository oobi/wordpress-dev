<?php // Navbar

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_navbar';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Navbar' ),
	'description' 	=> ff__( 'Customize the elements of the newsletter navbar.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );

/****************************************************************
 * FIELDS
 ****************************************************************/

// show issue title
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_title",
	'label'     => ff__( 'Show issue title' ),
	'section'   => $section_id,
	'default'	=> true
) );

// show issue date
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_date",
	'label'     => ff__( 'Show issue date' ),
	'section'   => $section_id,
	'default'	=> true
) );

// show issue date
\Kirki::add_field( $this->config_id, array(
	'type'      => 'radio-buttonset',
	'settings'  => "{$section_id}_date_position",
	'label'     => ff__( 'Date position' ),
	'section'   => $section_id,
	'default'	=> 'left',
	'choices'   => [
		'left'		=> 'Left',
		'right'		=> 'Right',
	],
	'required'	=> [
		[
			'setting'	=> "{$section_id}_date",
			'operator'	=> '=',
			'value'		=> true,
		]
	]
) );

// date format
\Kirki::add_field( $this->config_id, array(
	'type'      	=> 'text',
	'settings'  	=> "{$section_id}_date_format",
	'label'    		=> ff__( 'Date format' ),
	'description' 	=> ff__( 'This field uses <a href="http://php.net/manual/en/function.strftime.php" target="_blank">PHP "strftime" format strings and limited html</a>' ),
	'section'   	=> $section_id,
	'default'		=> '%e %B',
	'sanitize_callback' => array($this, 'sanitize_basic_html'),
	'required' 		=> [
		[
			'setting'	=> "{$section_id}_date",
			'operator'	=> '=',
			'value'		=> true,
		]
	]
) );

// show search
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_search",
	'label'     => ff__( 'Show search' ),
	'section'   => $section_id,
	'default'	=> true
) );