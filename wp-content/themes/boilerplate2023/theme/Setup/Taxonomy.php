<?php

namespace Firefly\Setup;

class Taxonomy
{
    protected $text_domain;

    public function __construct()
    {
        $this->text_domain = wp_get_theme()->get($this->text_domain);
		add_action('init', [$this, 'register']);
    }

    /**
     * Create Custom Taxonomies
     *
     * @return  mixed [array, error]
     */
    public function register()
    {
		// media taxonomies
		$this->media();
        $this->event_category();
        $this->event_tag();
	}

	/**
	 * Media Category
	 */
	public function media() {
		// Add new taxonomy, make it hierarchical (like categories)
        $labels = [
            'name'              => _x('Media Categories', 'taxonomy general name', $this->text_domain),
            'singular_name'     => _x('Media Category', 'taxonomy singular name', $this->text_domain),
            'search_items'      => __('Search Categories', $this->text_domain),
            'all_items'         => __('All Categories', $this->text_domain),
            'parent_item'       => __('Parent Category', $this->text_domain),
            'parent_item_colon' => __('Parent Category:', $this->text_domain),
            'edit_item'         => __('Edit Category', $this->text_domain),
            'update_item'       => __('Update Category', $this->text_domain),
            'add_new_item'      => __('Add New Category', $this->text_domain),
            'new_item_name'     => __('New Category Name', $this->text_domain),
            'menu_name'         => __('Media Category', $this->text_domain),
        ];

        $args = [
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'media-category'],
        ];

        register_taxonomy('media-category', ['attachment'], $args);
	}

    /**
	 * Event Categories
	 */
	public function event_category() {
		// Add new taxonomy, make it hierarchical (like categories)
        $labels = [
            'name'              => _x('Event Categories', 'taxonomy general name', $this->text_domain),
            'singular_name'     => _x('Event Category', 'taxonomy singular name', $this->text_domain),
            'search_items'      => __('Search Event Categories', $this->text_domain),
            'all_items'         => __('All Event Categories', $this->text_domain),
            'parent_item'       => __('Parent Event Category', $this->text_domain),
            'parent_item_colon' => __('Parent Event Category:', $this->text_domain),
            'edit_item'         => __('Edit Event Category', $this->text_domain),
            'update_item'       => __('Update Event Category', $this->text_domain),
            'add_new_item'      => __('Add New Event Category', $this->text_domain),
            'new_item_name'     => __('New Event Category Name', $this->text_domain),
            'menu_name'         => __('Event Category', $this->text_domain),
        ];

        $args = [
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'event-category'],
        ];

        register_taxonomy('event_category', ['event'], $args);
	}

    public function event_tag() {
		// Add new taxonomy, make it hierarchical (like categories)
        $labels = [
            'name'              => _x('Event Tags', 'taxonomy general name', $this->text_domain),
            'singular_name'     => _x('Tag', 'taxonomy singular name', $this->text_domain),
            'search_items'      => __('Search Tags', $this->text_domain),
            'all_items'         => __('All Tags', $this->text_domain),
            'parent_item'       => __('Parent Tag', $this->text_domain),
            'parent_item_colon' => __('Parent Tag:', $this->text_domain),
            'edit_item'         => __('Edit Tag', $this->text_domain),
            'update_item'       => __('Update Tag', $this->text_domain),
            'add_new_item'      => __('Add New Tag', $this->text_domain),
            'new_item_name'     => __('New Tag Name', $this->text_domain),
            'menu_name'         => __('Event Tags', $this->text_domain),
        ];

        $args = [
            'hierarchical'      => false,
            'show_in_rest'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'event-tag'],
        ];

        register_taxonomy('event_tag', ['event'], $args);
	}

}
