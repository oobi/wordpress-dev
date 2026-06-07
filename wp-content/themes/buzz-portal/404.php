<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

	<?php get_template_part('content-sidebar'); ?>

	<div id="content" class="col-xs-12 col-sm-9">
		<div class="row">
			<div class="col-xs-12">
				<h1 class="page-title"><?php _e( 'Page not found', 'firefly' ); ?></h1>
				<p>Sorry, but we can't find the page you are looking for.<br/>Perhaps try to search for it.</p>
				<div class="search">
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</div><!-- #content -->

<?php get_footer(); ?>