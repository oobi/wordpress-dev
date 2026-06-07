<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

	<div id="content">

		<h1 class="entry-title"><?php _e( 'No content found', 'firefly' ); ?></h1>
		<p>There are no posts currently. Check back later!</p>

		<div class="search-wrapper">
			<?php get_search_form(); ?>
		</div>

	</div><!-- #content -->

<?php get_footer(); ?>