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
 * FONT URLS
 ****************************************************************/

 \Kirki::add_field($this->config_id, [
	'type'        => 'repeater',
	'settings' 		=> "buzz_font_urls",
	'label'      	=> ff__( 'External Font URLs'),
	'section'    	=> $section_id,
	'priority'   	=> 5,
	'row_label'    => [
		'type'  => 'field',
		'value' => esc_html( 'New font CSS', 'kirki' ),
		'field' => 'name',
	],
	'fields'		=> [
		'name' => [
			'type'        => 'text',
			'label'       => ff__( 'Font Slug'),
			'description' => ff__( 'Stylesheet identifier - unique, lowercase, no spaces. Use  "google_fonts" to override default Google Fonts in this theme.'),
			'default'     => 'google_fonts',
		],
		'src' => [
			'type'        => 'text',
			'label'       => ff__( 'Font CSS URL'),
			'description' => ff__( 'Enter the URL of the font css.'),
			'default'     => '',
		],
	],
]);


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
// \Kirki::add_field( $this->config_id, array(
// 	'type'     	=> 'typography',
// 	'settings'  	=> "{$section_id}_body",
// 	'label'     	=> ff__( 'Body Font' ),
// 	'section'		=> $section_id,
// 	'default'		=> [
// 	   'font-family'    => 'Rubik',
// 	   'variant'        => 'regular',
// 	   'font-size'      => '1rem',
// 	   'line-height'    => '1.5',
// 	   'letter-spacing' => '0',
// 	   // 'text-transform' => 'none',
// 	   // 'color'          => '#333333',
// 	   // 'text-align'     => 'left',
// 	],
// 	'choices'		=> $font_choice,
// 	'output'		=> [
// 		// web
// 	   [ 'element' => ':root', 'choice'=> 'font-family', 'property'=>'--bs-body-font-family' ],
// 	   [ 'element' => ':root', 'choice'=> 'line-height', 'property'=>'--bs-body-font-line-height' ],
// 	   [ 'element' => ':root', 'choice'=> 'font-size', 'property'=>'--bs-body-font-size' ],
// 	   // email
// 	   [ 'element' => '#email', 'choice'=> 'font-family', 'property'=>'font-family' ],
// 	   [ 'element' => '#email', 'choice'=> 'line-height', 'property'=>'line-height' ],
// 	   [ 'element' => '#email', 'choice'=> 'font-size',   'property'=>'font-size' ],
// 	],
// ) );

// \Kirki::add_field( $this->config_id, array(
// 	'type'        => 'text',
// 	'settings'    => "{$section_id}_font_url",
// 	'label'       => ff__( 'Font URL' ),
// 	'section'     => $section_id,
// 	'priority'    => 10,
// ) );

// \Kirki::add_field( $this->config_id, array(
// 	'type'        => 'text',
// 	'settings'    => "{$section_id}_font_name",
// 	'label'       => ff__( 'Font URL Name' ),
// 	'section'     => $section_id,
// 	'priority'    => 10,
// ) );

// Body Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_font_family",
	'label'       => ff__( 'Body Font Family' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_body" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Body Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_font_weight",
	'label'       => ff__( 'Body Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_body" )['variant'] ?? 'regular',
	'priority'    => 10,
) );

// Body Font Size
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_font_size",
	'label'       => ff__( 'Body Font Size' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_body" )['font-size'] ?? '1rem',
	'priority'    => 10,
) );

// Body Line Height
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_line_height",
	'label'       => ff__( 'Body Line Height' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_body" )['line-height'] ?? '1.5',
	'priority'    => 10,
) );

// Body Letter Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_letter_spacing",
	'label'       => ff__( 'Body Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_body" )['letter-spacing'] ?? '0',
	'priority'    => 10,
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
// \Kirki::add_field( $this->config_id, array(
// 	'type'     	=> 'typography',
// 	'settings'  	=> "{$section_id}_{$group_id}_header",
// 	'label'     	=> ff__( 'Header Font' ),
// 	'section'		=> $section_id,
// 	'default'		=> [
// 	   'font-family'    => 'inherit',
// 	   'variant'        => 'regular',
// 	   // 'font-size'      => '24px',
// 	   // 'line-height'    => '1.5',
// 	   'letter-spacing' => '0',
// 	   'text-transform' => 'none',
// 	   // 'color'          => '#333333',
// 	   // 'text-align'     => 'left',
// 	],
// 	'choices'		=> $font_choice,
// 	'output'		=> [
// 	   [ 'element' => '.site-header' ],
// 	],
// 	'required' 	=> [
// 		[
// 			'setting'	=> 'buzz_header_type',
// 			'value'		=> 'text',
// 			'operator'	=> '=',
// 		]
// 	]
// ) );

// Header Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_header_font_family",
	'label'       => ff__( 'Header Font Family' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_header" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Header Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_header_font_weight",
	'label'       => ff__( 'Header Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_header" )['variant'] ?? 'regular',
	'priority'    => 10,
) );

// Header Letter Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_header_letter_spacing",
	'label'       => ff__( 'Header Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_header" )['letter-spacing'] ?? '0',
	'priority'    => 10,
) );

// Header Text Transform
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_header_text_transform",
	'label'       => ff__( 'Header Text Transform' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_header" )['text-transform'] ?? 'none',
	'priority'    => 10,
) );

// Navbar Text
// \Kirki::add_field( $this->config_id, array(
// 	'type'     	=> 'typography',
// 	'settings'  	=> "{$section_id}_{$group_id}_navbar",
// 	'label'     	=> ff__( 'Navbar Font' ),
// 	'section'		=> $section_id,
// 	'default'		=> [
// 	   'font-family'    => 'inherit',
// 	   'variant'        => 'regular',
// 	   'font-size'      => '1rem',
// 	   // 'line-height'    => '1.5',
// 	   'letter-spacing' => '0',
// 	   'text-transform' => 'none',
// 	   // 'color'          => '#333333',
// 	   // 'text-align'     => 'left',
// 	],
// 	'choices'		=> $font_choice,
// 	'output'		=> [
// 	   [ 'element' => '#primary-navigation' ],
// 	   [ 'element' => '#primary-navigation A' ],
// 	   [ 'element' => '#primary-navigation INPUT' ],
// 	],
// ) );

// Navbar Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_navbar_font_family",
	'label'       => ff__( 'Navbar Font Family' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Navbar Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_navbar_font_weight",
	'label'       => ff__( 'Navbar Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['variant'] ?? 'regular',
	'priority'    => 10,
) );

// Navbar Font Size
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_navbar_font_size",
	'label'       => ff__( 'Navbar Font Size' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['font-size'] ?? '1rem',
	'priority'    => 10,
) );

// Navbar Letter Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_navbar_letter_spacing",
	'label'       => ff__( 'Navbar Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['letter-spacing'] ?? '1rem',
	'priority'    => 10,
) );

// Navbar Text Transform
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_navbar_text_transform",
	'label'       => ff__( 'Navbar Text Transform' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['text-transform'] ?? 'none',
	'priority'    => 10,
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

// Heading Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_heading_font_family",
	'label'       => ff__( 'Heading Font Family' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Heading Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_heading_font_weight",
	'label'       => ff__( 'Heading Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['variant'] ?? 'regular',
	'priority'    => 10,
) );

// Heading Line Height
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_heading_line_height",
	'label'       => ff__( 'Heading Line Height' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['line-height'] ?? 'none',
	'priority'    => 10,
) );

// Highlight Text
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

 // Highlight Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_highlight_font_family",
	'label'       => ff__( 'Highlight Font Family' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

 // Highlight Font Weight
 \Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_highlight_font_weight",
	'label'       => ff__( 'Highlight Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['variant'] ?? '300',
	'priority'    => 10,
) );

 // Highlight Letter Spacing
 \Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_highlight_letter_spacing",
	'label'       => ff__( 'Highlight Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['letter-spacing'] ?? '0',
	'priority'    => 10,
) );

 // Highlight Text Align
 \Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_highlight_text_align",
	'label'       => ff__( 'Highlight Text Align' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['text-align'] ?? 'left',
	'priority'    => 10,
) );


// Pull Quote Text
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
//  ) );

// Pull Quote Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_pullquote_font_family",
	'label'       => ff__( 'Pull Quote Font Family' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Pull Quote Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_pullquote_font_weight",
	'label'       => ff__( 'Pull Quote Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['variant'] ?? '300',
	'priority'    => 10,
) );

// Pull Quote Font Size
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_pullquote_font_size",
	'label'       => ff__( 'Pull Quote Font Size' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['font-size'] ?? '1rem',
	'priority'    => 10,
) );

// Pull Quote Line Height
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_pullquote_line_height",
	'label'       => ff__( 'Pull Quote Line Height' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['line-height'] ?? '1.5',
	'priority'    => 10,
) );

// Pull Quote Letter Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_pullquote_letter_spacing",
	'label'       => ff__( 'Pull Quote Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['letter-spacing'] ?? '0',
	'priority'    => 10,
) );

// Pull Quote Text Align
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_{$group_id}_pullquote_text_align",
	'label'       => ff__( 'Pull Quote Text Align' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['text-align'] ?? 'left',
	'priority'    => 10,
) );