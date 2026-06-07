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
        add_action('init', [$this, 'custom_post_type']);
        add_action('after_switch_theme', [$this, 'rewrite_flush']);
    }

    /**
     * Create Custom Post Types
     * Generate the post type fields here https://generatewp.com/post-type/
     * @return  mixed [array, error]
     */
    public function custom_post_type() 
    {

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
