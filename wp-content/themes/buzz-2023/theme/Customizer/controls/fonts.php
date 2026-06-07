<?php

$group_id = 'global';

$font_choice = [
	'fonts' => [
		'standard' => [
			'initial',
			'inherit',
			'serif',
			'sans-serif',
			'Arial, sans-serif',
			'Georgia, serif',
			'Helvetica, Arial, sans-serif',
			'Times, serif',
			'"Times New Roman", serif',
			'Verdana, sans-serif'
		]
	]
];


/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_font';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Theme Fonts' ),
	'description' 	=> ff__( 'Customize the newsletter fonts.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );


/****************************************************************
 * GLOBAL
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_1",
	'label'       => ff__( 'Global' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Body Text
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'typography',
	'settings'  	=> "{$section_id}_body",
	'label'     	=> ff__( 'Body Font' ),
	'section'		=> $section_id,
	'default'		=> [
	   'font-family'    => 'Rubik',
	   'variant'        => 'regular',
	   'font-size'      => '1rem',
	   'line-height'    => '1.5',
	   'letter-spacing' => '0',
	   // 'text-transform' => 'none',
	   // 'color'          => '#333333',
	   // 'text-align'     => 'left',
	],
	'choices'		=> $font_choice,
	'output'		=> [
		// web
	   [ 'element' => ':root', 'choice'=> 'font-family', 'property'=>'--bs-body-font-family' ],
	   [ 'element' => ':root', 'choice'=> 'line-height', 'property'=>'--bs-body-font-line-height' ],
	   [ 'element' => ':root', 'choice'=> 'font-size', 'property'=>'--bs-body-font-size' ],
	   // email
	   [ 'element' => '#email', 'choice'=> 'font-family', 'property'=>'font-family' ],
	   [ 'element' => '#email', 'choice'=> 'line-height', 'property'=>'line-height' ],
	   [ 'element' => '#email', 'choice'=> 'font-size',   'property'=>'font-size' ],
	],
) );

/****************************************************************
 * LAYOUT
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_2",
	'label'       => ff__( 'Layout' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Header Text
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'typography',
	'settings'  	=> "{$section_id}_{$group_id}_header",
	'label'     	=> ff__( 'Header Font' ),
	'section'		=> $section_id,
	'default'		=> [
	   'font-family'    => 'inherit',
	   'variant'        => 'regular',
	   // 'font-size'      => '24px',
	   // 'line-height'    => '1.5',
	   'letter-spacing' => '0',
	   'text-transform' => 'none',
	   // 'color'          => '#333333',
	   // 'text-align'     => 'left',
	],
	'choices'		=> $font_choice,
	'output'		=> [
	   [ 'element' => '.site-header' ],
	],
	'required' 	=> [
		[
			'setting'	=> 'buzz_header_type',
			'value'		=> 'text',
			'operator'	=> '=',
		]
	]
) );

// Navbar Text
\Kirki::add_field( $this->config_id, array(
	'type'     	=> 'typography',
	'settings'  	=> "{$section_id}_{$group_id}_navbar",
	'label'     	=> ff__( 'Navbar Font' ),
	'section'		=> $section_id,
	'default'		=> [
	   'font-family'    => 'inherit',
	   'variant'        => 'regular',
	   'font-size'      => '1rem',
	   // 'line-height'    => '1.5',
	   'letter-spacing' => '0',
	   'text-transform' => 'none',
	   // 'color'          => '#333333',
	   // 'text-align'     => 'left',
	],
	'choices'		=> $font_choice,
	'output'		=> [
	   [ 'element' => '#primary-navigation' ],
	   [ 'element' => '#primary-navigation A' ],
	   [ 'element' => '#primary-navigation INPUT' ],
	],
) );

/****************************************************************
 * CONTENT
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_3",
	'label'       => ff__( 'Content' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Heading Text
// \Kirki::add_field( $this->config_id, array(
// 	'type'     	=> 'typography',
// 	'settings'  	=> "{$section_id}_{$group_id}_heading",
// 	'label'     	=> ff__( 'Heading Font' ),
// 	'description' 	=> ff__( 'Affects all heading tags (h1 to h6)' ),
// 	'section'		=> $section_id,
// 	'default'		=> [
// 	   'font-family'    => 'Nunito Sans',
// 	   'variant'        => 'regular',
// 	   // 'font-size'      => '24px',
// 	   'line-height'    => '1.1',
// 	   //'letter-spacing' => '0',
// 	   //'text-transform' => 'none',
// 	   // 'color'          => '#333333',
// 	   //'text-align'     => 'initial',
// 	],
// 	'choices'		=> $font_choice,
// 	'output'		=> [
// 		// web
// 		[ 'element' => ':root', 'choice'=> 'font-family', 'property'=>'--bz-heading-font-family' ],
// 		[ 'element' => ':root', 'choice'=> 'line-height', 'property'=>'--bz-heading-font-line-height' ],
// 		// email
// 		[ 'element' => '#email h1, #email h2, #email h3, #email h4, #email h5, #email h6', 'choice'=> 'font-family', 'property'=>'font-family' ],
// 		[ 'element' => '#email h1, #email h2, #email h3, #email h4, #email h5, #email h6', 'choice'=> 'line-height', 'property'=>'line-height' ]
// 	 ],
// ) );

// // Highlight Text
//  \Kirki::add_field( $this->config_id, array(
// 	 'type'     	=> 'typography',
// 	 'settings'  	=> "{$section_id}_highlight",
// 	 'label'     	=> ff__( 'Highlight Font' ),
// 	 'section'		=> $section_id,
// 	 'default'		=> [
// 		'font-family'    => 'inherit',
// 		'variant'        => '300',
// 		//'font-size'      => '1.5rem',
// 		//'line-height'    => '1.5',
// 		'letter-spacing' => '0',
// 		// 'text-transform' => 'none',
// 		// 'color'          => '#333333',
// 		'text-align'     => 'center',
// 	 ],
// 	 'choices'		=> $font_choice,
// 	 'output'		=> [
// 		[ 'element' => '.content-area .highlight' ],
// 	 ],
//  ) );

// // Pull Quote Text
//  \Kirki::add_field( $this->config_id, array(
// 	 'type'     	=> 'typography',
// 	 'settings'  	=> "{$section_id}_pullquote",
// 	 'label'     	=> ff__( 'Pull Quote Font' ),
// 	 'section'		=> $section_id,
// 	 'default'		=> [
// 		'font-family'    => 'inherit',
// 		'variant'        => '300',
// 		'font-size'      => '1.5rem',
// 		'line-height'    => '1.5',
// 		'letter-spacing' => '0',
// 		// 'text-transform' => 'none',
// 		// 'color'          => '#333333',
// 		'text-align'     => 'center',
// 	 ],
// 	 'choices'		=> $font_choice,
// 	 'output'		=> [
// 		[ 'element' => '.content-area .pullquote' ],
// 	 ],
//  ) ); #}