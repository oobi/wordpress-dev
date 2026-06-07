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
	 ***********************************************/ ?>

	 <div class="articles-outer col-sm-4 col-sm-push-8">
		<?php 
		$image_size = 'article';
		include( locate_template( 'blocks/articles/featured/1x1col.php' ) ); ?>
	</div>

	<?php 
	/***********************************************
	 * Display Index articles
	 ***********************************************/ ?>

	 <div class="articles-outer col-sm-8 col-sm-pull-4">
	 	<?php 
		$num_columns 			= 2; // Can ONLY be: 1, 2, 3, 4, 6 or 12
		$show_excerpt_noimage 	= true;
		include( locate_template( 'blocks/articles/index/columns-list.php' ) ); ?>
	</div>

<?php endwhile; // end loop

get_footer(); ?>