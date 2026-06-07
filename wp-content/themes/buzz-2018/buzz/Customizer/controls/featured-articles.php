<?php // Featured Articles

use Firefly\Buzz\Setup\ArticleTemplates;

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
	'default'	=> 'single-2col',
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

// Read more link type
\Kirki::add_field( $this->config_id, array(
	'type'      => 'radio-buttonset',
	'settings'  => "{$section_id}_more_type",
	'label'     => ff__( '"Read more" link type' ),
	'section'   => $section_id,
	'default'	=> 'text',
	'choices'   => [
		'none'		=> 'None',
		'text'		=> 'Text',
		'button'	=> 'Button',
	],
) );

// Text Decoration
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'typography',
	'settings'  	=> "{$section_id}_more_text_decoration",
	'label'     	=> ff__( 'Read more link style' ),
	'section'		=> $section_id,
	'default'		=> [
	   'text-decoration'    => 'underline',
	],
	'output'		=> [
		[ 'element' 		=> '.featured-articles A.read-more' ],

		// Email View
		[ 'element' 		=> '#email-view .featured A.read-more' ],
	],
	'required'		=> [
		[
			'setting'	=> "{$section_id}_more_type",
			'value'		=> ['text'],
			'operator'	=> 'in',
		],
	]
) );

// Read more link label
\Kirki::add_field( $this->config_id, array(
	'type'      => 'text',
	'settings'  => "{$section_id}_more_label",
	'label'     => ff__( '"Read more" link label' ),
	'section'   => $section_id,
	'default'	=> 'Read more',
) );

// Button Icon
\Kirki::add_field( $this->config_id, array(
	'type'			=> 'text',
	'settings'  	=> "{$section_id}_icon",
	'label'     	=> ff__( 'Button Icon' ),
	'description'	=> ff__( '<a href="https://fontawesome.com/icons" target="_blank">FontAwesome Class String</a> eg. "fas fa-search"</a>' ),
	'section'		=> $section_id,
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_readmore",
			'value'		=> ['button'],
			'operator'	=> 'in',
		],
	]
) );

// Button Icon Position
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'radio-buttonset',
	'settings'  	=> "{$section_id}_icon_position",
	'label'     	=> ff__( 'Button Icon Position' ),
	'section'		=> $section_id,
	'default'		=> 'right',
	'choices'   	=> [
		'left'			=> 'Left',
		'right'			=> 'Right',
	],
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_readmore",
			'value'		=> ['button'],
			'operator'	=> 'in',
		],
	]
) );
