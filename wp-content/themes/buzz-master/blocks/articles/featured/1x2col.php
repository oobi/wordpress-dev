<?php 
/*********************************************************************
 * 1 Featured article in 2 columns
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $show_share_links (bool)
 * 	- $image_size (string)
 * 	- $excerpt_length (int)
 *********************************************************************/

// defaults - make sure all variables except $articles are unset at the bottom of this file
if( !isset( $articles ) ) 			{ $articles = array(); }
if( !isset( $show_share_links ) ) 	{ $show_share_links = false; }
if( !isset( $image_size ) )			{ $image_size = 'article'; } 
if( !isset( $excerpt_length ) ) 	{ $excerpt_length = 60; }


foreach( $articles['featured'] as $feature ) : ?>

	<div class="feature-container">
		<div class="feature-thumb col-sm-6">
			<a href="<?php echo $feature['permalink']; ?>">
				<?php if( $feature['has_thumb'] ) :
					echo get_the_post_thumbnail( $feature['id'], $image_size );
				endif; ?>
			</a>

			<?php // Share links
			if( $show_share_links ) { ff_share_links(); } ?>

		</div>
		<div class="feature-text col-sm-6">
			<h3><a href="<?php echo $feature['permalink']; ?>"><?php echo $feature['title']; ?></a></h3>
			<p class="excerpt"><?php ff_the_excerpt( $excerpt_length, $feature['id'] ); ?></p>
			<?php ff_the_tags( $feature['id'] ); ?>
		</div>

	</div>

<?php endforeach; 

// remove defaults (to prevent affecting other includes)
unset( $show_share_links, $image_size, $excerpt_length );
?>