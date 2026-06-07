<?php 
/*********************************************************************
 * 2 Featured articles in 2 columns
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $image_size (string)
 * 	- $show_featured_image (bool)
 * 	- $excerpt_length (int)
 *********************************************************************/
 
// defaults - make sure all variables except $articles are unset at the bottom of this file
if( !isset( $articles ) ) 				{ $articles = array(); }
if( !isset( $image_size ) )				{ $image_size = 'article'; } 
if( !isset( $show_featured_image ) ) 	{ $show_featured_image = true; }
if( !isset( $excerpt_length ) ) 		{ $excerpt_length = 25; }


foreach( $articles['featured'] as $feature ) : ?>

	<div class="feature-container col-sm-6">

		<?php if( $feature['has_thumb'] && $show_featured_image ) : ?>
			<div class="feature-thumb">
				<a href="<?php echo $feature['permalink']; ?>">
					<?php echo get_the_post_thumbnail( $feature['id'], $image_size ); ?>
				</a>
			</div>
		<?php endif; ?>

		<div class="feature-text">
			<h3><a href="<?php echo $feature['permalink']; ?>"><?php echo $feature['title']; ?></a></h3>
			<p class="excerpt <?php echo $show_featured_image ? 'hidden-xs' : '';?>"><?php ff_the_excerpt( $excerpt_length, $feature['id'] ); ?></p>
			<?php ff_the_tags( $feature['id'] ); ?>
		</div>

	</div>

<?php endforeach; 

// remove defaults (to prevent affecting other includes)
unset( $show_share_links, $image_size, $show_featured_image, $excerpt_length );
?>