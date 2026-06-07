<?php

namespace Buzz\Blocks;

/**
 * Plugin Name:       Buzz Blocks
 * Description:       Gutenberg blocks for The Buzz
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Firefly Interactive
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       buzz
 *
 * @package           buzz
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

define('BUZZ_BLOCKS_DIR', plugin_dir_path(__FILE__));
define('BUZZ_BLOCKS_DIR_URL', plugin_dir_url(__FILE__));

// utility methods
require_once(BUZZ_BLOCKS_DIR . '/inc/utils.php');

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */


/**
 * Registering the blocks
 */
add_action('init', function () {
	// register_block_type(__DIR__ . '/build/block1');
	// register_block_type(__DIR__ . '/build/block2');


	register_block_type(__DIR__ . '/build/share-links');
	register_block_type(__DIR__ . '/build/dates');
	register_block_type(__DIR__ . '/build/date');
	// register_block_type(__DIR__ . '/build/categories');
	register_block_type(__DIR__ . '/build/article-grid');
	register_block_type(__DIR__ . '/build/article-sidebar');
	register_block_type(__DIR__ . '/build/article-nav');
	register_block_type(__DIR__ . '/build/buzz-nav');

	// only register if the legacy dates plugin is active
	if( defined('BUZZ_ADDON_DATES')) {
		register_block_type(__DIR__ . '/build/dates-legacy');
	}
});

/**
 * Adding a new category to the block editor.
 */
add_filter('block_categories_all', function ($categories) {

	// Adding a new category.
	$categories[] = array(
		'slug'  => 'buzz',
		'title' => 'Buzz Blocks'
	);

	return $categories;
});

/**
 * Enqueue script to give us access to paths and globals
 */
add_action('enqueue_block_editor_assets', function () {
	wp_register_script('buzz-blocks', '');
	wp_enqueue_script('buzz-blocks');

	wp_localize_script('buzz-blocks', 'buzzblocks', array(
		'BUZZ_BLOCKS_DIR' => BUZZ_BLOCKS_DIR,
		'BUZZ_BLOCKS_DIR_URL' => BUZZ_BLOCKS_DIR_URL,
		'BUZZ_IMAGE_DIMENSIONS' => get_image_dimensions()
	));

	if( defined('BUZZ_ADDON_DATES')) {
		$legacy_date_sets = get_theme_mod( 'buzz_dates_sets' );

		wp_localize_script('buzz-blocks', 'buzzblockslegacy', array(
			'BUZZ_LEGACY_DATE_SETS' => $legacy_date_sets
		));
	}
});

/**
 * Add the text-align class to the block wrapper
 */
add_filter('block_buzz-share-links_get_custom_class_name', function ($attributes, $context) {

	if (isset($attributes['textAlign'])) {
		$text_align = $attributes['textAlign'];
		return "text-align-{$text_align}";
	}

	return '';
}, 10, 2);

/**
 * Allow REST to filter by meta_key and meta_value
 */
add_filter('rest_article_query', function ($args, $request) {
	// single meta query
	if ($meta_key = $request->get_param('metaKey')) {
		$args['meta_key'] = $meta_key;
		$args['meta_value'] = $request->get_param('metaValue');
	}

	// multiple meta queryies
	if ($meta_query = $request->get_param('metaQuery')) {
		$args['meta_query'] = $meta_query;
	}

	return $args;
}, 10, 2);



/**
 * Add link on newsletter list item - "edit layout"
 */
add_filter('post_row_actions', function ($actions, $post) {
	global $current_screen, $mode;

	// only for newsletter post type
	if ($post->post_type != 'newsletter') {
		return $actions;
	}

	$post_type_object = get_post_type_object($post->post_type);

	// only if current user is an editor or better
	if (!current_user_can($post_type_object->cap->publish_posts, $post->ID)) {
		return $actions;
	}

	// create layout edit link
	$edit_url = get_edit_post_link($post->ID);
	$layout_url = add_query_arg('bz_edit_mode', 'meta', $edit_url);

	// Add layout edit link
	$actions['buzz_edit_layout'] = sprintf(
		'<a href="%s" title="%s">%s</a>',
		$layout_url,
		esc_attr(__('Edit the articles of this newsletter', 'ff_newsletter')),
		__('Edit articles', 'ff_newsletter')
	);

	return $actions;
}, 10, 2);


/**
 * Add the block editor to the newsletter post type
 */

// toggle between block editor and meta edit
$editMode = $_GET['bz_edit_mode'] ?? 'layout';

if ($editMode === 'meta') {

	add_action('init', function () {
		remove_post_type_support('newsletter', 'editor');
	}, 100);

} else {

	add_action('init', function () {
		add_post_type_support('newsletter', 'editor');
	}, 100);

	/**
	 * Conditionally remove Buzz custom meta boxes
	 */
	add_action('buzz_after_custom_meta_box', function () {
		global $wp_meta_boxes;
		$boxes = $wp_meta_boxes['newsletter']['normal']['default'] ?? [];
		foreach ($boxes as $box) {
			// if the box id starts with 'newsletter' remove it
			if (isset($box['id']) && strpos($box['id'], 'newsletter') === 0) {
				remove_meta_box($box['id'], 'newsletter', 'normal');
			}
		}
	}, 100);
}
