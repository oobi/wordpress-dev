<?php
/**
 * Implement Custom Header functionality for Firefly Newsletter Theme
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly 1.0
 */


/**
 * Define default theme colours
 */
if ( ! function_exists( 'ff_get_theme_default_css_tokens' ) ) :
function ff_get_theme_default_css_tokens() {
	$defaults = array(
		'color' => array(
			'background_color'				=> '000000',
			// theme colors
			'ff_newsletter_header_bgcolor'		=> '1ea6d8',
			'ff_newsletter_header_txtcolor'		=> 'FFFFFF',
			'ff_newsletter_menu_bgcolor'		=> '333333',
			'ff_newsletter_menu_txtcolor'		=> 'FFFFFF',
			'ff_newsletter_primary_color' 		=> '294196',
			'ff_newsletter_secondary_color' 	=> 'bababa',
			'ff_newsletter_link_color' 			=> '1ea6d8',
			'ff_social_icon_bgcolor' 			=> '1ea6d8',
			'ff_social_icon_txtcolor' 			=> 'FFFFFF',
			'ff_newsletter_widgets_bgcolor'		=> 'cfe7f3',
			'ff_newsletter_widgets_txtcolor' 	=> '333333',
			'ff_newsletter_footer_bgcolor' 		=> '59595a',
			'ff_newsletter_footer_txtcolor' 	=> '999999'
		),
		'bool' => array(
			// link underline
			'ff_newsletter_link_decoration'		=> array('underline', 'none'),
			'ff_nav_font_transform'				=> array('uppercase', 'none')
		),
		'string' => array(
			// 	ff_google_font_url // used in functions.php to embed font
			'ff_body_font'		=> "Arial, Helvetica, sans-serif",
			'ff_heading_font'	=> "'Raleway', Arial, sans-serif",
			'ff_banner_font'	=> "inherit",
			'ff_nav_font'		=> "inherit",
			'ff_nav_font'		=> "inherit"
		)
	);
	return $defaults;
}
endif;

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since Twenty Fifteen 1.0
 */
function ff_customize_preview_js() {
	wp_enqueue_script( 'ff-customize-preview', get_stylesheet_directory_uri() . '/admin/js/theme-customizer.js', array( 'customize-preview' ), '20150722', true );
}
add_action( 'customize_preview_init', 'ff_customize_preview_js' );