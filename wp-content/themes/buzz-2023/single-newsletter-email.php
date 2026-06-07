<?php
use Firefly\Customizer\Customizer;

// Set to FALSE to just echo without inlining (debug)
$inline = true;

// if unsubscribe link is not set, show error
if( class_exists( 'Buzz_Addon_Email_View' ) && ! Buzz_Addon_Email_View::is_unsubscribe_set() ) {
	$message = Buzz_Addon_Email_View::unsubscribe_not_set_message();
	wp_die( $message );
}

use Firefly\Buzz\Newsletter;
use Firefly\Buzz\Articles;
use Firefly\Buzz\ArticleTemplates;

$context = Timber::get_context();

// prefixes to get customizer settings
$featured_prefix 	= 'buzz_articles_featured_';
$index_prefix 		= 'buzz_index_page_';
$email_prefix 		= 'buzz_email_';

/********************************************************************************************************
 * get the newsletter
 ********************************************************************************************************/

// if the current post is not a newsletter, get the latest newsletter
// if $post is not set, we are on the homepage so return the latest newsletter anyway
$nid = get_post_type( $post->ID ) == 'newsletter' ? $post->ID : false;
$newsletter = Newsletter::get( $nid );

// only continue if we have a newsletter
if( $newsletter ) {

	$context['newsletter'] 		= $newsletter;
		
	/********************************************************************************************************
	 * get the articles
	 ********************************************************************************************************/

	// get articles slated for email
	$articles = (new Articles($newsletter))->get()->filter()->articles;

	// get the featured articles template (need this to determine how many featured articles to pluck)
	$ft					= Customizer::get_theme_mod( $email_prefix . 'featured_template' );
	$featured_template 	= (new ArticleTemplates())->get( 'email_featured', $ft );

	// split into featured/index article arrays
	$context['articles']['featured'] 	= Articles::pluck( $articles, $featured_template['max'], ['ff_featured_article'], false );
	$context['articles']['index'] 		= Articles::categorize( $articles ); // the rest go into index


	/********************************************************************************************************
	 * get featured article layout config
	 ********************************************************************************************************/

	// template
	$context['config']['featured']['template']				= $featured_template['slug'];
	$context['config']['featured']['show_thumb']			= Customizer::get_theme_mod( $email_prefix . 'featured_thumbnails' );
	$context['config']['featured']['text_padding']			= Customizer::get_theme_mod( $email_prefix . 'featured_text_padding' );

	// excerpt and links
	$context['config']['featured']['excerpt']['image']		= Customizer::get_theme_mod( $email_prefix . 'featured_excerpt_image' );
	$context['config']['featured']['excerpt']['no_image']	= Customizer::get_theme_mod( $email_prefix . 'featured_excerpt_no_image' );


	/********************************************************************************************************
	 * get index article layout config
	 ********************************************************************************************************/

	// TODO: Get rid of any of these settings not used in email view

	// wrapper
	$context['config']['index']['class']			= Customizer::get_theme_mod( $index_prefix . 'class' );

	// template
	$context['config']['index']['template']			= Customizer::get_theme_mod( $email_prefix . 'index_template' );
	$context['config']['index']['col_width']		= ff_get_col_width( Customizer::get_theme_mod( $index_prefix . 'columns' ) );
	$context['config']['index']['show_thumb']		= Customizer::get_theme_mod( $email_prefix . 'index_thumbnails' );
	$context['config']['index']['text_padding']		= Customizer::get_theme_mod( $email_prefix . 'index_text_padding');

	// excerpt and links
	$context['config']['index']['excerpt']['image']		= Customizer::get_theme_mod( $email_prefix . 'index_excerpt_image' );
	$context['config']['index']['excerpt']['no_image']	= Customizer::get_theme_mod( $email_prefix . 'index_excerpt_no_image' );
	$context['config']['index']['excerpt']['list']	= Customizer::get_theme_mod( $email_prefix . 'index_excerpt_list' );

	/********************************************************************************************************
	 * footer
	 ********************************************************************************************************/

	$context['footer']['credit'] 	= Customizer::get_theme_mod( $email_prefix . 'credit' );
	$context['widgets']['after_index'] = Timber::get_widgets('buzz-widget-after-index');
} // if newsletter

/********************************************************************************************************
 * inline
 ********************************************************************************************************/

// buffer the output to a variable so we can send to the inliner
ob_start();
Timber::render( 'email/base.html.twig', $context );
$html = ob_get_clean();

// inline the HTML
if( $inline && class_exists( 'FF_Newsletter_Inline_CSS' ) ) {
	$html = FF_Newsletter_Inline_CSS::inline_html($html);
}

/********************************************************************************************************
 * add email styles as an inline style block (responsive blocks don't work otherwise)
 ********************************************************************************************************/

// inject custom stylesheet for font
global $wp_styles;

// this is a text variable which will be built up of HTML content to inject into the BODY tag
$injected = '';


// create email <style> tag
if( array_key_exists( 'buzz-email-view', $wp_styles->registered ) ) {
	$email_css_url  = $wp_styles->registered['buzz-email-view']->src;
	$to_inject = wp_remote_get( $email_css_url );
	$injected  .= is_array( $to_inject ) && isset( $to_inject['body'] ) ? "\n" . '<style>' . $to_inject['body'] . '</style>' : '';
}

// create customizer <style> tag
if( array_key_exists( 'buzz-email-view-custom', $wp_styles->registered ) ) {
	$email_css_url  = $wp_styles->registered['buzz-email-view-custom']->src;
	$to_inject = wp_remote_get( $email_css_url );
	$injected  .= is_array( $to_inject ) && isset( $to_inject['body'] ) ? "\n" . '<style>' . $to_inject['body'] . '</style>' : '';
}

// outlook fixes
$injected .= "\n" . '<!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->';

// inject into email body
if( !empty( $injected ) ) {
	$html = preg_replace( '@<body[^>]*>@', '$0' . $injected, $html );
	$html = str_replace( 'font-family:inherit;', '', $html ); // remove font-family:inherit from email view (doesn't work)
}

/********************************************************************************************************
 * render
 ********************************************************************************************************/

echo $html;