<?php

namespace FF\LogicalDoc;

/**
 * Create and manage custom taxonomies, post types and fields.
 *
 * @package	ff_newsletter
 * @subpackage ff_newsletter/includes
 * @author	 Firefly Interactive
 */
class Custom_Types {

	/**
	 * Create the custom post types
	 *
	 * @since	3.0.0
	 * @access   public
	 */
	public function add_custom_types() {

		$labels = array(
			'name'                       => _x( 'Logical Tags', 'Taxonomy General Name', 'ff-logicaldoc' ),
			'singular_name'              => _x( 'Logical Tag', 'Taxonomy Singular Name', 'ff-logicaldoc' ),
			'menu_name'                  => __( 'Logical Tags', 'ff-logicaldoc' ),
			'all_items'                  => __( 'All Items', 'ff-logicaldoc' ),
			'parent_item'                => __( 'Parent Item', 'ff-logicaldoc' ),
			'parent_item_colon'          => __( 'Parent Item:', 'ff-logicaldoc' ),
			'new_item_name'              => __( 'New Item Name', 'ff-logicaldoc' ),
			'add_new_item'               => __( 'Add New Item', 'ff-logicaldoc' ),
			'edit_item'                  => __( 'Edit Item', 'ff-logicaldoc' ),
			'update_item'                => __( 'Update Item', 'ff-logicaldoc' ),
			'view_item'                  => __( 'View Item', 'ff-logicaldoc' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'ff-logicaldoc' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'ff-logicaldoc' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'ff-logicaldoc' ),
			'popular_items'              => __( 'Popular Items', 'ff-logicaldoc' ),
			'search_items'               => __( 'Search Items', 'ff-logicaldoc' ),
			'not_found'                  => __( 'Not Found', 'ff-logicaldoc' ),
			'no_terms'                   => __( 'No items', 'ff-logicaldoc' ),
			'items_list'                 => __( 'Items list', 'ff-logicaldoc' ),
			'items_list_navigation'      => __( 'Items list navigation', 'ff-logicaldoc' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'logical-tag', array( 'post' ), $args );

	}

}
