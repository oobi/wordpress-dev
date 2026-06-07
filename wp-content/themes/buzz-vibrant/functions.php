<?php

/**
 * Register stylesheets and scripts for use in the theme
 */
if ( ! function_exists( 'register_my_custom_scripts' ) ) :
function register_my_custom_scripts() {
	$base = get_template_directory_uri();
	$theme= get_stylesheet_directory_uri();

	$script_version = "3.0";
	global $is_IE, $wp_query, $wp_styles;

	// Theme Specific Overrides
	wp_enqueue_style('theme_specific_css',	($theme . "/css/theme-specific.css"), array(), $script_version);

}
endif;
add_action('wp_enqueue_scripts','register_my_custom_scripts');