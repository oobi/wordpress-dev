<?php
/**
 * The template page for newsletter archive pages.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>

	<div id="content">

		<div class="col-sm-12">
			<h1>Newsletter Archive</h1>
		</div>

		<?php if(have_posts()) : ?>

			<div id="newsletter-archive" class="clearfix">

				<?php 
				/***********************************************
				* Display posts
				***********************************************/

				$num_columns 			= 2; // Can ONLY be: 1, 2, 3, 4, 6 or 12
				$show_excerpt_image 	= false;
				$show_excerpt_noimage 	= false;
				$show_date 				= true;
				include( locate_template( 'blocks/content/posts/columns.php' ) ); ?>

			</div><!-- #newsletter-archive -->

		<?php endif; ?>

	</div><!-- #content -->

<?php get_footer(); ?>
