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

	<div id="search-results">

		<?php if ( have_posts() ) : ?>

			<h1><span class="glyphicon glyphicon-search" aria-hidden="true" title="Tags"></span>&nbsp;&nbsp;"<span class="search-criteria"><?php echo get_search_query() ?></span>"</h1>
			<?php 

			/***********************************************
			* Display posts
			***********************************************/

			$num_columns 			= 2; // Can ONLY be: 1, 2, 3, 4, 6 or 12
			$show_excerpt_image 	= true;
			$show_excerpt_noimage 	= true;
			include( locate_template( 'blocks/content/posts/columns.php' ) );

		else : ?>

			<h1 class="entry-title"><?php _e( 'Nothing Found', 'firefly' ); ?></h1>
			<p>Sorry, but nothing matched <span class="search-criteria"><?php echo get_search_query() ?></span>.<br/>Please try again with some different keywords.</p>

			<div class="search-wrapper">
				<?php get_search_form(); ?>
			</div>

		<?php endif; ?>

	</div><!-- #content -->

<?php get_footer(); ?>