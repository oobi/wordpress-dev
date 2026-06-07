<?php
/**
 * Template Name: Homepage
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

$context = Timber::get_context();

$context['post'] = Timber::query_post(false, FireflyPost::class);

$templates = ['page-home.html.twig', 'page.html.twig'];

Timber::render($templates, $context);