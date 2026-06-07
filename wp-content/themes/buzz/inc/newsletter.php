<?php
/**
 * Methods for newsletter building
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly 1.0
 */

/////////////////////////////////////////////////////////////////////////////////////
// MAKE FRONT PAGE SHOW LATEST NEWSLETTER
/////////////////////////////////////////////////////////////////////////////////////


/**
* Use single-newsletter template insetad of default index on homepage
*/
if ( ! function_exists( 'ff_replace_home_template' ) ) :
function ff_replace_home_template( $template ) {
    global $wp_query, $post;
    if ( is_home() ) {
        $new_template = locate_template( array( 'single-newsletter.php' ) );
        if ( '' != $new_template ) {
            return $new_template ;
        }
    }
    return $template;
}
endif;
add_filter( 'template_include', 'ff_replace_home_template', 99 );

/**
 * Alter the $wp_query to get latest newsletters by default instead of posts.
 */
if ( ! function_exists( 'ff_show_latest_newsletter_on_index' ) ) :
function ff_show_latest_newsletter_on_index( $query ) {
	// plugin not active! Don't continue loading website
	if( !is_admin() && !function_exists('ff_get_latest_newsletter') ) {
		//die( "Newsletter plugin must be active" );
		return;
	}
	// newsletter plugin exists - show latest newsletter on index page
	else {

	    if ( $query->is_home() && $query->is_main_query() ) {
	    	$newsletter = ff_get_latest_newsletter();

			// check if there is a newsletter to grab
			if( $newsletter ) {
	        	$query->set( 'p', 		  $newsletter->ID );
	        	$query->set( 'post_type', $newsletter->post_type );
			}

	    }

	}
}
endif;
add_action( 'pre_get_posts', 'ff_show_latest_newsletter_on_index' );



/////////////////////////////////////////////////////////////////////////////////////
// ADMIN INTERFACE
/////////////////////////////////////////////////////////////////////////////////////

/**
 * Hide admin bar for newsletter email
 */
if ( ! function_exists( 'ff_remove_admin_bar_for_newsletter_email' ) ) :
function ff_remove_admin_bar_for_newsletter_email($value) {
	global $wp_query;
	if(ff_is_newsletter() && isset($wp_query->query_vars['email'])) {
		return FALSE;
	}
	return $value;
}
endif;
add_filter('show_admin_bar', 'ff_remove_admin_bar_for_newsletter_email');


/**
 * Show admin message if Newsletter plugin is not active
 */
if ( ! function_exists( 'ff_newsletter_plugin_not_active' ) ) :
function ff_newsletter_plugin_not_active() {

	if ( ! is_plugin_active( 'buzz-newsletter/buzz-newsletter.php' ) ) :
		$class 		= "update-nag";
		$message 	= sprintf( '<b>Firefly Newsletter</b> plugin is not active. This plugin must be activated for the website to work correctly. <a href="%s">Go to Plugins</a>', admin_url( 'plugins.php' ) );
		echo 		"<div class=\"$class\"> <p>$message</p></div>";
	endif;

}
endif;
add_action( 'admin_notices', 'ff_newsletter_plugin_not_active' );



/////////////////////////////////////////////////////////////////////////////////////
// TEMPLATE OUTPUT TWEAKS
/////////////////////////////////////////////////////////////////////////////////////

/**
 * Add 'home' to body class if we are looking at newsletter index
 * This is automatically shown when looking at /demo2 but not /demo2/newsletter/blah
 */
if ( ! function_exists( 'ff_newsletter_body_class' ) ) :
function ff_newsletter_body_class($classes = '') {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if(ff_is_newsletter(NULL, 'newsletter')) {
		array_push($classes, 'home', 'single', 'single-newsletter');
	}
	return $classes;
}
endif;
add_filter('body_class','ff_newsletter_body_class');
