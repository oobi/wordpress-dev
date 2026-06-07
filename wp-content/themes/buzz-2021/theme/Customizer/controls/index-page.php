<?php // Index Articles

use Firefly\Setup\ArticleTemplates;

$article_templates = new ArticleTemplates();

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_index_page';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Index Page' ),
	'description' 	=> ff__( 'Customize the layout of the index page.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );

/****************************************************************
 * HERO IMAGE
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_0",
	'label'       => ff__( 'Hero Image' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_hero_show",
	'label'     => ff__( 'Show Hero Image' ),
	'section'   => $section_id,
	'default'	=> true
) );

 \Kirki::add_field( $this->config_id, array(
	 'type'     	=> 'radio-buttonset',
	 'settings'  	=> "{$section_id}_hero_position",
	 'label'     	=> ff__( 'Hero Image Position' ),
	 'section'		=> $section_id,
	 'default'		=> 'above',
	 'choices'		=> [
		 'above'		=> ff__( 'Above Navbar' ),
		 'below'		=> ff__( 'Below Navbar' ),
	 ],
	 'required' 	=> [
		[
			'setting'	=> "{$section_id}_hero_show",
			'value'		=> true,
			'operator'	=> '=',
		]
	]
 ) );

/****************************************************************
 * ARTICLE LIST WRAPPER
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_1",
	'label'       => ff__( 'Article List Wrapper' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Classes
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_class",
	'label'       => ff__( 'HTML Classes' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'     => $section_id,
) );

// Section Title
\Kirki::add_field( $this->config_id, array(
	'type'      => 'text',
	'settings'  => "{$section_id}_title",
	'label'     => ff__( 'Section Title' ),
	'description' => ff__( 'Optional. Leave blank to hide. If <strong>Taxonomies Add On</strong> is enabled,
							this field controls the section title for uncategorized articles' ),
	'section'   => $section_id,
	'default'	=> '',
) );


/****************************************************************
 * ARTICLE LIST TEMPLATE
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_2",
	'label'       => ff__( 'Template' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Template
\Kirki::add_field( $this->config_id, array(
	'type'      => 'select',
	'settings'  => "{$section_id}_template",
	'label'     => ff__( 'Article List Template' ),
	'section'   => $section_id,
	// values of default/choices need to match the name of twig files in views/templates/index
	'default'	=> 'grid',
	'choices'   => $article_templates->choices( 'index' ), // get choices from ArticleTemplates class
) );

// Number of columns
\Kirki::add_field( $this->config_id, array(
	'type'      => 'radio-buttonset',
	'settings'  => "{$section_id}_columns",
	'label'     => ff__( 'Number of Columns' ),
	'section'   => $section_id,
	'default'	=> '4',
	'choices'   => [
		'1'	=> '1',
		'2'	=> '2',
		'3'	=> '3',
		'4'	=> '4',
	],
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_template",
			'value'		=> [ 'grid' ],
			'operator'	=> 'in',
		],
	],
) );

// Show thumbnails
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_thumbnails",
	'label'     => ff__( 'Show Thumbnails' ),
	'section'   => $section_id,
	'default'	=> true,
) );

// Show excerpts
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_excerpts",
	'label'     => ff__( 'Show Excerpts' ),
	'section'   => $section_id,
	'default'	=> true,
) );

// Classes for individual article containers
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_article_class",
	'label'       => ff__( 'Article Wrapper HTML Classes' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'     => $section_id,
	'default'	 => 'mb-4'
) );

// Classes for thumbnail in list mode
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_thumbnail_list_class",
	'label'       => ff__( 'Thumbnail List Class' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'     => $section_id,
	'default'	 => 'col-12 col-md-4 col-lg-3 col-xl-2'
) );

// article excerpt class
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'text',
	'settings'  	=> "{$section_id}_excerpt_class",
	'label'     	=> ff__( 'Article Excerpt HTML Class' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'		=> $section_id,
	'default'		=> '',
) );

/****************************************************************
 * EXCERPT AND LINKS
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_3",
	'label'       => ff__( 'Excerpt and Links' ),
	'section'     => $section_id,
	'priority'    => 10,
) );


// Excerpt with image length
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_excerpt_image",
	'label'     	=> ff__( 'Excerpt word length (with image)' ),
	'description'  	=> ff__( 'Does not affect manually-entered excerpts' ),
	'section'		=> $section_id,
	'default'		=> 15,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 100,
		'step'			=> 1,
	],
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_template",
			'value'		=> [ 'grid' ],
			'operator'	=> 'in',
		],
	]
) );

// Excerpt no image length
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_excerpt_no_image",
	'label'     	=> ff__( 'Excerpt word length (no image)' ),
	'description'  	=> ff__( 'Does not affect manually-entered excerpts' ),
	'section'		=> $section_id,
	'default'		=> 30,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 150,
		'step'			=> 1,
	],
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_template",
			'value'		=> [ 'grid' ],
			'operator'	=> 'in',
		],
	]
) );

// Excerpt in list mode
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_excerpt_list",
	'label'     	=> ff__( 'List excerpt word length' ),
	'description'  	=> ff__( 'Does not affect manually-entered excerpts' ),
	'section'		=> $section_id,
	'default'		=> 50,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 150,
		'step'			=> 1,
	],
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_template",
			'value'		=> [ 'list' ],
			'operator'	=> 'in',
		],
	],
) );