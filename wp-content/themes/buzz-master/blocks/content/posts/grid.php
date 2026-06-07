<?php
/*********************************************************************
 *  A generic grid of articles for all result-based pages 
 * 	(search results, category archives, tag archives)
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $num_columns (int) 
 * 	- $show_excerpt_noimage (bool)
 * 	- $show_excerpt_image (bool)
 * 	- $show_date (bool)
 * 	- $excerpt_length (int)
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $articles ) ) 				{ $articles = array(); }
if( !isset( $num_columns ) ) 			{ $num_columns = 2; }
if( !isset( $show_excerpt_noimage ) ) 	{ $show_excerpt_noimage = false; }
if( !isset( $show_excerpt_image ) ) 	{ $show_excerpt_image = false; }
if( !isset( $show_date ) ) 				{ $show_date = false; }
if( !isset( $excerpt_length ) ) 		{ $excerpt_length = 12; }
if( !isset( $image_size ) )				{ $image_size = 'thumbnail'; } ?>

<div id="query-results" class="clearfix">
<?php
	// calculate bootstrap column widths
	$bs_col_width 		= 12 / $num_columns;

	// display index articles
	$i = 0;

	while ( have_posts() ) : the_post(); /* start the loop */

		$newsletter 		= ff_get_newsletter( get_the_ID() );
		$newsletter_title 	= ff_get_newsletter_title( $newsletter );
		$newsletter_url		= ff_get_newsletter_url( $newsletter );
		$article_id 		= get_the_ID();

		if( $i <= 0 ) { echo '<div class="row even-height">'; } // begin row ?>

		<div class="post-container 
					<?php echo !has_post_thumbnail() ? 'no-img' : ''; ?> 
					col-sm-<?php echo $bs_col_width; ?> 
					col-xs-12">

			<?php // HAS FEATURED IMAGE
			if( has_post_thumbnail() ) : ?>
				<div class="post-thumb col-sm-12 col-xs-3 col-xs-push-9 col-sm-push-0">
					<a href="<?php the_permalink(); ?>">
						<?php echo get_the_post_thumbnail( get_the_ID(), $image_size ); ?>
					</a>
				</div>
				<div class="post-text col-sm-12 col-xs-9 col-xs-pull-3 col-sm-pull-0">
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php if( $show_date ) : ?>
							<p class="date"><?php the_date(); ?></p>
						<?php endif; ?>
					<?php if( $show_excerpt_image ) : ?>
						<p class="excerpt hidden-xs hidden-sm"><?php ff_the_excerpt( $excerpt_length/2, get_the_ID() ); ?></p>
					<?php endif; ?>
					<?php ff_the_tags( get_the_ID() ); ?>
				</div>

			<?php // NO FEATURED IMAGE
			else : ?>
				<div class="post-wrapper">
					<div class="post-text no-img">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php if( $show_date ) : ?>
							<p class="date"><?php the_date(); ?></p>
						<?php endif; ?>
						<?php if( $show_excerpt_noimage ) : ?>
							<p class="excerpt hidden-xs hidden-sm"><?php ff_the_excerpt( $excerpt_length, get_the_ID() ); ?></p>
						<?php endif; ?>
						<?php ff_the_tags( get_the_ID() ); ?>
					</div>
				</div>
			<?php endif; ?>

		</div>

		<?php // increment i and end row
		if( ++$i >= $num_columns ) {
			echo '</div>';
			$i = 0; // reset counter
		}

	endwhile; /* end the loop */

	// if $i is not equal to zero here, it means the loop has ended without finishing a row.
	if( $i != 0 ) {
		echo '</div>'; // close ROW div
	} ?>

</div><!-- #query-results -->

<?php // pagination
	ff_paging_nav();
	wp_reset_query(); 

	// remove defaults (to prevent affecting other includes)
	unset( $num_columns, $show_excerpt_noimage, $show_excerpt_image, $excerpt_length, $image_size );
?>