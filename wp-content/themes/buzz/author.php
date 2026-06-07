<?php
/**
 * The template page for author archive pages.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>

	<div id="content" class="col-sm-12">

		<h1>Author Archives</h1>

		<?php if ( have_posts() ) : ?>

			<?php
				/* Queue the first post, that way we know
				 * what author we're dealing with (if that is the case).
				 *
				 * We reset this later so we can run the loop
				 * properly with a call to rewind_posts().
				 */
				the_post();
			?>

			<p><span class="archive-criteria"><?php the_author(); ?></span></p>

			<?php
				/* Since we called the_post() above, we need to
				 * rewind the loop back to the beginning that way
				 * we can run the loop properly, in full.
				 */
				rewind_posts();
			
			/***********************************************
			* Display posts
			***********************************************/

			$num_columns 			= 2; // Can ONLY be: 1, 2, 3, 4, 6 or 12
			$show_excerpt_image 	= true;
			$show_excerpt_noimage 	= true;
			include( locate_template( 'blocks/content/posts/columns.php' ) ); 

		else : ?>

			<p>Sorry, but nothing matched your archive criteria.<br/>Perhaps try searching.</p>
			<div class="search">
				<?php get_search_form(); ?>
			</div>

		<?php endif; ?>

	</div><!-- #content -->

<?php get_footer(); ?>