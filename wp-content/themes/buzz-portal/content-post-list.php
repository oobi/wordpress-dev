<?php
/**
 * A list region of page or post excerpts.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<div class="row post-item <?php echo implode(" ", get_post_class()); ?>">
	<?php
		$has_thumb = has_post_thumbnail();
		if($has_thumb) : ?>
			<div class="col-md-3">
				<a class="post-thumb" href="<?php the_permalink();?>" title="<?php the_title_attribute(); ?>">
					<?php the_post_thumbnail('landing-thumb'); ?>
				</a>
			</div>
		<?php endif; ?>
		<div class="<?php echo $has_thumb ? 'col-md-9' : 'col-md-12'; ?>">
			<div class="post-main">
				<h3><a href="<?php the_permalink();?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<?php the_excerpt(); ?>
			</div>
		</div>
</div>


