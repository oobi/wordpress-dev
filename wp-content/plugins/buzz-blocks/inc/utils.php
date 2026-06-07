<?php
/**
 * Helper methods for render
 */
namespace Buzz\Blocks;

 /**
 * Return first n words of article excerpt
 *
 * @param WP_Post $post
 * @param int $n
 * @return string
 */
function excerpt($post, $n) {
    // Get the excerpt or content of the post
    $html = $post->post_excerpt ? $post->post_excerpt : $post->post_content;

    // Strip HTML tags
    $text = wp_strip_all_tags($html);

    // Return first n words
    $words = preg_split('/\s+/', $text);
    $resultWords = array_slice($words, 0, $n);
    $result = implode(' ', $resultWords);
    $ellipsis = strlen($result) < strlen($text) ? '...' : '';
    return $result . $ellipsis;
}

/**
 * Returns a string of concatenated class names based on the input arguments.
 *
 * @param [type] $classNames
 * @return void
 */
function classnames($classNames) {
	return implode(' ', array_keys(array_filter($classNames, function ($val) {
		return $val;
	})));
}

/**
 * pretty debug output
 */
function dump_r($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

/**
 * Is this the email view?
 * @return boolean
 */
function is_email_view( ) {
	$has_email_view = class_exists( 'Buzz_Addon_Email_View' );
	$is_email_view = $has_email_view && \Buzz_Addon_Email_View::is_email_view();
	return $is_email_view;
}

/**
 * Is this the print view?
 * @return boolean
 */
function is_print_view( ) {
	$has_print_view = class_exists( 'Buzz_Addon_Print_View' );
	$is_print_view = $has_print_view && \Buzz_Addon_Print_View::is_print_view();
	return $is_print_view;
}

/**
 * block theme support (@since 4.0.0)
 *
 * @return boolean
 */
function is_block_theme() {
	return function_exists('wp_is_block_theme') && wp_is_block_theme();
}

/**
 * Retrieve articles after and before the current one
 *
 * @param [type] $current_article
 * @return void
 */
function get_adjacent_articles($current_article=null) {
	$post = get_post();
	if(!$current_article) $current_article = $post;

	// defaults
	$result = array(
		'previous' 	=> null,
		'current'	=> $current_article,
		'next'		=> null
	);

	// if we have a article then find its siblings
	if($current_article) {
		$parent_id = get_post_meta($current_article->ID, 'ff_parent_id', true);

		$articles = get_posts(array(
			'posts_per_page' => -1,
			'post_type' 	=> 'article',
			'post_status'	=> current_user_can('edit_others_posts') ? 'any' : 'publish',
			'orderby'		=> 'menu_order',
			'order'			=> 'ASC',
			'meta_query'    => array(
				array(
					'key' 		=> 'ff_parent_id',
					'value' 	=> $parent_id,
					'compare'	=> '='
				)
			)
		));

		$previous 	= -1;
		$next 	  	= 0;
		$count 	  	= 0;

		// iterate over newsletters to find next/previous indicies
		foreach($articles as $article) {
			if( $article->ID == $current_article->ID ){
				$previous = $count-1;
				$next 	  = $count+1;
				break;
			}
			$count++;
		}

		if($previous >= 0) 			 $result['previous'] = $articles[$previous];
		if($next < count($articles)) $result['next'] 	 = $articles[$next];
	}

	return $result;
}

/**
 * return all the defined image sizes
 *
 * @return void
 */
function get_image_dimensions() {
	// return all the defined image sizes with width/height

	$sizes = array();
	foreach(get_intermediate_image_sizes() as $size) {
		$sizes[$size] = array(
			'width'  => get_option($size.'_size_w'),
			'height' => get_option($size.'_size_h'),
			'crop'   => get_option($size.'_crop')
		);
	}

	return $sizes;
}
