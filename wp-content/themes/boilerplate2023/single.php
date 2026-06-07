<?php
/**
 * @package   Firefly Theme
 * @author    Firefly https://fi.net.au
 * @copyright Copyright (C) 2007 - 2018 Firefly Interactive, PTY LTD
 * @license   GNU/GPLv2 and later
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die;

use Firefly\Timber\FireflyPost;

/*
 * The Template for displaying all single posts
 */
$context = Timber::get_context();
$post = Timber::query_post(false, FireflyPost::class);
$context['post'] = $post;

///// RELATED POSTS //////

$terms = get_the_terms( get_the_ID(), 'category' );
$term_list = wp_list_pluck( $terms, 'slug' );

$related_args = array(
	'post_type' => 'post',
	'posts_per_page' => 3,
	'post_status' => 'publish',
	'post__not_in' => [ get_the_ID() ],
	'orderby' => 'date',
	'tax_query' => [
		[
			'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => $term_list
        ]
    ]
);

$context['related'] = Timber::get_posts($related_args, FireflyPost::class);

$templates = ['single-' . $post->ID . '.html.twig', 'single-' . $post->post_type . '.html.twig', 'single.html.twig'];

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'password-required.html.twig', $context );
} else {
	Timber::render($templates, $context);
}
