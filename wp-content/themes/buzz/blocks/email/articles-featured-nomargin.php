<?php 
/*********************************************************************
 * Featured articles in 1 column
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
if( !isset( $image_size ) ) 			{ $image_size = 'article'; }

if( !empty( $articles['featured'] ) ) :
	foreach( $articles['featured'] as $feature ) : ?>

		<table class="feature-table" border="0" cellpadding="0" cellspacing="0">
			<tr class="feature">
				<td class="feature image" valign="top">
					<a href="<?php echo $feature['permalink']; ?>">
						<?php if( has_post_thumbnail( $feature['id'] ) ) :
							echo get_the_post_thumbnail( $feature['id'], $image_size );
						endif; ?>
					</a>
				</td>
			</tr>
			<tr class="feature-excerpt">
				<td valign="top">
					<h3><?php echo $feature['title']; ?></h3>
					<?php ff_the_excerpt( 25, $feature['id'] ); ?>
				</td>
			</tr>
		</table>

	<?php endforeach;
endif;

// remove defaults (to prevent affecting other includes)
unset( $image_size );
?>