<?php

namespace Firefly\Buzz\Setup;

/**
 * custom
 * use it to write your custom functions.
 */
class PostTypes
{
    public function __construct()
    {
        add_action('init', array($this, 'custom_post_type'));
        add_action('after_switch_theme', array($this, 'rewrite_flush'));
    }

    /**
     * Create Custom Post Types
     * @return The registered post type object, or an error object
     */
    public function custom_post_type()
    {
        $labels = array(
            'name'               => _x( 'Projects', 'post type general name', 'firefly' ),
            'singular_name'      => _x( 'Project', 'post type singular name', 'firefly' ),
            'menu_name'          => _x( 'Projects', 'admin menu', 'firefly' ),
            'name_admin_bar'     => _x( 'Project', 'add new on admin bar', 'firefly' ),
            'add_new'            => _x( 'Add New', 'book', 'firefly' ),
            'add_new_item'       => __( 'Add New Project', 'firefly' ),
            'new_item'           => __( 'New Project', 'firefly' ),
            'edit_item'          => __( 'Edit Project', 'firefly' ),
            'view_item'          => __( 'View Project', 'firefly' ),
            'view_items'         => __( 'View Projects', 'firefly' ),
            'all_items'          => __( 'All Projects', 'firefly' ),
            'search_items'       => __( 'Search Projects', 'firefly' ),
            'parent_item_colon'  => __( 'Parent Projects:', 'firefly' ),
            'not_found'          => __( 'No projects found.', 'firefly' ),
            'not_found_in_trash' => __( 'No projects found in Trash.', 'firefly' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'firefly' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-admin-customizer',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'projects' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5, // below post
            'supports'           => array( 'title', 'thumbnail' )
        );

        // register_post_type( 'project', $args );
        // register_taxonomy_for_object_type( 'category', 'project' );
    }

    /**
     * Flush Rewrite on CPT activation
     * @return empty
     */
    public function rewrite_flush()
    {
        // call the CPT init function
        $this->custom_post_type();

        // Flush the rewrite rules only on theme activation
        flush_rewrite_rules();
    }
}
