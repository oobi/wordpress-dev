<?php
/**
 * Plugin Name: Firefly Frontend Dashicons
 * Plugin URI: https://fi.net.au
 * Description: Load the Dashicons icon font on the front-end of your site, with shortcodes
 * Version: 1.0.0
 * Author: Chris Carey
 * Author URI: https://fi.net.au
 * License: MIT
 */

defined( 'ABSPATH' ) or die();

function ff_dashicons_enqueue_frontend_dashicons() {
    wp_enqueue_style( 'dashicons' );
}
add_action( 'wp_enqueue_scripts', 'ff_dashicons_enqueue_frontend_dashicons' );


/**
 * Registers an editor stylesheet for the theme.
 */
function ff_dashicons_add_editor_styles() {
    add_editor_style( includes_url('/css/dashicons.min.css') );
}
add_action( 'admin_init', 'ff_dashicons_add_editor_styles' );

/**
 * Regtister a [dashicon] shortcode
 */
// [dashicon icon="format-quote" class="my-custom-class" style="color:red; font-size:20px"]
function ff_dashicons_shortcode( $atts ) {
    $a = shortcode_atts( array(
		'tag'	=> 'span',
		'icon'	=> '',
        'class' => null,
        'style' => null,
    ), $atts );

	return sprintf('<%1$s class="dashicons dashicons-%2$s %3$s" %4$s></%1$s>',
					$a['tag'],
					$a['icon'],
					$a['class'],
					$a['style'] ? 'style="' . $a['style'] . '"' : ''
				);
}
add_shortcode( 'dashicon', 'ff_dashicons_shortcode' );