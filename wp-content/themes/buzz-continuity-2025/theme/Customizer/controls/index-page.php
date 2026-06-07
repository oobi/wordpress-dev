<?php // Index Articles

use Firefly\Setup\ArticleTemplates;

$article_templates = new ArticleTemplates();

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_index_page';
\Kirki::add_section($section_id, array(
	'title' 		=> ff__('Index Page'),
	'description' 	=> ff__('Customize the layout of the index page.'),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
));

/****************************************************************
 * HERO IMAGE
 ****************************************************************/

// Info Label
\Kirki::add_field($this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_0",
	'label'       => ff__('Hero Image'),
	'section'     => $section_id,
	'priority'    => 10,
));

\Kirki::add_field($this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_hero_show",
	'label'     => ff__('Show Hero Image'),
	'section'   => $section_id,
	'default'	=> true
));

\Kirki::add_field($this->config_id, array(
	'type'     	=> 'radio-buttonset',
	'settings'  	=> "{$section_id}_hero_position",
	'label'     	=> ff__('Hero Image Position'),
	'section'		=> $section_id,
	'default'		=> 'above',
	'choices'		=> [
		'above'		=> ff__('Above Navbar'),
		'below'		=> ff__('Below Navbar'),
	],
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_hero_show",
			'value'		=> true,
			'operator'	=> '=',
		]
	]
));

/****************************************************************
 * ARTICLE LIST WRAPPER
 ****************************************************************/

// Info Label
\Kirki::add_field($this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_1",
	'label'       => ff__('Article List Wrapper'),
	'section'     => $section_id,
	'priority'    => 10,
));

// Classes
\Kirki::add_field($this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_class",
	'label'       => ff__('HTML Classes'),
	'description' => ff__('Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>'),
	'section'     => $section_id,
));

// Section Title
\Kirki::add_field($this->config_id, array(
	'type'      => 'text',
	'settings'  => "{$section_id}_title",
	'label'     => ff__('Section Title'),
	'description' => ff__('Optional. Leave blank to hide. If <strong>Taxonomies Add On</strong> is enabled,
							this field controls the section title for uncategorized articles'),
	'section'   => $section_id,
	'default'	=> '',
));


/****************************************************************
 * ARTICLE LIST TEMPLATE
 ****************************************************************/

// Info Label
\Kirki::add_field($this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_2",
	'label'       => ff__('Article Template'),
	'section'     => $section_id,
	'priority'    => 10,
));

// Show featured images
\Kirki::add_field($this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_thumbnails",
	'label'     => ff__('Show Featured Image'),
	'section'   => $section_id,
	'default'	=> true,
));

// Overlay title on featured image
\Kirki::add_field($this->config_id, array(
	'type'      => 'toggle',
	'settings'  => "{$section_id}_overlay_title",
	'label'     => ff__('Overlay Title On Featured Image'),
	'section'   => $section_id,
	'default'	=> true,
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_thumbnails",
			'value'		=> true,
			'operator'	=> '=',
		]
	]
));

// Featured image crop
\Kirki::add_field($this->config_id, array(
	'type'      => 'select',
	'settings'  => "{$section_id}_thumbnail_size",
	'label'     => ff__('Featured Image Size'),
	'section'   => $section_id,
	'default'	=> 'article_hero',
	'choices'	=> get_image_sizes(),
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_thumbnails",
			'value'		=> true,
			'operator'	=> '=',
		]
	]
));


// class for article
\Kirki::add_field($this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_article_css",
	'label'       => ff__('Article Container CSS class'),
	'description' => ff__('Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>'),
	'section'     => $section_id,
	'default'	 => 'my-8',
));

// Classes for individual article containers
\Kirki::add_field($this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_thumbnail_css",
	'label'       => ff__('Featured Image CSS class'),
	'description' => ff__('Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>'),
	'section'     => $section_id,
	'default'	 => 'mb-8',
	'required' 	=> [
		[
			'setting'	=> "{$section_id}_thumbnails",
			'value'		=> true,
			'operator'	=> '=',
		]
	]
));

// Classes for individual article containers
\Kirki::add_field($this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_text_css",
	'label'       => ff__('Article Body class'),
	'description' => ff__('Useful for adding <a href="https://hackerthemes.com/bootstrap-cheatsheet/" target="_blank">bootstrap classes</a>'),
	'section'     => $section_id,
	'default'	 => ''
));

/****************************************************************
 * EXCERPT AND LINKS
 ****************************************************************/

// Info Label
\Kirki::add_field($this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_3",
	'label'       => ff__('Feature Excerpt and Links'),
	'section'     => $section_id,
	'priority'    => 10,
));


// Excerpt with image length
\Kirki::add_field($this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_excerpt_image",
	'label'     	=> ff__('Excerpt word length (with image)'),
	'description'  	=> ff__('Does not affect manually-entered excerpts'),
	'section'		=> $section_id,
	'default'		=> 15,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 100,
		'step'			=> 1,
	]
));

// Excerpt no image length
\Kirki::add_field($this->config_id, array(
	'type'     		=> 'number',
	'settings'  	=> "{$section_id}_excerpt_no_image",
	'label'     	=> ff__('Excerpt word length (no image)'),
	'description'  	=> ff__('Does not affect manually-entered excerpts'),
	'section'		=> $section_id,
	'default'		=> 30,
	'choices'   	=> [
		'min'			=> 5,
		'max'			=> 150,
		'step'			=> 1,
	]
));
