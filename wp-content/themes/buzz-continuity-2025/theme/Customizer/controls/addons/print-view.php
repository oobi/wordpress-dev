<?php // Print View

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_print';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Print View' ),
	'description' 	=> ff__( 'Customize the elements of the print view.' ),
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
	'description' => ff__( 'Recommended size: <strong>760 x 120px</strong>' ),
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
