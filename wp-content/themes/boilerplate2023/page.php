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
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.html.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 */

$context = Timber::get_context();
$post = Timber::query_post(false, FireflyPost::class);
$context['post'] = $post;
$templates = ['page-' . $post->post_name . '.html.twig', 'page.html.twig'];

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'password-required.html.twig', $context );
} else {
	Timber::render($templates, $context);
}
