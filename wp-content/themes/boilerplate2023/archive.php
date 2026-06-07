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
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */

$context = Timber::get_context();

$posts_per_page = get_option('posts_per_page');
$templates = ['archive.html.twig', 'index.html.twig'];
$context['categories'] = get_categories(['type' => 'post']);

$context['title'] = __('Archive', 'firefly');
if (is_day()) {
    $context['title'] = __('Archive:', 'firefly') . ' ' . get_the_date('j F Y');
} else if (is_month()) {
    $context['title'] = __('Archive:', 'firefly') . ' ' . get_the_date('F Y');
} else if (is_year()) {
    $context['title'] = __('Archive:', 'firefly') . ' ' . get_the_date('Y');
} else if (is_tag()) {
    $context['title'] = single_tag_title('', false);
} else if (is_category()) {
    $context['title'] = single_cat_title('', false);

    $obj = get_queried_object();
	$cat_slug = $obj->slug;
    $context['cat_slug'] = $cat_slug;

    array_unshift($templates, 'archive-' . get_query_var('cat') . '.html.twig');
} else if (is_tax()) {
    $context['title'] = single_term_title('', false);

    $obj = get_queried_object();
	$cat_slug = $obj->slug;
    $context['cat_slug'] = $cat_slug;

    if(get_post_type() == 'event') {
        $context['categories'] = get_terms('event_category', [
            'hide_empty' => false
        ]);
    }

    array_unshift($templates, 'archive-' . get_post_type() . '.html.twig');
} else if (is_post_type_archive()) {
    $context['title'] = post_type_archive_title('', false);

    if(get_post_type() == 'event') {
        $context['categories'] = get_terms('event_category', [
            'hide_empty' => false
        ]);
    }

    array_unshift($templates, 'archive-' . get_post_type() . '.html.twig');
}

if( get_post_type() == 'post')


$context['posts'] = new Timber\PostQuery();
$context['pagination'] = get_pagination();

Timber::render($templates, $context);