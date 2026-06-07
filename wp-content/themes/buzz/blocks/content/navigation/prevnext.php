<?php 
/*********************************************************************
 * Previous/Next article links.
 *
 * This file must be called from an 'article' post type
 *********************************************************************/

$adjacent = ff_get_adjacent_articles(); ?>

<div id="article-prevnext" class="clearfix">

	<div class="col-xs-6 prev">
		<?php if( !empty($adjacent['previous']) ) : ?>
			<a href="<?php echo get_permalink($adjacent['previous']->ID); ?>" class="">
				<span class="direction"><i class="fa fa-angle-left"></i> Previous Article</span>
				<span class="title"><?php echo $adjacent['previous']->post_title; ?></span>
			</a>
		<?php endif; ?>
	</div>

	<div class="col-xs-6 next">
		<?php if( !empty($adjacent['next']) ) : ?>
			<a href="<?php echo get_permalink($adjacent['next']->ID); ?>" class="">
				<span class="direction">Next Article <i class="fa fa-angle-right"></i></span>
				<span class="title"><?php echo $adjacent['next']->post_title; ?></span>
			</a>
		<?php endif; ?>
	</div>

</div><!-- #article-prevnext -->