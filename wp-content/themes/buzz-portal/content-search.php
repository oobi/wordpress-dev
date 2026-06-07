<?php
/**
 * A list region of page or post excerpts specifically for the search page.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>
<?php
	while ( have_posts() ) : the_post(); /* start the loop */
?>
	<div class="post-item <?php echo implode(" ", get_post_class()); ?>">
		<?php
		if(has_post_thumbnail()) : ?>
			<a class="post-thumb hidden-xs" href="<?php the_permalink();?>" title="<?php the_title_attribute(); ?>">
				<?php the_post_thumbnail('home-thumb'); ?>
			</a>
		<?php endif; ?>
		<div class="post-main">
			<h3><a href="<?php the_permalink();?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
			<?php the_excerpt(); ?>
		</div>
		<div class="clear"></div>
	</div>
<?php
	endwhile; /* end the loop */
	ff_paging_nav();
	wp_reset_query();
?>
<div class="clear"></div>
