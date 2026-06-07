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

		<?php get_template_part('content-sidebar', $post->post_type); ?>

		<?php while ( have_posts() ) : the_post(); // start loop ?>
			<div id="content" class="col-xs-12 col-sm-9">
				<div class="row">
					<div class="col-xs-12">
						<div class="content-hero-wrapper">
							<?php the_post_thumbnail('hero', array('class'=>'hero-image')); ?>
							<h1 class="post-title"><?php the_title(); ?></h1>
						</div>
						
						<?php ff_the_breadcrumbs(); ?>
						<div class="content-body-wrapper">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</div><!-- #content -->
		<?php endwhile; // end loop ?>

	<?php endif; // end conditional ?>

<?php get_footer(); ?>
