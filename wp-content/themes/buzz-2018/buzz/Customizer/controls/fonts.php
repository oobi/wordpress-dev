<?php
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

/****************************************************************
 * FONT URLS
 ****************************************************************/

 \Kirki::add_field($this->config_id, [
	'type'        => 'repeater',
	'settings' 		=> "{$section_id}_urls",
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

	// Info Label
	\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_info_1",
	'label'       => ff__( 'Content' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// // Body Text
// \Kirki::add_field( $this->config_id, array(
// 	'type'     	=> 'typography',
// 	'settings'  	=> "{$section_id}_body",
// 	'label'     	=> ff__( 'Body Font' ),
// 	'section'		=> $section_id,
// 	'default'		=> [
// 	   'font-family'    => 'Open Sans',
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
// 	   [ 'element' => 'body' ]
// 	],
// ) );

// BODY FONT GROUP
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "{$section_id}_label_1",
	'label'       => ff__( 'Body Font' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Body Font URL
// \Kirki::add_field( $this->config_id, array(
// 	'type'        => 'text',
// 	'settings'    => "{$section_id}_body_font_url",
// 	'label'       => ff__( 'Google Fonts URL' ),
// 	'section'     => $section_id,
// 	'priority'    => 10,
// ) );

// Body Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_font_family",
	'label'       => ff__( 'Font Family' ),
	'section'     => $section_id,
	'default' 	  => Kirki::get_option( 'buzz_customizer', 'buzz_font_body' )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Body Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_font_variant",
	'label'       => ff__( 'Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', 'buzz_font_body' )['variant'] ?? 'regular',
	'priority'    => 10,
) );

// Body Font Size
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_font_size",
	'label'       => ff__( 'Font Size' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', 'buzz_font_body' )['font-size'] ?? '1rem',
	'priority'    => 10,
) );

// Body Font Line Height
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_font_line-height",
	'label'       => ff__( 'Line Height' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', 'buzz_font_body' )['line-height'] ?? '1.5',
	'priority'    => 10,
) );

// Body Font Letter Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_body_font_letter-spacing",
	'label'       => ff__( 'Letter Spacing' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', 'buzz_font_body' )['letter-spacing'] ?? '0',
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

// Header font (font field) - TO REMOVE
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

// END TO REMOVE

// Header font (no font field)
// Header Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_header_font_family",
	'label'       => ff__( 'Header Font Family' ),
	'section'     => $section_id,
	'default' 	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_header" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Header Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_header_font_weight",
	'label'       => ff__( 'Header Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_header" )['variant'] ?? '300',
	'priority'    => 10,
) );

// Header Font Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_header_font_letter_spacing",
	'label'       => ff__( 'Header Font Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_header" )['letter-spacing'] ?? '0px',
	'priority'    => 10,
) );

// Header Font Transform
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_header_font_transform",
	'label'       => ff__( 'Header Font Text Transform' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_header" )['text-transform'] ?? 'none',
	'priority'    => 10,
) );


// Navbar Font (font field) - TO REMOVE
// \Kirki::add_field( $this->config_id, array(
// 	'type'     	=> 'typography',
// 	'settings'  	=> "{$section_id}_{$group_id}_navbar",
// 	'label'     	=> ff__( 'Navbar Font' ),
// 	'section'		=> $section_id,
// 	'default'		=> [
// 	   'font-family'    => 'inherit',
// 	   'variant'        => 'regular',
// 	   'font-size'      => '0.9rem',
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

// END TO REMOVE

// Navbar font (no font field)
// Navbar Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_navbar_font_family",
	'label'       => ff__( 'Navbar Font Family' ),
	'section'     => $section_id,
	'default' 	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Navbar Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_navbar_font_weight",
	'label'       => ff__( 'Navbar Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['variant'] ?? '300',
	'priority'    => 10,
) );

// Navbar Font Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_navbar_font_letter_size",
	'label'       => ff__( 'Navbar Font Size' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['font-size'] ?? '0.9rem',
	'priority'    => 10,
) );

// Navbar Font Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_navbar_font_letter_spacing",
	'label'       => ff__( 'Navbar Font Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['letter-spacing'] ?? '0px',
	'priority'    => 10,
) );

// Body Font Transform
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_navbar_font_transform",
	'label'       => ff__( 'Navbar Font Text Transform' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_navbar" )['text-transform'] ?? 'none',
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

// Heading Text - TO REMOVE
// \Kirki::add_field( $this->config_id, array(
// 	'type'     	=> 'typography',
// 	'settings'  	=> "{$section_id}_{$group_id}_heading",
// 	'label'     	=> ff__( 'Heading Font' ),
// 	'description' 	=> ff__( 'Affects all heading tags (h1 to h6)' ),
// 	'section'		=> $section_id,
// 	'default'		=> [
// 	   'font-family'    => 'inherit',
// 	   'variant'        => 'regular',
// 	   // 'font-size'      => '24px',
// 	   'line-height'    => '1.5',
// 	   'letter-spacing' => '0',
// 	   'text-transform' => 'none',
// 	   // 'color'          => '#333333',
// 	   'text-align'     => 'initial',
// 	],
// 	'choices'		=> $font_choice,
// 	'output'		=> [
// 	   [ 'element' => 'h1' ],
// 	   [ 'element' => 'h2' ],
// 	   [ 'element' => 'h3' ],
// 	   [ 'element' => 'h4' ],
// 	   [ 'element' => 'h5' ],
// 	   [ 'element' => 'h6' ],
// 	],
// ) );

// END TO REMOVE

// Heading font (no font field)

// Heading Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_heading_family",
	'label'       => ff__( 'Heading Font Family' ),
	'section'     => $section_id,
	'default' 	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Heading Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_heading_weight",
	'label'       => ff__( 'Heading Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['variant'] ?? '300',
	'priority'    => 10,
) );

// Heading Line Height
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_heading_line_height",
	'label'       => ff__( 'Heading Line Height' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['line-height'] ?? '1.5',
	'priority'    => 10,
) );

// Heading Letter Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_heading_letter_spacing",
	'label'       => ff__( 'Heading Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['letter-spacing'] ?? '0px',
	'priority'    => 10,
) );

// Heading Text Align
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_heading_text_align",
	'label'       => ff__( 'Heading Text Align' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['text-align'] ?? 'initial',
	'priority'    => 10,
) );

// Heading Text Transform
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_heading_text_transform",
	'label'       => ff__( 'Heading Text Transform' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', "{$section_id}_{$group_id}_heading" )['text-transform'] ?? 'none',
	'priority'    => 10,
) );


// Highlight Text - TO REMOVE
//  \Kirki::add_field( $this->config_id, array(
// 	 'type'     	=> 'typography',
// 	 'settings'  	=> "{$section_id}_highlight",
// 	 'label'     	=> ff__( 'Highlight Font' ),
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
// 		[ 'element' => '.content-area .highlight' ],
// 	 ],
//  ) );

 // END TO REMOVE

 // Highlight (no font field)

 // Highlight Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_highlight_family",
	'label'       => ff__( 'Highlight Font Family' ),
	'section'     => $section_id,
	'default' 	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Highlight Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_highlight_weight",
	'label'       => ff__( 'Highlight Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['variant'] ?? '300',
	'priority'    => 10,
) );

// Highlight Font Size
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_highlight_size",
	'label'       => ff__( 'Highlight Font Size' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['font-size'] ?? '1rem',
	'priority'    => 10,
) );

// Highlight Line Height
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_highlight_line_height",
	'label'       => ff__( 'Highlight Line Height' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['line-height'] ?? '1.5',
	'priority'    => 10,
) );

// Highlight Letter Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_highlight_letter_spacing",
	'label'       => ff__( 'Highlight Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['letter-spacing'] ?? '0px',
	'priority'    => 10,
) );

// Highlight Text Align
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_highlight_text_align",
	'label'       => ff__( 'Highlight Text Align' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', "{$section_id}_highlight" )['text-align'] ?? 'initial',
	'priority'    => 10,
) );



// Pull Quote Text - TO REMOVE
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

 // END TO REMOVE

  // Pull Quote (no font field)
// Pull Quote Font Family
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_pullquote_family",
	'label'       => ff__( 'Pull Quote Font Family' ),
	'section'     => $section_id,
	'default' 	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['font-family'] ?? 'sans-serif',
	'priority'    => 10,
) );

// Pull Quote Font Weight
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_pullquote_weight",
	'label'       => ff__( 'Pull Quote Font Weight' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['variant'] ?? '300',
	'priority'    => 10,
) );

// Pull Quote Font Size
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_pullquote_size",
	'label'       => ff__( 'Pull Quote Font Size' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['font-size'] ?? '1rem',
	'priority'    => 10,
) );

// Pull Quote Line Height
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_pullquote_line_height",
	'label'       => ff__( 'Pull Quote Line Height' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['line-height'] ?? '1.5',
	'priority'    => 10,
) );

// Pull Quote Letter Spacing
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_pullquote_letter_spacing",
	'label'       => ff__( 'Pull Quote Letter Spacing' ),
	'section'     => $section_id,
	'default'	  => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['letter-spacing'] ?? '0px',
	'priority'    => 10,
) );

// Pull Quote Text Align
\Kirki::add_field( $this->config_id, array(
	'type'        => 'text',
	'settings'    => "{$section_id}_pullquote_text_align",
	'label'       => ff__( 'Pull Quote Text Align' ),
	'section'     => $section_id,
	'default'     => Kirki::get_option( 'buzz_customizer', "{$section_id}_pullquote" )['text-align'] ?? 'initial',
	'priority'    => 10,
) );