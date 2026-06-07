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
 * The Template for displaying all single posts
 */
$context = Timber::get_context();

$context['post'] = Timber::query_post(false, FireflyPost::class);

$templates = ['single-' . $post->ID . '.html.twig', 'single-' . $post->post_type . '.html.twig', 'single.html.twig'];

Timber::render($templates, $context);