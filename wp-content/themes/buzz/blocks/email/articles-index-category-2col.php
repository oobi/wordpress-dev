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

	// display index articles in category sections 
	foreach( $articles['in-email'] as $category ) : 
		$cat_slug = $category['cat_slug'] ? $category['cat_slug'] : 'uncategorized';
		$cat_name = $category['cat_name'] ? $category['cat_name'] : 'General'; ?>

		<table class="category-container <?php echo $cat_slug; ?>" border="0" cellpadding="0" cellspacing="0" width="640">
			<tr><td>
				<h2 class="category-name"><span><?php echo $cat_name; ?></span></h2>
			</td></tr>
		</table>

		<?php
		$i = 0;
		$num_columns = 2;
		foreach( $category['articles'] as $article ) :

			if( $i % $num_columns == 0 ) {
				echo '<table class="row-container ' . $cat_slug . '" border="0" cellpadding="0" cellspacing="0" width="640"><tr><td>';
			} ?>

			<table class="<?php echo $article['has_thumb'] ? '' : 'no-thumb'; ?> article-table" border="0" cellpadding="0" cellspacing="15" width="320" align="left">
				<tr>
					<td class="<?php echo $article['has_thumb'] ? 'image' : 'no-image'; ?>" valign="top">
						<table class="article-table-content" border="0" cellpadding="0" cellspacing="0" width="100%">
							<?php if( $article['has_thumb'] ) : ?>
								<tr><td class="thumb">
									<a href="<?php echo $article['permalink']; ?>">
										<?php echo get_the_post_thumbnail( $article['id'], $image_size ); ?>
									</a>
								</td></tr>
							<?php endif; ?>
							<tr><td class="text">
								<span class="header"><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></span>
								<span class="more"><a href="<?php echo $article['permalink']; ?>">Read more</a></span>
							</td></tr>
						</table>
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

	endforeach;

endif;

// remove defaults (to prevent affecting other includes)
unset( $image_size );
?>