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
 * Search results page
 */

$context = Timber::get_context();
$content['post'] = new FireflyPost();
$context['title'] = __('Search results', 'firefly');
$context['query'] = get_search_query();
$context['posts'] = Timber::get_posts();
$context['pagination'] = get_pagination();

$templates = ['search.html.twig', 'archive.html.twig', 'index.html.twig'];

Timber::render($templates, $context);