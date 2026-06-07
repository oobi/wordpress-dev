<?php

/**
 * OVERRIDE ANY ff_methods HERE
 */

// include utility methods
require_once(get_template_directory() . '/inc/load_custom_include.php');

// include custom CMS tools
ff_load_custom_include("/inc/utils.php", true);
// include widgets
ff_load_custom_include("/inc/widgets.php", true);
// include admin interface customisations
ff_load_custom_include("/inc/admin_interface.php", false);

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
	$content_width = 660;
endif;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
if ( ! function_exists( 'ff_setup' ) ) :
function ff_setup() {
	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu().
	// register_nav_menu( 'primary', 		'Main Menu' );
	register_nav_menu( 'quicklinks', 	'Quicklinks Menu' );

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// name of the thumbnail, width, height, crop mode
	add_image_size( 'home-hero',	1200, 600, true );
	add_image_size( 'hero', 	 	1000, 400, true );
	add_image_size( 'portal-link',	365, 295, true );
	add_image_size( 'landing', 	 	380, 300, true );
}
endif;
add_action( 'after_setup_theme', 'ff_setup', 0 );

/**
 * Register stylesheets and scripts for use in the theme
 */
if ( ! function_exists( 'register_my_scripts' ) ) :
function register_my_scripts() {
	$base = get_template_directory_uri();
	$theme = get_stylesheet_directory_uri();
	$script_version = "1.0";
	global $is_IE, $wp_styles;
	//$wp_styles = $GLOBALS['wp_styles'];

	if( !is_admin() ) {

		// Bootstrap
		wp_enqueue_style('bootstrap_css', ($base . "/bootstrap/css/bootstrap.min.css"), array(), $script_version);
		wp_enqueue_script('bootstrap_js', ($base . "/bootstrap/js/bootstrap.min.js"), array('jquery'), $script_version, true);

		// Styles
		wp_enqueue_style('main_css', ($base . "/css/styles.css"), array(), $script_version);
		wp_enqueue_style('content_css', ($base . "/css/content.css"), array(), $script_version);

		// custom fonts
		wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
		wp_enqueue_style('google_fonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600');

		// home page
		if( is_front_page() ) {
			// styles/scripts
			wp_enqueue_style('home_css', ($base . "/css/home.css"), array(), $script_version);
		}

		// portal page
		if( is_page_template( 'page-portal.php' ) ) {
			// styles/scripts
			wp_enqueue_style('portal_css', ($base . "/css/portal.css"), array(), $script_version);
		}

		// JS
		wp_enqueue_script( 'init_js', $base . '/js/init.js', array('jquery'), '1.0' );

		// OLD IE
	    if($is_IE) {
			// Placeholders
	   		wp_enqueue_script('placeholders_js', ($base . "/js/placeholders.min.js"), array(), $script_version, true);

	        wp_enqueue_style( 'styles_ie_common', ($base . "/css/ie-old.css"), array( 'main_css' ) );
	        $wp_styles->add_data( 'styles_ie_common', 'conditional', 'lte IE 8' );
	        // respond JS for (old) IE
	        wp_enqueue_script('respond_js', ($base . "/js/respond.min.js"), NULL, $script_version, false); // this should go in header
	    }
	}
}
endif;
add_action('wp_enqueue_scripts','register_my_scripts');

if ( ! function_exists( 'register_my_editor_styles' ) ) :
function register_my_editor_styles() {
	$base = ff_get_theme_directory();

	// Custom fonts
	add_editor_style('https://fonts.googleapis.com/css?family=Source+Sans+Pro');

	// content css
	add_editor_style($base . "/css/content.css");
}
endif;
add_action( 'admin_init', 'register_my_editor_styles' );

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
    if($field["type"] != 'hidden'
    && $field["type"] != 'list'
    && $field["type"] != 'multiselect'
    && $field["type"] != 'checkbox'
    && $field["type"] != 'fileupload'
    && $field["type"] != 'date'
    && $field["type"] != 'html'
    && $field["type"] != 'address') {
        $content = str_replace('class=\'medium', 'class=\'form-control medium', $content);
    }

    if($field["type"] == 'name'
    	|| $field["type"] == 'address'
    	|| $field["type"] == 'email'
		|| $field["type"] == 'text') {
		$content = preg_replace('/<input([^>]+class=\')/', '<input\1 form-control ', $content);
    }

    if($field["type"] == 'textarea') {
    	$content = preg_replace('/<textarea([^>]+class=\')/', '<textarea\1 form-control ', $content);
    }

    if($field["type"] == 'checkbox') {
        $content = str_replace('li class=\'', 'li class=\'checkbox ', $content);
        $content = str_replace('<input ', '<input style=\'margin-left:1px;\' ', $content);
    }

    if($field["type"] == 'radio') {
        $content = str_replace('li class=\'', 'li class=\'radio ', $content);
        $content = str_replace('<input ', '<input style=\'margin-left:1px; margin-top:3px;\' ', $content);
    }

	if($field["type"] == 'select' || $field["type"] == 'multiselect') {
		$content = preg_replace('/<select([^>]+class=\')/', '<select\1 form-control ', $content);
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
		),
		array(
			'title' => 'Link Button',
			'selector' => 'a',
			'classes' => 'btn btn-cta'
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

/** ------------------------------------------------------
 *	 Theme utils
 *	------------------------------------------------------ */

if( ! function_exists('ff_social_links')) :
function ff_social_links() {
 	echo '<a href="#"><i class="fa fa-facebook-square"></i></a>';
	echo '<a href="#"><i class="fa fa-linkedin-square"></i></a>';
	echo '<a href="#"><i class="fa fa-twitter-square"></i></a>';
	echo '<a href="#"><i class="fa fa-instagram"></i></a>';
 }
endif;

/**
 * hide admin bar for subscriber group
 */
function remove_admin_bar() {
    if ( ! current_user_can('edit_posts') ) {
		add_filter('show_admin_bar', '__return_false');
	}
}
add_action('after_setup_theme', 'remove_admin_bar');

/**
 * Add categories and tags to pages
 */
function add_taxonomies_to_pages() {
	register_taxonomy_for_object_type( 'post_tag', 'page' );
	register_taxonomy_for_object_type( 'category', 'page' );
}
add_action( 'init', 'add_taxonomies_to_pages' );

/**
 * Adds args query string argument to embedded YouTube videos using wp_ombed_get
 */
function ff_oembed_result($html, $url, $args) {
	if(array_key_exists('force_no_rel', $args) && $args['force_no_rel']) {
		$html = str_replace( '?feature=oembed', '?feature=oembed&rel=0', $html );
	}
	return $html;
}
add_filter( 'oembed_result', 'ff_oembed_result', 10, 3);


/**
 * Default media to link to file rather than attachment page
 */
function ff_gallery_default_type_set_link( $settings ) {
    $settings['galleryDefaults']['link'] = 'file';
    return $settings;
}
add_filter( 'media_view_settings', 'ff_gallery_default_type_set_link');


/**
 * Return link to "my portal"
 */
function ff_get_my_portal_link() {
	return get_home_url(); // placeholder
}
