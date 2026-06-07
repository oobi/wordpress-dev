<?php

namespace FF\Midgard;

/**
 * Create and manage custom taxonomies, post types and fields.
 *
 * @package	midgard
 * @subpackage midgard/includes
 * @author	 Firefly Interactive
 */
class Midgard_Custom_Types {

	// The menu name
	public $menu_name;

	public function __construct() {
		$this->menu_name = 'Feeds';
	}

	/**
	 * Create the custom post types
	 *
	 * @since	3.0.0
	 * @access   public
	 */
	public function add_custom_types() {

		// Feeds
		$labels = array(
			'name'                  => _x( $this->menu_name, 'Post Type General Name', 'midgard' ),
			'singular_name'         => _x( 'Feed', 'Post Type Singular Name', 'midgard' ),
			'menu_name'             => __( $this->menu_name, 'midgard' ),
			'name_admin_bar'        => __( 'Feed', 'midgard' ),
			'archives'              => __( 'Feed Archives', 'midgard' ),
			'parent_item_colon'     => __( 'Parent Feed:', 'midgard' ),
			'all_items'             => __( 'All ' . $this->menu_name, 'midgard' ),
			'add_new_item'          => __( 'Add New Feed', 'midgard' ),
			'add_new'               => __( 'Add New', 'midgard' ),
			'new_item'              => __( 'New Feed', 'midgard' ),
			'edit_item'             => __( 'Edit Feed', 'midgard' ),
			'update_item'           => __( 'Update Feed', 'midgard' ),
			'view_item'             => __( 'View Feed', 'midgard' ),
			'search_items'          => __( 'Search Feed', 'midgard' ),
			'not_found'             => __( 'Not found', 'midgard' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'midgard' ),
			'featured_image'        => __( 'Featured Image', 'midgard' ),
			'set_featured_image'    => __( 'Set featured image', 'midgard' ),
			'remove_featured_image' => __( 'Remove featured image', 'midgard' ),
			'use_featured_image'    => __( 'Use as featured image', 'midgard' ),
			'insert_into_item'      => __( 'Insert into feed', 'midgard' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'midgard' ),
			'items_list'            => __( 'Feeds list', 'midgard' ),
			'items_list_navigation' => __( 'Feeds list navigation', 'midgard' ),
			'filter_items_list'     => __( 'Filter feeds list', 'midgard' ),
		);
		$args = array(
			'labels' 				=> $labels,
			'hierarchical' 			=> false,
			'supports' 				=> array( 'title' ),
			'public' 				=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> 'midgard_app', 				// Instead of adding feeds menu page normally, we will add it as a submenu page under 'App'
			'menu_icon' 			=> 'dashicons-download',
			'show_in_nav_menus' 	=> true,
			'publicly_queryable'	=> true,
			'exclude_from_search'	=> true,
			'has_archive' 			=> true,
			'query_var' 			=> true,
			'can_export' 			=> true,
			'rewrite' 				=> true,
			'capability_type' 		=> 'post',
			'capabilities' 			=> array(
				'publish_posts' 		=> 'manage_options',
				'edit_posts' 			=> 'manage_options',
				'edit_others_posts'		=> 'manage_options',
				'delete_posts'			=> 'manage_options',
				'delete_others_posts' 	=> 'manage_options',
				'read_private_posts' 	=> 'manage_options',
				'edit_post' 			=> 'manage_options',
				'delete_post' 			=> 'manage_options',
				'read_post' 			=> 'manage_options'
			),
			//'menu_position'			=> 59		// above first separator - doesn't work if show_in_menu is not "true"
		);
		register_post_type( 'data_feed', $args );

	}

}
