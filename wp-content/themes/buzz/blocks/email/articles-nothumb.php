<?php 
/*********************************************************************
 * Articles with no featured image list
 *
 * This file must be called from within an email template
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 *********************************************************************/

// defaults 
if( !isset( $articles ) ) 				{ $articles = array(); }

if( !empty( $articles['no-thumb'] ) ) : ?>

	<table border="0" cellpadding="0" cellspacing="15" width="610" align="center" class="no-thumb-article-table">

		<?php
		// display email articles with no featured image
		foreach( $articles['no-thumb'] as $article ) : ?>
			<tr class="no-thumb">
				<td class="no-thumb-article" valign="top">
					<h3><a href="<?php echo $article['permalink']; ?>"><?php echo $article['title']; ?></a></h3>
				</td>
			</tr>
		<?php endforeach; ?>

	</table>

<?php endif; ?>