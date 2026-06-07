<?php // Footer

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_footer';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Footer' ),
	'description' 	=> ff__( 'Customize the newsletter footer.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );

\Kirki::add_field( $this->config_id, array(
	'type'      => 'text',
	'settings'  => "${section_id}_copyright",
	'label'     => ff__( 'Copyright Text' ),
	'section'   => $section_id,
	'default'	=> get_bloginfo( 'name' ),
) );


/****************************************************************
 * WRAPPER
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_1",
	'label'       => ff__( 'Wrapper' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Classes
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "${section_id}_class",
	'label'       => ff__( 'HTML Classes' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'     => $section_id,
) );

/****************************************************************
 * WIDGET AREA
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_2",
	'label'       => ff__( 'Widget Area' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Classes
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "${section_id}_widget_class",
	'label'       => ff__( 'HTML Classes' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'     => $section_id,
	'default'	  => '',
) );

/****************************************************************
 * COLOPHON AREA
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_3",
	'label'       => ff__( 'Colophon Area' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Classes
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "${section_id}_colophon_class",
	'label'       => ff__( 'HTML Classes' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'     => $section_id,
	'default'	  => ''
) );