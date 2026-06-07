<?php

namespace Firefly\Setup;

/**
 * custom
 * use it to write your custom functions.
 */
class PostTypes
{
    public function __construct()
    {
        add_action('init', [$this, 'event_post_type']);
        add_action('after_switch_theme', [$this, 'rewrite_flush']);
    }

    /**
     * Create Custom Post Types
     * Generate the post type fields here https://generatewp.com/post-type/
     * @return  mixed [array, error]
     */
    public function event_post_type() {
        $labels = array(
            'name'                  => _x( 'Events', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Events', 'text_domain' ),
            'name_admin_bar'        => __( 'Event', 'text_domain' ),
            'archives'              => __( 'Item Archives', 'text_domain' ),
            'attributes'            => __( 'Item Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
            'all_items'             => __( 'All Items', 'text_domain' ),
            'add_new_item'          => __( 'Add New Item', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Item', 'text_domain' ),
            'edit_item'             => __( 'Edit Item', 'text_domain' ),
            'update_item'           => __( 'Update Item', 'text_domain' ),
            'view_item'             => __( 'View Item', 'text_domain' ),
            'view_items'            => __( 'View Items', 'text_domain' ),
            'search_items'          => __( 'Search Item', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
            'items_list'            => __( 'Items list', 'text_domain' ),
            'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
        );

        $args = array(
            'label'                 => __( 'Event', 'text_domain' ),
            'description'           => __( 'Event Description', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions' ),
            'taxonomies'            => array( 'event_category', 'event_tag' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-calendar',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );

        register_post_type( 'event', $args );
    }

    /**
     * Flush Rewrite on CPT activation
     * @return empty
     */
    public function rewrite_flush()
    {
        // call the CPT init function
        $this->event_post_type();

        // Flush the rewrite rules only on theme activation
        flush_rewrite_rules();
    }
}
