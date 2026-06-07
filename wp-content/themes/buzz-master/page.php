<?php
/**
 * Description: Default page template.
 *
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>

	<?php if ( have_posts() ) : ?>


		<?php while ( have_posts() ) : the_post(); // start loop ?>

			<div id="content">

				<?php
				// featured image in header

				/***********************************************
				* TITLE AND CONTENT 
				***********************************************/

				// this heading should only show in XS (handset) mode ?>
				<h1 class="post-title visible-xs"><?php the_title(); ?></h1>

				<div class="clearfix">
					<?php the_content(); ?>
				</div>

			</div><!-- #content -->

		<?php endwhile; // end loop ?>

	<?php endif; // end conditional ?>

<?php get_footer(); ?>