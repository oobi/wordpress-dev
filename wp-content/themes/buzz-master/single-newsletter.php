<?php
/**
 * Newsletter article index view (the main view of the newsletter)
 */
get_header(); 

if( class_exists('Timber') ) :

while ( have_posts() ) : the_post(); // start loop

	// Get the customizer settings 
	$articles_layout 			= get_theme_mod( 'ff_theme_articles_layout' );
	$featured_articles_layout 	= get_theme_mod( 'ff_theme_featured_articles_layout' );
	$index_articles_layout 		= get_theme_mod( 'ff_theme_index_articles_layout' );
	$index_articles_excerpt 	= get_theme_mod( 'ff_theme_index_articles_excerpt' );
	$index_articles_image 		= get_theme_mod( 'ff_theme_index_articles_image' );

	// set the number of featured articles to display
	switch( $featured_articles_layout ) {
		case 'single-2-col' :
		case 'single-2-col-alt' :
		case 'single-1-col' :
			$num_featured_articles = 1;
			break;
		case 'double-2-col-text' :
		case 'double-2-col-alt' :
		case 'double-2-col' :
		default :
			$num_featured_articles = 2;
			break;
	}

	// Get all articles
	$args = array(
		'parent-id'			=> get_the_ID()
	);
	$all_articles = ff_get_article_query( $args );

	// Format the articles array to be displayed
	$articles = ff_format_article_array( $all_articles, $num_featured_articles );

	/***********************************************
	 * Display Featured articles
	 ***********************************************/ 

	// display the featured articles according to chosen layout
	$featured_context = array();
	$featured_context['layout'] 				= $articles_layout;
	$featured_context['featured_layout'] 		= $featured_articles_layout;
	$featured_context['articles'] 				= $articles['featured'];
	$featured_context['show_featured_image'] 	= true;
	$featured_context['show_share_links'] 		= false;
	$featured_context['image_size'] 			= 'article';
	$featured_context['excerpt_length'] 		= 60;
	switch( $featured_articles_layout ) {
		case 'double-2-col-text' :
			$featured_context['show_featured_image'] = false;
			$featured_context['excerpt_length'] = 50;
			Timber::render('articles/featured/2x2col.twig', $featured_context);
			break;
		case 'double-2-col-alt' :
			$featured_context['excerpt_length'] = 15;
			Timber::render('articles/featured/2x2col.twig', $featured_context);
			break;
		case 'single-2-col' :
		case 'single-2-col-alt' :
			Timber::render('articles/featured/1x2col.twig', $featured_context);
			break;
		case 'single-1-col' :
			$featured_context['image_size'] = 'banner';
			Timber::render('articles/featured/1x1col.twig', $featured_context);
			break;
		case 'double-2-col' :
		default :
			$featured_context['excerpt_length'] = 30;
			Timber::render('articles/featured/2x2col.twig', $featured_context);
			break;
	}

	/***********************************************
	 * Display Index articles
	 ***********************************************/ 

	// display the index articles according to chosen layout
	$index_context = array();
	$index_context['layout'] 				= $articles_layout;
	$index_context['index_layout'] 			= $index_articles_layout;
	$index_context['articles'] 				= $articles['article'];
	$index_context['col_width'] 			= ff_get_col_width( 4 ); // Calc bootstrap col width. 12 divided by number of columns (Can ONLY be: 1, 2, 3, 4, 6 or 12)
	$index_context['show_featured_image'] 	= $index_articles_image;
	$index_context['show_excerpt'] 			= $index_articles_excerpt;
	$index_context['excerpt_length'] 		= 15;
	switch( $index_articles_layout ) {
		case 'three-col-grid' :
			$index_context['col_width'] = ff_get_col_width( 3 );
			Timber::render('articles/index/columns.twig', $index_context);
			break;
		case 'two-col-grid' :
			$index_context['col_width'] = ff_get_col_width( 2 );
			Timber::render('articles/index/columns.twig', $index_context);
			break;
		case 'three-col-alt-grid' :
			$index_context['col_width'] = ff_get_col_width( 3 );
			Timber::render('articles/index/columns-alt-row.twig', $index_context);
			break;
		case 'two-col-list-highlight' :
			$index_context['col_width'] = ff_get_col_width( 2 );
			$index_context['show_featured_image'] = false;
			Timber::render('articles/index/columns-list.twig', $index_context);
			break;
		case 'four-col-grid' :
		default :
			Timber::render('articles/index/columns.twig', $index_context);
			break;
	}	

endwhile; // end loop

endif; // end class_exists

get_footer(); ?>