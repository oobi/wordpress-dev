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
use Timber\Timber;

/*
 * The template for displaying Author Archive pages
 */

global $wp_query;

$context = Timber::get_context();
$context['post'] = new FireflyPost();

$context['posts'] = Timber::get_posts();
$context['pagination'] = Timber::get_pagination();

if (isset($authordata)) {
    $author            = new \Timber\User($authordata->ID);
    $context['author'] = $author;
    $context['title']  = __('Author:', 'firefly') . ' ' . $author->name();
}
$templates = ['author.html.twig', 'archive.html.twig', 'index.html.twig'];

Timber::render($templates, $context);