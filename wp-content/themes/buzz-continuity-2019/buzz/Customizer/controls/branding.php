<?php // Header

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_branding';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Branding' ),
	'description' 	=> ff__( 'Customize elements of the newsletter branding.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );

/****************************************************************
 * LOGO
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_1",
	'label'       => ff__( 'Global' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

// Site Logo
\Kirki::add_field( $this->config_id, array(
	'type'      => 'image',
	'settings'  => "${section_id}_site_logo",
	'label'     => ff__( 'Site Logo' ),
	'description' => ff__( 'Logo image must be 192 x 192 pixels in size. Used in Mobile Menu, Site Icon, Login page and app icon on Android devices. Supports transparency.' ),
	'section'   => $section_id,
	'default'	=> '',
	'choices'   => [
		'save_as' => 'id',
	],
) );

// Home Screen Logo
\Kirki::add_field( $this->config_id, array(
	'type'      => 'image',
	'settings'  => "${section_id}_home_screen_logo",
	'label'     => ff__( 'iOS Home Screen Logo' ),
	'description' => ff__( 'Logo image must be 180 x 180 pixels in size. Used as an app icon on iOS devices. Does NOT support transparency.' ),
	'section'   => $section_id,
	'default'	=> '',
	'choices'   => [
		'save_as' => 'id',
	],
) );

/****************************************************************
 * FIELD SWITCH
 ****************************************************************/

// Info Label
\Kirki::add_field( $this->config_id, array(
	'type'        => 'custom',
	'settings'    => "${section_id}_info_2",
	'label'       => ff__( 'Header' ),
	'section'     => $section_id,
	'priority'    => 10,
) );

 // Switch between logo/text header and image header
$conditonal_switch = $section_id . '_header_type';
\Kirki::add_field( $this->config_id, array(
	'type'      => 'radio-buttonset',
	'settings'  => $conditonal_switch,
	'label'     => ff__( 'Header type' ),
	'section'	=> $section_id,
	'default'	=> 'text',
	'priority'	=> 10,
	// 'transport' => 'postMessage',
	'choices'	=> [
		'text'		=> ff__( 'Text/Logo Header' ),
		'image'		=> ff__( 'Image Header' ),
	]
) );

/****************************************************************
 * TEXT/LOGO HEADER
 ****************************************************************/

\Kirki::add_field( $this->config_id, array(
	'type'      => 'image',
	'settings'  => "${section_id}_header_text_logo",
	'label'     => ff__( 'Header Logo' ),
	'description' => ff__( 'Logo image should be no more than 150px tall (any width)' ),
	'section'   => $section_id,
	'default'	=> '',
	'choices'   => [
		'save_as' => 'id',
	],
	'required' 	=> [
		[
			'setting'	=> $conditonal_switch,
			'value'		=> 'text',
			'operator'	=> '=',
		]
	]
) );

\Kirki::add_field( $this->config_id, array(
	'type'      => 'text',
	'settings'  => "${section_id}_header_text_title",
	'label'     => ff__( 'Header Title' ),
	'section'   => $section_id,
	'default'	=> get_bloginfo('name'),
	'required' 	=> [
		[
			'setting'	=> $conditonal_switch,
			'value'		=> 'text',
			'operator'	=> '=',
		]
	]
) );

\Kirki::add_field( $this->config_id, array(
	'type'      => 'text',
	'settings'  => "${section_id}_header_text_subtitle",
	'label'     => ff__( 'Header Subtitle' ),
	'section'   => $section_id,
	'default'	=> get_bloginfo('description'),
	'required' 	=> [
		[
			'setting'	=> $conditonal_switch,
			'value'		=> 'text',
			'operator'	=> '=',
		]
	]
) );

/****************************************************************
 * IMAGE HEADER
 ****************************************************************/

\Kirki::add_field( $this->config_id, array(
	'type'      => 'image',
	'settings'  => "${section_id}_header_image_large",
	'label'     => ff__( 'Header Image (Large Screens)' ),
	'description' => ff__( 'Recommended size: <strong>1170px x 120px</strong>' ),
	'section'   => $section_id,
	'choices'   => [
		'save_as' => 'id',
	],
	'required' 	=> [
		[
			'setting'	=> $conditonal_switch,
			'value'		=> 'image',
			'operator'	=> '=',
		]
	]
) );

\Kirki::add_field( $this->config_id, array(
	'type'      => 'image',
	'settings'  => "${section_id}_header_image_small",
	'label'     => ff__( 'Header Image (Small Screens)' ),
	'description' => ff__( 'Recommended size: <strong>720px x 120px</strong>' ),
	'section'   => $section_id,
	'choices'   => [
		'save_as' => 'id',
	],
	'required' 	=> [
		[
			'setting'	=> $conditonal_switch,
			'value'		=> 'image',
			'operator'	=> '=',
		]
	]
) );
