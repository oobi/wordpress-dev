<?php 
/*********************************************************************
 * Index articles in x columns
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
if( !isset( $num_columns ) ) 			{ $num_columns = 4; }
if( !isset( $show_featured_image ) ) 	{ $show_featured_image = true; }
if( !isset( $show_excerpt_noimage ) ) 	{ $show_excerpt_noimage = false; }
if( !isset( $show_excerpt_image ) ) 	{ $show_excerpt_image = false; }
if( !isset( $excerpt_length ) ) 		{ $excerpt_length = 15; }
if( !isset( $image_size ) )				{ $image_size = 'article'; } ?>

<?php
// calculate bootstrap column widths
$bs_col_width 		= 12 / $num_columns;

// display index articles
$i = 0;
foreach( $articles['article'] as $article ) :
	if( $i <= 0 ) { echo '<div class="row even-height">'; } // begin row ?>

	<div class="article-container clearfix
				<?php echo $article['has_thumb'] ? 'has-img' : 'no-img'; ?> 
				col-sm-<?php echo $bs_col_width; ?>">

		<?php // If the Customizer option is set to show featured images and article has one, show it
		if( $show_featured_image && $article['has_thumb'] ) : ?>
			<div class="article-thumb col-sm-12 col-xs-3 col-xs-push-9 col-sm-push-0">
				<a href="<?php echo $article['permalink']; ?>">
					<?php echo get_the_post_thumbnail( $article['id'], $image_size ); ?>
				</a>
			</div>
		<?php endif; ?>

		<?php // If the Customizer option is set to show featured images AND article doesn't have one, show special article text
		if( $show_featured_image && !$article['has_thumb'] ) : ?>
			<div class="article-wrapper">
				<div class="article-text no-img">
					<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
					<?php if( $show_excerpt_noimage ) : ?>
						<p class="excerpt hidden-xs hidden-sm"><?php ff_the_excerpt( $excerpt_length, $article['id'] ); ?></p>
					<?php endif; ?>
					<?php ff_the_tags( $article['id'] ); ?>
				</div>
			</div>
		<?php 
		// else just show the regular article text
		else : ?>
			<div class="article-text col-sm-12 col-xs-9 col-xs-pull-3 col-sm-pull-0">
				<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
				<?php if( $show_excerpt_image ) : ?>
					<p class="excerpt hidden-xs hidden-sm"><?php ff_the_excerpt( $excerpt_length/2, $article['id'] ); ?></p>
				<?php endif; ?>
				<?php ff_the_tags( $article['id'] ); ?>
			</div>
		<?php endif; ?>

	</div>

	<?php // increment i and end row
	if( ++$i >= $num_columns ) {
		echo '</div>';
		$i = 0; // reset counter
	} ?>

<?php endforeach; ?>

<?php // if $i is not equal to zero here, it means the loop has ended without finishing a row.
if( $i != 0 ) {
	echo '</div>'; // close ROW div
} 

// remove defaults (to prevent affecting other includes)
unset( $num_columns, $show_featured_image, $show_excerpt_noimage, $show_excerpt_image, $excerpt_length, $image_size );
?>