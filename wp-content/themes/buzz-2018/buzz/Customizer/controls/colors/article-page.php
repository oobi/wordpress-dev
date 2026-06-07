<?php
$group_id = 'content';

/****************************************************************
 * ARTICLE PAGE
 ****************************************************************/

// Group Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_{$group_id}_info",
	'label'       => ff__( 'Article Page' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Content Area Colours
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "{$section_id}_{$group_id}_main",
	'label'       	=> ff__( 'Content Area' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#FFFFFF',
		'text'			=> '#333333',
		'title'			=> '#000000',
		'headings'		=> '#000000',
		'links'			=> '#3476a6',
		'links-hover'	=> '#999999',
		'highlight'		=> '#999999',
		'pullquote'		=> '#999999',
		'pullquote-icon'=> '#CCCCCC',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Text' ),
		'title'			=> ff__( 'Article Title' ),
		'headings'		=> ff__( 'Headings' ),
		'links'			=> ff__( 'Links' ),
		'links-hover'	=> ff__( 'Links Hover' ),
		'highlight'		=> ff__( 'Highlight Text' ),
		'pullquote'		=> ff__( 'Pull Quote Text' ),
		'pullquote-icon'=> ff__( 'Pull Quote Icon' ),
	],
	'output'		=> [
		[ 'choice' => 'background',	 	'element' => '.content-area', 						'property' => 'background-color' ],
		[ 'choice' => 'text', 			'element' => '.content-area', 						'property' => 'color' ],
		[ 'choice' => 'text', 			'element' => '.content-area .sidebar .current A', 	'property' => 'color' ],
		[ 'choice' => 'links', 			'element' => '.content-area A:not(.btn)', 			'property' => 'color' ],
		[ 'choice' => 'links-hover',	'element' => '.content-area A:not(.btn):hover', 	'property' => 'color' ],
		[ 'choice' => 'title', 			'element' => '.content-area .page-title', 			'property' => 'color' ],
		[ 'choice' => 'headings', 		'element' => '.content-area h2,.content-area h3,.content-area h4,.content-area h5,.content-area h6',
																							'property' => 'color' ],
		[ 'choice' => 'highlight', 		'element' => '.content-area .highlight', 			'property' => 'color' ],
		[ 'choice' => 'pullquote', 		'element' => '.content-area .pullquote', 			'property' => 'color' ],
		[ 'choice' => 'pullquote-icon', 'element' => '.content-area .pullquote::before', 	'property' => 'color' ],

		// Email View
		[ 'choice' => 'background',	'element' => '#email-view #email-body', 	'property' => 'background-color' ],
		[ 'choice' => 'text',		'element' => '#email-view #email-body', 	'property' => 'color' ],
	]
) );

// Table Colours
$table_header = '.table-banded THEAD TD, .table-banded .table-row-header TD,  .table-standard THEAD TD, .table-standard .table-row-header TD';
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "{$section_id}_{$group_id}_tables",
	'label'       	=> ff__( 'Tables' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'header-bg'		=> '#b8bbbe',
		'header-text'	=> '#FFFFFF',
		'row-bg'		=> '#FFFFFF',
		'row-bg-alt'	=> '#e6e6e6',
		'row-text'		=> '#333333',
		'row-text-alt'	=> '#333333',
		'border'		=> '#b8bbbe',
	],
	'choices'		=> [
		'header-bg'		=> ff__( 'Header Background' ),
		'header-text'	=> ff__( 'Header Text' ),
		'row-bg'		=> ff__( 'Row Background' ),
		'row-bg-alt'	=> ff__( 'Row Background Alt' ),
		'row-text'		=> ff__( 'Row Text' ),
		'row-text-alt'	=> ff__( 'Row Text Alt' ),
		'border'		=> ff__( 'Border' ),
	],
	'output'		=> [
		[ 'choice' => 'header-bg',	 	'element' => $table_header, 							'property' => 'background-color' ],
		[ 'choice' => 'header-text', 	'element' => $table_header, 							'property' => 'color' ],
		[ 'choice' => 'row-bg', 		'element' => '.table-banded TD, .table-standard TD', 	'property' => 'background-color' ],
		[ 'choice' => 'row-text', 		'element' => '.table-banded TD, .table-standard TD', 	'property' => 'color' ],
		[ 'choice' => 'row-bg-alt', 	'element' => '.table-banded TR:nth-of-type(2n) TD', 	'property' => 'background-color' ],
		[ 'choice' => 'row-text-alt', 	'element' => '.table-banded TR:nth-of-type(2n) TD', 	'property' => 'color' ],
		[ 'choice' => 'border', 		'element' => '.table-banded, .table-standard, .table-banded TD, .table-standard TD',
																								'property' => 'border-color' ],
	]
) );

// Sidebar Colours
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "{$section_id}_{$group_id}_sidebar",
	'label'       	=> ff__( 'Sidebar' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'background'	=> '#FFFFFF',
		'text'			=> '#333333',
		'title'			=> '#000000',
		'cat-title'		=> '#000000',
		'links'			=> '#3476a6',
		'links-hover'	=> '#999999',
		'divider'		=> '#f0f0f0',
		'featured'		=> '#000000',
		'current'		=> '#000000',
	],
	'choices'		=> [
		'background'	=> ff__( 'Background' ),
		'text'			=> ff__( 'Text' ),
		'title'			=> ff__( 'Title' ),
		'cat-title'		=> ff__( 'Category Titles' ),
		'links'			=> ff__( 'Links' ),
		'links-hover'	=> ff__( 'Links Hover' ),
		'current'		=> ff__( 'Current Article Link' ),
		'divider'		=> ff__( 'Divider' ),
		'featured'		=> ff__( 'Featured Indicator' ),
	],
	'output'		=> [
		[ 'choice' => 'background', 'element' => '.content-area .sidebar', 					'property' => 'background-color' ],
		[ 'choice' => 'text', 		'element' => '.content-area .sidebar', 					'property' => 'color' ],
		[ 'choice' => 'title', 		'element' => '.content-area .sidebar .section-title', 	'property' => 'color' ],
		[ 'choice' => 'cat-title', 	'element' => '.content-area .sidebar .category-title', 	'property' => 'color' ],
		[ 'choice' => 'links', 		'element' => '.content-area .sidebar A', 				'property' => 'color' ],
		[ 'choice' => 'links-hover', 'element' => '.content-area .sidebar A:hover', 		'property' => 'color' ],
		[ 'choice' => 'divider', 	'element' => '.content-area .sidebar .in-this-issue__list .article-item',
																							'property' => 'border-color' ],
		[ 'choice' => 'featured',	'element' => '.content-area .sidebar .featured A::before',
																							'property' => 'color' ],
		[ 'choice' => 'current',	'element' => '.content-area .sidebar .current A', 		'property' => 'color' ],
	]
) );
