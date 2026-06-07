<?php
/**
 * The most generic template file.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>

	<?php get_template_part('content-sidebar'); ?>

	<?php if ( have_posts() ) : ?>

		<div id="content" class="col-xs-12 col-sm-9">

			<h1 class="post-title"><?php echo ff_get_posts_page_title(); ?></h1>

			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); // start loop ?>
					<?php get_template_part('content', 'posts'); ?>
				<?php endwhile; // end loop ?>

				<?php ff_paging_nav(); ?>

			<?php else : ?>

				<p>There are no current posts. Come back later!</p>

			<?php endif; ?>

		</div><!-- /#content -->

	<?php endif; // end conditional ?>

<?php get_footer(); ?>