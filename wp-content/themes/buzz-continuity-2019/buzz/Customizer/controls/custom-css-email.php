<?php // Custom CSS - Email View

/****************************************************************
 * SECTION
 ****************************************************************/

$section_id = 'buzz_custom_css_email';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Custom CSS (Email only)' ),
	// 'description' 	=> ff__( 'Customize the elements of the email view.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );


/****************************************************************
 * ADDITIONAL CSS
 ****************************************************************/

\Kirki::add_field( $this->config_id, array(
	'type'      => 'code',
	'settings'  => "${section_id}_code",
	// 'label'     => ff__( 'Additional CSS' ),
	// 'description' => ff__( 'CSS injected into the Email View' ),
	'section'   => $section_id,
	'default'	=> '',
	'choices' 	=> [
		'language' => 'css',
	]
) );
