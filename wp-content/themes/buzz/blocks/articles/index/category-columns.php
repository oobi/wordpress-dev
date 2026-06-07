<?php 
/*********************************************************************
 * Categorized index articles in x columns
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $num_columns (int) 
 * 	- $show_excerpt_noimage (bool)
 * 	- $show_excerpt_image (bool)
 * 	- $excerpt_length (int)
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $articles ) ) 				{ $articles = array(); }
if( !isset( $num_columns ) ) 			{ $num_columns = 4; }
if( !isset( $show_excerpt_noimage ) ) 	{ $show_excerpt_noimage = false; }
if( !isset( $show_excerpt_image ) ) 	{ $show_excerpt_image = false; }
if( !isset( $excerpt_length ) ) 		{ $excerpt_length = 15; }
if( !isset( $image_size ) )				{ $image_size = 'article'; } ?>

<div id="index-articles" class="clearfix">
<?php
	// calculate bootstrap column widths
	$bs_col_width 		= 12 / $num_columns;

	// display index articles in category sections 
	foreach( $articles['article'] as $category ) : 
		$cat_slug = $category['cat_slug'] ? $category['cat_slug'] : 'uncategorized';
		$cat_name = $category['cat_name'] ? $category['cat_name'] : 'General';
	?>

		<div class="article-category <?php echo $cat_slug; ?>">
			<h2 class="category-name"><?php echo $cat_name; ?></h2>

			<?php // display index articles 
			$i = 0;
			foreach( $category['articles'] as $article ) :

				if( $i <= 0 ) { echo '<div class="row even-height">'; } // begin row ?>

				<div class="article-container clearfix
							<?php echo $article['has_thumb'] ? 'has-img' : 'no-img'; ?> 
							col-sm-<?php echo $bs_col_width; ?>">

					<?php // HAS FEATURED IMAGE
					if( $article['has_thumb'] ) : ?>
						<div class="article-thumb col-sm-12 col-xs-3 col-xs-push-9 col-sm-push-0">
							<a href="<?php echo $article['permalink']; ?>">
								<?php echo get_the_post_thumbnail( $article['id'], $image_size ); ?>
							</a>
						</div>
						<div class="article-text col-sm-12 col-xs-9 col-xs-pull-3 col-sm-pull-0">
							<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
							<?php if( $show_excerpt_image ) : ?>
								<p class="excerpt hidden-xs hidden-sm"><?php ff_the_excerpt( $excerpt_length/2, $article['id'] ); ?></p>
							<?php endif; ?>
							<?php ff_the_tags( $article['id'] ); ?>
						</div>

					<?php // NO FEATURED IMAGE
					else : ?>
						<div class="article-wrapper">
							<div class="article-text">
								<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
								<?php if( $show_excerpt_noimage ) : ?>
									<p class="excerpt hidden-xs hidden-sm"><?php ff_the_excerpt( $excerpt_length, $article['id'] ); ?></p>
								<?php endif; ?>
								<?php ff_the_tags( $article['id'] ); ?>
							</div>
						</div>
					<?php endif; ?>

				</div>

				<?php // increment i and end row
				if( ++$i >= $num_columns ) {
					echo '</div>';
					$i = 0; // reset counter
				} 
				
			endforeach; 
			
			// if $i is not equal to zero here, it means the loop has ended without finishing a row.
			if( $i != 0 ) {
				echo '</div>'; // close ROW div
			} ?>

		</div><!-- /article-category -->

	<?php endforeach; ?>

</div><!-- #index-articles -->

<?php // remove defaults (to prevent affecting other includes)
unset( $num_columns, $show_excerpt_noimage, $show_excerpt_image, $excerpt_length, $image_size );
?>