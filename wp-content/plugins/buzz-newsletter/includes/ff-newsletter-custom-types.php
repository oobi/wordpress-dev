<?php

/**
 * Create and manage custom taxonomies, post types and fields.
 *
 * @package	ff_newsletter
 * @subpackage ff_newsletter/includes
 * @author	 Firefly Interactive
 */
class FF_Newsletter_Custom_Types {

	/**
	 * Create the custom post types
	 *
	 * @since	3.0.0
	 * @access   public
	 */
	public function add_custom_types() {

		// Newsletters
		$labels = array(
			'name' 					=> _x( 'Newsletters', 'ff_newsletter' ),
			'singular_name' 		=> _x( 'Newsletter', 'ff_newsletter' ),
			'add_new' 				=> _x( 'Add New', 'ff_newsletter' ),
			'add_new_item' 			=> _x( 'Add New Newsletter', 'ff_newsletter' ),
			'edit_item' 			=> _x( 'Edit Newsletter', 'ff_newsletter' ),
			'new_item' 				=> _x( 'New Newsletter', 'ff_newsletter' ),
			'view_item' 			=> _x( 'View Newsletter', 'ff_newsletter' ),
			'search_items' 			=> _x( 'Search Newsletters', 'ff_newsletter' ),
			'not_found' 			=> _x( 'No newsletters found', 'ff_newsletter' ),
			'not_found_in_trash'	=> _x( 'No newsletters found in Trash', 'ff_newsletter' ),
			'parent_item_colon' 	=> _x( 'Parent Newsletter:', 'ff_newsletter' ),
			'menu_name' 			=> _x( 'Newsletters', 'ff_newsletter' ),
		);

		$args = array(
			'labels' 				=> $labels,
			'hierarchical' 			=> false,
			'supports' 				=> array( 'title', 'thumbnail' ),
			'public' 				=> true,
			'show_in_rest' 			=> true,
			'rest_base' 			=> 'newsletter',
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'menu_icon' 			=> 'dashicons-book',
			'show_in_nav_menus' 	=> true,
			'publicly_queryable'	=> true,
			'exclude_from_search'	=> false,
			'has_archive' 			=> true,
			'query_var' 			=> true,
			'can_export' 			=> true,
			'rewrite' 				=> true,
			'capability_type' 		=> 'post',
			//'menu_position'			=> 22		// above 'comments'
		);
		register_post_type( 'newsletter', $args );

		// Articles
		$labels = array(
			'name' 					=> _x( 'Articles', 'ff_newsletter' ),
			'singular_name' 		=> _x( 'Article', 'ff_newsletter' ),
			'add_new' 				=> _x( 'Add New', 'ff_newsletter' ),
			'add_new_item' 			=> _x( 'Add New Article', 'ff_newsletter' ),
			'edit_item' 			=> _x( 'Edit Article', 'ff_newsletter' ),
			'new_item' 				=> _x( 'New Article', 'ff_newsletter' ),
			'view_item' 			=> _x( 'View Article', 'ff_newsletter' ),
			'search_items' 			=> _x( 'Search Articles', 'ff_newsletter' ),
			'not_found' 			=> _x( 'No articles found', 'ff_newsletter' ),
			'not_found_in_trash' 	=> _x( 'No articles found in Trash', 'ff_newsletter' ),
			'parent_item_colon' 	=> _x( 'Newsletter:', 'ff_newsletter' ),
			'menu_name' 			=> _x( 'Articles', 'ff_newsletter' ),
		);

		$args = array(
			'labels'				=> $labels,
			'hierarchical'			=> false,
			'supports' 				=> array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt' ),
			'public' 				=> true,
			'show_in_rest' 			=> true,
			'rest_base' 			=> 'article',
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'menu_icon' 			=> 'dashicons-media-text',
			'show_in_nav_menus' 	=> true,
			'publicly_queryable' 	=> true,
			'exclude_from_search' 	=> false,
			'has_archive' 			=> true,
			'query_var' 			=> true,
			'can_export' 			=> true,
			'rewrite' 				=> true,
			'capability_type' 		=> 'post',
			//'menu_position'			=> 22		// above 'comments'
		);
		register_post_type( 'article', $args );

	}

}
