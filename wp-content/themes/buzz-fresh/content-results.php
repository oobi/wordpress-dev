<?php
/**
 * A generic list of articles for all result-based pages (search results, category archives, tag archives)
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */

// row params
$col_count = 4;
$i = 0;
?>

<div id="query-results" class="clearfix">

	<?php while ( have_posts() ) : the_post(); /* start the loop */

		$newsletter 		= ff_get_newsletter( get_the_ID() );
		$newsletter_title 	= ff_get_newsletter_title( $newsletter );
		$newsletter_url		= ff_get_newsletter_url( $newsletter );
		$article_id 		= get_the_ID();

		if( $i <= 0 ) { echo '<div class="row">'; } // begin row ?>

			<div class="article-container col-sm-3 col-xs-12">
				<?php if(has_post_thumbnail()) : ?>
					<div class="article-thumb">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail('article'); ?>
						</a>
					</div>
					<div class="article-text">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p><?php printf('<a href="%s">%s</a>', $newsletter_url, $newsletter_title); ?></p>
						<?php ff_the_tags( $article_id ); ?>
					</div>

				<?php // NO FEATURED IMAGE
				else : ?>
					<div class="article-wrapper col-sm-12 col-xs-9">
						<div class="article-text no-img">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="excerpt"><?php ff_the_excerpt( 10, $article_id ); ?></p>
							<p><?php printf('<a href="%s">%s</a>', $newsletter_url, $newsletter_title); ?></p>
							<?php ff_the_tags( $article_id ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

		<?php // increment i and end row
		if( ++$i >= $col_count ) {
			echo '</div>';
			$i = 0; // reset counter
		}

	endwhile; /* end the loop */ ?>

	<?php // if $i is not equal to zero here, it means the loop has ended without finishing a row.
	if( $i != 0 ) {
		echo '</div>'; // close ROW div
	} ?>

</div><!-- #newsletter-archive -->

<?php
	// pagination
	ff_paging_nav();
	wp_reset_query();
?>