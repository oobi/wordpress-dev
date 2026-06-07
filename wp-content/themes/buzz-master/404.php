<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

	<div id="content" class="col-md-12">

		<h1 class="entry-title"><?php _e( 'Page not found', 'firefly' ); ?></h1>
		<p>Sorry, but we can't find the page you are looking for.<br/>Perhaps try to search for it.</p>

		<div class="search-wrapper">
			<?php get_search_form(); ?>
		</div>

	</div><!-- #content -->

<?php get_footer(); ?>