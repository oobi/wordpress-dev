<?php

use Firefly\Buzz\Setup\Newsletter;
use Firefly\Buzz\Setup\Articles;
use Firefly\Buzz\Setup\ArticleTemplates;
use Firefly\Buzz\Customizer\Customizer;
use Timber\Timber;
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

	$articles = (new Articles($newsletter))->get()->articles;

	$context['articles'] = $articles;

} // if newsletter

/********************************************************************************************************
 * output
 ********************************************************************************************************/

Timber::render( 'single-newsletter-print.twig', $context );
