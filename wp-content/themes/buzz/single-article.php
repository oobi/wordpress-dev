<?php
/**
 * The template page for the Article post type.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>

	<?php while ( have_posts() ) : the_post(); // start loop ?>

		<div id="content" class="col-sm-8 col-md-9">

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

			<?php 
			/***********************************************
			 * PREV/NEXT NAVIGATION 
			 ***********************************************/
			 
			get_template_part( 'blocks/content/navigation/prevnext' ); ?>

		</div><!-- #content -->

		<?php 
		/***********************************************
		 * SIDEBAR 
		 ***********************************************/

		$show_share_links = true;
		include( locate_template( 'blocks/content/sidebar/article-list.php' ) ); ?>

	<?php endwhile; // end loop ?>

<?php get_footer(); ?>