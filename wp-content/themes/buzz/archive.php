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

	<div id="content" class="col-sm-12">

		<?php if ( is_day() ) : ?>
			<h1>Daily Archives</h1>
			<p><span class="archive-criteria"><?php echo get_the_date(); ?></span></p>
		<?php elseif ( is_month() ) : ?>
			<h1>Monthly Archives</h1>
			<p><span class="archive-criteria"><?php echo get_the_date( _x( 'F Y', 'monthly archives date format', 'firefly' ) ); ?></span></p>
		<?php elseif ( is_year() ) : ?>
			<h1>Yearly Archives</h1>
			<p><span class="archive-criteria"><?php echo get_the_date( _x( 'Y', 'yearly archives date format', 'firefly' ) ); ?></span></p>
		<?php else : ?>
			<h1>Archives</h1>
		<?php endif; ?>

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
