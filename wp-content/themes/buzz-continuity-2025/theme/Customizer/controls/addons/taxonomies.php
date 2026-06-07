<?php // Footer

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_taxonomies';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Taxonomies' ),
	'description' 	=> ff__( 'Customize the newsletter categories and tags.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );

/****************************************************************
 * CLASSES
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_1",
	'label'       => ff__( 'Tags' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Tag type
\Kirki::add_field( $this->config_id, array(
	'type'      => 'radio-buttonset',
	'settings'  => "{$section_id}_tag_type",
	'label'     => ff__( 'Tag type' ),
	'section'   => $section_id,
	'default'	=> 'text',
	'choices'   => [
		'text'		=> 'Text',
		'button'	=> 'Button',
	],
) );