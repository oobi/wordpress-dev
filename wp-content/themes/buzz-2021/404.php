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
 * The template for displaying 404 pages (Not Found)
 */
$context = Timber::get_context();

$context['post'] = new FireflyPost();
$context['title'] = __('404 Page not found', 'firefly');

Timber::render('404.html.twig', $context);