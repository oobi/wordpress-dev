<?php
/*
Template Name: Redirect to first child
*/


// get the first child page
$first_child = get_pages( [
	'child_of' => $post->ID,
	'parent' => $post->ID,
	'sort_column' => 'menu_order',
	'number' => 1,
	'sort_order' => 'asc'
] );


if (is_array($first_child) && count($first_child) > 0) {
	wp_redirect(get_permalink($first_child[0]->ID));
} else {
	include( __DIR__ . '/page.php');
}
