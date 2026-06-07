<?php

namespace Firefly\Setup;

class Taxonomy
{
    protected $text_domain;

    public function __construct()
    {
        $this->text_domain = wp_get_theme()->get($this->text_domain);
    }

    /**
     * Create Custom Taxonomies
     *
     * @return  mixed [array, error]
     */
    public function register()
    {
		// register taxonomies
		$this->state();
	}

	/**
	 * State
	 */
	public function state() {
		// Add new taxonomy, make it hierarchical (like categories)
        $labels = [
            'name'              => _x('States', 'taxonomy general name', $this->text_domain),
            'singular_name'     => _x('State', 'taxonomy singular name', $this->text_domain),
            'search_items'      => __('Search States', $this->text_domain),
            'all_items'         => __('All States', $this->text_domain),
            'parent_item'       => __('Parent State', $this->text_domain),
            'parent_item_colon' => __('Parent State:', $this->text_domain),
            'edit_item'         => __('Edit State', $this->text_domain),
            'update_item'       => __('Update State', $this->text_domain),
            'add_new_item'      => __('Add New State', $this->text_domain),
            'new_item_name'     => __('New State Name', $this->text_domain),
            'menu_name'         => __('State', $this->text_domain),
        ];

        $args = [
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'state'],
        ];

        register_taxonomy('state', ['page', 'post'], $args);
	}
}
