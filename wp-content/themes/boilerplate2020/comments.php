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
 * The template for displaying comments
 */

$context = Timber::get_context();

$post = new FireflyPost();
$context['post'] = $post;

if (post_password_required($post)) {
    return;
}

$template = ['partials/comments-' . $post->ID . '.html.twig', 'partials/comments-' . $post->post_type . '.html.twig', 'partials/comments.html.twig'];

Timber::render($templates, $context);
