<?php

use Firefly\Setup\Newsletter;
use Firefly\Setup\Articles;
use Firefly\Setup\ArticleTemplates;
use Firefly\Customizer\Customizer;

$context = Timber::get_context();

// prefixes to get customizer settings
$featured_prefix 	= 'buzz_articles_featured_';
$index_prefix 		= 'buzz_index_page_';

/********************************************************************************************************
 * get the newsletter
 ********************************************************************************************************/

// if the current post is not a newsletter, get the latest newsletter
// if $post is not set, we are on the homepage so return the latest newsletter anyway
$nid = get_post_type( $post ) == 'newsletter' ? $post->ID : false;
$newsletter = Newsletter::get( $nid );

// allow gallery lightbox to work since the 'has_block' test
// does not work for the child posts from here
add_filter( 'baguettebox_enqueue_assets', '__return_true' );

// only continue if we have a newsletter
if( $newsletter ) {

	$context['newsletter'] 		= $newsletter;

	/********************************************************************************************************
	 * get taxonomies config
	 ********************************************************************************************************/

	$context['taxonomies']['tag']['type']	= Customizer::get_theme_mod( 'buzz_taxonomies_tag_type' );

	/********************************************************************************************************
	 * get the articles
	 ********************************************************************************************************/

	$articles = (new Articles($newsletter))->get()->articles;
	$featured = $articles;  // keep a second copy of the articles -- we dont want to pluck / remove them this time


	// get the featured articles template (need this to determine how many featured articles to pluck)
	$ft					= Customizer::get_theme_mod( $featured_prefix . 'template' );
	$featured_template 	= (new ArticleTemplates())->get( 'featured', $ft );
	$featured_fill 		= isset( $featured_template['fill'] ) ? $featured_template['fill'] : false;

	// split into featured/index article arrays

	$max = isset($featured_template['max']) ? $featured_template['max'] : null;

	// $context['articles']['featured'] 	= Articles::pluck( $articles, $max, ['ff_featured_article'], $featured_fill );
	$context['articles']['featured'] 	= Articles::pluck( $featured, $max, ['ff_featured_article'], $featured_fill );

	// the rest go into index
	$context['articles']['index'] 		= Articles::categorize( $articles );


	/********************************************************************************************************
	 * get featured article layout config
	 ********************************************************************************************************/

	$featured_col_width			= ff_get_col_width( $max );

	// wrapper
	$context['config']['featured']['class']					= Customizer::get_theme_mod( $featured_prefix . 'class' );

	// template
	$context['config']['featured']['template']				= isset($featured_template['slug']) ? $featured_template['slug'] : null;
	$context['config']['featured']['thumb_responsive']		= $featured_col_width == 12 ? 'article-large' : 'article';
	$context['config']['featured']['show_thumb']			= Customizer::get_theme_mod( $featured_prefix . 'thumbnails' );
	$context['config']['featured']['excerpt_class']			= Customizer::get_theme_mod( $featured_prefix . 'excerpt_class' );

	// excerpt and links
	$context['config']['featured']['excerpt']['image']		= Customizer::get_theme_mod( $featured_prefix . 'excerpt_image' );
	$context['config']['featured']['excerpt']['no_image']	= Customizer::get_theme_mod( $featured_prefix . 'excerpt_no_image' );

	/********************************************************************************************************
	 * get index article layout config
	 ********************************************************************************************************/

	// wrapper
	$context['config']['index']['class']			= Customizer::get_theme_mod( $index_prefix . 'class' );
	$context['config']['index']['title']			= Customizer::get_theme_mod( $index_prefix . 'title' );

	// template
	$context['config']['index']['show_thumb']		= Customizer::get_theme_mod( $index_prefix . 'thumbnails' );
	$context['config']['index']['overlay_title']	= Customizer::get_theme_mod( $index_prefix . 'overlay_title' );
	$context['config']['index']['show_excerpt']		= Customizer::get_theme_mod( $index_prefix . 'excerpts' );
	$context['config']['index']['text_padding']		= Customizer::get_theme_mod( $index_prefix . 'padding' );
	$context['config']['index']['thumbnail_size']	= Customizer::get_theme_mod( $index_prefix . 'thumbnail_size' ) ?? 'article_feature';
	$context['config']['index']['thumbnail_css']	= Customizer::get_theme_mod( $index_prefix . 'thumbnail_css' ) ?? 'mb-8';
	$context['config']['index']['text_css']			= Customizer::get_theme_mod( $index_prefix . 'text_css' ) ?? '';
	$context['config']['index']['article_css']		= Customizer::get_theme_mod( $index_prefix . 'article_css' ) ?? 'my-8';

	// excerpt and links
	$context['config']['index']['excerpt']['image']		= Customizer::get_theme_mod( $index_prefix . 'excerpt_image' );
	$context['config']['index']['excerpt']['no_image']	= Customizer::get_theme_mod( $index_prefix . 'excerpt_no_image' );
	$context['config']['index']['excerpt']['list']		= Customizer::get_theme_mod( $index_prefix . 'excerpt_list' );
} // if newsletter


/********************************************************************************************************
 * render
 ********************************************************************************************************/

// Set the templates array - we will unshift the appropriate template if needed
$templates = ['single-newsletter.html.twig', 'index.html.twig' ];

Timber::render( $templates, $context );
