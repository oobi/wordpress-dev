<?php
/**
 * Template Name: Events
 *
 * @package   Firefly Theme
 * @author    Firefly https://fi.net.au
 * @copyright Copyright (C) 2007 - 2018 Firefly Interactive, PTY LTD
 * @license   GNU/GPLv2 and later
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die;

use Firefly\Timber\FireflyPost;

global $paged;
if (!isset($paged) || !$paged){
    $paged = 1;
}

$context = Timber::get_context();
$post = Timber::query_post(false, FireflyPost::class);
$context['post'] = $post;

// load all events from the events post type that are in the future or today using timber
$context['posts'] = new Timber\PostQuery([
	'post_type' => 'event',
	'post_status' => 'publish',
	'posts_per_page' => 12,
	'paged' => $paged,
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
	'orderby' => 'meta_value',
	'order' => 'ASC',
], FireflyPost::class);

// get pagination
$context['pagination'] = get_pagination();

$context['cat_slug'] = get_queried_object()->slug;
$context['categories'] = get_terms([
	'taxonomy' => 'event_category',
	'hide_empty' => true,
]);

$templates = ['page-events.html.twig', 'page.html.twig'];

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'password-required.html.twig', $context );
} else {
	Timber::render($templates, $context);
}