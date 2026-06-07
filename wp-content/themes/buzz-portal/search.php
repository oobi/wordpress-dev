<?php
/**
 * The template page for search results.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>

<?php get_template_part('content-sidebar'); ?>

<div id="content" class="col-xs-12 col-sm-9">
	<div class="row">
		<div class="col-xs-12">
			<?php //ff_the_breadcrumbs(); ?>

			<?php if ( have_posts() ) : ?>
				<h1 class="post-title">Search Results</h1>
				<p>For keywords <span class="search-criteria"><?php echo get_search_query() ?></span></p>

				<div class="post-list">
				<?php while ( have_posts() ) : the_post(); // start loop ?>
					<?php get_template_part('content-post-list'); ?>
				<?php endwhile; // end loop ?>
				</div>

				<?php ff_paging_nav(); ?>

			<?php else : ?>

				<h1 class="entry-title"><?php _e( 'Nothing Found', 'firefly' ); ?></h1>
				<p>Sorry, but nothing matched <span class="search-criteria"><?php echo get_search_query() ?></span>.<br/>Please try again with some different keywords.</p>
				<div class="search">
					<?php get_search_form(); ?>
				</div>

			<?php endif; ?>

		</div><!-- /.col-xs-12 -->
	</div><!-- /.row -->
</div><!-- /#content -->

<?php get_footer(); ?>


