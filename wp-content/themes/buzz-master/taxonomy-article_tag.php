<?php
/**
 * The template page for archive pages.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>

	<div id="content">

			<h1><?php single_tag_title( '', true ); ?></h1>

			<?php if ( have_posts() ) : 
			
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
