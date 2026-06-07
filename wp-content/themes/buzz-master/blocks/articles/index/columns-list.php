<?php 
/*********************************************************************
 * Index articles in x columns in list format with no images
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $num_columns (int) 
 * 	- $show_excerpt_noimage (bool)
 * 	- $excerpt_length (int)
 *********************************************************************/

// defaults 
if( !isset( $articles ) ) 				{ $articles = array(); }
if( !isset( $num_columns ) ) 			{ $num_columns = 4; }
if( !isset( $show_excerpt_noimage ) ) 	{ $show_excerpt_noimage = false; }
if( !isset( $excerpt_length ) ) 		{ $excerpt_length = 15; } ?>

<?php
// calculate bootstrap column widths
$bs_col_width 		= 12 / $num_columns;

// display index articles
$i = 0;
$highlight_count = 0;
foreach( $articles['article'] as $article ) :
	$is_highlight = $highlight_count++ < $num_columns;
	if( $i <= 0 ) { echo '<div class="row even-height">'; } // begin row ?>

	<div class="article-container 
				<?php echo $article['has_thumb'] ? 'has-img' : 'no-img'; ?> 
				col-sm-<?php echo $bs_col_width; ?> 
				<?php echo $is_highlight ? 'article-highlight' : ''; ?>">

		<div class="article-wrapper">
			<div class="article-text">
				<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
				<?php if( $show_excerpt_noimage ) : ?>
					<p class="excerpt hidden-xs hidden-sm"><?php ff_the_excerpt( $excerpt_length, $article['id'] ); ?></p>
				<?php endif; ?>
				<?php ff_the_tags( $article['id'] ); ?>
			</div>
		</div>

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
unset( $num_columns, $show_excerpt_noimage, $excerpt_length );
?>