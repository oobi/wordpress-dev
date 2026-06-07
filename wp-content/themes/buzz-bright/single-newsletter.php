<?php
/**
 * Newsletter article index view (the main view of the newsletter)
 */
get_header(); 

while ( have_posts() ) : the_post(); // start loop 

	// Get all articles
	$args = array(
		'parent-id'			=> get_the_ID()
	);
	$all_articles = ff_get_article_query($args);

	// Format the articles array to be displayed
	$articles = ff_format_article_array( $all_articles, 2 );

	/***********************************************
	 * Display Featured articles
	 ***********************************************/

	$show_share_links = true;
	include( locate_template( 'blocks/articles/featured/1x1col.php' ) ); ?>

	<hr>

	<?php
	/***********************************************
	 * Display Index articles
	 ***********************************************/
	
	$num_columns 			= 3; // Can ONLY be: 1, 2, 3, 4, 6 or 12
	$show_excerpt_image 	= true;
	$show_excerpt_noimage 	= true;
	$excerpt_length 		= 30;
	include( locate_template( 'blocks/articles/index/columns-alt-row.php' ) );

endwhile; // end loop 

get_footer(); ?>