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

	// Theme Specific Newsletter Overrides
	if(isset($wp_query->query_vars['email'])) {
		wp_enqueue_style('theme_specific_email_css', ($theme . "/css/theme-specific-email.css"), array(), $script_version);
	}

    // OLD IE
    if($is_IE) {
    	// main feature image does not support object-fit in IE
    	$ie_css = "#main .feature-thumb IMG		{ height:auto !important; }";
    	wp_add_inline_style( 'theme_specific_css', $ie_css );
    }
}
endif;
add_action('wp_enqueue_scripts','register_my_custom_scripts');
