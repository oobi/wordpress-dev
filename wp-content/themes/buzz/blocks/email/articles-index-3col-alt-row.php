<?php 
/*********************************************************************
 * Index articles in 3 columns with an alternate full-width row
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
$image_size_alt 						= 'email-article-alt';

if( !empty( $articles['in-email'] ) ) :

	$i = 0;
	$num_columns = 3;
	$is_alt_row = FALSE;
	foreach( $articles['in-email'] as $article ) :

		// Check if displaying an alternate or normal row
		if( !$is_alt_row ) :

			/****************************************
			* REGULAR ROW
			****************************************/

			if( $i % $num_columns == 0 ) {
				echo '<table class="row-container" border="0" cellpadding="0" cellspacing="0" width="640"><tr><td>';
			} ?>

			<table class="article-table" border="0" cellpadding="0" cellspacing="15" width="213" align="left">
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
			</td><td>
			<![endif]-->

			<?php
			// x columns per row
			if( $i % $num_columns == $num_columns-1 ) :
				echo '</td></tr></table>';
			endif;

		else :

			/****************************************
			* ALTERNATE ROW
			****************************************/ ?>

			<table class="row-container" border="0" cellpadding="0" cellspacing="0" width="640"><tr><td>
				<table class="article-table-alt" border="0" cellpadding="0" cellspacing="15" width="320" align="left">
					<tr>
						<td class="image" valign="top">
							<?php if( $article['has_thumb'] ) : ?>
								<a href="<?php echo $article['permalink']; ?>">
									<?php echo get_the_post_thumbnail( $article['id'], $image_size_alt ); ?>
								</a>
							<?php endif; ?>
						</td>
					</tr>
				</table>

				<table class="article-table-alt" border="0" cellpadding="0" cellspacing="15" width="320" align="left">
					<tr>
						<td class="excerpt" valign="top">
							<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
							<p class="excerpt"><?php ff_the_excerpt( 30, $article['id'] ); ?></p>
						</td>
					</tr>
				</table>

				<!-- Fix for responsive email issue in Outlook -->
				<!--[if mso]>
				</td><td>
				<![endif]-->
			</td></tr></table>

			<?php
			// reset alt row and col count
			$is_alt_row = FALSE;
			$i = -1; // needs to be -1 because it is incremented at bottom of FOREACH

		endif;

		// increment i and end row
		if( ++$i >= $num_columns ) : 
			$is_alt_row = TRUE; // every x articles, display an alt row
			$i = 0; // reset counter ?>
		<?php endif;

	endforeach;

endif;

// remove defaults (to prevent affecting other includes)
unset( $image_size );
?>