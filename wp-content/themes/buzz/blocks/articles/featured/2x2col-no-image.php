<?php 
/*********************************************************************
 * 2 Featured articles in 2 columns
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $articles (array)
 * 	- $image_size (string)
 *********************************************************************/
 
// defaults 
if( !isset( $articles ) ) 			{ $articles = array(); }
if( !isset( $image_size ) )			{ $image_size = 'article'; } ?>

<div id="featured-articles" class="clearfix">
	<?php foreach( $articles['featured'] as $feature ) : ?>

		<div class="feature-container col-sm-6">

			<div class="feature-text">
				<h3><a href="<?php echo $feature['permalink']; ?>"><?php echo $feature['title']; ?></a></h3>
				<p class="excerpt"><?php ff_the_excerpt( 50, $feature['id'] ); ?></p>
				<?php ff_the_tags( $feature['id'] ); ?>
			</div>

		</div>

	<?php endforeach; ?>
</div><!-- #featured-articles -->

<?php // remove defaults (to prevent affecting other includes)
unset( $show_share_links, $image_size );
?>