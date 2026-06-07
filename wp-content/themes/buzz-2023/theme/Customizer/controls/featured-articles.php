<?php // Featured Articles

use Firefly\Buzz\ArticleTemplates;

$article_templates = new ArticleTemplates();

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_articles_featured';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Featured Articles' ),
	'description' 	=> ff__( 'Customize the layout of featured articles.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );


/****************************************************************
 * WRAPPER
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_1",
	'label'       => ff__( 'Wrapper' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Classes
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_class",
	'label'       => ff__( 'Featured Articles Wrapper HTML Classes' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'     => $section_id,
) );


/****************************************************************
 * TEMPLATE
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
	'label'     => ff__( 'Template' ),
	'section'   => $section_id,
	// values of default/choices need to match the name of twig files in views/templates/featured
	// 'default'	=> 'single-2col',
	// 'default'	=> 'double-2col',
	'default'	=> 'grid-quad',
	'choices'   => $article_templates->choices( 'featured' ), // get choices from ArticleTemplates class
) );

// Show thumbnails
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_thumbnails",
	'label'     => ff__( 'Show Thumbnails' ),
	'section'   => $section_id,
	'default'	=> true,
) );

// article excerpt class
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'text',
	'settings'  	=> "{$section_id}_excerpt_class",
	'label'     	=> ff__( 'Article Excerpt HTML Class' ),
	'description' => ff__( 'Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>' ),
	'section'		=> $section_id,
	'default'		=> 'p-4 p-xl-8',
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
	'default'		=> 30,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 100,
		'step'			=> 1,
	],
) );

// Excerpt no image length
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_excerpt_no_image",
	'label'     	=> ff__( 'Excerpt word length (no image)' ),
	'description'  	=> ff__( 'Does not affect manually-entered excerpts' ),
	'section'		=> $section_id,
	'default'		=> 45,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 150,
		'step'			=> 1,
	],
) );