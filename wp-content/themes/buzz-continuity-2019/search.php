<?php

/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

use Firefly\Buzz\Timber\FireflyPost;

$context = Timber::get_context();
$context['post'] = new FireflyPost();

$posts = Timber::get_posts( false, 'Firefly\Buzz\Timber\BuzzPost' ); // get as BuzzPosts

$context['title'] = 'Search results for: '. get_search_query();
$context['posts'] = $posts;
$context['query'] = get_search_query();

$context['pagination'] = Timber::get_pagination();

$templates = [ 'search.twig', 'archive.twig', 'index.twig' ];
Timber::render( $templates, $context );
