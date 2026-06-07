<?php
/**
 * The template page for category archive pages.
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

			<h1 class="post-title"><?php single_cat_title( '', true ); ?></h1>
			<p>Archive of posts in the <span class="archive-criteria">'<?php single_cat_title( '', true ); ?>'</span> category</p>

			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); // start loop ?>
					<?php get_template_part('content', 'posts'); ?>
				<?php endwhile; // end loop ?>

				<?php ff_paging_nav(); ?>

			<?php else : ?>

				<p>Sorry, but nothing matched your archive criteria.<br/>Perhaps try searching.</p>
				<div class="search">
					<?php get_search_form(); ?>
				</div>

			<?php endif; ?>

		</div><!-- /#content -->

	<?php endif; // end conditional ?>

<?php get_footer(); ?>