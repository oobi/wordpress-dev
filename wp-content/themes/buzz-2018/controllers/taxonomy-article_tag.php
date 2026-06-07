<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$context = Timber::get_context();

// Get term details
$context['term'] = new TimberTerm();

// get posts
$context['posts'] = Timber::get_posts( false, 'Firefly\Buzz\Timber\BuzzPost' ); // get as BuzzPosts

Timber::render( [ 'taxonomy-article_tag.twig', 'taxonomy.twig' ], $context );
