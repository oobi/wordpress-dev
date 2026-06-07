<?php
// Group Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_taxonomies",
	'label'       => ff__( 'Taxonomies' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Taxonomies Colours
\Kirki::add_field( $this->config_id, array(
	'type'        	=> 'multicolor',
	'settings'   	=> "${section_id}_taxonomies",
	'label'       	=> ff__( 'Tags' ),
	'section'     	=> $section_id,
	'transport'		=> 'auto',
	'alpha'			=> false,
	'default'		=> [
		'icon'			=> '#999999',
		'link-bg'		=> '#999999',
		'link-txt'		=> '#FFFFFF',
		'link-hover'	=> '#333333',
		'divider'		=> '#333333',
	],
	'choices'		=> [
		'icon'			=> ff__( 'Icon' ),
		'link-bg'		=> ff__( 'Button Background/Link Colour' ),
		'link-txt'		=> ff__( 'Button Text' ),
		'link-hover'	=> ff__( 'Hover' ),
		'divider'		=> ff__( 'Divider' ),
	],
	'output'		=> [
		[ 'choice' => 'icon', 		'element' => '.content-area .tags .tags__icon', 					'property' => 'color' ],
		[ 'choice' => 'link-bg', 	'element' => '.content-area .tags .buzz-article_tag.text', 			'property' => 'color' ],
		[ 'choice' => 'link-bg', 	'element' => '.content-area .tags .buzz-article_tag.button', 		'property' => 'background-color' ],
		[ 'choice' => 'link-txt', 	'element' => '.content-area .tags .buzz-article_tag.button', 		'property' => 'color' ],
		[ 'choice' => 'link-hover',	'element' => '.content-area .tags .buzz-article_tag.text:hover',	'property' => 'color' ],
		[ 'choice' => 'link-hover',	'element' => '.content-area .tags .buzz-article_tag.button:hover', 	'property' => 'background-color' ],
		[ 'choice' => 'divider',	'element' => '.content-area .tags .tags__divider', 					'property' => 'color' ],
	]
) );
