<?php 
/*********************************************************************
 * Index articles in 2 columns
 *
 * This file must be called from within an email template
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $articles ) ) 				{ $articles = array(); }
if( !isset( $image_size ) ) 			{ $image_size = 'email-article'; }

if( !empty( $articles['in-email'] ) ) :

	$i = 0;
	$num_columns = 2;
	foreach( $articles['in-email'] as $article ) :

		if( $i % $num_columns == 0 ) {
			echo '<table class="row-container" border="0" cellpadding="0" cellspacing="0" width="640"><tr><td>';
		} ?>

		<table class="article-table" border="0" cellpadding="0" cellspacing="15" width="320" align="left">
			<tr>
				<td class="image" valign="top">
					<?php if( $article['has_thumb'] ) : ?>
						<a href="<?php echo $article['permalink']; ?>">
							<?php echo get_the_post_thumbnail( $article['id'], $image_size ); ?>
						</a>
					<?php endif; ?>
					<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
					<?php if( ! $article['has_thumb'] ) : ?>
						<p class="excerpt"><?php ff_the_excerpt( 20, $article['id'] ); ?></p>
					<?php endif; ?>
				</td>
			</tr>
		</table>

		<!-- Fix for responsive email issue in Outlook -->
		<!--[if mso]>
		</td>
		<td>
		<![endif]-->

		<?php
		// 2 columns per row
		if( $i % $num_columns == $num_columns-1 ) {
			echo '</td></tr></table>';
		}
		$i++; // iterate count

	endforeach;

	// close incomplete row
	if( ($i % $num_columns < $num_columns) && ($i % $num_columns > 0) ) {
		echo '</td></tr></table>';
	}

endif;

// remove defaults (to prevent affecting other includes)
unset( $image_size );
?>