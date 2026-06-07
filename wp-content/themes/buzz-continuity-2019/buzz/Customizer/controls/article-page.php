<?php // Email View

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_article_page';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Article Page' ),
	'description' 	=> ff__( 'Customize the elements of the individual article pages.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );

/****************************************************************
 * HERO IMAGE
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_0",
	'label'       => ff__( 'Hero Image' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "${section_id}_hero_show",
	'label'     => ff__( 'Show Hero Image' ),
	'section'   => $section_id,
	'default'	=> true
) );

 \Kirki::add_field( $this->config_id, array(
	 'type'     	=> 'radio-buttonset',
	 'settings'  	=> "${section_id}_hero_position",
	 'label'     	=> ff__( 'Hero Image Position' ),
	 'section'		=> $section_id,
	 'default'		=> 'content',
	 'choices'		=> [
		 'above'		=> ff__( 'Above Navbar' ),
		 'below'		=> ff__( 'Below Navbar' ),
		 'content'		=> ff__( 'Above Content' ),
	 ],
	 'required' 	=> [
		[
			'setting'	=> "${section_id}_hero_show",
			'value'		=> true,
			'operator'	=> '=',
		]
	]
 ) );

/****************************************************************
 * CONTENT AREA
 ****************************************************************/


// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_1",
	'label'       => ff__( 'Content Area' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Text Decoration
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'typography',
	'settings'  	=> "${section_id}_link_text_decoration",
	'label'     	=> ff__( 'Link style' ),
	'section'		=> $section_id,
	'default'		=> [
	   'text-decoration'    => 'underline',
	],
	'output'		=> [
		[ 'element' 		=> '.single-article .post-content A' ],
	],
) );

/****************************************************************
 * SIDEBAR
 ****************************************************************/


// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_1",
	'label'       => ff__( 'Sidebar' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// sidebar html class
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'text',
	'settings'  	=> "${section_id}_sidebar_class",
	'label'     	=> ff__( 'Sidebar HTML Class' ),
	'section'		=> $section_id,
	'default'		=> 'col-md-4',
) );

// sidebar position
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'radio-buttonset',
	'settings'  	=> "${section_id}_sidebar_position",
	'label'     	=> ff__( 'Sidebar Position' ),
	'section'		=> $section_id,
	'default'		=> 'right',
	'choices'		=> [
		'left'		=> ff__( 'Left' ),
		'right'		=> ff__( 'Right' ),
	],
) );

// sidebar title
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'text',
	'settings'  	=> "${section_id}_sidebar_title",
	'label'     	=> ff__( 'Sidebar Title' ),
	'section'		=> $section_id,
	'default'		=> 'In this Issue',
) );
