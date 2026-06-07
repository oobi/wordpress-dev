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

$terms = get_the_terms( get_the_ID(), 'event_category' );
$term_list = wp_list_pluck( $terms, 'slug' );

$related_args = array(
	'post_type' => 'event',
	'posts_per_page' => 3,
	'post_status' => 'publish',
	'post__not_in' => [ get_the_ID() ],
	'orderby' => 'date',
	'tax_query' => [
		[
			'taxonomy' => 'event_category',
			'field' => 'slug',
			'terms' => $term_list
        ]
	],
	'meta_query' => [
		'relation' => 'OR',
		[
			'key' => '_ff_event_date',
			'value' => date('Ymd'),
			'compare' => '>=',
			'type' => 'DATE'
		],
		[
			'key' => '_ff_event_end',
			'value' => date('Ymd'),
			'compare' => '>=',
			'type' => 'DATE',
		],
	],
);

$context['location'] = $post->get_field('event_location');
$context['date'] = $post->get_field('event_date');
$context['end_date'] = $post->get_field('event_end');
$context['time'] = $post->get_field('event_time');
$context['end'] = $post->get_field('event_time_end');
$context['meta'] = $post->get_field('event_meta');

$context['related'] = Timber::get_posts($related_args, FireflyPost::class);


$templates = ['single-event.html.twig', 'single.html.twig'];

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'password-required.html.twig', $context );
} else {
	Timber::render($templates, $context);
}
