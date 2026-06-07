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

$post = Timber::query_post(false, FireflyPost::class);
$context['post'] = $post;
$context['hide_cta'] 	= carbon_get_theme_option('ff_hide_home_cta');

$templates = ['page-home.html.twig', 'page.html.twig'];

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'password-required.html.twig', $context );
} else {
	Timber::render($templates, $context);
}
