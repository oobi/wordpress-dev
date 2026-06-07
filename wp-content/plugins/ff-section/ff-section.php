<?php

/**
 * @link            http://www.fi.net.au
 * @since           1.0.2
 *
 * @wordpress-plugin
 * Plugin Name: 	Section Prototype
 * Plugin URI: 		http://www.fi.net.au
 * Description: 	Add configurable sections
 * Version: 		1.0
 * Author: 			Firefly Interactive
 * Author URI: 		http://www.fi.net.au
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 */

 /////////////////////////////////////////////////////////////////////////////////
 // SHORTCODES
 /////////////////////////////////////////////////////////////////////////////////

 /**
  * Shortcode for configurable section
  * [section]some content[/section]
  * [section align="left"]some content[/section]
  * [section align="right"]some content[/section]
  */
function ff_section_shortcode( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'title' 	=> FALSE,
		'excerpt'	=> FALSE,
		'align' 	=> FALSE // defaults to no alignment
	), $atts );

	return sprintf('<section class="test-section"><div class="left"><h2>%s</h2><p>%s</p></div><div class="right">%s</div></section>', 
				$a['title'], $a['excerpt'], do_shortcode($content));
}
add_shortcode( 'section', 'ff_section_shortcode' );

 /////////////////////////////////////////////////////////////////////////////////
 // TINYMCE HOOKS
 /////////////////////////////////////////////////////////////////////////////////

 /**
  * Register TinyMCE plugin script
  */
function ff_section_enqueue_plugin_scripts($plugin_array)
{
    //enqueue TinyMCE plugin script with its ID.
    $plugin_array["ff_section_plugin"] =  plugin_dir_url(__FILE__) . "tinymce-section.js";
    return $plugin_array;
}

add_filter("mce_external_plugins", "ff_section_enqueue_plugin_scripts");

/**
 * Register TinyMCE button
 */
function ff_section_register_buttons_editor($buttons)
{
    //register buttons with their id.
    array_push($buttons, "section");
    return $buttons;
}

add_filter("mce_buttons", "ff_section_register_buttons_editor");


 /////////////////////////////////////////////////////////////////////////////////
 // ADMIN BAR
 /////////////////////////////////////////////////////////////////////////////////

function ff_section_admin_bar( $wp_admin_bar ) {
	global $post, $wp_admin_bar;

	if( $post && is_singular() ) {
		$is_public 	= array_key_exists('force_public', $_GET) && $_GET['force_public'] ? '1' : '0';

		$params = array_merge($_GET, array('force_public' => $is_public ? '0' : '1') );
		$querystring = http_build_query($params);

		$newurl = get_permalink($post) . '?' . $querystring;

		$args = array(
			'id'    => 'toggle_public_view',
			'title' => $is_public ? 'Private View' : 'Public View',
			'href'  => $newurl
		);
		$wp_admin_bar->add_node( $args );
	}
}

//add_action( 'admin_bar_menu', 'ff_section_admin_bar', 999 );

 /////////////////////////////////////////////////////////////////////////////////
 // SHORTCODE UI
 /////////////////////////////////////////////////////////////////////////////////

/**
 * Private Block Shortcode UI
 */
/*
function ff_section_shortcode_ui() {

	// abort if ShortCake not available
	if( ! function_exists( 'shortcode_ui_register_for_shortcode' ) )
		return;

	shortcode_ui_register_for_shortcode( 'private', array(
		'label'         => 'Private Block',
		'listItemImage' => 'dashicons-hidden',
		'attrs' 		=> array(
			'label'		=> 'Visibility',
			'attr'		=> 'vis',
			'type'		=> 'select',
			'options'	=> array(
				'0' 	=> 'Private',
				'1' 	=> 'Public'
			)
		),
		'inner_content'    => array(
			array(
				'label'    => 'Hidden Text',
				'attr'     => 'text',
				'type'     => 'textarea'
			)
		)
	) );
}
*/
// can't use this currently as the UI needs to be inline
// https://github.com/wp-shortcake/shortcake/issues/317
//add_action( 'init', 'ff_section_shortcode_ui' );

