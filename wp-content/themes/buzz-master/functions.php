<?php

/*
if( !class_exists('FF_Newsletter') && !is_admin() ) {
	die( "Newsletter plugin must be activated for this theme." );
}
 */

/**
 * OVERRIDE ANY ff_methods HERE
 */

// include utility methods
require_once(get_template_directory() . '/admin/inc/load-custom-include.php');

// theme update code
ff_load_custom_include("/admin/inc/updater.php", true);
// bootstrap nav walker (if using header with WP menus)
ff_load_custom_include('/bootstrap/wp_bootstrap_navwalker.php', true);
// include custom CMS tools
ff_load_custom_include("/inc/utils.php", true);
// include widgets
ff_load_custom_include("/inc/widgets.php", true);
// include admin interface customisations
ff_load_custom_include("/admin/inc/admin-interface.php", false);
// include customizer setup
ff_load_custom_include("/admin/inc/customizer-setup.php", true);
// include customizer output customisations
ff_load_custom_include("/admin/inc/customizer-output.php", true);

/**
 * Only add customiser files if newsletter plugin is active
 */
if( class_exists('FF_Newsletter') ) {
	// include custom newsletter tools
	ff_load_custom_include("/inc/newsletter.php", true);
}

/**
 * Firefly functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, firefly_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * Based on Twenty Eleven
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly 1.0
 */

/** ------------------------------------------------------
 *	 T H E M E   S E T U P
 *	------------------------------------------------------ */

/**
 * The content width is reported to only effect generated image and video
 * sizes. We set it to some generic value since we endeavor to use explicit
 * sizes.
 */
if ( ! isset( $content_width ) ) :
	$content_width = 600;
endif;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override firefly_setup() in a child theme, add your own firefly_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, and Post Formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Firefly 1.0
 */

// disable emoji stuff (prints extraneous JS/CSS into header)
// we don't want it to interfere with email etc
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

if ( ! function_exists( 'ff_setup' ) ) :
function ff_setup() {
	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// name of the thumbnail, width, height, crop mode
	add_image_size( 'banner', 			1170, 366, true);
	add_image_size( 'article', 			640, 360, true);
	add_image_size( 'article-large', 	960, 540, true);
	add_image_size( 'article-alt', 		480, 530, true);
	add_image_size( 'email-banner', 	640, 200, true);
	add_image_size( 'email-featured', 	610, 343, true);
	add_image_size( 'email-article', 	290, 163, true);
	add_image_size( 'email-article-alt',183, 202, true);
	add_image_size( 'logo', 			150, 150, false);
}
endif;
add_action( 'after_setup_theme', 'ff_setup', 0 );

/**
 * Register stylesheets and scripts for use in the theme
 */
if ( ! function_exists( 'ff_register_my_scripts' ) ) :
function ff_register_my_scripts() {
	global $is_IE, $wp_query, $wp_styles;
	$base = get_template_directory_uri();
	$theme = get_stylesheet_directory_uri();
	$script_version = "3.0.2";

	if( !is_admin() ) {

		 // Custom fonts
		 if(! get_theme_mod('ff_google_font_url')) {
			wp_enqueue_style('custom_font_css', 'http://fonts.googleapis.com/css?family=Raleway:400,500,600');
		} else {
			$font_setting = trim(get_theme_mod('ff_google_font_url'));
			
			// filter URL - select everything starting with http(s):// and terminating either at end of string or first instance of a quote or parenthesis
			// any of the following formats would be acceptable:
			// e.g. @import url(https://fonts.googleapis.com/css?family=Open+Sans+Condensed) or 
			//      @import url('https://fonts.googleapis.com/css?family=Open+Sans+Condensed')
			//      https://fonts.googleapis.com/css?family=Open+Sans+Condensed
			$font_match = preg_match_all('/https?:\/\/[^\'\")]+/im', $font_setting, $matches );

			if($font_match) {
				foreach($matches[0] as $index => $url) {
					wp_enqueue_style("custom_font_css", $url);

				}
			}
		}

		// newsletter email contains ONE sheet and no scripts
		if(isset($wp_query->query_vars['email'])) {
			wp_enqueue_style('newsletter_email_css', ($base . "/css/newsletter-email.css"), array(), $script_version);
		}
		// otherwise do a normal layout
		else {

		    // Bootstrap
	        wp_enqueue_style('bootstrap_css', ($base . "/bootstrap/css/bootstrap.min.css"), array(), $script_version, 'screen');
	        wp_enqueue_script('bootstrap_js', ($base . "/bootstrap/js/bootstrap.min.js"), array('jquery'), $script_version, true);

	        wp_enqueue_style('fontawesome_css', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
	        wp_enqueue_style('main_css',    ($base . "/css/styles.css"), array(), $script_version);
	        wp_enqueue_style('content_css', ($base . "/css/content.css"), array(), $script_version);

	        // Scripts
	        wp_enqueue_script('init_js', ($base . "/js/init.js"), array('jquery'), $script_version, true);

	        // OLD IE
	        if($is_IE) {
				// Placeholders
	       		wp_enqueue_script('placeholders_js', ($base . "/js/placeholders.min.js"), array(), $script_version, true);

	            wp_enqueue_style( 'styles_ie_common', ($base . "/css/ie.css"), array( 'main_css' ) );
	            $wp_styles->add_data( 'styles_ie_common', 'conditional', 'lte IE 8' );
	            // respond JS for (old) IE
	            wp_enqueue_script('respond_js', ($base . "/js/respond.min.js"), NULL, $script_version, false); // this should go in header
	        }

	        // Newletter print
	        if(isset($wp_query->query_vars['print'])) {
                wp_enqueue_style('newsletter_print_css', ($base . "/css/newsletter-print.css"), array('main_css'), $script_version);
                //wp_enqueue_style('newsletter_print_colours_css', ($theme . "/~tmp/custom.css"), array('main_css'), $script_version);
	        }

			// Print CSS
			wp_enqueue_style('print_css', ($base . "/css/newsletter-print.css"), array('main_css'), $script_version, 'print');

		} // end check for email newsletter form

	}
}
endif;
add_action('wp_enqueue_scripts','ff_register_my_scripts', 5);

/**
 * Register stylesheets and scripts for use in the WP dashboard
 */
if ( ! function_exists( 'register_my_admin_scripts' ) ) :
function register_my_admin_scripts() {
	global $is_IE;
	$base = get_template_directory_uri();
	$script_version = "1.0";

	wp_enqueue_style('admin_css', ($base . "/css/admin.css"), array(), $script_version);
}
endif;
add_action('admin_enqueue_scripts','register_my_admin_scripts');

if ( ! function_exists( 'register_my_editor_styles' ) ) :
function register_my_editor_styles() {
	$base = get_template_directory_uri();
	$theme = get_stylesheet_directory_uri();

	// add content css
	add_editor_style($base . '/css/content.css');

	// font awesome
	add_editor_style('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');

	// Custom fonts
	if(! get_theme_mod('ff_google_font_url')) {
		add_editor_style('http://fonts.googleapis.com/css?family=Raleway:400,500,600');
	} else {
		$font_setting = trim(get_theme_mod('ff_google_font_url'));
		$font_match = preg_match_all('/@import (url\(\"?)?(url\()?(\")?(.*?)(?(1)\")+(?(2)\))+(?(3)\")/im', $font_setting, $matches );

		if($font_match && count($matches) >= 5) {
			foreach($matches[4] as $index => $url) {
				add_editor_style( $url );

			}
		}
	}

	// Customiser CSS
	add_editor_style( WP_CONTENT_URL . '/cache/buzz/custom-' . get_current_blog_id() . '.css' );

}
endif;
add_action( 'admin_init', 'register_my_editor_styles' );



/** ------------------------------------------------------
 *	 F I L T E R S
 *	------------------------------------------------------ */

add_filter('image_size_names_choose', 'ff_image_sizes');
function ff_image_sizes($sizes) {
	$addsizes = array(
		"logo" => __( "Logo 150")
	);
	$newsizes = array_merge($sizes, $addsizes);
	return $newsizes;
}

/**
 * Sets the post excerpt length to 40 words.
 *
 * To override add another filter with a higher priority.
 */
if( ! function_exists('ff_excerpt_length')) :
function ff_excerpt_length( $length ) {
	return 40;
}
endif;
add_filter( 'excerpt_length', 'ff_excerpt_length' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and ff_continue_reading_link().
 *
 */
if ( ! function_exists( 'ff_auto_excerpt_more' ) ) :
function ff_auto_excerpt_more( $more ) {
	$text 		= apply_filters('ff_continue_reading_text', 'read more');
	$more_link 	= ' <a class="more" href="'. esc_url( get_permalink() ) . '">' . $text . '</a>';

	return ' &hellip;' . $more_link;
}
endif;
add_filter( 'excerpt_more', 'ff_auto_excerpt_more' );

/**
 * add PDF filter to media manager
 */
if ( ! function_exists( 'ff_modify_post_mime_types' ) ) :
function ff_modify_post_mime_types( $post_mime_types ) {
	// select the mime type, here: 'application/pdf'
	// then we define an array with the label values
	$post_mime_types['application/pdf'] = array( __( 'PDFs' ), __( 'Manage PDFs' ), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' ) );

	// then we return the $post_mime_types variable
	return $post_mime_types;
}
endif;
add_filter('post_mime_types', 'ff_modify_post_mime_types');

/**
 * Convert gravity forms field markup to Bootstrap form fields
 */
if ( ! function_exists( 'ff_convert_gforms_fields_to_bootstrap' ) ) :
function ff_convert_gforms_fields_to_bootstrap( $content, $field, $value, $lead_id, $form_id ) {

    if($field["type"] != 'hidden' && $field["type"] != 'list' && $field["type"] != 'multiselect' && $field["type"] != 'checkbox' && $field["type"] != 'fileupload' && $field["type"] != 'date' && $field["type"] != 'html' && $field["type"] != 'address') {
        $content = str_replace('class=\'medium', 'class=\'form-control medium', $content);
    }

    if($field["type"] == 'name' || $field["type"] == 'address') {
        $content = str_replace('<input ', '<input class=\'form-control\' ', $content);
    }

    if($field["type"] == 'textarea') {
        $content = str_replace('class=\'textarea', 'class=\'form-control textarea', $content);
    }

    if($field["type"] == 'checkbox') {
        $content = str_replace('li class=\'', 'li class=\'checkbox ', $content);
        $content = str_replace('<input ', '<input style=\'margin-left:1px;\' ', $content);
    }

    if($field["type"] == 'radio') {
        $content = str_replace('li class=\'', 'li class=\'radio ', $content);
        $content = str_replace('<input ', '<input style=\'margin-left:1px; margin-top:3px;\' ', $content);
    }

	return $content;
}
endif;
add_filter("gform_field_content", "ff_convert_gforms_fields_to_bootstrap", 10, 5);

/**
 * Convert gravity forms submit button markup to Bootstrap button
 */
if ( ! function_exists( 'ff_convert_gforms_button_to_bootstrap' ) ) :
function ff_convert_gforms_button_to_bootstrap( $button, $form ) {
    $button = str_replace('class=\'', 'class=\'btn btn-default ', $button);
	return $button;
}
endif;
add_filter( 'gform_submit_button', 'ff_convert_gforms_button_to_bootstrap', 10, 2 );

/** ------------------------------------------------------
 *	 T I N Y  M C E
 *	------------------------------------------------------ */

if( ! function_exists('ff_tinymce_settings')) :
function ff_tinymce_settings( $init ) {

	$init['preview_styles'] = 'font-family font-size font-weight font-style text-decoration text-transform color background background-color';

	//theme_advanced_blockformats seems deprecated - instead the hook from Helgas post did the trick
	$init['block_formats'] = "Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6";

	//$init['style_formats']  doesn't work - instead you have to use tinymce style selectors
	$style_formats = array(
		/* TEXT STYLES */
		array(
			'title' => 'Highlight',
			'block' => 'div',
			'classes' => 'highlight',
			'wrapper' => true
		),
		array(
			'title' => 'Subdued',
			'selector' => 'p',
			'classes' => 'subdued',
			'wrapper' => true
		),
		array(
			'title' => 'Pull Quote',
			'block' => 'blockquote',
			'classes' => 'pullquote',
			'wrapper' => true
		),
		array(
			'title' => 'Table Banded',
			'selector' => 'table',
			'classes' => 'table-banded',
			'wrapper' => true
		),
		array(
			'title' => 'Table Standard',
			'selector' => 'table',
			'classes' => 'table-standard',
			'wrapper' => true
		)
	);

	// Merge old & new styles
	$settings['style_formats_merge'] = true;

	$init['style_formats'] = json_encode( $style_formats );
	//$init['statusbar'] = false;
	return $init;
}
endif;
add_filter('tiny_mce_before_init', 'ff_tinymce_settings');



