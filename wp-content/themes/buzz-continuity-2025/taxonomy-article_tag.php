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

use Firefly\Customizer\Customizer;

$context = Timber::get_context();

// Get term details
$context['term'] = new TimberTerm();

// get posts
$context['posts'] = Timber::get_posts( false, 'Firefly\Timber\BuzzPost' ); // get as BuzzPosts
$context['pagination'] = Timber::get_pagination();

// excerpt and links
$index_prefix 		= 'buzz_index_page_';
$context['config']['index']['excerpt']['image']		= Customizer::get_theme_mod( $index_prefix . 'excerpt_image' );
$context['config']['index']['excerpt']['no_image']	= Customizer::get_theme_mod( $index_prefix . 'excerpt_no_image' );


Timber::render( [ 'taxonomy-article_tag.html.twig', 'taxonomy.html.twig' ], $context );
