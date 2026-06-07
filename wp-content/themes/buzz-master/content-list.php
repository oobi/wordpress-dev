<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>

<div class="article-container col-sm-3 col-xs-12">
	<div class="article-thumb col-sm-12 col-xs-3">
		<a href="<?php the_permalink(); ?>">
			<?php if(has_post_thumbnail()) :
   				the_post_thumbnail('article');
   			else : ?>
   				<?php printf('<img src="%s" class="placeholder-img">', get_template_directory_uri() . '/images/default/placeholder.png'); ?>
   			<?php endif; ?>
		</a>
	</div>
	<div class="article-text col-sm-12 col-xs-9">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	</div>
</div>
