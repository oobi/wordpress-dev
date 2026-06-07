<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Firefly\Buzz\Timber\FireflyPost;

$context = Timber::get_context();
$context['post'] = new FireflyPost();

$context['title'] = 'Resource not found';

Timber::render( '404.twig', $context );
