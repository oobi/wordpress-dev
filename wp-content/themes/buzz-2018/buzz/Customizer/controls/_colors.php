<?php // Theme colours

// define transport type for colours and link underline
// 'refresh' for debugging
// $transport = 'postMessage';

/****************************************************************
 * PANEL
 ****************************************************************/

$section_id = 'buzz_colors';
\Kirki::add_section( $section_id, array(
	'title' 		=> ff__( 'Theme Colours' ),
	'description' 	=> ff__( 'Customize the colour of the newsletter elements.' ),
	'priority'		=> 80,
	'panel'			=> $this->panel_id
) );

include 'colors/global.php';
include 'colors/header.php';
include 'colors/navbar.php';
include 'colors/article-page.php';
include 'colors/index-page.php';
include 'colors/footer.php';

// Hook for add-ons to add their own customizer options
do_action( 'buzz_customizer_addons_colors', 10 );

if( $config['add-ons'] && class_exists('Buzz_Addon_Taxonomies') ) {
	include 'colors/taxonomies.php';
}