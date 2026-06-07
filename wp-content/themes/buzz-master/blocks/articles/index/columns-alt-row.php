<?php 
/*********************************************************************
 * Index articles in x columns with large images and an alternating full-width row
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $num_columns (int) 
 * 	- $show_featured_image (bool)
 * 	- $show_excerpt_noimage (bool)
 * 	- $show_excerpt_image (bool)
 * 	- $excerpt_length (int)
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $articles ) ) 				{ $articles = array(); }
if( !isset( $num_columns ) ) 			{ $num_columns = 3; }
if( !isset( $show_featured_image ) ) 	{ $show_featured_image = true; }
if( !isset( $show_excerpt_noimage ) ) 	{ $show_excerpt_noimage = false; }
if( !isset( $show_excerpt_image ) ) 	{ $show_excerpt_image = false; }
if( !isset( $excerpt_length ) ) 		{ $excerpt_length = 15; }
if( !isset( $image_size ) )				{ $image_size = 'article-alt'; } 
$image_size_alt 						= 'article'; ?>

<?php
// set up variables
$i = 0;
$is_alt_row = FALSE;
$last_article = end( $articles['article'] );

// calculate bootstrap column widths
$bs_col_width 		= 12 / $num_columns;

// loop articles
foreach( $articles['article'] as $article ) :
	$article_css = $article['has_thumb'] ? 'has-img' : 'no-img';

	// Check if displaying an alternate or normal row
	if( !$is_alt_row ) : 
		// NORMAL ROW - display three articles in a row

		if( $i <= 0 ) { echo '<div class="row even-height">'; } // begin row ?>

		<div class="article-container col-xs-12 
				col-sm-<?php echo $bs_col_width; ?>
				<?php echo $article_css; ?>">

			<?php // HAS FEATURED IMAGE
				if( $article['has_thumb']  ) :
					if( $show_featured_image ) : ?>
						<div class="article-thumb col-sm-12 col-xs-3 col-xs-push-9 col-sm-push-0">
							<a href="<?php echo $article['permalink']; ?>">
								<?php echo get_the_post_thumbnail( $article['id'], $image_size ); ?>
							</a>
						</div>
					<?php endif; ?>

					<div class="article-text col-sm-12 col-xs-9 col-xs-pull-3 col-sm-pull-0">
						<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
						<?php if( $show_excerpt_image ) : ?>
							<p class="excerpt hidden-xs"><?php ff_the_excerpt( $excerpt_length/2, $article['id'] ); ?></p>
						<?php endif; ?>
						<?php ff_the_tags( $article['id'] ); ?>
					</div>

			<?php // NO FEATURED IMAGE
				else : ?>
				<div class="article-text col-sm-12 col-xs-12">
					<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
					<?php if( $show_excerpt_noimage ) : ?>
						<p class="excerpt visible-lg"><?php ff_the_excerpt( $excerpt_length*1.4, $article['id'] ); ?></p>
						<p class="excerpt hidden-xs hidden-lg"><?php ff_the_excerpt( $excerpt_length, $article['id'] ); ?></p>
					<?php endif; ?>
					<?php ff_the_tags( $article['id'] ); ?>
				</div>
			<?php endif; ?>

		</div>

	<?php else : 
		// ALT ROW - display one article with big thumb and content to left ?>
		
		<div class="row alt even-height">
			<div class="article-container col-xs-12">
				<?php
					// HAS FEATURED IMAGE
					if( $article['has_thumb'] && $show_featured_image ) : ?>

						<div class="article-thumb col-xs-12 col-sm-6">
							<a href="<?php echo $article['permalink']; ?>">
								<?php echo get_the_post_thumbnail( $article['id'], $image_size_alt ); ?>
							</a>
						</div>
						<div class="article-text col-xs-12 col-sm-6">
							<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
							<p class="hidden-xs"><?php ff_the_excerpt( 80, $article['id'] ); ?></p>
							<p class="visible-xs"><?php ff_the_excerpt( 20, $article['id'] ); ?></p>
							<?php ff_the_tags( $article['id'] ); ?>
						</div>

				<?php // NO FEATURED IMAGE
					else : ?>
					<div class="article-text col-xs-12 col-sm-12">
						<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
						<p class="hidden-xs"><?php ff_the_excerpt( 80, $article['id'] ); ?></p>
						<p class="visible-xs"><?php ff_the_excerpt( 20, $article['id'] ); ?></p>
						<?php ff_the_tags( $article['id'] ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php
		// echo hr if this is NOT the last article
		if( $article !== $last_article ) {
			echo '<hr>';
		}
		
		// reset alt row and col count
		$is_alt_row = FALSE;
		$i = -1; // needs to be -1 because it is incremented at bottom of WHILE loop ?>

	<?php endif;

	// increment i and end row
	if( ++$i >= $num_columns ) {
		echo '</div>';
		// echo hr if this is NOT the last article
		if( $article !== $last_article ) {
			echo '<hr>';
		}
		$is_alt_row = TRUE; // every x articles, display an alt row
		$i = 0; // reset counter
	} ?>

<?php endforeach; ?>

<?php // if $i is not equal to zero here, it means the loop has ended without finishing a row.
if( $i != 0 ) {
	echo '</div>'; // close ROW div
} ?>

<?php // remove defaults (to prevent affecting other includes)
unset( $num_columns, $show_featured_image, $show_excerpt_noimage, $show_excerpt_image, $excerpt_length, $image_size, $image_size_alt );
?>