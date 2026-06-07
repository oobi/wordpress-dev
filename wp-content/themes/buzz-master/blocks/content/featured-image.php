<?php 
/*********************************************************************
 * Article Featured image
 *
 * This file must be called from within the loop 
 *********************************************************************/

// defaults 
if( !isset( $image_size ) )	{ $image_size = 'article-large'; } 

$has_thumb = has_post_thumbnail(); 

if( $has_thumb ) : ?>

	<div id="featured-image" <?php echo !$has_thumb ? 'class="hidden-xs"' : ''; ?>>

		<?php $attr = array(
				'class' => 'col',
				'alt'   => get_bloginfo( 'description', 'display' )
			);
			the_post_thumbnail( $image_size, $attr ); ?>

	</div><!-- #featured-image-->

<?php endif; ?>

<?php // remove defaults (to prevent affecting other includes)
unset( $image_size );
?>