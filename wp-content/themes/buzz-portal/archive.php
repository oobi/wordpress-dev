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

	<?php get_template_part('content-sidebar'); ?>

	<?php if ( have_posts() ) : ?>

		<div id="content" class="col-xs-12 col-sm-9">

			<?php if ( is_day() ) : ?>
			<h1 class="post-title">Daily Archives</h1>
				<p><span class="archive-criteria">Archive criteria : <?php echo get_the_date(); ?></span></p>
			<?php elseif ( is_month() ) : ?>
				<h1 class="post-title">Monthly Archives</h1>
				<p><span class="archive-criteria">Archive criteria : <?php echo get_the_date( _x( 'F Y', 'monthly archives date format', 'firefly' ) ); ?></span></p>
			<?php elseif ( is_year() ) : ?>
				<h1 class="post-title">Yearly Archives</h1>
				<p><span class="archive-criteria">Archive criteria : <?php echo get_the_date( _x( 'Y', 'yearly archives date format', 'firefly' ) ); ?></span></p>
			<?php else : ?>
				<h1 class="post-title">Archives</h1>
			<?php endif; ?>

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
