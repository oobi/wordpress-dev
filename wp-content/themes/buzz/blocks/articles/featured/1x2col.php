<?php 
/*********************************************************************
 * 1 Featured article in 2 columns
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $show_share_links (bool)
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $articles ) ) 			{ $articles = array(); }
if( !isset( $show_share_links ) ) 	{ $show_share_links = false; }
if( !isset( $image_size ) )			{ $image_size = 'article'; } ?>

<div id="featured-articles" class="clearfix">
	<?php foreach( $articles['featured'] as $feature ) : ?>

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
				<p class="excerpt"><?php ff_the_excerpt( 60, $feature['id'] ); ?></p>
				<?php ff_the_tags( $feature['id'] ); ?>
			</div>

		</div>

	<?php endforeach; ?>
</div><!-- #featured-articles -->

<?php // remove defaults (to prevent affecting other includes)
unset( $show_share_links, $image_size );
?>