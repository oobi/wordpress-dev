<?php

/**
 * @link            http://www.fi.net.au
 * @since           1.0.2
 *
 * @wordpress-plugin
 * Plugin Name: 	Private Text Block
 * Plugin URI: 		http://www.fi.net.au
 * Description: 	Add the ability to create "private" text blocks in the visual editor which can only be seen when a user is logged in
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
  * Shortcodes for public / private content
  * [private]some text that won't show unless logged in[/private]
  * [private vis="1"]some text that will only show if NOT logged in[/private]
  */
function ff_private_block_shortcode( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'vis' 	=> FALSE // defaults to private
	), $atts );

	$force 			= is_user_logged_in() && array_key_exists('force_public', $_GET) && $_GET['force_public'] ? FALSE : TRUE;
	$vis 			= $a['vis'];
	$logged_in 		= is_user_logged_in() && $force;
	$content_ok 	= !is_null( $content ) && !is_feed();
	$private_ok   	= !$vis && $logged_in && $content_ok;
	$public_ok   	= $vis && !$logged_in && $content_ok;

	// ok to display
	if( $private_ok || $public_ok ) {
		return do_shortcode($content);
	}

	// otherwise show nothing
	return '';
}
add_shortcode( 'private', 'ff_private_block_shortcode' );

 /////////////////////////////////////////////////////////////////////////////////
 // TINYMCE HOOKS
 /////////////////////////////////////////////////////////////////////////////////

 /**
  * Register TinyMCE plugin script
  */
function ff_private_block_enqueue_plugin_scripts($plugin_array)
{
    //enqueue TinyMCE plugin script with its ID.
    $plugin_array["ff_private_block_plugin"] =  plugin_dir_url(__FILE__) . "tinymce-private-block.js";
    return $plugin_array;
}

add_filter("mce_external_plugins", "ff_private_block_enqueue_plugin_scripts");

/**
 * Register TinyMCE button
 */
function ff_private_block_register_buttons_editor($buttons)
{
    //register buttons with their id.
    array_push($buttons, "private_block");
    array_push($buttons, "public_block");
    return $buttons;
}

add_filter("mce_buttons", "ff_private_block_register_buttons_editor");


 /////////////////////////////////////////////////////////////////////////////////
 // ADMIN BAR
 /////////////////////////////////////////////////////////////////////////////////

function ff_private_block_admin_bar( $wp_admin_bar ) {
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

add_action( 'admin_bar_menu', 'ff_private_block_admin_bar', 999 );

 /////////////////////////////////////////////////////////////////////////////////
 // SHORTCODE UI
 /////////////////////////////////////////////////////////////////////////////////

/**
 * Private Block Shortcode UI
 */
/*
function ff_private_block_shortcode_ui() {

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
//add_action( 'init', 'ff_private_block_shortcode_ui' );

