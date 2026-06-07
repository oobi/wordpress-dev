<?php // Email View

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_email';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Email View' ),
	'description' 	=> ff__( 'Customize the elements of the email view.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );


/****************************************************************
 * HEADER
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_1",
	'label'       => ff__( 'Header' ),
	'description' => ff__( 'This section will be empty if Text header type is selected.' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Header Image
\Kirki::add_field( $this->config_id, array(
	'type'      => 'image',
	'settings'  => "{$section_id}_image",
	'label'     => ff__( 'Header Image' ),
	'description' => ff__( 'Recommended size: <strong>640px x 120px</strong>' ),
	'section'   => $section_id,
	'choices'   => [
		'save_as' => 'id',
	],
	'required' 	=> [
		[
			'setting'	=> 'buzz_header_type',
			'operator'	=> '=',
			'value'		=> 'image',
		],
	]
) );

/* Hero image */
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_hero_show",
	'label'     => ff__( 'Show Hero Image' ),
	'section'   => $section_id,
	'default'	=> true
) );



/****************************************************************
 * ARTICLES
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_2",
	'label'       => ff__( 'Articles' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Read more link label
\Kirki::add_field( $this->config_id, array(
	'type'      => 'text',
	'settings'  => "{$section_id}_more_label",
	'label'     => ff__( '"Read more" link label' ),
	'section'   => $section_id,
	'default'	=> 'Read more',
) );



/****************************************************************
 * FEATURED ARTICLES
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_3",
	'label'       => ff__( 'Featured Articles' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Template
\Kirki::add_field( $this->config_id, array(
	'type'      => 'select',
	'settings'  => "{$section_id}_featured_template",
	'label'     => ff__( 'Template' ),
	'section'   => $section_id,
	// values of default/choices need to match the name of twig files in views/email/templates/featured
	'default'	=> 'image-right',
	'choices'   => $article_templates->choices( 'email_featured' ), // get choices from ArticleTemplates class
) );

// Show thumbnails
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_featured_thumbnails",
	'label'     => ff__( 'Show Thumbnails' ),
	'section'   => $section_id,
	'default'	=> true,
) );

// Add padding around text
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_featured_text_padding",
	'label'     => ff__( 'Additional text padding' ),
	'description' => ff__( 'Useful when adding a background colour' ),
	'section'   => $section_id,
	'default'	=> false,
) );

// Excerpt with image length
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_featured_excerpt_image",
	'label'     	=> ff__( 'Excerpt word length (with image)' ),
	'description'  	=> ff__( 'Does not affect manually-entered excerpts' ),
	'section'		=> $section_id,
	'default'		=> 15,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 100,
		'step'			=> 1,
	],
) );

// Excerpt no image length
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_featured_excerpt_no_image",
	'label'     	=> ff__( 'Excerpt word length (no image)' ),
	'description'  	=> ff__( 'Does not affect manually-entered excerpts' ),
	'section'		=> $section_id,
	'default'		=> 30,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 150,
		'step'			=> 1,
	],
) );

/****************************************************************
 * INDEX ARTICLES
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_4",
	'label'       => ff__( 'Index Articles' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Template
\Kirki::add_field( $this->config_id, array(
	'type'      => 'select',
	'settings'  => "{$section_id}_index_template",
	'label'     => ff__( 'Template' ),
	'section'   => $section_id,
	// values of default/choices need to match the name of twig files in views/email/templates/index
	'default'	=> 'list',
	'choices'   => $article_templates->choices( 'email_index' ), // get choices from ArticleTemplates class
) );

// Show thumbnails
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_index_thumbnails",
	'label'     => ff__( 'Show Thumbnails' ),
	'section'   => $section_id,
	'default'	=> true,
) );

// Add padding around text
\Kirki::add_field( $this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_index_text_padding",
	'label'     => ff__( 'Additional text padding' ),
	'description' => ff__( 'Useful when adding a background colour' ),
	'section'   => $section_id,
	'default'	=> false,
) );

// Excerpt with image length
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_index_excerpt_image",
	'label'     	=> ff__( 'Excerpt word length (with image)' ),
	'description'  	=> ff__( 'Does not affect manually-entered excerpts' ),
	'section'		=> $section_id,
	'default'		=> 15,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 100,
		'step'			=> 1,
	],
) );

// Excerpt no image length
\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_index_excerpt_no_image",
	'label'     	=> ff__( 'Excerpt word length (no image)' ),
	'description'  	=> ff__( 'Does not affect manually-entered excerpts' ),
	'section'		=> $section_id,
	'default'		=> 30,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 150,
		'step'			=> 1,
	],
) );

/****************************************************************
 * DATES
 ****************************************************************/

if( class_exists( 'Buzz_Addon_Dates' ) ) {

	// Info Label
	\Kirki::add_field( $this->config_id, array(
		'type'        => 'custom',
		'settings'    => "{$section_id}_info_5",
		'label'       => ff__( 'Dates' ),
		'section'     => $section_id,
		'priority'    => 10,
	) );

	// Template
	\Kirki::add_field( $this->config_id, array(
		'type'      => 'select',
		'settings'  => "{$section_id}_dates_template",
		'label'     => ff__( 'Template' ),
		'section'   => $section_id,
		// values of default/choices need to match the name of twig files in views/email/templates/dates
		'default'	=> 'list-2col',
		'choices'   => $article_templates->choices( 'email_dates' ), // get choices from ArticleTemplates class
	) );

}


/****************************************************************
 * FOOTER
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_6",
	'label'       => ff__( 'Footer' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

\Kirki::add_field( $this->config_id, array(
	'type'     		=> 'radio-buttonset',
	'settings'  	=> "{$section_id}_credit",
	'label'     	=> ff__( 'The Buzz Logo Colour' ),
	'section'		=> $section_id,
	'default'		=> 'dark',
	'choices'   	=> [
		'dark'			=> 'Dark',
		'light'			=> 'Light',
		'colour'		=> 'Brand Colour',
	],
) );