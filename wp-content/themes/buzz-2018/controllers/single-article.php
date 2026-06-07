<?php

use Firefly\Buzz\Setup\Newsletter;
use Firefly\Buzz\Setup\Articles;
use Firefly\Buzz\Setup\ArticleTemplates;
use Firefly\Buzz\Timber\BuzzPost;
use Firefly\Buzz\Customizer\Customizer;

$context = Timber::get_context();


/********************************************************************************************************
 * get the newsletter
 ********************************************************************************************************/

$newsletter 		= Newsletter::get( $post->ff_parent_id, true );
$article			= new BuzzPost();
$context['post'] 		= $article;

// only continue if we have a newsletter and article
if( $newsletter ) {
	$issue_list 		= (new Articles( $newsletter ))->get()->articles;
	$context['newsletter'] 	= $newsletter;

	// add taxonomy classes to body if addon is active
	if( $context['addons']['taxonomies'] ) {
		$context['body_class'] .= $article->classes();
	}

	/********************************************************************************************************
	 * Hero image options
	 ********************************************************************************************************/

	$context['hero']['show'] 		= Customizer::get_theme_mod( 'buzz_article_page_hero_show' );
	$context['hero']['position'] 	= Customizer::get_theme_mod( 'buzz_article_page_hero_position' );
	$context['hero']['suppress'] 	= get_post_meta( $post->ID, 'ff_suppress_featured_image', true );

	/********************************************************************************************************
	 * the article navigation
	 ********************************************************************************************************/

	$adjacent 					= ff_get_adjacent_articles();
	$context['prev_article']	= $adjacent['previous'] ? 	new TimberPost( $adjacent['previous'] )	: false;
	$context['next_article']	= $adjacent['next'] ? 		new TimberPost( $adjacent['next'] ) 	: false;

	/********************************************************************************************************
	 * the sidebar data
	 ********************************************************************************************************/

	$context['sidebar']['title']				= Customizer::get_theme_mod( 'buzz_article_page_sidebar_title' );
	$context['sidebar']['position']				= Customizer::get_theme_mod( 'buzz_article_page_sidebar_position' );
	$context['sidebar']['class']				= Customizer::get_theme_mod( 'buzz_article_page_sidebar_class' );

	// get the featured articles template (need this to determine how many featured articles to pluck)
	$ft					= Customizer::get_theme_mod( 'buzz_articles_featured_template' );
	$featured_template 	= (new ArticleTemplates())->get( 'featured', $ft );
	$featured_fill 		= isset( $featured_template['fill'] ) ? $featured_template['fill'] : false;

	// $context['sidebar']['articles']['featured'] = Articles::pluck( $issue_list, $featured_template['max'], ['ff_featured_article'], $featured_fill );
	$context['sidebar']['articles']['index'] 	= Articles::categorize( $issue_list ); // the rest go into index

} // if newsletter


/********************************************************************************************************
 * render
 ********************************************************************************************************/

// Set the templates array - we will unshift the appropriate template if needed
$templates = [ 'single-article.twig' ];
Timber::render( $templates, $context );