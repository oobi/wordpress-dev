<?php 
/*********************************************************************
 * Previous/Next article links with thumbnail images
 *
 * This file must be called from an 'article' post type
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $image_size ) )	{ $image_size = 'email-article'; } 

$adjacent = ff_get_adjacent_articles(); ?>

<div id="article-prevnext" class="clearfix">

	<div class="col-xs-6 prev">
		<?php if( !empty($adjacent['previous']) ) : ?>
			<a href="<?php echo get_permalink($adjacent['previous']->ID); ?>" class="">
				<?php echo get_the_post_thumbnail( $adjacent['previous']->ID, $image_size ); ?>
				<span class="direction">Previous Article</span>
				<span class="title"><?php echo $adjacent['previous']->post_title; ?></span>
			</a>
		<?php endif; ?>
	</div>

	<div class="col-xs-6 next">
		<?php if( !empty($adjacent['next']) ) : ?>
			<a href="<?php echo get_permalink($adjacent['next']->ID); ?>" class="">
				<?php echo get_the_post_thumbnail( $adjacent['next']->ID, $image_size ); ?>
				<span class="direction">Next Article</span>
				<span class="title"><?php echo $adjacent['next']->post_title; ?></span>
			</a>
		<?php endif; ?>
	</div>

</div><!-- #article-prevnext -->

<?php // remove defaults (to prevent affecting other includes)
unset( $image_size );
?>