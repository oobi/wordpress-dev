<?php
$group_id = 'content';

/****************************************************************
 * INDEX PAGE
 ****************************************************************/

// Group Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_${group_id}_info2",
	'label'       => ff__( 'Index Page' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Widget Area - above featured articles
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_widget_before_featured",
	'label'       	=> ff__( 'Widget area before featured articles' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#FFFFFF',
		'text'			=> '#000',
		'title'			=> '#000',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Text' ),
		'title'			=> ff__( 'Title' ),
	],
	'output'		=> [
		[ 'choice' => 'background', 	'element' => '.widget-before-featured', 				'property' => 'background-color' ],
		[ 'choice' => 'text', 			'element' => '.widget-before-featured', 				'property' => 'color' ],
		[ 'choice' => 'title', 			'element' => '.widget-before-featured .widget-title', 	'property' => 'color' ],

		// email view
		[ 'choice' => 'background', 	'element' => '#email-view .widget-before-featured', 				'property' => 'background-color' ],
		[ 'choice' => 'text', 			'element' => '.widget-before-featured', 				'property' => 'color' ],
		[ 'choice' => 'title', 			'element' => '.widget-before-featured .widget-title', 	'property' => 'color' ],
	]
) );

// Featured Article Colours
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_featured_articles",
	'label'       	=> ff__( 'Featured Articles' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#FFFFFF',
		'tease-bg'		=> '#FFFFFF',
		'tease-title'	=> '#333333',
		'tease-text'	=> '#333333',
		'link-bg'		=> '#333333',
		'link-txt'		=> '#FFFFFF',
	],
	'choices'		=> [
		'background'	=> ff__( 'Section Background' ),
		'tease-bg'		=> ff__( 'Article Tease Background' ),
		'tease-title'	=> ff__( 'Article Tease Title' ),
		'tease-text'	=> ff__( 'Article Tease Text' ),
		'link-bg'		=> ff__( 'Button Background/Link Colour' ),
		'link-txt'		=> ff__( 'Button Text' ),
	],
	'output'		=> [
		[ 'choice' => 'background', 	'element' => '.featured-articles', 					'property' => 'background-color' ],
		[ 'choice' => 'tease-bg', 		'element' => '.featured-articles .article-tease', 	'property' => 'background-color' ],
		[ 'choice' => 'tease-title', 	'element' => '.featured-articles .article-tease .title__link',
																							'property' => 'color' ],
		[ 'choice' => 'tease-text', 	'element' => '.featured-articles .article-tease', 	'property' => 'color' ],
		[ 'choice' => 'tease-text', 	'element' => '.featured-articles .title__link', 	'property' => 'color' ],
		[ 'choice' => 'link-bg', 		'element' => '.featured-articles .btn', 			'property' => 'background-color' ],
		[ 'choice' => 'link-bg', 		'element' => '.featured-articles A.read-more', 		'property' => 'color' ],
		[ 'choice' => 'link-txt', 		'element' => '.featured-articles .btn', 			'property' => 'color' ],

		// email view
		[ 'choice' => 'background', 	'element' => '#email-view .featured-wrapper', 		'property' => 'background-color' ],
		[ 'choice' => 'tease-bg', 		'element' => '#email-view .featured TD.text', 		'property' => 'background-color' ],
		[ 'choice' => 'tease-title', 	'element' => '#email-view .featured A.title__link', 'property' => 'color' ],
		[ 'choice' => 'tease-text', 	'element' => '#email-view .featured', 				'property' => 'color' ],
		[ 'choice' => 'link-bg', 		'element' => '#email-view .featured A.read-more', 	'property' => 'color' ],
		[ 'choice' => 'link-bg', 		'element' => '#email-view .featured .btn', 			'property' => 'background-color' ],
		[ 'choice' => 'link-txt', 		'element' => '#email-view .featured .btn', 			'property' => 'color' ],
	]
) );

// Widget Area - between featured and index articles
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_widget_between_featured_index",
	'label'       	=> ff__( 'Widget area between articles' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#FFFFFF',
		'text'			=> '#000',
		'title'			=> '#000',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Text' ),
		'title'			=> ff__( 'Title' ),
	],
	'output'		=> [
		[ 'choice' => 'background', 	'element' => '.widget-between-featured-index', 		'property' => 'background-color' ],
		[ 'choice' => 'text', 			'element' => '.widget-between-featured-index', 		'property' => 'color' ],
		[ 'choice' => 'title', 			'element' => '.widget-between-featured-index .widget-title', 	'property' => 'color' ],

		// email view
		[ 'choice' => 'background', 	'element' => '.widget-between-featured-index', 		'property' => 'background-color' ],
		[ 'choice' => 'text', 			'element' => '.widget-between-featured-index', 		'property' => 'color' ],
		[ 'choice' => 'title', 			'element' => '.widget-between-featured-index .widget-title', 	'property' => 'color' ],
	]
) );

// Index Article Colours
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_index_articles",
	'label'       	=> ff__( 'Index Articles' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'title'			=> '#333333',
		'tease-bg'		=> '#FFFFFF',
		'tease-title'	=> '#333333',
		'tease-text'	=> '#333333',
		'link-bg'		=> '#333333',
		'link-txt'		=> '#FFFFFF',
		'no-img'		=> '#777777',
	],
	'choices'		=> [
		'title'			=> ff__( 'Section Title' ),
		'tease-bg'		=> ff__( 'Article Tease Background' ),
		'tease-title'	=> ff__( 'Article Tease Title' ),
		'tease-text'	=> ff__( 'Article Tease Text' ),
		'link-bg'		=> ff__( 'Button Background/Link Colour' ),
		'link-txt'		=> ff__( 'Button Text' ),
		'no-img'		=> ff__( 'No Image Accent' ),
	],
	'output'		=> [
		[ 'choice' => 'title', 			'element' => '.index-articles .section-title', 		'property' => 'color' ],
		[ 'choice' => 'tease-bg', 		'element' => '.index-articles .article-tease', 		'property' => 'background-color' ],
		[ 'choice' => 'tease-title', 	'element' => '.index-articles .article-tease .title__link',
																							'property' => 'color' ],
		[ 'choice' => 'tease-text', 	'element' => '.index-articles .article-tease', 		'property' => 'color' ],
		[ 'choice' => 'link-bg', 		'element' => '.index-articles A.read-more', 		'property' => 'color' ],
		[ 'choice' => 'link-bg', 		'element' => '.index-articles .btn', 				'property' => 'background-color' ],
		[ 'choice' => 'link-txt', 		'element' => '.index-articles .btn', 				'property' => 'color' ],
		[ 'choice' => 'no-img', 		'element' => '.index-articles .article-tease.no-thumb .article-tease__content',
																							'property' => 'border-color' ],

		// email view
		[ 'choice' => 'title', 			'element' => '#email-view .index .section-title', 			'property' => 'color' ],
		[ 'choice' => 'tease-bg', 		'element' => '#email-view .index__article', 				'property' => 'background-color' ],
		[ 'choice' => 'tease-title', 	'element' => '#email-view .index .title__link', 			'property' => 'color' ],
		[ 'choice' => 'tease-text', 	'element' => '#email-view .index .index__article > TABLE', 	'property' => 'color' ],
		[ 'choice' => 'link-bg', 		'element' => '#email-view .index A.read-more', 				'property' => 'color' ],
		[ 'choice' => 'link-txt', 		'element' => '#email-view .index .btn A', 					'property' => 'color' ],
		[ 'choice' => 'link-bg', 		'element' => '#email-view .index .btn', 					'property' => 'background-color' ],
	]
) );

// Widget Area - below featured and index articles
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_${group_id}_widget_after",
	'label'       	=> ff__( 'Widget area after index articles' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#FFFFFF',
		'text'			=> '#000',
		'title'			=> '#000',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Text' ),
		'title'			=> ff__( 'Title' ),
	],
	'output'		=> [
		[ 'choice' => 'background', 	'element' => '.widget-after-index', 		'property' => 'background-color' ],
		[ 'choice' => 'text', 			'element' => '.widget-after-index', 		'property' => 'color' ],
		[ 'choice' => 'title', 			'element' => '.widget-after-index .widget-title', 	'property' => 'color' ],

		// email view
		// TODO: email view options
	]
) );